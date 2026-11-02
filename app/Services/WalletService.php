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
     * Credit franchise commission when a student admission is completed.
     * Prevents duplicate wallet credits for the same admission.
     */
    public static function creditAdmissionCommission(Admission $admission): bool
    {
        if (empty($admission->centre_id)) {
            return false;
        }

        // 1. Check if admission was already flagged as credited
        if ($admission->commission_credited) {
            return false;
        }

        $refNo = "COMM-ADM-{$admission->id}";

        // 2. Database transaction with row-level lock
        return DB::transaction(function () use ($admission, $refNo) {
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

            // Configurable commission from Admin Panel (Default ₹200)
            $commissionAmount = (float) Setting::get('franchise_admission_commission', 200.00);
            if ($commissionAmount <= 0) {
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
            $newBalance = $oldBalance + $commissionAmount;

            $wallet->balance = $newBalance;
            $wallet->total_credited = (float) $wallet->total_credited + $commissionAmount;
            $wallet->save();

            $studentName = $freshAdmission->student?->full_name ?? 'Student';
            $rollNo = $freshAdmission->student?->roll_no ?? "ADM-{$freshAdmission->id}";

            $txn = CentreWalletTransaction::create([
                'centre_id'     => $freshAdmission->centre_id,
                'type'          => 'credit',
                'amount'        => $commissionAmount,
                'balance_after' => $newBalance,
                'reference_no'  => $refNo,
                'description'   => "Franchise Commission for Student Admission: {$studentName} ({$rollNo})",
                'approved_by'   => Auth::id() ?? 1,
                'status'        => 'approved',
            ]);

            $freshAdmission->update([
                'commission_credited' => true,
                'commission_amount'   => $commissionAmount,
            ]);

            AuditLog::record('wallet_commission_credited', $txn, null, [
                'centre_id'          => $freshAdmission->centre_id,
                'admission_id'       => $freshAdmission->id,
                'student_id'         => $freshAdmission->student_id,
                'commission_amount'  => $commissionAmount,
                'new_wallet_balance' => $newBalance,
            ]);

            return true;
        });
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
