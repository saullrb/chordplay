<?php

namespace Tests\Unit\Models;

use App\Models\Artist;
use App\Models\Song;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArtistTest extends TestCase
{
    use RefreshDatabase;

    public function test_artist_has_songs()
    {
        $artist = Artist::factory()->create();
        Song::factory()->count(3)->create(['artist_id' => $artist->id]);
        
        $this->assertCount(3, $artist->songs);
    }

    public function test_artist_can_be_favorited()
    {
        $artist = Artist::factory()->create();
        $this->assertDatabaseCount('favorite_artists', 0);
    }

    public function test_artist_generates_slug()
    {
        $artist = Artist::factory()->create(['name' => 'Test Artist']);
        $this->assertEquals('test-artist', $artist->slug);
    }
} 