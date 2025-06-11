<?php

namespace Tests\Unit\Models;

use App\Models\Artist;
use App\Models\Song;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SongTest extends TestCase
{
    use RefreshDatabase;

    public function test_song_belongs_to_artist(): void
    {
        $artist = Artist::factory()->create();
        $song = Song::factory()->create(['artist_id' => $artist->id]);

        $this->assertTrue($song->artist->is($artist));
    }

    public function test_song_name_is_trimmed(): void
    {
        $artist = Artist::factory()->create();
        $song = Song::factory()->create([
            'artist_id' => $artist->id,
            'name' => '    Test Song       ',
        ]);
        $this->assertEquals('Test Song', $song->name);
    }

    public function test_song_generates_slug(): void
    {
        $artist = Artist::factory()->create();
        $song = Song::factory()->create([
            'artist_id' => $artist->id,
            'name' => 'Test Song',
        ]);
        $this->assertEquals('test-song', $song->slug);
    }
}
