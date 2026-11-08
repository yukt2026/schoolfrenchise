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
        'verification_token',
        'id_card_status',
        'id_card_issued_at',
        'id_card_expires_at',
    ];

    protected $casts = [
        'dob'                => 'date',
        'id_card_issued_at'  => 'datetime',
        'id_card_expires_at' => 'date',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($student) {
            if (empty($student->verification_token)) {
                $student->verification_token = static::generateUniqueVerificationToken();
            }
            if (empty($student->id_card_status)) {
                $student->id_card_status = 'valid';
            }
            if (empty($student->id_card_issued_at)) {
                $student->id_card_issued_at = now();
            }
        });
    }

    /**
     * Generate a unique, unguessable cryptographic verification token.
     */
    public static function generateUniqueVerificationToken(): string
    {
        do {
            $token = 'STU-' . strtoupper(\Illuminate\Support\Str::random(10)) . '-' . rand(1000, 9999);
        } while (static::where('verification_token', $token)->exists());

        return $token;
    }

    /**
     * Ensure student has a verification token, generating one if missing.
     */
    public function ensureVerificationToken(): string
    {
        if (empty($this->verification_token)) {
            $this->verification_token = static::generateUniqueVerificationToken();
            $this->saveQuietly();
        }
        return $this->verification_token;
    }

    /**
     * Public verification URL for QR code encoding.
     */
    public function getVerificationUrlAttribute(): string
    {
        $token = $this->ensureVerificationToken();
        return route('verify.student', ['token' => $token]);
    }

    /**
     * Calculate card expiry date dynamically from enrolled course duration or 1 year.
     */
    public function getCalculatedExpiryDateAttribute(): ?\Illuminate\Support\Carbon
    {
        if ($this->id_card_expires_at) {
            return \Illuminate\Support\Carbon::parse($this->id_card_expires_at);
        }

        $admission = $this->admissions->first() ?? $this->admissions()->first();
        if ($admission && $admission->admission_date) {
            $months = 12; // default 12 months
            if ($admission->course && !empty($admission->course->duration)) {
                $dur = strtolower($admission->course->duration);
                if (preg_match('/(\d+)\s*month/i', $dur, $m)) {
                    $months = (int) $m[1];
                } elseif (preg_match('/(\d+)\s*year/i', $dur, $m)) {
                    $months = (int) $m[1] * 12;
                }
            }
            return \Illuminate\Support\Carbon::parse($admission->admission_date)->addMonths($months);
        }

        return now()->addYear();
    }

    public function getCalculatedIssueDateAttribute(): string
    {
        $admission = $this->admissions->first() ?? $this->admissions()->latest('admission_date')->first();
        if ($admission && $admission->admission_date) {
            $course = $admission->course;
            $months = $course ? $course->duration_months : 6;
            return \Illuminate\Support\Carbon::parse($admission->admission_date)->addMonths($months)->toDateString();
        }
        return now()->toDateString();
    }

    /**
     * Determine effective status of ID card (valid, expired, revoked, invalid).
     */
    public function getEffectiveIdCardStatusAttribute(): string
    {
        if ($this->id_card_status === 'revoked') {
            return 'revoked';
        }

        if ($this->id_card_status === 'invalid') {
            return 'invalid';
        }

        $expiry = $this->calculated_expiry_date;
        if ($expiry && $expiry->isPast()) {
            return 'expired';
        }

        return $this->id_card_status ?: 'valid';
    }

    /**
     * Get vector SVG Data URI QR code for student ID card.
     */
    public function getQrCodeDataUriAttribute(): string
    {
        return \App\Services\QrCodeService::generateDataUri($this->verification_url, 140);
    }

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

    /**
     * Get the resolved URL for the student's passport photo.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (empty($this->photo_path)) {
            return null;
        }

        if (str_starts_with($this->photo_path, 'http://') || str_starts_with($this->photo_path, 'https://')) {
            return $this->photo_path;
        }

        // 1. Direct in public directory (e.g. public/uploads/students/photos/...)
        if (file_exists(public_path($this->photo_path))) {
            return asset($this->photo_path);
        }

        // 2. In public/storage symlink
        if (file_exists(public_path('storage/' . ltrim($this->photo_path, '/')))) {
            return asset('storage/' . ltrim($this->photo_path, '/'));
        }

        // 3. Fallback: storage/app/public exists on server, serve via asset or fallback
        if (file_exists(storage_path('app/public/' . ltrim($this->photo_path, '/')))) {
            return asset('storage/' . ltrim($this->photo_path, '/'));
        }

        return asset('storage/' . ltrim($this->photo_path, '/'));
    }

    /**
     * Check if the student has a valid photo file.
     */
    public function getHasPhotoAttribute(): bool
    {
        if (empty($this->photo_path)) {
            return false;
        }

        return file_exists(public_path($this->photo_path))
            || file_exists(public_path('storage/' . ltrim($this->photo_path, '/')))
            || file_exists(storage_path('app/public/' . ltrim($this->photo_path, '/')));
    }

    /**
     * Get the resolved URL for the student's signature.
     */
    public function getSignatureUrlAttribute(): ?string
    {
        if (empty($this->signature_path)) {
            return null;
        }

        if (str_starts_with($this->signature_path, 'http://') || str_starts_with($this->signature_path, 'https://')) {
            return $this->signature_path;
        }

        if (file_exists(public_path($this->signature_path))) {
            return asset($this->signature_path);
        }

        return asset('storage/' . ltrim($this->signature_path, '/'));
    }

    /**
     * Get the resolved URL for the student's ID proof / KYC doc.
     */
    public function getIdProofDocUrlAttribute(): ?string
    {
        if (empty($this->id_proof_doc)) {
            return null;
        }

        if (str_starts_with($this->id_proof_doc, 'http://') || str_starts_with($this->id_proof_doc, 'https://')) {
            return $this->id_proof_doc;
        }

        if (file_exists(public_path($this->id_proof_doc))) {
            return asset($this->id_proof_doc);
        }

        return asset('storage/' . ltrim($this->id_proof_doc, '/'));
    }
}
