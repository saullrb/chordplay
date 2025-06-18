<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Artist;
use App\Models\Song;
use App\Models\SongSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $regular_user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $this->regular_user = User::factory()->create();
    }

    public function test_guests_cannot_access_dashboard(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_redirects_the_user_if_trying_to_use_page_parameter(): void
    {
        $this->actingAs($this->regular_user)
            ->get(route('dashboard', ['page' => 1]))
            ->assertRedirect(route('dashboard'));
    }

    public function test_dashboard_shows_empty_state_for_new_users(): void
    {
        $response = $this->actingAs($this->regular_user)
            ->get(route('dashboard'));

        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('favorite_songs.data', 0)
            ->has('favorite_artists.data', 0)
            ->has('submissions', 0)
        );
    }

    public function test_dashboard_shows_favorite_songs(): void
    {
        $artist = Artist::factory()->create();
        $song = Song::factory()->create([
            'artist_id' => $artist->id,
            'name' => 'Favorite Song',
        ]);
        $this->regular_user->addFavoriteSong($song);

        $response = $this->actingAs($this->regular_user)
            ->get(route('dashboard'));

        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('favorite_songs.data', 1)
            ->where('favorite_songs.data.0.name', 'Favorite Song')
        );
    }

    public function test_dashboard_shows_favorite_artists(): void
    {
        $artist = Artist::factory()->create(['name' => 'Favorite Artist']);
        $this->regular_user->favoriteArtists()->attach($artist);

        $this->actingAs($this->regular_user)
            ->get(route('dashboard'))
            ->assertInertia(fn ($page) => $page
                ->component('Dashboard')
                ->has('favorite_artists.data', 1)
                ->where('favorite_artists.data.0.name', 'Favorite Artist')
            );
    }

    public function test_dashboard_shows_latest_submissions_for_admin(): void
    {
        $artist = Artist::factory()->create();
        SongSubmission::factory()->count(15)->create([
            'artist_id' => $artist->id,
            'user_id' => $this->regular_user->id,
        ]);

        $this->actingAs($this->admin)
            ->get(route('dashboard'))
            ->assertInertia(fn ($page) => $page
                ->component('Dashboard')
                ->has('submissions', 10)
                ->where('submissions.0.artist.name', $artist->name)
                ->where('submissions.0.user.name', $this->regular_user->name)
            );
    }

    public function test_dashboard_shows_only_own_latest_submissions_for_regular_user(): void
    {
        $artist = Artist::factory()->create();
        SongSubmission::factory()->count(5)->create([
            'artist_id' => $artist->id,
            'user_id' => $this->regular_user->id,
        ]);
        SongSubmission::factory()->count(5)->create([
            'artist_id' => $artist->id,
            'user_id' => $this->admin->id,
        ]);

        $this->actingAs($this->regular_user)
            ->get(route('dashboard'))
            ->assertInertia(fn ($page) => $page
                ->component('Dashboard')
                ->has('submissions', 5)
                ->where('submissions.0.artist.name', $artist->name)
                ->where('submissions.0.user.name', $this->regular_user->name)
            );
    }
}
