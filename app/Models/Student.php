<?php

namespace App\Models;

use App\Models\Traits\BelongsToCentre;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes, BelongsToCentre;

    protected $fillable = [
        'user_id',
        'centre_id',
        'registration_no',
        'roll_no',
        'full_name',
        'father_name',
        'mother_name',
        'dob',
        'gender',
        'mobile',
        'alternate_mobile',
        'email',
        'state_id',
        'district_id',
        'city',
        'address',
        'pincode',
        'highest_qualification',
        'photo_path',
        'signature_path',
        'id_proof_doc',
        'status',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function centre()
    {
        return $this->belongsTo(Centre::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }

    public function fees()
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

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function examResults()
    {
        return $this->hasMany(ExamResult::class);
    }

    public function results()
    {
        return $this->hasMany(ExamResult::class);
    }

    public function marksheets()
    {
        return $this->hasMany(Marksheet::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function admitCards()
    {
        return $this->hasMany(AdmitCard::class);
    }

    public function admissionApplications()
    {
        return $this->hasMany(AdmissionApplication::class, 'converted_student_id');
    }

    public function getPhoneAttribute(): ?string
    {
        return $this->mobile;
    }

    public function getDateOfBirthAttribute(): ?\Illuminate\Support\Carbon
    {
        return $this->dob;
    }

    public function getAcademicSessionAttribute(): string
    {
        $admission = $this->admissions->first() ?? $this->admissions()->latest('admission_date')->first();
        if ($admission && $admission->admission_date) {
            $year = (int) $admission->admission_date->format('Y');
            return $year . '-' . substr((string)($year + 1), -2);
        }
        return date('Y') . '-' . substr((string)(date('Y') + 1), -2);
    }
}
