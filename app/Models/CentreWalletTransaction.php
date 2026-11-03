<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentreWalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'centre_id',
        'type',
        'amount',
        'balance_after',
        'reference_no',
        'payment_mode',
        'payment_slip',
        'admin_remarks',
        'description',
        'approved_by',
        'status',
    ];

    /**
     * Get the accessible public URL of the uploaded payment screenshot.
     */
    public function getPaymentSlipUrlAttribute(): ?string
    {
        if (empty($this->payment_slip)) {
            return null;
        }

        if (str_starts_with($this->payment_slip, 'http://') || str_starts_with($this->payment_slip, 'https://')) {
            return $this->payment_slip;
        }

        return asset('storage/' . $this->payment_slip);
    }

    public function centre()
    {
        return $this->belongsTo(Centre::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
