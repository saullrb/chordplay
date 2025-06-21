<?php

namespace App\Services;

use App\Models\Artist;

class ArtistService
{
    public function store(array $data): Artist
    {
        return Artist::create($data);
    }
}
