<?php

namespace App\Http\Controllers\Centre;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Centre;
use App\Models\FeePayment;
use App\Models\PaymentReceipt;
use App\Models\Student;
use App\Models\StudentFee;
use App\Services\FeeService;
use App\Services\NotificationDispatchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CentreFeeController extends Controller
{
    protected function getAuthCentre(): Centre
    {
        $user = Auth::user();
        $centre = $user->centre;

        if (!$centre && $user->isSuperAdmin()) {
            $centre = Centre::where('status', 'active')->first();
        }

        if (!$centre) {
            abort(403, 'No franchise study centre is linked to your user account.');
        }

        return $centre;
    }

    public function index(Request $request)
    {
        $centre = $this->getAuthCentre();
        $query = FeePayment::where('centre_id', $centre->id)
            ->with(['student', 'admission.course', 'paymentReceipt']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('receipt_no', 'like', "%{$s}%")
                    ->orWhere('transaction_ref', 'like', "%{$s}%")
                    ->orWhereHas('student', function ($sq) use ($s) {
                        $sq->where('full_name', 'like', "%{$s}%")
                            ->orWhere('roll_no', 'like', "%{$s}%");
                    });
            });
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

        $payments = $query->latest('id')->paginate(15)->withQueryString();

        $stats = [
            'total_collected' => (float) FeePayment::where('centre_id', $centre->id)->sum('amount'),
            'today_collected' => (float) FeePayment::where('centre_id', $centre->id)->whereDate('payment_date', now()->toDateString())->sum('amount'),
            'cash_count' => FeePayment::where('centre_id', $centre->id)->where('payment_mode', 'cash')->count(),
            'online_count' => FeePayment::where('centre_id', $centre->id)->whereIn('payment_mode', ['upi', 'online', 'bank_transfer'])->count(),
        ];

        return view('centre.fees.index', compact('centre', 'payments', 'stats'));
    }

    public function create(Request $request)
    {
        $centre = $this->getAuthCentre();

        $students = Student::where('centre_id', $centre->id)
            ->whereHas('admissions', function ($q) {
                $q->whereRaw('net_fee > total_paid');
            })
            ->with(['admissions.course', 'admissions.studentFees'])
            ->orderBy('full_name')
            ->get();

        $selectedAdmission = null;
        if ($request->filled('admission_id')) {
            $selectedAdmission = Admission::where('centre_id', $centre->id)
                ->with(['student', 'course', 'studentFees'])
                ->findOrFail($request->admission_id);
        }

        return view('centre.fees.create', compact('centre', 'students', 'selectedAdmission'));
    }

    public function store(Request $request)
    {
        $centre = $this->getAuthCentre();

        $validated = $request->validate([
            'admission_id' => 'required|exists:admissions,id',
            'amount' => 'required|numeric|min:1',
            'payment_mode' => 'required|in:cash,upi,cheque,bank_transfer,dd',
            'student_fee_id' => 'nullable|exists:student_fees,id',
            'transaction_ref' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:255',
        ]);

        $admission = Admission::where('centre_id', $centre->id)->findOrFail($validated['admission_id']);
        $due = max(0, (float) $admission->net_fee - (float) $admission->total_paid);

        if ((float) $validated['amount'] > $due) {
            return back()->withInput()->with('error', "Amount cannot exceed outstanding dues of ₹" . number_format($due, 2) . ".");
        }

        $receipt = FeeService::collectPayment(
            $admission,
            (float) $validated['amount'],
            $validated['payment_mode'],
            $validated['student_fee_id'] ?? null,
            $validated['transaction_ref'] ?? null,
            $validated['notes'] ?? "Collected by Centre [{$centre->centre_code}]"
        );

        return redirect()->route('centre.fees.receipt', $receipt->id)
            ->with('success', "Fee of ₹" . number_format($receipt->amount, 2) . " received successfully. Receipt No: {$receipt->receipt_no}.");
    }

    public function receipt($receipt)
    {
        $centre = $this->getAuthCentre();

        if ($receipt instanceof PaymentReceipt) {
            $paymentReceipt = $receipt;
        } else {
            $paymentReceipt = PaymentReceipt::find($receipt);
            if (!$paymentReceipt) {
                $paymentReceipt = PaymentReceipt::where('fee_payment_id', $receipt)->first();
            }
            if (!$paymentReceipt) {
                $paymentReceipt = PaymentReceipt::where('receipt_no', $receipt)->first();
            }
            if (!$paymentReceipt) {
                $feePayment = \App\Models\FeePayment::find($receipt);
                if ($feePayment) {
                    $paymentReceipt = PaymentReceipt::create([
                        'fee_payment_id' => $feePayment->id,
                        'receipt_no' => $feePayment->receipt_no ?? PaymentReceipt::generateReceiptNo($feePayment->centre_id ?? $centre->id),
                        'student_id' => $feePayment->student_id,
                        'admission_id' => $feePayment->admission_id,
                        'centre_id' => $feePayment->centre_id ?? $centre->id,
                        'amount' => $feePayment->amount,
                        'payment_mode' => $feePayment->payment_mode ?? 'cash',
                        'receipt_date' => $feePayment->payment_date ?? now(),
                        'qr_token' => 'QR-' . strtoupper(bin2hex(random_bytes(6))),
                        'issued_by' => Auth::id(),
                    ]);
                }
            }

            if (!$paymentReceipt) {
                abort(404, 'Payment receipt record not found.');
            }
        }

        if ($paymentReceipt->centre_id !== $centre->id && !Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access: This payment receipt was issued by another study centre.');
        }

        $paymentReceipt->load(['student', 'admission.course', 'centre.state', 'centre.district', 'payment', 'issuer']);

        return view('centre.fees.receipt', compact('centre', 'paymentReceipt'));
    }

    public function dues(Request $request)
    {
        $centre = $this->getAuthCentre();
        $query = Admission::where('centre_id', $centre->id)
            ->whereRaw('net_fee > total_paid')
            ->with(['student', 'course', 'studentFees']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->whereHas('student', function ($sq) use ($s) {
                $sq->where('full_name', 'like', "%{$s}%")
                    ->orWhere('roll_no', 'like', "%{$s}%")
                    ->orWhere('mobile', 'like', "%{$s}%");
            });
        }

        $admissionsWithDues = $query->paginate(15)->withQueryString();

        $totalOutstanding = (float) Admission::where('centre_id', $centre->id)->sum('net_fee')
                          - (float) Admission::where('centre_id', $centre->id)->sum('total_paid');

        return view('centre.fees.dues', compact('centre', 'admissionsWithDues', 'totalOutstanding'));
    }
}
