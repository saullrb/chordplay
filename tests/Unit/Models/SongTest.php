<?php

namespace Tests\Unit\Models;

use App\Models\Song;
use App\Models\Artist;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SongTest extends TestCase
{
    use RefreshDatabase;

    public function test_song_belongs_to_artist()
    {
        $artist = Artist::factory()->create();
        $song = Song::factory()->create(['artist_id' => $artist->id]);
        
        $this->assertTrue($song->artist->is($artist));
    }

    public function test_song_can_be_favorited()
    {
        $artist = Artist::factory()->create();
        $song = Song::factory()->create(['artist_id' => $artist->id]);
        $this->assertDatabaseCount('favorite_songs', 0);
    }

    public function test_song_generates_slug()
    {
        $artist = Artist::factory()->create();
        $song = Song::factory()->create([
            'artist_id' => $artist->id,
            'name' => 'Test Song'
        ]);
        $this->assertEquals('test-song', $song->slug);
    }
} 