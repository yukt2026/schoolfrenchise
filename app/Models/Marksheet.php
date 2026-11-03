<?php

namespace App\Models;

use App\Services\QrCodeService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Marksheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_result_id',
        'student_id',
        'centre_id',
        'course_id',
        'marksheet_no',
        'serial_no',
        'verification_token',
        'issue_date',
        'security_hash',
        'is_issued',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'is_issued' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($marksheet) {
            if (empty($marksheet->verification_token)) {
                $marksheet->verification_token = 'MS-' . strtoupper(Str::random(10)) . '-' . sprintf('%04d', random_int(100, 9999));
            }
            if (empty($marksheet->serial_no)) {
                $year = $marksheet->issue_date ? date('y', strtotime($marksheet->issue_date)) : date('y');
                $marksheet->serial_no = sprintf('%s%06d', $year, random_int(100000, 999999));
            }
        });
    }

    /**
     * Ensure a unique verification token exists.
     */
    public function ensureVerificationToken(): string
    {
        if (empty($this->verification_token)) {
            $this->verification_token = 'MS-' . strtoupper(Str::random(10)) . '-' . sprintf('%04d', $this->id ?: random_int(100, 9999));
            $this->saveQuietly();
        }

        return $this->verification_token;
    }

    /**
     * Get direct online verification URL for QR code.
     */
    public function getVerificationUrlAttribute(): string
    {
        $token = $this->verification_token ?: $this->marksheet_no;
        return route('verify.marksheet.token', ['token' => $token]);
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

    public function certificate()
    {
        return $this->hasOne(Certificate::class, 'exam_result_id', 'exam_result_id');
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
