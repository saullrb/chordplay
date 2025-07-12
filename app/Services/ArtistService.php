<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Artist;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ArtistService
{
    public function store(array $data): Artist
    {
        $profileImageUrl = null;

        if (isset($data['profile_image'])) {
            try {
                $path = $data['profile_image']->store('artists');
                $profileImageUrl = Storage::url($path);
            } catch (\Throwable $e) {
                Log::error('Failed to upload profile image artist', ['name' => $data['name'], 'error' => $e->getMessage()]);

                throw new Exception('Failed to upload image to Cloudinary: '.$e->getMessage(), 500, $e);
            }
        }

        return Artist::create([
            'name' => $data['name'],
            'profile_image_url' => $profileImageUrl,
        ]);
    }
}
