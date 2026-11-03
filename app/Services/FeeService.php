<?php

namespace App\Services;

use App\Models\Admission;
use App\Models\AuditLog;
use App\Models\FeePayment;
use App\Models\FeeStructure;
use App\Models\PaymentReceipt;
use App\Models\StudentFee;
use App\Models\SystemNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class FeeService
{
    /**
     * Collect a student fee payment using an atomic database transaction.
     */
    public static function collectPayment(
        Admission $admission,
        float $amount,
        string $paymentMode = 'cash',
        ?int $studentFeeId = null,
        ?string $transactionRef = null,
        ?string $notes = null
    ): PaymentReceipt {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Payment amount must be greater than zero.');
        }

        $pendingAmount = $admission->net_fee - $admission->total_paid;
        if ($amount > $pendingAmount) {
            throw new InvalidArgumentException("Payment amount (Rs. {$amount}) exceeds the remaining balance (Rs. {$pendingAmount}).");
        }

        return DB::transaction(function () use ($admission, $amount, $paymentMode, $studentFeeId, $transactionRef, $notes) {
            $centreId = $admission->centre_id;
            $studentId = $admission->student_id;
            $receiptNo = PaymentReceipt::generateReceiptNo($centreId);
            $userId = Auth::id();

            // 1. Create Fee Payment Transaction
            $feePayment = FeePayment::create([
                'student_fee_id' => $studentFeeId,
                'admission_id' => $admission->id,
                'student_id' => $studentId,
                'centre_id' => $centreId,
                'receipt_no' => $receiptNo,
                'payment_date' => now()->toDateString(),
                'amount' => $amount,
                'payment_mode' => $paymentMode,
                'transaction_ref' => $transactionRef,
                'collected_by' => $userId,
                'notes' => $notes,
            ]);

            // 2. Allocate payment to student_fees installment
            if ($studentFeeId) {
                $installment = StudentFee::lockForUpdate()->find($studentFeeId);
                if ($installment) {
                    $newPaid = $installment->paid_amount + $amount;
                    $installment->update([
                        'paid_amount' => $newPaid,
                        'status' => $newPaid >= $installment->amount ? 'paid' : 'partial',
                    ]);
                }
            } else {
                // Auto-settle oldest pending installments
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
                        'status' => $newPaid >= $inst->amount ? 'paid' : 'partial',
                    ]);

                    $remainingToAllocate -= $alloc;
                    if ($remainingToAllocate <= 0) break;
                }
            }

            // 3. Update Admission Total Paid
            $admission->lockForUpdate();
            $newTotalPaid = $admission->total_paid + $amount;
            $admission->update([
                'total_paid' => $newTotalPaid,
                'status' => $newTotalPaid >= $admission->net_fee ? 'completed' : $admission->status,
            ]);

            // 4. Generate Official Cryptographic Payment Receipt
            $qrToken = hash('sha256', "TEC:REC:{$receiptNo}:{$amount}:" . now()->toIso8601String() . ':' . env('APP_PAYMENT_SALT', 'TECH_ERA_SALT'));
            $receipt = PaymentReceipt::create([
                'fee_payment_id' => $feePayment->id,
                'receipt_no' => $receiptNo,
                'student_id' => $studentId,
                'admission_id' => $admission->id,
                'centre_id' => $centreId,
                'amount' => $amount,
                'payment_mode' => $paymentMode,
                'receipt_date' => now()->toDateString(),
                'qr_token' => $qrToken,
                'issued_by' => $userId,
                'notes' => $notes,
            ]);

            // 5. Security Audit Log
            AuditLog::record('fee_collected', $receipt, null, [
                'receipt_no' => $receiptNo,
                'amount' => $amount,
                'admission_id' => $admission->id,
                'student_id' => $studentId,
                'payment_mode' => $paymentMode,
            ]);

            // 6. Dispatch System + WhatsApp/SMS Receipt Notification
            try {
                \App\Services\NotificationDispatchService::dispatchFeeReceipt($receipt);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Notification dispatch failed for receipt {$receiptNo}: " . $e->getMessage());
            }

            return $receipt;
        });
    }

    /**
     * Assign fee structure installments to an admission within a transaction.
     */
    public static function assignFeeSchedule(Admission $admission, FeeStructure $structure): void
    {
        DB::transaction(function () use ($admission, $structure) {
            $total = $structure->total_fee;
            $installments = max(1, $structure->installments_count);
            $admissionFee = $structure->admission_fee;
            $remaining = $total - $admissionFee;
            $installmentAmount = round($remaining / max(1, $installments - 1), 2);

            // Clear previous uncollected fees if any
            StudentFee::where('admission_id', $admission->id)->where('paid_amount', 0)->delete();

            // 1. Admission installment (Due on admission date)
            StudentFee::create([
                'admission_id' => $admission->id,
                'student_id' => $admission->student_id,
                'centre_id' => $admission->centre_id,
                'installment_no' => 1,
                'title' => 'Admission & Registration Fee',
                'due_date' => $admission->admission_date,
                'amount' => $admissionFee,
                'paid_amount' => 0.00,
                'status' => 'pending',
            ]);

            // Remaining installments (monthly)
            $accumulated = $admissionFee;
            for ($i = 2; $i <= $installments; $i++) {
                $amount = ($i === $installments) ? ($total - $accumulated) : $installmentAmount;
                $accumulated += $amount;

                StudentFee::create([
                    'admission_id' => $admission->id,
                    'student_id' => $admission->student_id,
                    'centre_id' => $admission->centre_id,
                    'installment_no' => $i,
                    'title' => "Tuition Installment #{$i}",
                    'due_date' => $admission->admission_date->copy()->addMonths($i - 1),
                    'amount' => $amount,
                    'paid_amount' => 0.00,
                    'status' => 'pending',
                ]);
            }

            $admission->update([
                'course_fee' => $total,
                'net_fee' => $total,
            ]);

            AuditLog::record('fee_schedule_assigned', $admission, null, [
                'fee_structure_id' => $structure->id,
                'total_fee' => $total,
                'installments' => $installments,
            ]);
        });
    }

    /**
     * Assign admission fees either via fee structure or direct single-installment.
     */
    public static function assignAdmissionFees(Admission $admission, float $discount = 0.0): void
    {
        $structure = FeeStructure::where('course_id', $admission->course_id)->first();
        $total = (float) $admission->net_fee;
        if ($total <= 0) {
            $total = $structure ? (float) $structure->total_fee : (float) $admission->course_fee;
            $total = max(0, $total - $discount);
            $admission->update(['net_fee' => $total, 'discount_amount' => $discount]);
        }

        if ($structure && $structure->installments_count > 1) {
            $installments = max(1, $structure->installments_count);
            $admissionFee = min($total, (float) $structure->admission_fee);
            $remaining = max(0, $total - $admissionFee);
            $installmentAmount = round($remaining / max(1, $installments - 1), 2);

            StudentFee::where('admission_id', $admission->id)->where('paid_amount', 0)->delete();

            // 1. First installment
            StudentFee::create([
                'admission_id' => $admission->id,
                'student_id' => $admission->student_id,
                'centre_id' => $admission->centre_id,
                'installment_no' => 1,
                'title' => 'Admission & Registration Fee',
                'due_date' => $admission->admission_date ?? now(),
                'amount' => $admissionFee,
                'paid_amount' => 0.00,
                'status' => 'pending',
            ]);

            $accumulated = $admissionFee;
            for ($i = 2; $i <= $installments; $i++) {
                $amount = ($i === $installments) ? ($total - $accumulated) : $installmentAmount;
                $accumulated += $amount;

                StudentFee::create([
                    'admission_id' => $admission->id,
                    'student_id' => $admission->student_id,
                    'centre_id' => $admission->centre_id,
                    'installment_no' => $i,
                    'title' => "Tuition Installment #{$i}",
                    'due_date' => $admission->admission_date ? \Carbon\Carbon::parse($admission->admission_date)->addMonths($i - 1) : now()->addMonths($i - 1),
                    'amount' => $amount,
                    'paid_amount' => 0.00,
                    'status' => 'pending',
                ]);
            }
        } else {
            StudentFee::where('admission_id', $admission->id)->where('paid_amount', 0)->delete();
            StudentFee::create([
                'admission_id' => $admission->id,
                'student_id' => $admission->student_id,
                'centre_id' => $admission->centre_id,
                'installment_no' => 1,
                'title' => 'Course Tuition Fee',
                'due_date' => $admission->admission_date ?? now(),
                'amount' => $total,
                'paid_amount' => 0.00,
                'status' => 'pending',
            ]);
        }
    }

    /**
     * Convenience alias for assigning fees to an admission.
     */
    public static function assignFee($param1, $param2 = null, $param3 = null, $param4 = null): void
    {
        if ($param1 instanceof Admission) {
            self::assignAdmissionFees($param1, (float) ($param2 ?? 0));
        } elseif ($param1 instanceof \App\Models\Student && $param2 instanceof \App\Models\Course) {
            $admission = Admission::where('student_id', $param1->id)
                ->where('course_id', $param2->id)
                ->latest()
                ->first();
            if ($admission) {
                self::assignAdmissionFees($admission, (float) ($param4 ?? 0));
            }
        }
    }

    /**
     * Assign custom installment schedule with due dates, concessions, and approved adjustments.
     */
    public static function assignCustomSchedule(
        Admission $admission,
        float $courseFee,
        float $concessionAmount,
        array $installments,
        ?string $adjustmentReason = null
    ): void {
        DB::transaction(function () use ($admission, $courseFee, $concessionAmount, $installments, $adjustmentReason) {
            $netFee = max(0, $courseFee - $concessionAmount);

            // Update admission financial summary
            $admission->update([
                'course_fee'      => $courseFee,
                'discount_amount' => $concessionAmount,
                'net_fee'         => $netFee,
                'remarks'         => $adjustmentReason ? ($admission->remarks . ' | Concession: ' . $adjustmentReason) : $admission->remarks,
            ]);

            // Clear previous unpaid installments
            StudentFee::where('admission_id', $admission->id)
                ->where('paid_amount', 0)
                ->delete();

            $index = 1;
            foreach ($installments as $inst) {
                $amount = (float) ($inst['amount'] ?? 0);
                if ($amount <= 0) continue;

                StudentFee::create([
                    'admission_id'   => $admission->id,
                    'student_id'     => $admission->student_id,
                    'centre_id'      => $admission->centre_id,
                    'installment_no' => $index++,
                    'title'          => $inst['title'] ?? "Instalment #{$index}",
                    'due_date'       => $inst['due_date'] ?? now()->addMonths($index - 1)->toDateString(),
                    'amount'         => $amount,
                    'paid_amount'    => 0.00,
                    'status'         => 'pending',
                ]);
            }

            AuditLog::record('fee_schedule_adjusted', $admission, null, [
                'course_fee'        => $courseFee,
                'concession_amount' => $concessionAmount,
                'net_fee'           => $netFee,
                'installments_count'=> count($installments),
                'reason'            => $adjustmentReason,
            ]);
        });
    }

    /**
     * Auto-reconcile admissions where total_paid > 0 but payment ledger rows are missing.
     */
    public static function reconcileAdmissionPayments(Admission $admission): void
    {
        if ((float) $admission->total_paid <= 0) {
            // Also ensure installment schedule exists if missing
            if (StudentFee::where('admission_id', $admission->id)->doesntExist() && (float) $admission->net_fee > 0) {
                self::assignAdmissionFees($admission, (float) $admission->discount_amount);
            }
            return;
        }

        $existingPaymentsCount = FeePayment::where('admission_id', $admission->id)->count();
        if ($existingPaymentsCount > 0) {
            return;
        }

        DB::transaction(function () use ($admission) {
            // Ensure student fees schedule exists
            if (StudentFee::where('admission_id', $admission->id)->doesntExist()) {
                self::assignAdmissionFees($admission, (float) $admission->discount_amount);
            }

            $paidAmount = (float) $admission->total_paid;
            $centreId = $admission->centre_id ?: 1;
            $receiptNo = PaymentReceipt::generateReceiptNo($centreId);
            $date = $admission->admission_date ? \Carbon\Carbon::parse($admission->admission_date)->toDateString() : now()->toDateString();

            // 1. First installment
            $firstInstallment = StudentFee::where('admission_id', $admission->id)->orderBy('installment_no')->first();
            $studentFeeId = $firstInstallment ? $firstInstallment->id : null;

            $payment = FeePayment::create([
                'student_fee_id'  => $studentFeeId,
                'admission_id'    => $admission->id,
                'student_id'      => $admission->student_id,
                'centre_id'       => $centreId,
                'receipt_no'      => $receiptNo,
                'payment_date'    => $date,
                'amount'          => $paidAmount,
                'payment_mode'    => 'cash',
                'transaction_ref' => 'INIT-ADM-' . $admission->id,
                'notes'           => $admission->remarks ?: 'Initial admission fee payment',
            ]);

            PaymentReceipt::create([
                'fee_payment_id' => $payment->id,
                'receipt_no'     => $receiptNo,
                'student_id'     => $admission->student_id,
                'admission_id'   => $admission->id,
                'centre_id'      => $centreId,
                'amount'         => $paidAmount,
                'payment_mode'   => 'cash',
                'receipt_date'   => $date,
                'qr_token'       => \Illuminate\Support\Str::random(40),
                'notes'          => 'Initial admission fee payment',
            ]);

            // Settle installments with paid amount
            $unpaidFees = StudentFee::where('admission_id', $admission->id)->orderBy('installment_no')->get();
            $remaining = $paidAmount;
            foreach ($unpaidFees as $fee) {
                if ($remaining <= 0) break;
                $unpaid = (float) $fee->amount - (float) $fee->paid_amount;
                if ($unpaid <= 0) continue;

                $alloc = min($remaining, $unpaid);
                $newPaid = (float) $fee->paid_amount + $alloc;
                $fee->update([
                    'paid_amount' => $newPaid,
                    'status'      => $newPaid >= (float) $fee->amount ? 'paid' : 'partial',
                ]);
                $remaining -= $alloc;
            }
        });
    }
}
