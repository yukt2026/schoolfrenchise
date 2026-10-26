<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'image_path',
        'mobile_image_path',
        'link_url',
        'link_target',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the full URL for the desktop/primary banner image.
     */
    public function getImageUrlAttribute(): string
    {
        if (empty($this->image_path)) {
            return asset('images/banners/banner1.jpg');
        }

        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }

        if (str_starts_with($this->image_path, 'images/')) {
            return asset($this->image_path);
        }

        if (file_exists(public_path($this->image_path))) {
            return asset($this->image_path);
        }

        return asset('storage/' . ltrim($this->image_path, '/'));
    }

    /**
     * Get the full URL for the optional mobile banner image.
     */
    public function getMobileImageUrlAttribute(): string
    {
        if (empty($this->mobile_image_path)) {
            return $this->image_url;
        }

        if (str_starts_with($this->mobile_image_path, 'http://') || str_starts_with($this->mobile_image_path, 'https://')) {
            return $this->mobile_image_path;
        }

        if (str_starts_with($this->mobile_image_path, 'images/')) {
            return asset($this->mobile_image_path);
        }

        if (file_exists(public_path($this->mobile_image_path))) {
            return asset($this->mobile_image_path);
        }

        return asset('storage/' . ltrim($this->mobile_image_path, '/'));
    }

    /**
     * Scope query to only active banners.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope query ordered by display priority.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('id', 'desc');
    }
}
