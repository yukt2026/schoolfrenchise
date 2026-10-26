<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

class FileUploadService
{
    protected const BANNED_EXTENSIONS = [
        'php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'phar',
        'exe', 'bat', 'cmd', 'sh', 'bash', 'bin',
        'js', 'jsp', 'asp', 'aspx', 'cgi', 'pl', 'py',
        'html', 'htm', 'xhtml', 'htaccess', 'svg',
    ];

    /**
     * Upload an image (photo, signature, logo)
     */
    public static function uploadImage(UploadedFile $file, string $folder = 'uploads/images', int $maxKb = 2048): string
    {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        return self::processUpload($file, $folder, $allowedMimes, $maxKb);
    }

    /**
     * Upload a document (identity proof, marksheet, affiliation certificate, study material)
     */
    public static function uploadDocument(UploadedFile $file, string $folder = 'uploads/docs', int $maxKb = 5120): string
    {
        $allowedMimes = ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'];
        return self::processUpload($file, $folder, $allowedMimes, $maxKb);
    }

    /**
     * Upload a study material (PDF or study documents)
     */
    public static function uploadMaterial(UploadedFile $file, string $folder = 'uploads/materials', int $maxKb = 20480): string
    {
        $allowedMimes = [
            'application/pdf',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/zip',
        ];
        return self::processUpload($file, $folder, $allowedMimes, $maxKb);
    }

    /**
     * Core secure processing logic
     */
    protected static function processUpload(UploadedFile $file, string $folder, array $allowedMimes, int $maxKb): string
    {
        if (!$file->isValid()) {
            throw new InvalidArgumentException('Uploaded file is corrupt or invalid.');
        }

        $extension = strtolower($file->getClientOriginalExtension());
        if (in_array($extension, self::BANNED_EXTENSIONS, true)) {
            throw new InvalidArgumentException('Upload rejected: Dangerous file extension detected.');
        }

        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, $allowedMimes, true)) {
            throw new InvalidArgumentException("Invalid file type: [{$mimeType}]. Allowed: " . implode(', ', $allowedMimes));
        }

        if ($file->getSize() > ($maxKb * 1024)) {
            throw new InvalidArgumentException("File size exceeds maximum allowed limit of {$maxKb} KB.");
        }

        // Generate cryptographically unguessable filename
        $safeFileName = Str::random(40) . '.' . $extension;

        // Store into public disk
        $path = $file->storeAs($folder, $safeFileName, 'public');

        // Automatic mirror to public directory for hosting environments where symlinks are disabled or broken
        try {
            $publicFolder = public_path($folder);
            if (!is_dir($publicFolder)) {
                @mkdir($publicFolder, 0755, true);
            }
            $storedSource = storage_path('app/public/' . $path);
            if (file_exists($storedSource)) {
                @copy($storedSource, $publicFolder . DIRECTORY_SEPARATOR . $safeFileName);
            }
        } catch (\Throwable $e) {
            // Non-blocking fallback
        }

        return $path;
    }
}
