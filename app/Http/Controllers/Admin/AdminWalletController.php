<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Centre;
use App\Models\CentreWallet;
use App\Models\CentreWalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdminWalletController extends Controller
{
    public function index(Request $request)
    {
        $query = CentreWalletTransaction::with(['centre.state', 'approver']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('reference_no', 'like', "%{$s}%")
                    ->orWhere('description', 'like', "%{$s}%")
                    ->orWhereHas('centre', function ($cq) use ($s) {
                        $cq->where('centre_name', 'like', "%{$s}%")
                            ->orWhere('centre_code', 'like', "%{$s}%");
                    });
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('centre_id')) {
            $query->where('centre_id', $request->centre_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->latest('id')->paginate(15)->withQueryString();

        // Strict financial calculations separating circulating, credited, debited, pending
        $stats = [
            'total_circulating_balance' => (float) CentreWallet::sum('balance'),
            'total_lifetime_credited'   => (float) CentreWalletTransaction::where('type', 'credit')->where('status', 'approved')->sum('amount'),
            'total_lifetime_debited'    => (float) CentreWalletTransaction::where('type', 'debit')->where('status', 'approved')->sum('amount'),
            'pending_recharge_count'    => CentreWalletTransaction::where('status', 'pending')->count(),
            'pending_recharge_amount'   => (float) CentreWalletTransaction::where('status', 'pending')->sum('amount'),
            'frozen_wallets_count'      => CentreWallet::where('status', 'frozen')->count(),
        ];

        $centres = Centre::where('status', 'active')->orderBy('centre_name')->get();

        return view('admin.wallet.index', compact('transactions', 'stats', 'centres'));
    }

    public function adjust()
    {
        $centres = Centre::with('wallet')->where('status', 'active')->orderBy('centre_name')->get();

        return view('admin.wallet.adjust', compact('centres'));
    }

    public function storeAdjustment(Request $request)
    {
        $validated = $request->validate([
            'centre_id'    => 'required|exists:centres,id',
            'type'         => 'required|in:credit,debit',
            'amount'       => 'required|numeric|min:1',
            'reference_no' => 'required|string|max:100',
            'description'  => 'required|string|max:255',
        ]);

        $amount = (float) $validated['amount'];
        $type = $validated['type'];

        $transaction = DB::transaction(function () use ($validated, $amount, $type) {
            $wallet = CentreWallet::firstOrCreate(
                ['centre_id' => $validated['centre_id']],
                [
                    'balance'        => 0.00,
                    'total_credited' => 0.00,
                    'total_debited'  => 0.00,
                    'status'         => 'active',
                ]
            );

            // Row-level lock
            $wallet = CentreWallet::where('id', $wallet->id)->lockForUpdate()->first();

            if ($wallet->status === 'frozen') {
                throw ValidationException::withMessages([
                    'centre_id' => 'Cannot execute ledger adjustment: This centre wallet is currently frozen.',
                ]);
            }

            if ($type === 'debit' && (float) $wallet->balance < $amount) {
                throw ValidationException::withMessages([
                    'amount' => "Insufficient balance: Available balance is ₹" . number_format($wallet->balance, 2) . ", cannot debit ₹" . number_format($amount, 2) . ".",
                ]);
            }

            $oldBalance = (float) $wallet->balance;
            $newBalance = $type === 'credit' ? $oldBalance + $amount : $oldBalance - $amount;

            $wallet->balance = $newBalance;
            if ($type === 'credit') {
                $wallet->total_credited = (float) $wallet->total_credited + $amount;
            } else {
                $wallet->total_debited = (float) $wallet->total_debited + $amount;
            }
            $wallet->save();

            $newTxn = CentreWalletTransaction::create([
                'centre_id'     => $validated['centre_id'],
                'type'          => $type,
                'amount'        => $amount,
                'balance_after' => $newBalance,
                'reference_no'  => strtoupper(trim($validated['reference_no'])),
                'description'   => $validated['description'],
                'approved_by'   => Auth::id(),
                'status'        => 'approved',
            ]);

            AuditLog::record('wallet_adjustment', $newTxn, null, [
                'centre_id'     => $validated['centre_id'],
                'type'          => $type,
                'amount'        => $amount,
                'balance_after' => $newBalance,
                'reference_no'  => $validated['reference_no'],
                'description'   => $validated['description'],
            ]);

            return $newTxn;
        });

        return redirect()->route('admin.wallet.index')
            ->with('success', "Wallet {$type} of ₹" . number_format($amount, 2) . " executed successfully. Reference #{$transaction->reference_no}.");
    }

    public function approveTransaction(Request $request, CentreWalletTransaction $transaction)
    {
        if ($transaction->status !== 'pending') {
            return back()->with('error', 'Only pending top-up requests can be approved.');
        }

        DB::transaction(function () use ($transaction) {
            $wallet = CentreWallet::firstOrCreate(
                ['centre_id' => $transaction->centre_id],
                [
                    'balance'        => 0.00,
                    'total_credited' => 0.00,
                    'total_debited'  => 0.00,
                    'status'         => 'active',
                ]
            );

            $wallet = CentreWallet::where('id', $wallet->id)->lockForUpdate()->first();

            $newBalance = (float) $wallet->balance + (float) $transaction->amount;
            $wallet->balance = $newBalance;
            $wallet->total_credited = (float) $wallet->total_credited + (float) $transaction->amount;
            $wallet->save();

            $transaction->update([
                'status'        => 'approved',
                'balance_after' => $newBalance,
                'approved_by'   => Auth::id(),
            ]);

            AuditLog::record('wallet_recharge_approved', $transaction, null, [
                'amount'        => $transaction->amount,
                'balance_after' => $newBalance,
                'approved_by'   => Auth::id(),
            ]);

            // Dispatch Wallet Alert Notification
            try {
                \App\Services\NotificationDispatchService::dispatchWalletAlert($transaction);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Notification dispatch failed for wallet transaction {$transaction->reference_no}: " . $e->getMessage());
            }
        });

        return back()->with('success', "Top-up request of ₹" . number_format($transaction->amount, 2) . " approved and credited to centre wallet.");
    }

    public function rejectTransaction(Request $request, CentreWalletTransaction $transaction)
    {
        if ($transaction->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be rejected.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $transaction->update([
            'status'      => 'rejected',
            'description' => $transaction->description . " [Rejected: {$validated['reason']}]",
            'approved_by' => Auth::id(),
        ]);

        AuditLog::record('wallet_recharge_rejected', $transaction, null, [
            'reason'      => $validated['reason'],
            'approved_by' => Auth::id(),
        ]);

        return back()->with('success', "Top-up request #{$transaction->id} has been marked as rejected.");
    }

    public function toggleFreeze(Centre $centre)
    {
        $wallet = CentreWallet::firstOrCreate(
            ['centre_id' => $centre->id],
            [
                'balance'        => 0.00,
                'total_credited' => 0.00,
                'total_debited'  => 0.00,
                'status'         => 'active',
            ]
        );

        $newStatus = $wallet->status === 'active' ? 'frozen' : 'active';
        $wallet->update(['status' => $newStatus]);

        AuditLog::record('wallet_freeze_toggled', $wallet, ['status' => $wallet->status], ['status' => $newStatus]);

        return back()->with('success', "Wallet for {$centre->centre_code} is now " . ucfirst($newStatus) . ".");
    }

    public function statement(Centre $centre)
    {
        $centre->load(['wallet', 'state', 'district']);

        $transactions = CentreWalletTransaction::with('approver')
            ->where('centre_id', $centre->id)
            ->latest('id')
            ->paginate(50)
            ->withQueryString();

        $stats = [
            'current_balance' => (float) ($centre->wallet?->balance ?? 0.00),
            'total_credited'  => (float) CentreWalletTransaction::where('centre_id', $centre->id)->where('type', 'credit')->where('status', 'approved')->sum('amount'),
            'total_debited'   => (float) CentreWalletTransaction::where('centre_id', $centre->id)->where('type', 'debit')->where('status', 'approved')->sum('amount'),
        ];

        return view('admin.wallet.statement', compact('centre', 'transactions', 'stats'));
    }
}
