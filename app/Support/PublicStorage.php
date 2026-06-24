<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

class PublicStorage
{
    public static function url(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if (File::exists(public_path('storage'))) {
            return asset('storage/' . $path);
        }

        return route('storage.public', ['path' => $path]);
    }
}
