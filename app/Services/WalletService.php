<?php

namespace App\Services;

use App\Models\Admission;
use App\Models\AuditLog;
use App\Models\Centre;
use App\Models\CentreWallet;
use App\Models\CentreWalletTransaction;
use App\Models\OnlineTransaction;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WalletService
{
    /**
     * Get configured franchise admission fee deduction per student (Default ₹200).
     */
    public static function getRequiredAdmissionFee(): float
    {
        return (float) Setting::get('franchise_admission_commission', 200.00);
    }

    /**
     * Check if centre wallet has sufficient balance for admission fee deduction.
     */
    public static function hasSufficientBalance(int $centreId, ?float $amount = null): bool
    {
        $required = $amount ?? self::getRequiredAdmissionFee();
        if ($required <= 0) {
            return true;
        }

        $wallet = CentreWallet::where('centre_id', $centreId)->first();
        return $wallet && ((float) $wallet->balance >= $required);
    }

    /**
     * Get current balance for a centre.
     */
    public static function getCentreBalance(int $centreId): float
    {
        $wallet = CentreWallet::where('centre_id', $centreId)->first();
        return $wallet ? (float) $wallet->balance : 0.00;
    }

    /**
     * Deduct franchise fee (₹200) when a student admission is completed.
     * Requirement: "Student ke Admission lene par Franchise ke Wallet se 200 rs kat hona chahiye"
     * Prevents duplicate wallet debits for the same admission.
     */
    public static function processAdmissionFeeDeduction(Admission $admission, bool $strict = true): bool
    {
        if (empty($admission->centre_id)) {
            return false;
        }

        // 1. Check if admission was already processed
        if ($admission->commission_credited) {
            return false;
        }

        $refNo = "ADM-DEBIT-{$admission->id}";

        // 2. Database transaction with row-level lock
        return DB::transaction(function () use ($admission, $refNo, $strict) {
            // Re-check under lock
            $freshAdmission = Admission::where('id', $admission->id)->lockForUpdate()->first();
            if (!$freshAdmission || $freshAdmission->commission_credited) {
                return false;
            }

            // Check if transaction with this reference already exists
            $existingTxn = CentreWalletTransaction::where('reference_no', $refNo)->first();
            if ($existingTxn) {
                $freshAdmission->update([
                    'commission_credited' => true,
                    'commission_amount'   => $existingTxn->amount,
                ]);
                return false;
            }

            // Configurable admission deduction from Admin Panel (Default ₹200)
            $feeAmount = self::getRequiredAdmissionFee();
            if ($feeAmount <= 0) {
                return false;
            }

            // Lock or create Centre Wallet
            $wallet = CentreWallet::firstOrCreate(
                ['centre_id' => $freshAdmission->centre_id],
                [
                    'balance'        => 0.00,
                    'total_credited' => 0.00,
                    'total_debited'  => 0.00,
                    'status'         => 'active',
                ]
            );

            $wallet = CentreWallet::where('id', $wallet->id)->lockForUpdate()->first();

            $oldBalance = (float) $wallet->balance;

            // Strict balance enforcement: fail if balance is less than required fee
            if ($strict && $oldBalance < $feeAmount) {
                throw new \RuntimeException("Insufficient wallet balance for Centre. Current Balance: ₹" . number_format($oldBalance, 2) . ", Required Admission Fee: ₹" . number_format($feeAmount, 2));
            }

            $newBalance = $oldBalance - $feeAmount;

            $wallet->balance = $newBalance;
            $wallet->total_debited = (float) $wallet->total_debited + $feeAmount;
            $wallet->save();

            $studentName = $freshAdmission->student?->full_name ?? 'Student';
            $rollNo = $freshAdmission->student?->roll_no ?? "ADM-{$freshAdmission->id}";

            $txn = CentreWalletTransaction::create([
                'centre_id'     => $freshAdmission->centre_id,
                'type'          => 'debit',
                'amount'        => $feeAmount,
                'balance_after' => $newBalance,
                'reference_no'  => $refNo,
                'description'   => "Admission Fee for Student: {$studentName} ({$rollNo})",
                'approved_by'   => Auth::id() ?? 1,
                'status'        => 'approved',
            ]);

            $freshAdmission->update([
                'commission_credited' => true,
                'commission_amount'   => $feeAmount,
            ]);

            AuditLog::record('wallet_admission_debited', $txn, null, [
                'centre_id'          => $freshAdmission->centre_id,
                'admission_id'       => $freshAdmission->id,
                'student_id'         => $freshAdmission->student_id,
                'fee_amount'         => $feeAmount,
                'new_wallet_balance' => $newBalance,
            ]);

            return true;
        });
    }

    /**
     * Backward-compatible alias for admission wallet processing.
     */
    public static function creditAdmissionCommission(Admission $admission): bool
    {
        return self::processAdmissionFeeDeduction($admission);
    }

    /**
     * Authoritatively credit centre wallet from verified online payment gateway transaction.
     */
    public static function creditFromOnlineRecharge(
        OnlineTransaction $transaction,
        string $gatewayPaymentId,
        string $signature,
        array $gatewayPayload = []
    ): CentreWalletTransaction {
        return DB::transaction(function () use ($transaction, $gatewayPaymentId, $signature, $gatewayPayload) {
            // Lock online transaction row
            $lockedTxn = OnlineTransaction::where('id', $transaction->id)->lockForUpdate()->first();

            if ($lockedTxn->status === 'success') {
                // Find existing wallet transaction for idempotency
                $existing = CentreWalletTransaction::where('reference_no', $lockedTxn->transaction_id)->first();
                if ($existing) {
                    return $existing;
                }
            }

            // Lock or create Centre Wallet
            $centreId = $lockedTxn->centre_id;
            if (!$centreId) {
                throw new \RuntimeException('Online recharge transaction missing associated study centre ID.');
            }

            $wallet = CentreWallet::firstOrCreate(
                ['centre_id' => $centreId],
                [
                    'balance'        => 0.00,
                    'total_credited' => 0.00,
                    'total_debited'  => 0.00,
                    'status'         => 'active',
                ]
            );

            $wallet = CentreWallet::where('id', $wallet->id)->lockForUpdate()->first();

            $amount = (float) $lockedTxn->amount;
            $oldBalance = (float) $wallet->balance;
            $newBalance = $oldBalance + $amount;

            $wallet->balance = $newBalance;
            $wallet->total_credited = (float) $wallet->total_credited + $amount;
            $wallet->save();

            // Mark online transaction as successful
            $lockedTxn->status = 'success';
            $lockedTxn->gateway_payment_id = $gatewayPaymentId;
            $lockedTxn->gateway_signature = $signature;
            $lockedTxn->response_payload = json_encode($gatewayPayload);
            $lockedTxn->save();

            $centreTxn = CentreWalletTransaction::create([
                'centre_id'     => $centreId,
                'type'          => 'credit',
                'amount'        => $amount,
                'balance_after' => $newBalance,
                'reference_no'  => $lockedTxn->transaction_id,
                'description'   => "Online Wallet Recharge via Payment Gateway (Ref: {$gatewayPaymentId})",
                'approved_by'   => Auth::id() ?? 1,
                'status'        => 'approved',
            ]);

            AuditLog::record('wallet_online_recharge', $centreTxn, null, [
                'centre_id'          => $centreId,
                'transaction_id'     => $lockedTxn->transaction_id,
                'gateway_payment_id' => $gatewayPaymentId,
                'amount'             => $amount,
                'new_balance'        => $newBalance,
            ]);

            return $centreTxn;
        });
    }
}
