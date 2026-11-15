<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageStorage
{
    private const DISK = 'public';

    /**
     * Save an uploaded image in the given folder and return its file name.
     */
    public function store(UploadedFile $image, string $folder): string
    {
        $name = Str::uuid() . '.' . $image->extension();

        $image->storeAs($folder, $name, self::DISK);

        return $name;
    }


    /**
     * Delete an image from the given folder, if there is one.
     */
    public function delete(?string $name, string $folder): void
    {
        if ($name && Storage::disk(self::DISK)->exists("{$folder}/{$name}")) {
            Storage::disk(self::DISK)->delete("{$folder}/{$name}");
        }
    }
}