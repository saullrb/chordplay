<?php

namespace Tests\Unit\Models;

use App\Models\Artist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArtistTest extends TestCase
{
    use RefreshDatabase;

    public function test_artist_name_is_trimmed(): void
    {
        $artist = Artist::factory()->create(['name' => '   Test Artist    ']);
        $this->assertEquals('Test Artist', $artist->name);
    }

    public function test_artist_generates_slug(): void
    {
        $artist = Artist::factory()->create(['name' => 'Test Artist']);
        $this->assertEquals('test-artist', $artist->slug);
    }
}
