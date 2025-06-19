<?php

namespace Tests\Unit\Models;

use App\Models\Song;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SongTest extends TestCase
{
    use RefreshDatabase;

    public function test_song_name_is_trimmed(): void
    {
        $song = Song::factory()->create([
            'name' => '    Test Song       ',
        ]);
        $this->assertEquals('Test Song', $song->name);
    }

    public function test_song_generates_slug(): void
    {
        $song = Song::factory()->create([
            'name' => 'Test Song',
        ]);
        $this->assertEquals('test-song', $song->slug);
    }

    public function test_search_by_name_scope(): void
    {
        Song::factory()->create(['name' => 'Test Song']);
        Song::factory()->create(['name' => 'Other Song']);

        $songs = Song::searchByName('test')->get();

        $this->assertCount(1, $songs);
        $this->assertEquals('Test Song', $songs->first()->name);
    }

    public function test_search_by_name_case_insensitive(): void
    {
        Song::factory()->create(['name' => 'TEST Song']);

        $songs = Song::searchByName('test')->get();

        $this->assertCount(1, $songs);
        $this->assertEquals('TEST Song', $songs->first()->name);
    }

    public function test_with_favorite_status_authenticated(): void
    {
        $user = User::factory()->create();
        $song = Song::factory()->create();
        $user->favoriteSongs()->attach($song);

        $song = Song::withFavoriteStatus($user->id)->first();

        $this->assertTrue($song->is_favorited);
    }

    public function test_with_favorite_status_unauthenticated(): void
    {
        $song = Song::factory()->create();

        $song = Song::withFavoriteStatus(null)->first();

        $this->assertFalse($song->is_favorited);
    }

    public function test_order_by_favorites_and_views(): void
    {
        $user = User::factory()->create();
        $song1 = Song::factory()->create(['name' => 'Song1', 'views' => 10]);
        Song::factory()->create(['name' => 'Song2', 'views' => 20]);
        $user->favoriteSongs()->attach($song1);

        $songs = Song::withFavoriteStatus($user->id)
            ->orderByFavoritesAndViews()
            ->get();

        $this->assertEquals(['Song1', 'Song2'], $songs->pluck('name')->toArray());
    }
}
