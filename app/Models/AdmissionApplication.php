<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_no',
        'centre_id',
        'course_id',
        'applicant_name',
        'father_name',
        'mother_name',
        'dob',
        'gender',
        'mobile',
        'email',
        'state_id',
        'district_id',
        'city',
        'address',
        'pincode',
        'highest_qualification',
        'photo_path',
        'id_proof_doc',
        'status',
        'rejection_reason',
        'reviewed_by',
        'converted_student_id',
    ];

    protected function casts(): array
    {
        return [
            'dob' => 'date',
        ];
    }

    public function centre()
    {
        return $this->belongsTo(Centre::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'converted_student_id');
    }

    public function convertedStudent()
    {
        return $this->belongsTo(Student::class, 'converted_student_id');
    }

    public static function generateApplicationNumber(): string
    {
        return 'APP-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (empty($this->photo_path)) {
            return null;
        }

        if (str_starts_with($this->photo_path, 'http://') || str_starts_with($this->photo_path, 'https://')) {
            return $this->photo_path;
        }

        if (file_exists(public_path($this->photo_path))) {
            return asset($this->photo_path);
        }

        return asset('storage/' . ltrim($this->photo_path, '/'));
    }

    public function getIdProofUrlAttribute(): ?string
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
