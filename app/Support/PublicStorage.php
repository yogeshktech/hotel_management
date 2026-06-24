<?php

namespace App\Support;

class PublicStorage
{
    public static function url(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        $path = ltrim(str_replace('\\', '/', $path), '/');

        if (self::usesStaticStorage()) {
            return asset('storage/' . $path);
        }

        return url('media/' . $path);
    }

    /**
     * Local dev (artisan serve) cannot reliably serve symlinked public/storage — returns 403 on Windows.
     * Use /media route locally; production with nginx/apache + storage:link can use /storage directly.
     */
    private static function usesStaticStorage(): bool
    {
        if (app()->environment('local')) {
            return false;
        }

        return is_dir(public_path('storage'));
    }
}
