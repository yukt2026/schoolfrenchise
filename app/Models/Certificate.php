<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_result_id',
        'student_id',
        'centre_id',
        'course_id',
        'certificate_no',
        'issue_date',
        'security_hash',
        'qr_code_url',
        'status',
    ];

    protected $casts = [
        'issue_date' => 'date',
    ];

    public function result()
    {
        return $this->belongsTo(ExamResult::class, 'exam_result_id');
    }

    public function examResult()
    {
        return $this->belongsTo(ExamResult::class, 'exam_result_id');
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
