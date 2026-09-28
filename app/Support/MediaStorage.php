<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class MediaStorage
{
    public static function diskName(): string
    {
        return (string) config('filesystems.media_disk', 'public');
    }

    public static function isLocal(): bool
    {
        return config('filesystems.disks.'.self::diskName().'.driver') === 'local';
    }

    public static function store(UploadedFile $file, string $directory): string
    {
        $path = Storage::disk(self::diskName())->putFile($directory, $file, [
            'CacheControl' => 'public, max-age=31536000, immutable',
        ]);

        if (! is_string($path) || $path === '') {
            throw new RuntimeException('The media file could not be stored.');
        }

        return $path;
    }

    public static function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (self::isExternalUrl($path)) {
            return $path;
        }

        $key = self::key($path);

        if (! $key) {
            return null;
        }

        $baseUrl = rtrim((string) config('filesystems.disks.'.self::diskName().'.url'), '/');

        return $baseUrl !== ''
            ? $baseUrl.'/'.ltrim($key, '/')
            : Storage::disk(self::diskName())->url($key);
    }

    public static function key(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $path = trim($path);

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $mediaUrl = rtrim((string) config('filesystems.disks.'.self::diskName().'.url'), '/');
            if ($mediaUrl === '' || ! str_starts_with($path, $mediaUrl.'/')) {
                return null;
            }

            $path = substr($path, strlen($mediaUrl) + 1);
        }

        $path = preg_replace('#^/?storage/#', '', $path);
        $path = ltrim((string) $path, '/');

        if ($path === '' || str_contains($path, '..')) {
            return null;
        }

        return $path;
    }

    public static function delete(?string $path): void
    {
        $key = self::key($path);

        if ($key) {
            Storage::disk(self::diskName())->delete($key);
        }
    }

    private static function isExternalUrl(string $path): bool
    {
        if (! filter_var($path, FILTER_VALIDATE_URL)) {
            return false;
        }

        $mediaUrl = rtrim((string) config('filesystems.disks.'.self::diskName().'.url'), '/');

        return $mediaUrl === '' || ! str_starts_with($path, $mediaUrl.'/');
    }
}
