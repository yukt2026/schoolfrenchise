<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\OnlineTransaction;
use App\Models\Student;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;

class PaymentPublicController extends Controller
{
    /**
     * Display public fee payment search & payment portal.
     */
    public function index(Request $request)
    {
        $student = null;
        $pendingFee = 0;
        $query = trim((string) ($request->input('roll_no') ?? $request->input('query') ?? ''));

        if (!empty($query)) {
            $student = Student::with(['admissions.course', 'centre', 'admissions.fees'])
                ->where('roll_no', $query)
                ->orWhere('registration_no', $query)
                ->orWhere('mobile', $query)
                ->orWhere('alternate_mobile', $query)
                ->orWhere('email', $query)
                ->first();

            if ($student && $student->admissions->isNotEmpty()) {
                $admission = $student->admissions->whereIn('status', ['active', 'enrolled'])->first() ?: $student->admissions->first();
                if ($admission) {
                    $pendingFee = max(0, (float) $admission->net_fee - (float) $admission->total_paid);
                }
            } elseif (!$student) {
                session()->flash('search_not_found', "No student record found matching '{$query}'. Please verify your Roll Number, Registration Number, or Mobile.");
            }
        }

        return view('pages.online-payment', compact('student', 'pendingFee'));
    }

    /**
     * Initiate server-side order for online payment.
     */
    public function initiateOrder(Request $request)
    {
        $validated = $request->validate([
            'payer_name'      => 'required|string|max:191',
            'payer_mobile'    => 'required|string|max:20',
            'payer_email'     => 'nullable|email|max:191',
            'student_roll_no' => 'nullable|string|max:60',
            'purpose'         => 'required|string|max:191',
            'amount'          => 'required|numeric|min:10',
            'payment_method'  => 'nullable|string|in:UPI,DebitCard,CreditCard,NetBanking',
        ]);

        $admissionId = null;
        $studentId = null;

        if (!empty($validated['student_roll_no'])) {
            $student = Student::where('roll_no', $validated['student_roll_no'])
                ->orWhere('registration_no', $validated['student_roll_no'])
                ->orWhere('mobile', $validated['student_roll_no'])
                ->first();
            if ($student) {
                $studentId = $student->id;
                $admission = Admission::where('student_id', $student->id)
                    ->whereIn('status', ['active', 'enrolled'])
                    ->latest()
                    ->first() ?: Admission::where('student_id', $student->id)->latest()->first();
                if ($admission) {
                    $admissionId = $admission->id;
                }
            }
        }

        $orderData = PaymentGatewayService::createOrder(
            (float) $validated['amount'],
            $validated['payer_name'],
            $validated['payer_mobile'],
            $validated['payer_email'] ?? null,
            $validated['purpose'],
            $validated['student_roll_no'] ?? null,
            $admissionId,
            $studentId,
            $validated['payment_method'] ?? 'UPI'
        );

        if ($request->wantsJson()) {
            return response()->json($orderData);
        }

        return view('pages.checkout-modal', [
            'order'       => $orderData,
            'transaction' => $orderData['transaction'],
        ]);
    }

