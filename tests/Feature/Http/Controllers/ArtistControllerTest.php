<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Artist;
use App\Models\Role;
use App\Models\Song;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArtistControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_anyone_can_view_artists_index(): void
    {
        $response = $this->get(route('artists.index'));
        $response->assertOk();
    }

    public function test_artists_index_displays_all_artists(): void
    {
        Artist::factory()->count(3)->create();

        $response = $this->get(route('artists.index'));

        $response->assertInertia(fn ($page) => $page
            ->component('Artists/Index')
            ->has('artists.data', 3)
        );
    }

    public function test_only_admin_can_access_create_page(): void
    {
        $user = User::factory()->create(['role_id' => Role::USER]);
        $admin = User::factory()->create(['role_id' => Role::ADMIN]);

        $this->actingAs($user)
            ->get(route('artists.create'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('artists.create'))
            ->assertOk();
    }

    public function test_admin_can_store_artist(): void
    {
        $admin = User::factory()->create(['role_id' => Role::ADMIN]);

        $response = $this->actingAs($admin)
            ->post(route('artists.store'), [
                'name' => 'New Artist',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('artists', ['name' => 'New Artist']);
    }

    public function test_artist_show_page_includes_songs(): void
    {
        $artist = Artist::factory()->create();
        Song::factory()->count(3)->create(['artist_id' => $artist->id]);

        $response = $this->get(route('artists.show', $artist));

        $response->assertInertia(fn ($page) => $page
            ->component('Artists/Show')
            ->has('artist.songs', 3)
        );
    }

    public function test_artist_show_page_includes_favorite_status(): void
    {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        $user->favoriteArtists()->attach($artist);

        $response = $this->actingAs($user)
            ->get(route('artists.show', $artist));

        $response->assertInertia(fn ($page) => $page
            ->where('is_favorited', true)
        );
    }

    public function test_artist_creation_requires_valid_data(): void
    {
        $admin = User::factory()->create(['role_id' => Role::ADMIN]);

        $response = $this->actingAs($admin)
            ->post(route('artists.store'), [
                'name' => '',
            ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_guests_cannot_favorite_artists(): void
    {
        $artist = Artist::factory()->create();

        $response = $this->post(route('artists.favorite', $artist));

        $response->assertRedirect(route('login'));
    }

    public function test_users_can_favorite_artists(): void
    {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('artists.favorite', $artist));

        $response->assertRedirect();
        $this->assertDatabaseHas('favorite_artists', [
            'user_id' => $user->id,
            'artist_id' => $artist->id,
        ]);
    }

    public function test_users_can_unfavorite_artists(): void
    {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        $user->favoriteArtists()->attach($artist);

        $response = $this->actingAs($user)
            ->delete(route('artists.favorite', $artist));

        $response->assertRedirect();
        $this->assertDatabaseMissing('favorite_artists', [
            'user_id' => $user->id,
            'artist_id' => $artist->id,
        ]);
    }

    public function test_regular_users_cannot_store_artist(): void
    {
        $user = User::factory()->create(['role_id' => Role::USER]);

        $response = $this->actingAs($user)
            ->post(route('artists.store'), [
                'name' => 'New Artist',
            ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('artists', ['name' => 'New Artist']);
    }
}
