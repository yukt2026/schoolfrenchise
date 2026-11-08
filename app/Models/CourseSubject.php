<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'subject_code',
        'subject_name',
        'theory_max',
        'practical_max',
        'viva_max',
        'total_max',
        'min_pass_marks',
        'sort_order',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function examResults()
    {
        return $this->hasMany(ExamResultSubject::class, 'course_subject_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'course_subject_id');
    }

    public function studyMaterials()
    {
        return $this->hasMany(StudyMaterial::class, 'course_subject_id');
    }

    public function getTheoryMarksAttribute()
    {
        return $this->theory_max;
    }

    public function getPracticalMarksAttribute()
    {
        return $this->practical_max;
    }

    public function getTotalMarksAttribute()
    {
        return $this->total_max;
    }
}