    /**
     * Standard online checkout submission and authoritative signature verification.
     */
    public function process(Request $request)
    {
        $validated = $request->validate([
            'payer_name'         => 'required|string|max:191',
            'payer_mobile'       => 'required|string|max:20',
            'payer_email'        => 'nullable|email|max:191',
            'student_roll_no'    => 'nullable|string|max:60',
            'purpose'            => 'required|string|max:191',
            'amount'             => 'required|numeric|min:10',
            'payment_method'     => 'required|string|in:UPI,DebitCard,CreditCard,NetBanking',
            'order_id'           => 'nullable|string|max:100',
            'gateway_payment_id' => 'nullable|string|max:100',
            'gateway_signature'  => 'nullable|string|max:255',
        ]);

        $studentId = null;
        $admissionId = null;

        if (!empty($validated['student_roll_no'])) {
            $student = Student::where('roll_no', $validated['student_roll_no'])
                ->orWhere('registration_no', $validated['student_roll_no'])
                ->first();
            if ($student) {
                $studentId = $student->id;
                $admission = Admission::where('student_id', $student->id)
                    ->whereIn('status', ['active', 'enrolled'])
                    ->latest()
                    ->first();
                if ($admission) {
                    $admissionId = $admission->id;
                }
            }
        }

        // If order_id is not already created, create server-side order
        $orderId = $validated['order_id'] ?? null;
        if (empty($orderId)) {
            $order = PaymentGatewayService::createOrder(
                (float) $validated['amount'],
                $validated['payer_name'],
                $validated['payer_mobile'],
                $validated['payer_email'] ?? null,
                $validated['purpose'],
                $validated['student_roll_no'] ?? null,
                $admissionId,
                $studentId,
                $validated['payment_method']
            );
            $orderId = $order['order_id'];
        }

        $paymentId = $validated['gateway_payment_id'] ?? ('PAY-' . strtoupper(\Illuminate\Support\Str::random(12)));
        $signature = $validated['gateway_signature'] ?? null;

        // In sandbox mode without frontend SDK callback, generate authoritative test signature
        if (empty($signature) && PaymentGatewayService::isSandbox()) {
            $signature = PaymentGatewayService::generateSignature($orderId, $paymentId);
        }

        if (empty($signature)) {
            return back()->withInput()->with('error', 'Payment verification failed: Missing gateway signature. Never trust unverified client redirects.');
        }

        // Authoritatively verify signature and reconcile fee ledger transactionally
        $transaction = PaymentGatewayService::reconcilePayment(
            $orderId,
            $paymentId,
            $signature,
            [
                'payment_method' => $validated['payment_method'],
                'verified_at'    => now()->toIso8601String(),
                'ip'             => $request->ip(),
            ]
        );

        if ($transaction->status !== 'success') {
            return redirect()->route('payment.receipt', $transaction->transaction_id)
                ->with('error', 'Payment verification failed: ' . ($transaction->error_message ?? 'Invalid signature'));
        }

        return redirect()->route('payment.receipt', $transaction->transaction_id)
            ->with('success', 'Payment verified and credited successfully!');
    }

    /**
     * Verify payment callback from gateway (Razorpay / Cashfree callback redirect).
     */
    public function verifyPayment(Request $request)
    {
        $validated = $request->validate([
            'razorpay_order_id'   => 'required_without:order_id|string|nullable',
            'order_id'            => 'required_without:razorpay_order_id|string|nullable',
            'razorpay_payment_id' => 'required_without:gateway_payment_id|string|nullable',
            'gateway_payment_id'  => 'required_without:razorpay_payment_id|string|nullable',
            'razorpay_signature'  => 'required_without:signature|string|nullable',
            'signature'           => 'required_without:razorpay_signature|string|nullable',
        ]);

        $orderId = $validated['razorpay_order_id'] ?? $validated['order_id'];
        $paymentId = $validated['razorpay_payment_id'] ?? $validated['gateway_payment_id'];
        $signature = $validated['razorpay_signature'] ?? $validated['signature'];

        $transaction = PaymentGatewayService::reconcilePayment($orderId, $paymentId, $signature, $request->all());

        if ($transaction->status === 'success') {
            return redirect()->route('payment.receipt', $transaction->transaction_id)
                ->with('success', 'Payment authorized and receipt generated successfully.');
        }

        return redirect()->route('payment.receipt', $transaction->transaction_id)
            ->with('error', 'Payment verification could not be confirmed: ' . ($transaction->error_message ?? 'Signature invalid'));
    }

    /**
     * Webhook endpoint for asynchronous payment gateway notifications with idempotency.
     */
    public function webhook(Request $request)
    {
        $rawPayload = $request->getContent();
        $signatureHeader = $request->header('X-Razorpay-Signature')
            ?? $request->header('x-webhook-signature')
            ?? $request->header('X-Webhook-Signature', '');

        $result = PaymentGatewayService::handleWebhook($rawPayload, $signatureHeader);

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        return response()->json($result, 200);
    }

    /**
     * View computerized payment receipt.
     */
    public function receipt($transaction_id)
    {
        $transaction = OnlineTransaction::where('transaction_id', $transaction_id)->firstOrFail();
        $student = null;
        if ($transaction->student_roll_no) {
            $student = Student::with(['centre', 'admissions.course'])
                ->where('roll_no', $transaction->student_roll_no)
                ->orWhere('registration_no', $transaction->student_roll_no)
                ->first();
        }

        return view('pages.payment-receipt', compact('transaction', 'student'));
    }
}
