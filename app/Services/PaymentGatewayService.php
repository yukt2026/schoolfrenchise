<?php

namespace App\Services;

use App\Models\Admission;
use App\Models\AuditLog;
use App\Models\FeePayment;
use App\Models\OnlineTransaction;
use App\Models\PaymentReceipt;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\SystemNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class PaymentGatewayService
{
    /**
     * Get the active gateway provider.
     */
    public static function getProvider(): string
    {
        return config('services.payment.provider', 'razorpay');
    }

    /**
     * Check if payment gateway is in sandbox / test mode.
     */
    public static function isSandbox(): bool
    {
        return config('services.payment.mode', 'sandbox') === 'sandbox';
    }

    /**
     * Get key secret based on current provider and environment.
     */
    public static function getKeySecret(): string
    {
        $provider = self::getProvider();

        if ($provider === 'cashfree') {
            $secret = config('services.payment.cashfree.secret_key');
        } else {
            $secret = config('services.payment.razorpay.key_secret');
        }

        if (empty($secret)) {
            if (!self::isSandbox()) {
                throw new RuntimeException("Live payment gateway cannot be activated. Merchant credentials for [{$provider}] are not configured.");
            }
            // In sandbox/test mode without credentials, use application salt
            return config('services.payment.salt', 'TECH_ERA_SECURE_SALT_KEY_2026');
        }

        return $secret;
    }

    /**
     * Get public gateway Key ID for frontend SDK initialization.
     */
    public static function getPublicKey(): string
    {
        $provider = self::getProvider();

        if ($provider === 'cashfree') {
            $key = config('services.payment.cashfree.app_id');
        } else {
            $key = config('services.payment.razorpay.key_id');
        }

        if (empty($key)) {
            return self::isSandbox() ? 'rzp_test_techera_sandbox' : '';
        }

        return $key;
    }

    /**
     * Get webhook secret.
     */
    public static function getWebhookSecret(): string
    {
        $provider = self::getProvider();

        if ($provider === 'cashfree') {
            $secret = config('services.payment.cashfree.webhook_secret');
        } else {
            $secret = config('services.payment.razorpay.webhook_secret');
        }

        if (empty($secret)) {
            return self::getKeySecret();
        }

        return $secret;
    }

    /**
     * Create a server-side order and persist pending OnlineTransaction.
     */
    public static function createOrder(
        float $amount,
        string $payerName,
        string $payerMobile,
        ?string $payerEmail = null,
        string $purpose = 'Fee Payment',
        ?string $rollNo = null,
        ?int $admissionId = null,
        ?int $studentId = null,
        string $paymentMethod = 'UPI',
        ?int $centreId = null
    ): array {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Payment amount must be greater than zero.');
        }

        if (!self::isSandbox()) {
            $publicKey = self::getPublicKey();
            $secret = self::getKeySecret();
            if (empty($publicKey) || empty($secret)) {
                throw new RuntimeException('Live payments cannot be activated until merchant onboarding, credentials and webhook configuration have been verified.');
            }
        }

        $orderId = 'order_' . date('Ymd') . '_' . strtolower(Str::random(14));
        $transactionId = 'TXN-' . date('Ymd') . '-' . strtoupper(Str::random(8));

        // Create transaction in pending state
        $transaction = OnlineTransaction::create([
            'transaction_id'     => $transactionId,
            'order_id'           => $orderId,
            'student_roll_no'    => $rollNo,
            'admission_id'       => $admissionId,
            'centre_id'          => $centreId,
            'student_id'         => $studentId,
            'payer_name'         => $payerName,
            'payer_mobile'       => $payerMobile,
            'payer_email'        => $payerEmail,
            'purpose'            => $purpose,
            'amount'             => $amount,
            'payment_method'     => $paymentMethod,
            'gateway_name'       => strtoupper(self::getProvider()) . (self::isSandbox() ? ' (Sandbox)' : ''),
            'status'             => 'pending',
        ]);

        return [
            'order_id'       => $orderId,
            'transaction_id' => $transactionId,
            'amount'         => $amount,
            'amount_paise'   => (int) round($amount * 100),
            'currency'       => 'INR',
            'key_id'         => self::getPublicKey(),
            'provider'       => self::getProvider(),
            'mode'           => self::isSandbox() ? 'sandbox' : 'live',
            'transaction'    => $transaction,
        ];
    }

    /**
     * Verify payment signature authoritatively according to official documentation.
     * Razorpay algorithm: HMAC-SHA256(order_id + '|' + payment_id, key_secret).
     */
    public static function verifySignature(string $orderId, string $paymentId, string $signature): bool
    {
        if (empty($orderId) || empty($paymentId) || empty($signature)) {
            return false;
        }

        $secret = self::getKeySecret();
        $payload = $orderId . '|' . $paymentId;
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Generate signature helper (used for testing and authorized sandbox responses).
     */
    public static function generateSignature(string $orderId, string $paymentId): string
    {
        $secret = self::getKeySecret();
        return hash_hmac('sha256', $orderId . '|' . $paymentId, $secret);
    }

    /**
     * Verify webhook signature from HTTP header against raw request body.
     */
    public static function verifyWebhookSignature(string $rawBody, string $signatureHeader): bool
    {
        if (empty($rawBody) || empty($signatureHeader)) {
            return false;
        }

        $webhookSecret = self::getWebhookSecret();
        $expectedSignature = hash_hmac('sha256', $rawBody, $webhookSecret);

        return hash_equals($expectedSignature, $signatureHeader);
    }

    /**
     * Authoritatively reconcile a payment with strict database transaction and idempotency.
     */
    public static function reconcilePayment(
        string $orderId,
        string $gatewayPaymentId,
        string $signature,
        array $gatewayPayload = []
    ): OnlineTransaction {
        return DB::transaction(function () use ($orderId, $gatewayPaymentId, $signature, $gatewayPayload) {
            // 1. Lock transaction row to prevent race conditions & duplicate credit
            $transaction = OnlineTransaction::where('order_id', $orderId)
                ->lockForUpdate()
                ->first();

            if (!$transaction) {
                throw new RuntimeException("No transaction found matching order ID: [{$orderId}].");
            }

            // 2. IDEMPOTENCY GUARD: If already processed successfully, return early
            if ($transaction->status === 'success') {
                return $transaction;
            }

            // 3. Verify signature strictly (never trust client redirect without verification)
            $isValidSignature = self::verifySignature($orderId, $gatewayPaymentId, $signature);

            if (!$isValidSignature) {
                $transaction->update([
                    'status'             => 'failed',
                    'gateway_payment_id' => $gatewayPaymentId,
                    'gateway_signature'  => $signature,
                    'error_message'      => 'Authoritative signature verification failed. Potential tampering detected.',
                    'response_payload'   => json_encode($gatewayPayload),
                ]);

                AuditLog::record('payment_verification_failed', $transaction, null, [
                    'order_id'   => $orderId,
                    'payment_id' => $gatewayPaymentId,
                    'reason'     => 'Signature mismatch',
                ]);

                return $transaction;
            }

            // 4. Mark transaction as success
            $transaction->status = 'success';
            $transaction->gateway_payment_id = $gatewayPaymentId;
            $transaction->gateway_signature = $signature;
            $transaction->response_payload = json_encode($gatewayPayload);
            $transaction->save();

            // Check if this transaction is for a franchise wallet recharge
            if ($transaction->centre_id || str_contains(strtolower($transaction->purpose), 'wallet')) {
                \App\Services\WalletService::creditFromOnlineRecharge($transaction, $gatewayPaymentId, $signature, $gatewayPayload);
                return $transaction;
            }

            // 5. Reconcile with Student Admission & Fee Ledger
            $admission = null;
            if ($transaction->admission_id) {
                $admission = Admission::with('student')->find($transaction->admission_id);
            } elseif (!empty($transaction->student_roll_no)) {
                $student = Student::where('roll_no', $transaction->student_roll_no)
                    ->orWhere('registration_no', $transaction->student_roll_no)
                    ->orWhere('mobile', $transaction->student_roll_no)
                    ->first();
                if ($student) {
                    $transaction->student_id = $student->id;
                    $admission = Admission::where('student_id', $student->id)
                        ->whereIn('status', ['active', 'enrolled'])
                        ->latest()
                        ->first() ?: Admission::where('student_id', $student->id)->latest()->first();
                    if ($admission) {
                        $transaction->admission_id = $admission->id;
                    }
                }
            }

            if ($admission) {
                $centreId = $admission->centre_id;
                $studentId = $admission->student_id;
                $receiptNo = PaymentReceipt::generateReceiptNo($centreId);
                $amount = (float) $transaction->amount;

                // Create FeePayment record
                $feePayment = FeePayment::create([
                    'admission_id'    => $admission->id,
                    'student_id'      => $studentId,
                    'centre_id'       => $centreId,
                    'receipt_no'      => $receiptNo,
                    'payment_date'    => now()->toDateString(),
                    'amount'          => $amount,
                    'payment_mode'    => 'online',
                    'transaction_ref' => $gatewayPaymentId,
                    'notes'           => "Online Payment ({$transaction->purpose}) - Ref: {$transaction->transaction_id}",
                ]);

                // Link fee payment to online transaction
                $transaction->fee_payment_id = $feePayment->id;
                $transaction->save();

                // Allocate payment to student_fees installments (oldest pending first)
                $remainingToAllocate = $amount;
                $pendingInstallments = StudentFee::where('admission_id', $admission->id)
                    ->whereIn('status', ['pending', 'partial'])
                    ->orderBy('installment_no')
                    ->lockForUpdate()
                    ->get();

                foreach ($pendingInstallments as $inst) {
                    $unpaid = $inst->amount - $inst->paid_amount;
                    if ($unpaid <= 0) continue;

                    $alloc = min($remainingToAllocate, $unpaid);
                    $newPaid = $inst->paid_amount + $alloc;
                    $inst->update([
                        'paid_amount' => $newPaid,
                        'status'      => $newPaid >= $inst->amount ? 'paid' : 'partial',
                    ]);

                    $remainingToAllocate -= $alloc;
                    if ($remainingToAllocate <= 0) break;
                }

                // Update admission total_paid
                $admission->lockForUpdate();
                $newTotalPaid = $admission->total_paid + $amount;
                $admission->update([
                    'total_paid' => $newTotalPaid,
                    'status'     => $newTotalPaid >= $admission->net_fee ? 'completed' : $admission->status,
                ]);

                // Generate Official Cryptographic Payment Receipt
                $qrToken = hash('sha256', "TEC:REC:{$receiptNo}:{$amount}:" . now()->toIso8601String() . ':' . env('APP_PAYMENT_SALT', 'TECH_ERA_SALT'));
                PaymentReceipt::create([
                    'fee_payment_id' => $feePayment->id,
                    'receipt_no'     => $receiptNo,
                    'student_id'     => $studentId,
                    'admission_id'   => $admission->id,
                    'centre_id'      => $centreId,
                    'amount'         => $amount,
                    'payment_mode'   => 'online',
                    'receipt_date'   => now()->toDateString(),
                    'qr_token'       => $qrToken,
                    'notes'          => "Online Transaction ID: {$transaction->transaction_id}",
                ]);

                // Send Notification to student user account
                $student = $admission->student;
                if ($student && $student->user_id) {
                    SystemNotification::create([
                        'user_id'    => $student->user_id,
                        'title'      => 'Online Fee Payment Confirmed',
                        'message'    => "Your online payment of Rs. " . number_format($amount, 2) . " has been verified. Official Receipt #{$receiptNo} is available.",
                        'type'       => 'fee',
                        'action_url' => route('student.login'),
                        'data'       => [
                            'receipt_no'     => $receiptNo,
                            'transaction_id' => $transaction->transaction_id,
                            'amount'         => $amount,
                        ],
                    ]);
                }
            }

            // 6. Security Audit Log
            AuditLog::record('online_payment_verified', $transaction, null, [
                'order_id'           => $orderId,
                'gateway_payment_id' => $gatewayPaymentId,
                'amount'             => $transaction->amount,
                'status'             => 'success',
            ]);

            return $transaction;
        });
    }

    /**
     * Process asynchronous webhook from payment gateway.
     */
    public static function handleWebhook(string $rawBody, string $signatureHeader): array
    {
        // 1. Authenticate webhook signature
        if (!self::verifyWebhookSignature($rawBody, $signatureHeader)) {
            return [
                'success' => false,
                'status'  => 'unauthorized',
                'message' => 'Invalid webhook signature.',
            ];
        }

        $payload = json_decode($rawBody, true);
        if (!$payload) {
            return [
                'success' => false,
                'status'  => 'bad_request',
                'message' => 'Malformed JSON payload.',
            ];
        }

        $event = $payload['event'] ?? ($payload['type'] ?? 'unknown');
        $orderId = null;
        $paymentId = null;

        // Parse Razorpay Webhook format
        if (isset($payload['payload']['payment']['entity'])) {
            $paymentEntity = $payload['payload']['payment']['entity'];
            $orderId = $paymentEntity['order_id'] ?? null;
            $paymentId = $paymentEntity['id'] ?? null;
        } elseif (isset($payload['data']['order'])) { // Cashfree format
            $orderId = $payload['data']['order']['order_id'] ?? null;
            $paymentId = $payload['data']['payment']['cf_payment_id'] ?? null;
        }

        if (!$orderId) {
            return [
                'success' => true,
                'status'  => 'ignored',
                'message' => 'Webhook event does not contain a recognizable order ID.',
            ];
        }

        // Handle success events
        if (in_array($event, ['payment.captured', 'order.paid', 'PAYMENT_SUCCESS_WEBHOOK'], true)) {
            // Build expected signature for reconcilePayment
            $signature = self::generateSignature($orderId, $paymentId);
            $transaction = self::reconcilePayment($orderId, $paymentId, $signature, $payload);

            return [
                'success' => true,
                'status'  => $transaction->status,
                'message' => "Order {$orderId} processed successfully.",
            ];
        }

        // Handle failure events
        if (in_array($event, ['payment.failed', 'PAYMENT_FAILED_WEBHOOK'], true)) {
            $transaction = OnlineTransaction::where('order_id', $orderId)->first();
            if ($transaction && $transaction->status !== 'success') {
                $transaction->update([
                    'status'             => 'failed',
                    'gateway_payment_id' => $paymentId,
                    'error_message'      => $payload['payload']['payment']['entity']['error_description'] ?? 'Payment failed at gateway.',
                    'response_payload'   => $rawBody,
                ]);
            }

            return [
                'success' => true,
                'status'  => 'failed',
                'message' => "Order {$orderId} marked as failed.",
            ];
        }

        // Handle refund events
        if (in_array($event, ['refund.processed', 'REFUND_SUCCESS_WEBHOOK'], true)) {
            $transaction = OnlineTransaction::where('order_id', $orderId)->first();
            if ($transaction) {
                $transaction->update([
                    'status'           => 'refunded',
                    'response_payload' => $rawBody,
                ]);
            }

            return [
                'success' => true,
                'status'  => 'refunded',
                'message' => "Order {$orderId} marked as refunded.",
            ];
        }

        return [
            'success' => true,
            'status'  => 'acknowledged',
            'message' => "Webhook event [{$event}] acknowledged.",
        ];
    }
}
