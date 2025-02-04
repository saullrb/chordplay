<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Song;
use App\Models\Artist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_favorite_songs()
    {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        $song = Song::factory()->create([
            'artist_id' => $artist->id,
            'name' => 'Favorite Song'
        ]);
        $user->addFavoriteSong($song);

        $response = $this->actingAs($user)
            ->get('/dashboard');

        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('favorite_songs', 1)
            ->where('favorite_songs.0.name', 'Favorite Song')
        );
    }

    public function test_dashboard_shows_favorite_artists()
    {
        $user = User::factory()->create();
        $artist = Artist::factory()->create([
            'name' => 'Favorite Artist'
        ]);
        $user->addFavoriteArtist($artist);

        $response = $this->actingAs($user)
            ->get('/dashboard');

        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('favorite_artists', 1)
            ->where('favorite_artists.0.name', 'Favorite Artist')
        );
    }

    public function test_dashboard_shows_empty_state_for_new_users()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/dashboard');

        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('favorite_songs', 0)
            ->has('favorite_artists', 0)
        );
    }
} 