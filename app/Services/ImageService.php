<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;

class ImageService
{
    // Max dimensions for teacher profile photos
    const PHOTO_MAX_WIDTH  = 400;
    const PHOTO_MAX_HEIGHT = 400;
    const PHOTO_QUALITY    = 70;

    /**
     * Compress and store a teacher profile photo.
     * Returns the stored path (relative to storage/app/public).
     */
    public static function compressProfilePhoto(
        UploadedFile $file,
        string $folder = 'teacher-photos'
    ): string {
        $manager = new ImageManager(new Driver());
        $image   = $manager->decode($file->getRealPath());

        // Resize keeping aspect ratio — never upscale
        $image->scaleDown(self::PHOTO_MAX_WIDTH, self::PHOTO_MAX_HEIGHT);

        // Generate unique filename
        $filename = $folder . '/' . Str::uuid() . '.jpg';

        // Encode as JPEG at quality 70 and save to public disk
        $encoded = $image->encode(new JpegEncoder(quality: self::PHOTO_QUALITY));
        Storage::disk('public')->put($filename, $encoded);

        return $filename;
    }

    /**
     * Delete an old photo from storage if it exists.
     */
    public static function deletePhoto(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Encode a project milestone photo.
     * Resizes to 800x600 JPEG at 75% quality.
     * Stored in storage/app/public/project-photos/
     */
    public function encodeProjectPhoto(UploadedFile $file): string
    {
        $manager = new ImageManager(new Driver());
        $image   = $manager->decode($file->getRealPath());

        // Cover crop to 800x600
        $image->cover(800, 600);

        // Ensure directory exists
        $dir = storage_path('app/public/project-photos');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = 'project-photos/' . Str::uuid() . '.jpg';
        $fullPath = storage_path('app/public/' . $filename);

        // Encode as JPEG at 75% quality and save
        $encoded = $image->encode(new JpegEncoder(quality: 75));
        file_put_contents($fullPath, $encoded);

        return $filename;
    }
}