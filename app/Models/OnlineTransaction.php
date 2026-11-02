<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'order_id',
        'student_roll_no',
        'admission_id',
        'centre_id',
        'student_id',
        'fee_payment_id',
        'payer_name',
        'payer_mobile',
        'payer_email',
        'purpose',
        'amount',
        'payment_method',
        'gateway_name',
        'gateway_payment_id',
        'gateway_signature',
        'idempotency_key',
        'status',
        'response_payload',
        'error_message',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function admission()
    {
        return $this->belongsTo(Admission::class);
    }

    public function centre()
    {
        return $this->belongsTo(Centre::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function feePayment()
    {
        return $this->belongsTo(FeePayment::class);
    }

    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }

    public function isPending(): bool
    {
        return in_array($this->status, ['initiated', 'pending'], true);
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }
}
