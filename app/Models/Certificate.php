<?php

namespace App\Models;

use App\Services\QrCodeService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_result_id',
        'student_id',
        'centre_id',
        'course_id',
        'certificate_no',
        'serial_no',
        'verification_token',
        'issue_date',
        'security_hash',
        'qr_code_url',
        'status',
    ];

    protected $casts = [
        'issue_date' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($certificate) {
            if (empty($certificate->verification_token)) {
                $certificate->verification_token = 'CERT-' . strtoupper(Str::random(10)) . '-' . sprintf('%04d', random_int(100, 9999));
            }
            if (empty($certificate->serial_no)) {
                $year = $certificate->issue_date ? date('y', strtotime($certificate->issue_date)) : date('y');
                $certificate->serial_no = sprintf('%s%06d', $year, random_int(100000, 999999));
            }
            if (empty($certificate->qr_code_url)) {
                $certificate->qr_code_url = route('verify.certificate.token', ['token' => $certificate->verification_token]);
            }
        });
    }

    /**
     * Ensure a unique verification token exists.
     */
    public function ensureVerificationToken(): string
    {
        if (empty($this->verification_token)) {
            $this->verification_token = 'CERT-' . strtoupper(Str::random(10)) . '-' . sprintf('%04d', $this->id ?: random_int(100, 9999));
            $this->saveQuietly();
        }

        return $this->verification_token;
    }

    /**
     * Get direct online verification URL for QR code.
     */
    public function getVerificationUrlAttribute(): string
    {
        $token = $this->verification_token ?: $this->certificate_no;
        return route('verify.certificate.token', ['token' => $token]);
    }

    /**
     * Generate inline Base64 SVG Data URI for QR code embedding.
     */
    public function getQrCodeDataUriAttribute(): string
    {
        $url = $this->verification_url;
        return QrCodeService::generateDataUri($url, 160);
    }

    /**
     * Generate raw SVG string for QR code embedding.
     */
    public function getQrCodeSvgAttribute(): string
    {
        $url = $this->verification_url;
        return QrCodeService::generateSvg($url, 160);
    }

    public function result()
    {
        return $this->belongsTo(ExamResult::class, 'exam_result_id');
    }

    public function examResult()
    {
        return $this->belongsTo(ExamResult::class, 'exam_result_id');
    }

    public function marksheet()
    {
        return $this->hasOne(Marksheet::class, 'exam_result_id', 'exam_result_id');
    }

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
}
