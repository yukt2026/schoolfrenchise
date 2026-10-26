<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'course_code',
        'course_name',
        'slug',
        'duration',
        'eligibility',
        'default_course_fee',
        'registration_fee',
        'exam_fee',
        'certificate_fee',
        'syllabus',
        'thumbnail',
        'is_popular',
        'status',
    ];

    protected $casts = [
        'is_popular' => 'boolean',
        'status' => 'boolean',
        'default_course_fee' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }

    public function subjects()
    {
        return $this->hasMany(CourseSubject::class)->orderBy('sort_order');
    }

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }

    public function feeStructures()
    {
        return $this->hasMany(FeeStructure::class);
    }

    public function admissionApplications()
    {
        return $this->hasMany(AdmissionApplication::class);
    }

    public function studyMaterials()
    {
        return $this->hasMany(StudyMaterial::class);
    }

    public function getTitleAttribute()
    {
        return $this->course_name;
    }

    public function getTotalFeeAttribute()
    {
        return (float) ($this->default_course_fee + $this->registration_fee + $this->exam_fee + $this->certificate_fee);
    }

    public function getDurationMonthsAttribute()
    {
        if (preg_match('/(\d+)/', (string) $this->duration, $m)) {
            return (int) $m[1];
        }
        return 6;
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function getThumbnailUrlAttribute(): string
    {
        if (empty($this->thumbnail)) {
            $slugCode = strtolower(\Illuminate\Support\Str::slug($this->course_code ?? ''));
            $svgPath = 'images/courses/' . $slugCode . '.svg';
            if (!empty($slugCode) && file_exists(public_path($svgPath))) {
                return asset($svgPath);
            }
            return asset('images/courses/default-course.svg');
        }

        if (str_starts_with($this->thumbnail, 'http://') || str_starts_with($this->thumbnail, 'https://')) {
            return $this->thumbnail;
        }

        if (file_exists(public_path($this->thumbnail))) {
            return asset($this->thumbnail);
        }

        if (file_exists(public_path('storage/' . ltrim($this->thumbnail, '/')))) {
            return asset('storage/' . ltrim($this->thumbnail, '/'));
        }

        if (file_exists(storage_path('app/public/' . ltrim($this->thumbnail, '/')))) {
            return asset('storage/' . ltrim($this->thumbnail, '/'));
        }

        return asset('storage/' . ltrim($this->thumbnail, '/'));
    }
}
