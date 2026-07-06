<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;

class AdminMediaService
{
    protected $cloudinary;

    public function __construct(CloudinaryService $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    public function uploadImage($file, $folder, $localFolder = null)
    {
        return $this->upload($file, $folder, $localFolder ?: $folder, 'image');
    }

    public function uploadVideo($file, $folder, $localFolder = null)
    {
        return $this->upload($file, $folder, $localFolder ?: $folder, 'video');
    }

    public function deleteMedia($value, $localFolder, $publicId = null, $resourceType = 'image')
    {
        $value = trim((string) $value);
        $publicId = trim((string) $publicId);

        if ($publicId !== '') {
            $this->cloudinary->deleteFile($publicId, $resourceType);
            return;
        }

        if (preg_match('#^https?://#i', $value)) {
            $derivedPublicId = $this->cloudinary->getPublicIdFromUrl($value);
            if ($derivedPublicId) {
                $this->cloudinary->deleteFile($derivedPublicId, $resourceType);
            }
            return;
        }

        if ($value === '') {
            return;
        }

        $path = public_path('upload/' . trim($localFolder, '/') . '/' . ltrim(str_replace('\\', '/', $value), '/'));
        if (File::exists($path)) {
            File::delete($path);
        }
    }

    protected function upload($file, $folder, $localFolder, $resourceType)
    {
        $this->validateUpload($file, $resourceType, $folder);

        if ($this->cloudinary->isConfigured()) {
            $result = $resourceType === 'video'
                ? $this->cloudinary->uploadVideo($file, $folder)
                : $this->cloudinary->uploadImage($file, $folder);

            if (!empty($result['url'])) {
                Log::info('Cloudinary media upload saved.', [
                    'folder' => $folder,
                    'resource_type' => $resourceType,
                    'url' => $result['url'],
                    'public_id' => $result['public_id'],
                ]);

                return [
                    'path' => $result['url'],
                    'public_id' => $result['public_id'],
                    'cloudinary' => true,
                ];
            }

            Log::error('Cloudinary upload returned no secure URL.', [
                'folder' => $folder,
                'resource_type' => $resourceType,
            ]);
            throw new \RuntimeException('Cloudinary upload failed. No secure URL was returned.');
        }

        throw new \RuntimeException('Cloudinary is not configured or the SDK is not installed. New uploads are not saved locally.');
    }

    protected function validateUpload($file, $resourceType, $folder = '')
    {
        if (!$file instanceof UploadedFile && !$file instanceof SymfonyUploadedFile) {
            return;
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $mime = strtolower((string) $file->getMimeType());

        if ($resourceType === 'video') {
            $allowedExtensions = ['mp4', 'mov', 'avi', 'wmv', 'webm'];
            $allowedMimes = ['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/x-ms-wmv', 'video/webm', 'application/octet-stream'];
            $maxBytes = 200 * 1024 * 1024;
            $label = 'MP4, MOV, AVI, WMV, or WebM video up to 200 MB';
        } else {
            $allowedExtensions = ['jpeg', 'jpg', 'png', 'gif', 'webp', 'bmp'];
            $allowedMimes = ['image/jpeg', 'image/pjpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp', 'image/x-ms-bmp'];
            $folderName = trim((string) $folder, '/');
            $isProductUpload = strpos($folderName, 'products/') === 0;
            $isTestimonialUpload = in_array($folderName, ['testimonials', 'testimonial-logos'], true);
            $maxMb = $isProductUpload ? 120 : ($isTestimonialUpload ? 50 : 10);
            $maxBytes = $maxMb * 1024 * 1024;
            $label = 'JPEG, PNG, GIF, or WebP image up to ' . $maxMb . ' MB';
        }

        if (!$file->isValid() || $file->getSize() > $maxBytes || (!in_array($extension, $allowedExtensions, true) && !in_array($mime, $allowedMimes, true))) {
            throw new \InvalidArgumentException('Please upload a valid ' . $label . '.');
        }
    }

    protected function uniqueFilename($filename)
    {
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $safeName = trim(preg_replace('/[^A-Za-z0-9_-]+/', '-', $name), '-');

        return time() . '_' . mt_rand(100000, 999999) . '_' . ($safeName ?: 'media') . ($extension ? '.' . $extension : '');
    }
}
