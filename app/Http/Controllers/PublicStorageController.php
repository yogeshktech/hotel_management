<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PublicStorageController extends Controller
{
    public function __invoke(string $path): BinaryFileResponse
    {
        $path = str_replace(['..', '\\'], ['', '/'], $path);
        $path = ltrim($path, '/');

        abort_unless(Storage::disk('public')->exists($path), 404);

        return response()->file(Storage::disk('public')->path($path));
    }
}
