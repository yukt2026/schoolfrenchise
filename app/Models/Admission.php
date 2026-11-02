<?php

namespace App\Models;

use App\Models\Traits\BelongsToCentre;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admission extends Model
{
    use HasFactory, SoftDeletes, BelongsToCentre;

    protected $fillable = [
        'student_id',
        'centre_id',
        'course_id',
        'batch_id',
        'admission_date',
        'course_fee',
        'discount_amount',
        'net_fee',
        'total_paid',
        'status',
        'commission_credited',
        'commission_amount',
        'remarks',
    ];

    protected $casts = [
        'admission_date' => 'date',
        'course_fee' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'net_fee' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'commission_credited' => 'boolean',
        'commission_amount' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function centre()
    {
        return $this->belongsTo(Centre::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function fees()
    {
        return $this->hasMany(StudentFee::class);
    }

    public function studentFees()
    {
        return $this->hasMany(StudentFee::class);
    }

    public function payments()
    {
        return $this->hasMany(FeePayment::class);
    }

    public function paymentReceipts()
    {
        return $this->hasMany(PaymentReceipt::class);
    }
}
