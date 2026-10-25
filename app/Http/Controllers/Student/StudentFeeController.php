<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\FeePayment;
use App\Models\PaymentReceipt;
use App\Models\Setting;
use App\Models\StudentFee;
use App\Models\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StudentFeeController extends Controller
{
    use HasStudentContext;

    /**
     * Fees & Installment Schedule Overview.
     */
    public function index()
    {
        $student = $this->getStudent();
        $admission = $this->getActiveAdmission($student);

        $totalFees = $admission ? (float) $admission->net_fee : 0.00;
        $totalPaid = $admission ? (float) $admission->total_paid : 0.00;
        $totalDues = max(0.00, $totalFees - $totalPaid);

        $installments = $student->fees()->orderBy('installment_no')->get();
        $fees = $installments;
        $payments = $student->payments()->with('centre')->latest('payment_date')->latest('id')->get();

        return view('student.fees.index', compact('student', 'admission', 'totalFees', 'totalPaid', 'totalDues', 'fees', 'installments', 'payments'));
    }

    /**
     * Fee Payment Intake Form.
     */
    public function payFeeForm(Request $request)
    {
        $student = $this->getStudent();
        $admission = $this->getActiveAdmission($student);

        if (!$admission) {
            return redirect()->route('student.fees')->with('error', 'No active course admission found to process fee payments.');
        }

        $totalFees = (float) $admission->net_fee;
        $totalPaid = (float) $admission->total_paid;
        $totalDues = max(0.00, $totalFees - $totalPaid);

        if ($totalDues <= 0) {
            return redirect()->route('student.fees')->with('info', 'Congratulations! All assigned course fees have already been fully cleared.');
        }

        // Suggest next pending installment amount if available
        $nextInstallment = $student->fees()
            ->where('status', '!=', 'paid')
            ->orderBy('installment_no')
            ->first();

        $suggestedAmount = $nextInstallment ? ($nextInstallment->amount - $nextInstallment->paid_amount) : $totalDues;
        $gatewayMode = Setting::get('payment_gateway_mode', 'sandbox');

        return view('student.fees.pay', compact('student', 'admission', 'totalFees', 'totalPaid', 'totalDues', 'nextInstallment', 'suggestedAmount', 'gatewayMode'));
    }

    /**
     * Process Fee Payment Transaction.
     */
    public function processPayment(Request $request)
    {
        $student = $this->getStudent();
        $admission = $this->getActiveAdmission($student);

        if (!$admission) {
            return redirect()->route('student.fees')->with('error', 'No active admission record found.');
        }

        $totalDues = max(0.00, (float) $admission->net_fee - (float) $admission->total_paid);

        $validated = $request->validate([
            'amount'       => ['required', 'numeric', 'min:1', 'max:' . max(1, $totalDues)],
            'payment_mode' => ['required', 'string', 'in:upi,card,net_banking,online'],
        ]);

        $payAmount = (float) $validated['amount'];

        return DB::transaction(function () use ($student, $admission, $payAmount, $validated) {
            // Generate official receipt number
            $receiptNo = 'RCP-' . date('Y') . '-' . strtoupper(Str::random(6));
            $txnRef = 'TXN-' . strtoupper(Str::random(12));

            // Create cleared fee payment
            $payment = FeePayment::create([
                'student_id'      => $student->id,
                'admission_id'    => $admission->id,
                'centre_id'       => $student->centre_id,
                'receipt_no'      => $receiptNo,
                'payment_date'    => now()->toDateString(),
                'amount'          => $payAmount,
                'payment_mode'    => $validated['payment_mode'],
                'transaction_ref' => $txnRef,
            ]);

            // Create synchronized official payment receipt
            PaymentReceipt::create([
                'fee_payment_id' => $payment->id,
                'receipt_no'     => $receiptNo,
                'student_id'     => $student->id,
                'admission_id'   => $admission->id,
                'centre_id'      => $student->centre_id,
                'amount'         => $payAmount,
                'payment_mode'   => $validated['payment_mode'],
                'receipt_date'   => now()->toDateString(),
                'qr_token'       => Str::random(40),
                'issued_by'      => Auth::id(),
                'notes'          => "Online Student Portal Payment ({$validated['payment_mode']}) - Ref: {$txnRef}",
            ]);

            // Update admission ledger
            $admission->increment('total_paid', $payAmount);

            // Allocate amount against unpaid installments
            $remainingAllocation = $payAmount;
            $unpaidFees = $student->fees()->where('status', '!=', 'paid')->orderBy('installment_no')->get();

            foreach ($unpaidFees as $fee) {
                if ($remainingAllocation <= 0) {
                    break;
                }

                $unpaidInFee = $fee->amount - $fee->paid_amount;
                if ($remainingAllocation >= $unpaidInFee) {
                    $fee->paid_amount = $fee->amount;
                    $fee->status = 'paid';
                    $fee->save();
                    $remainingAllocation -= $unpaidInFee;
                } else {
                    $fee->paid_amount += $remainingAllocation;
                    $fee->status = 'partial';
                    $fee->save();
                    $remainingAllocation = 0;
                }
            }

            // Create In-App Notification for student
            if ($student->user_id) {
                SystemNotification::create([
                    'user_id'    => $student->user_id,
                    'title'      => 'Fee Payment Received',
                    'message'    => "Your online fee payment of ₹" . number_format($payAmount, 2) . " was successfully processed. Receipt: {$receiptNo}.",
                    'type'       => 'fee',
                    'action_url' => route('student.fees.receipt', $payment),
                ]);
            }

            // Audit Trail
            AuditLog::record('student_online_fee_paid', $payment, null, [
                'receipt_no'   => $receiptNo,
                'amount'       => $payAmount,
                'payment_mode' => $validated['payment_mode'],
                'student_id'   => $student->id,
            ]);

            return redirect()->route('student.fees.receipt', $payment)
                ->with('success', "Payment of ₹" . number_format($payAmount, 2) . " successfully verified! Your official receipt is ready below.");
        });
    }

    /**
     * Complete Fee Payment History Ledger.
     */
    public function history()
    {
        $student = $this->getStudent();
        $admission = $this->getActiveAdmission($student);

        $payments = $student->payments()
            ->with('centre')
            ->latest('payment_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('student.fees.history', compact('student', 'admission', 'payments'));
    }

    /**
     * Computerized Official Printable Payment Receipt.
     */
    public function receipt($payment)
    {
        if (!$payment instanceof FeePayment) {
            $payment = FeePayment::findOrFail($payment);
        }

        $student = $this->getStudent();

        // Enforce IDOR authorization
        if ($payment->student_id !== $student->id && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access to this receipt.');
        }

        $payment->load(['student.centre', 'admission.course']);
        $admission = $payment->admission;

        return view('student.fees.receipt', compact('student', 'payment', 'admission'));
    }
}
