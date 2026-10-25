<?php

namespace App\Http\Controllers\Centre;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Batch;
use App\Models\Centre;
use App\Models\CentreWalletTransaction;
use App\Models\Notice;
use App\Models\Exam;
use App\Models\FeePayment;
use App\Models\Student;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CentreDashboardController extends Controller
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

    public function index()
    {
        $centre = $this->getAuthCentre();
        $wallet = $centre->wallet;

        $stats = [
            'centre_code' => $centre->centre_code,
            'centre_name' => $centre->centre_name,
            'wallet_balance' => (float) ($wallet ? $wallet->balance : 0.00),
            'total_students' => Student::where('centre_id', $centre->id)->count(),
            'active_batches' => Batch::where('centre_id', $centre->id)->where('status', 'active')->count(),
            'total_admissions' => Admission::where('centre_id', $centre->id)->count(),
            'fee_collected' => (float) FeePayment::where('centre_id', $centre->id)->sum('amount'),
            'open_tickets' => SupportTicket::where('centre_id', $centre->id)->whereIn('status', ['open', 'in_progress'])->count(),
            'upcoming_exams' => Exam::where('status', 'scheduled')->whereDate('exam_date', '>=', now()->toDateString())->count(),
        ];

        $recentStudents = Student::where('centre_id', $centre->id)
            ->with(['admissions.course'])
            ->latest()
            ->take(6)
            ->get();

        $recentPayments = FeePayment::where('centre_id', $centre->id)
            ->with(['student', 'admission.course', 'paymentReceipt'])
            ->latest()
            ->take(6)
            ->get();

        $recentWalletTxns = CentreWalletTransaction::where('centre_id', $centre->id)
            ->latest()
            ->take(5)
            ->get();

        $notices = Notice::where('status', true)
            ->where(function ($q) {
                $q->whereNull('target_audience')
                  ->orWhere('target_audience', 'all')
                  ->orWhere('target_audience', 'centre');
            })
            ->latest('id')
            ->take(4)
            ->get();

        return view('centre.dashboard', compact('centre', 'stats', 'recentStudents', 'recentPayments', 'recentWalletTxns', 'notices'));
    }
}
