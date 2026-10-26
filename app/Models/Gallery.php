<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'category', 'image_path', 'sort_order', 'status'];

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the full URL of the gallery image.
     */
    public function getImageUrlAttribute(): string
    {
        if (empty($this->image_path)) {
            return 'https://placehold.co/600x400?text=Gallery+Photo';
        }

        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }

        if (str_starts_with($this->image_path, 'storage/')) {
            return asset($this->image_path);
        }

        if (file_exists(public_path($this->image_path))) {
            return asset($this->image_path);
        }

        return asset('storage/' . ltrim($this->image_path, '/'));
    }
}
