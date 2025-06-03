<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Artist;
use App\Models\Song;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_favorite_songs()
    {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        $song = Song::factory()->create([
            'artist_id' => $artist->id,
            'name' => 'Favorite Song',
        ]);
        $user->addFavoriteSong($song);

        $response = $this->actingAs($user)
            ->get(route('dashboard'));

        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('favorite_songs', 1)
            ->where('favorite_songs.0.name', 'Favorite Song')
        );
    }

    public function test_dashboard_shows_empty_state_for_new_users()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('dashboard'));

        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('favorite_songs', 0)
            ->has('favorite_artists', 0)
        );
    }

    public function test_guests_cannot_access_dashboard()
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect('/login');
    }

    public function test_dashboard_shows_favorite_artists()
    {
        $user = User::factory()->create();
        $artist = Artist::factory()->create(['name' => 'Favorite Artist']);
        $user->favoriteArtists()->attach($artist);

        $response = $this->actingAs($user)
            ->get(route('dashboard'));

        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('favorite_artists', 1)
            ->where('favorite_artists.0.name', 'Favorite Artist')
        );
    }
}

