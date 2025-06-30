<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Artist;
use App\Models\User;
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

    public function test_search_by_name_scope(): void
    {
        Artist::factory()->create(['name' => 'Test Artist']);
        Artist::factory()->create(['name' => 'Other Artist']);

        $artists = Artist::searchByName('test')->get();

        $this->assertCount(1, $artists);
        $this->assertEquals('Test Artist', $artists->first()->name);
    }

    public function test_search_by_name_case_insensitive(): void
    {
        Artist::factory()->create(['name' => 'TEST Artist']);

        $artists = Artist::searchByName('test')->get();

        $this->assertCount(1, $artists);
        $this->assertEquals('TEST Artist', $artists->first()->name);
    }

    public function test_with_favorite_status_authenticated(): void
    {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        $user->favoriteArtists()->attach($artist);

        $artist = Artist::withFavoriteStatus($user->id)->first();

        $this->assertTrue($artist->is_favorited);
    }

    public function test_with_favorite_status_unauthenticated(): void
    {
        $artist = Artist::factory()->create();

        $artist = Artist::withFavoriteStatus(null)->first();

        $this->assertFalse($artist->is_favorited);
    }

    public function test_order_by_favorites_and_views(): void
    {
        $user = User::factory()->create();
        $artist1 = Artist::factory()->create(['name' => 'Artist1', 'views' => 10]);
        Artist::factory()->create(['name' => 'Artist2', 'views' => 20]);
        $user->favoriteArtists()->attach($artist1);

        $artists = Artist::withFavoriteStatus($user->id)
            ->orderByFavoritesAndViews()
            ->get();

        $this->assertEquals(['Artist1', 'Artist2'], $artists->pluck('name')->toArray());
    }
}
