<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\AuditLog;
use App\Models\Centre;
use App\Models\Course;
use App\Models\FeePayment;
use App\Models\FeeStructure;
use App\Models\PaymentReceipt;
use App\Models\Student;
use App\Models\StudentFee;
use App\Services\FeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminFeeController extends Controller
{
    public function index(Request $request)
    {
        $query = FeePayment::with(['student', 'admission.course', 'centre', 'receipt', 'collector']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('receipt_no', 'like', "%{$s}%")
                    ->orWhere('transaction_ref', 'like', "%{$s}%")
                    ->orWhereHas('student', function ($sq) use ($s) {
                        $sq->where('full_name', 'like', "%{$s}%")
                            ->orWhere('roll_no', 'like', "%{$s}%")
                            ->orWhere('registration_no', 'like', "%{$s}%");
                    });
            });
        }

        if ($request->filled('centre_id')) {
            $query->where('centre_id', $request->centre_id);
        }

        if ($request->filled('payment_mode')) {
            $query->where('payment_mode', $request->payment_mode);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        $payments = $query->latest('payment_date')->latest('id')->paginate(15)->withQueryString();

        // Strict financial calculations separating collected, pending dues, online, and discounts
        $totalCollected = (float) FeePayment::sum('amount');
        $onlineCollected = (float) FeePayment::whereIn('payment_mode', ['online', 'upi'])->sum('amount');
        $cashCollected = (float) FeePayment::where('payment_mode', 'cash')->sum('amount');
        $totalDiscounts = (float) Admission::sum('discount_amount');

        // Total Outstanding Dues across all active admissions
        $allAdmissions = Admission::whereIn('status', ['active', 'enrolled'])->get(['net_fee', 'total_paid']);
        $totalDues = $allAdmissions->sum(function ($adm) {
            return max(0, (float) $adm->net_fee - (float) $adm->total_paid);
        });

        $stats = [
            'total_collected'  => $totalCollected,
            'total_dues'       => $totalDues,
            'online_collected' => $onlineCollected,
            'cash_collected'   => $cashCollected,
            'total_discounts'  => $totalDiscounts,
        ];

        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();

        return view('admin.fees.index', compact('payments', 'stats', 'centres'));
    }

    public function dues(Request $request)
    {
        $query = Admission::with(['student.centre', 'course', 'centre', 'studentFees'])
            ->whereIn('status', ['active', 'enrolled'])
            ->whereRaw('(net_fee - total_paid) > 0');

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->whereHas('student', function ($sq) use ($s) {
                $sq->where('full_name', 'like', "%{$s}%")
                    ->orWhere('roll_no', 'like', "%{$s}%")
                    ->orWhere('registration_no', 'like', "%{$s}%");
            });
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('centre_id')) {
            $query->where('centre_id', $request->centre_id);
        }

        $totalOutstanding = (float) (clone $query)->sum(DB::raw('net_fee - total_paid'));
        $admissions = $query->latest()->paginate(15)->withQueryString();

        $courses = Course::where('status', true)->orderBy('course_name')->get();
        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();

        return view('admin.fees.dues', compact('admissions', 'totalOutstanding', 'courses', 'centres'));
    }

    public function create(Request $request)
    {
        $admissions = Admission::with(['student.centre', 'course', 'studentFees'])
            ->whereIn('status', ['active', 'enrolled'])
            ->get()
            ->filter(function ($adm) {
                return ((float) $adm->net_fee - (float) $adm->total_paid) > 0;
            });

        $selectedAdmission = null;
        if ($request->filled('admission_id')) {
            $selectedAdmission = Admission::with(['student.centre', 'course', 'studentFees'])->find($request->admission_id);
        } elseif ($request->filled('student_id')) {
            $selectedAdmission = Admission::with(['student.centre', 'course', 'studentFees'])
                ->where('student_id', $request->student_id)
                ->latest()
                ->first();
        }

        return view('admin.fees.create', compact('admissions', 'selectedAdmission'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'admission_id'    => 'required|exists:admissions,id',
            'amount'          => 'required|numeric|min:1',
            'payment_mode'    => 'required|in:cash,upi,online,cheque,dd',
            'transaction_ref' => 'nullable|string|max:100',
            'student_fee_id'  => 'nullable|exists:student_fees,id',
            'notes'           => 'nullable|string|max:255',
        ]);

        $admission = Admission::with('student')->findOrFail($validated['admission_id']);

        try {
            $receipt = FeeService::collectPayment(
                $admission,
                (float) $validated['amount'],
                $validated['payment_mode'],
                $validated['student_fee_id'] ?? null,
                $validated['transaction_ref'] ?? null,
                $validated['notes'] ?? null
            );

            return redirect()->route('admin.fees.receipt', $receipt)
                ->with('success', "Payment of Rs. " . number_format($validated['amount'], 2) . " collected successfully. Receipt #{$receipt->receipt_no} generated.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function receipt(PaymentReceipt $receipt)
    {
        $receipt->load([
            'student.centre',
            'admission.course',
            'centre',
            'issuer',
            'payment',
        ]);

        return view('admin.fees.receipt', compact('receipt'));
    }

    public function refund(Request $request, FeePayment $payment)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($payment, $validated) {
            $admission = $payment->admission;
            $refundAmount = (float) $payment->amount;

            // Reduce total paid on admission
            if ($admission) {
                $newTotalPaid = max(0, (float) $admission->total_paid - $refundAmount);
                $admission->update([
                    'total_paid' => $newTotalPaid,
                    'status'     => 'active',
                ]);
            }

            // Adjust linked installment if any
            if ($payment->student_fee_id) {
                $installment = StudentFee::find($payment->student_fee_id);
                if ($installment) {
                    $newInstPaid = max(0, (float) $installment->paid_amount - $refundAmount);
                    $installment->update([
                        'paid_amount' => $newInstPaid,
                        'status'      => $newInstPaid > 0 ? 'partial' : 'pending',
                    ]);
                }
            }

            AuditLog::record(
                'fee_payment_refunded',
                $payment,
                $payment->toArray(),
                [
                    'refunded_amount' => $refundAmount,
                    'reason'          => $validated['reason'],
                    'processed_by'    => Auth::id(),
                ]
            );

            PaymentReceipt::where('fee_payment_id', $payment->id)->delete();
            $payment->delete();
        });

        return redirect()->route('admin.fees.index')
            ->with('success', "Payment of Rs. " . number_format($payment->amount, 2) . " has been refunded and admission ledger adjusted.");
    }

    /**
     * Display configured fee structures list.
     */
    public function structures(Request $request)
    {
        $structures = FeeStructure::with(['course', 'centre'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.fees.structures', compact('structures'));
    }

    /**
     * Show form to configure a new course fee structure.
     */
    public function createStructure()
    {
        $courses = Course::where('status', true)->orderBy('course_name')->get();
        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();

        return view('admin.fees.create_structure', compact('courses', 'centres'));
    }

    /**
     * Store newly configured fee structure.
     */
    public function storeStructure(Request $request)
    {
        $validated = $request->validate([
            'course_id'          => 'required|exists:courses,id',
            'centre_id'          => 'nullable|exists:centres,id',
            'name'               => 'required|string|max:120',
            'total_fee'          => 'required|numeric|min:0',
            'admission_fee'      => 'nullable|numeric|min:0',
            'tuition_fee'        => 'nullable|numeric|min:0',
            'exam_fee'           => 'nullable|numeric|min:0',
            'certificate_fee'    => 'nullable|numeric|min:0',
            'installments_count' => 'required|integer|min:1|max:24',
            'terms'              => 'nullable|string|max:2000',
            'is_active'          => 'nullable|boolean',
        ]);

        $validated['admission_fee'] = $validated['admission_fee'] ?? 0;
        $validated['tuition_fee'] = $validated['tuition_fee'] ?? 0;
        $validated['exam_fee'] = $validated['exam_fee'] ?? 0;
        $validated['certificate_fee'] = $validated['certificate_fee'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active', true);

        $structure = FeeStructure::create($validated);

        AuditLog::record('fee_structure_created', $structure, null, [
            'name'      => $structure->name,
            'total_fee' => $structure->total_fee,
        ]);

        return redirect()->route('admin.fees.structures')
            ->with('success', "Fee structure [{$structure->name}] configured successfully.");
    }

    /**
     * Show edit form for fee structure.
     */
    public function editStructure(FeeStructure $structure)
    {
        $courses = Course::where('status', true)->orderBy('course_name')->get();
        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();

        return view('admin.fees.edit_structure', compact('structure', 'courses', 'centres'));
    }

    /**
     * Update existing fee structure.
     */
    public function updateStructure(Request $request, FeeStructure $structure)
    {
        $validated = $request->validate([
            'course_id'          => 'required|exists:courses,id',
            'centre_id'          => 'nullable|exists:centres,id',
            'name'               => 'required|string|max:120',
            'total_fee'          => 'required|numeric|min:0',
            'admission_fee'      => 'nullable|numeric|min:0',
            'tuition_fee'        => 'nullable|numeric|min:0',
            'exam_fee'           => 'nullable|numeric|min:0',
            'certificate_fee'    => 'nullable|numeric|min:0',
            'installments_count' => 'required|integer|min:1|max:24',
            'terms'              => 'nullable|string|max:2000',
            'is_active'          => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $old = $structure->toArray();
        $structure->update($validated);

        AuditLog::record('fee_structure_updated', $structure, $old, $structure->toArray());

        return redirect()->route('admin.fees.structures')
            ->with('success', "Fee structure [{$structure->name}] updated successfully.");
    }

    /**
     * Deactivate or delete fee structure.
     */
    public function destroyStructure(FeeStructure $structure)
    {
        $structure->delete();

        AuditLog::record('fee_structure_deleted', $structure, ['status' => 'deleted']);

        return redirect()->route('admin.fees.structures')
            ->with('success', "Fee structure removed successfully.");
    }

    /**
     * Show form to configure custom installments & concessions for a student admission.
     */
    public function assignScheduleForm(Admission $admission)
    {
        $admission->load(['student', 'course', 'centre', 'studentFees']);
        $feeStructures = FeeStructure::where('course_id', $admission->course_id)
            ->where('is_active', true)
            ->get();

        return view('admin.fees.custom_schedule', compact('admission', 'feeStructures'));
    }

    /**
     * Store custom installment schedule and concessions for student admission.
     */
    public function storeScheduleAssignment(Request $request, Admission $admission)
    {
        $validated = $request->validate([
            'course_fee'           => 'required|numeric|min:0',
            'concession_amount'    => 'nullable|numeric|min:0',
            'adjustment_reason'    => 'nullable|string|max:255',
            'installments'         => 'required|array|min:1',
            'installments.*.title' => 'required|string|max:100',
            'installments.*.amount'=> 'required|numeric|min:1',
            'installments.*.due_date' => 'required|date',
        ]);

        $courseFee = (float) $validated['course_fee'];
        $concession = (float) ($validated['concession_amount'] ?? 0);
        $totalInstallmentSum = array_sum(array_column($validated['installments'], 'amount'));
        $netFee = max(0, $courseFee - $concession);

        if (abs($totalInstallmentSum - $netFee) > 0.5) {
            return back()->withInput()->with('error', "Total of installments (₹{$totalInstallmentSum}) must equal the net fee (₹{$netFee} = ₹{$courseFee} - ₹{$concession}).");
        }

        FeeService::assignCustomSchedule(
            $admission,
            $courseFee,
            $concession,
            $validated['installments'],
            $validated['adjustment_reason'] ?? null
        );

        return redirect()->route('admin.admissions.print', $admission->id)
            ->with('success', "Instalment schedule and fee adjustments saved successfully for student.");
    }
}
