<?php

namespace App\Http\Controllers\Centre;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Centre;
use App\Models\CentreWallet;
use App\Models\CentreWalletTransaction;
use App\Models\OnlineTransaction;
use App\Models\Setting;
use App\Services\FileUploadService;
use App\Services\PaymentGatewayService;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CentreWalletController extends Controller
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

        $wallet = CentreWallet::firstOrCreate(
            ['centre_id' => $centre->id],
            [
                'balance' => 0.00,
                'total_credited' => 0.00,
                'total_debited' => 0.00,
                'status' => 'active',
            ]
        );

        $query = CentreWalletTransaction::where('centre_id', $centre->id)->with('approver');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transactions = $query->latest('id')->paginate(15)->withQueryString();

        $stats = [
            'balance' => (float) $wallet->balance,
            'status' => $wallet->status,
            'total_credited' => (float) $wallet->total_credited,
            'total_debited' => (float) $wallet->total_debited,
            'pending_requests' => CentreWalletTransaction::where('centre_id', $centre->id)->where('status', 'pending')->count(),
            'pending_amount' => (float) CentreWalletTransaction::where('centre_id', $centre->id)->where('status', 'pending')->sum('amount'),
        ];

        return view('centre.wallet.index', compact('centre', 'wallet', 'transactions', 'stats'));
    }

    public function topupForm()
    {
        $centre = $this->getAuthCentre();
        $wallet = $centre->wallet;

        $adminUpiId = Setting::get('admin_upi_id', '9162384660@upi');
        $adminUpiName = Setting::get('admin_upi_name', 'TECH ERA COMPUTER');
        $adminQrCode = Setting::get('admin_qr_code');
        $adminBank = [
            'name'       => Setting::get('admin_bank_name', 'State Bank of India'),
            'account_no' => Setting::get('admin_bank_account_no', '38491028472'),
            'ifsc'       => Setting::get('admin_bank_ifsc', 'SBIN0001234'),
            'holder'     => Setting::get('admin_bank_holder', 'TECH ERA COMPUTER'),
        ];

        $defaultAmount = 1000.00;
        $upiUri = "upi://pay?pa=" . rawurlencode($adminUpiId) . "&pn=" . rawurlencode($adminUpiName) . "&am=" . number_format($defaultAmount, 2, '.', '') . "&cu=INR&tn=" . rawurlencode("Recharge " . $centre->centre_code);
        $qrSvg = QrCodeService::generateSvg($upiUri, 220);

        return view('centre.wallet.topup', compact('centre', 'wallet', 'adminUpiId', 'adminUpiName', 'adminQrCode', 'adminBank', 'defaultAmount', 'upiUri', 'qrSvg'));
    }

    /**
     * AJAX endpoint to generate a live dynamic UPI QR code for the exact amount.
     */
    public function generateQr(Request $request)
    {
        $centre = $this->getAuthCentre();
        $amount = (float) $request->input('amount', 1000);
        if ($amount < 1) {
            $amount = 100.00;
        }

        $adminUpiId = Setting::get('admin_upi_id', '9162384660@upi');
        $adminUpiName = Setting::get('admin_upi_name', 'TECH ERA COMPUTER');

        $upiUri = "upi://pay?pa=" . rawurlencode($adminUpiId) . "&pn=" . rawurlencode($adminUpiName) . "&am=" . number_format($amount, 2, '.', '') . "&cu=INR&tn=" . rawurlencode("Recharge " . $centre->centre_code);
        $qrSvg = QrCodeService::generateSvg($upiUri, 220);

        return response()->json([
            'amount'         => $amount,
            'amount_formatted' => number_format($amount, 2),
            'admin_upi_id'   => $adminUpiId,
            'admin_upi_name' => $adminUpiName,
            'upi_uri'        => $upiUri,
            'qr_svg'         => $qrSvg,
        ]);
    }

    public function storeTopup(Request $request)
    {
        $centre = $this->getAuthCentre();

        $validated = $request->validate([
            'amount' => 'required|numeric|min:100',
            'payment_mode' => 'nullable|string|in:upi,bank_transfer,neft,rtgs,imps,cheque',
            'reference_no' => 'required|string|max:100',
            'notes' => 'nullable|string|max:255',
            'payment_slip' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,gif|max:5120',
        ]);

        $mode = $validated['payment_mode'] ?? 'upi';

        try {
            $slipPath = $request->hasFile('payment_slip')
                ? FileUploadService::uploadDocument($request->file('payment_slip'), 'uploads/wallet/slips')
                : null;
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['payment_slip' => $e->getMessage()]);
        }

        $desc = "Recharge request via " . strtoupper($mode) . " (Ref: " . strtoupper(trim($validated['reference_no'])) . ")";
        if (!empty($validated['notes'])) {
            $desc .= " - " . $validated['notes'];
        }

        $txn = CentreWalletTransaction::create([
            'centre_id'     => $centre->id,
            'type'          => 'credit',
            'amount'        => (float) $validated['amount'],
            'balance_after' => (float) ($centre->wallet?->balance ?? 0.00),
            'reference_no'  => strtoupper(trim($validated['reference_no'])),
            'payment_mode'  => $mode,
            'payment_slip'  => $slipPath,
            'description'   => $desc,
            'status'        => 'pending',
        ]);

        AuditLog::record('centre_wallet_topup_requested', $txn, null, [
            'centre_id'    => $centre->id,
            'amount'       => $txn->amount,
            'reference_no' => $txn->reference_no,
            'payment_mode' => $mode,
            'payment_slip' => $slipPath,
        ]);

        return redirect()->route('centre.wallet.index')
            ->with('success', "Top-up request for ₹" . number_format($txn->amount, 2) . " (Ref #{$txn->reference_no}) submitted successfully! It is now pending Admin approval. Your wallet balance will be credited as soon as verified.");
    }

    /**
     * Initiate server-side order for online franchise wallet recharge.
     */
    public function initiateOnlineRecharge(Request $request)
    {
        $centre = $this->getAuthCentre();

        $validated = $request->validate([
            'amount'         => 'required|numeric|min:100|max:500000',
            'payment_method' => 'nullable|string|in:UPI,DebitCard,CreditCard,NetBanking',
        ]);

        $orderData = PaymentGatewayService::createOrder(
            amount: (float) $validated['amount'],
            payerName: $centre->director_name ?? $centre->centre_name,
            payerMobile: $centre->mobile,
            payerEmail: $centre->email,
            purpose: 'Franchise Wallet Recharge',
            paymentMethod: $validated['payment_method'] ?? 'UPI',
            centreId: $centre->id
        );

        if ($request->wantsJson()) {
            return response()->json($orderData);
        }

        return redirect()->route('centre.wallet.topup')->with([
            'online_order' => $orderData,
            'success'      => "Gateway order [{$orderData['order_id']}] initiated for ₹" . number_format($validated['amount'], 2) . ". Please complete online authorization below."
        ]);
    }

    /**
     * Authoritatively verify gateway signature and credit wallet.
     */
    public function verifyOnlineRecharge(Request $request)
    {
        $centre = $this->getAuthCentre();

        $validated = $request->validate([
            'order_id'           => 'required|string|max:100',
            'gateway_payment_id' => 'required|string|max:100',
            'gateway_signature'  => 'nullable|string|max:255',
        ]);

        $orderId = $validated['order_id'];
        $paymentId = $validated['gateway_payment_id'];
        $signature = $validated['gateway_signature'] ?? null;

        // In sandbox mode without live gateway callback, generate valid test signature
        if (empty($signature) && PaymentGatewayService::isSandbox()) {
            $signature = PaymentGatewayService::generateSignature($orderId, $paymentId);
        }

        if (empty($signature)) {
            return back()->with('error', 'Payment verification failed: Missing gateway signature. Never trust unverified client redirects.');
        }

        $onlineTxn = OnlineTransaction::where('order_id', $orderId)->first();
        if (!$onlineTxn || (int)$onlineTxn->centre_id !== (int)$centre->id) {
            abort(403, 'Unauthorized transaction order reference.');
        }

        $reconciledTxn = PaymentGatewayService::reconcilePayment(
            $orderId,
            $paymentId,
            $signature,
            [
                'recharge_verified_at' => now()->toIso8601String(),
                'centre_id'            => $centre->id,
                'ip'                   => $request->ip(),
            ]
        );

        if ($reconciledTxn->status !== 'success') {
            return redirect()->route('centre.wallet.index')->with('error', 'Online payment verification failed: ' . ($reconciledTxn->error_message ?? 'Signature mismatch or payment declined.'));
        }

        return redirect()->route('centre.wallet.index')->with('success', "Wallet recharge of ₹" . number_format($reconciledTxn->amount, 2) . " verified and credited successfully!");
    }
}
