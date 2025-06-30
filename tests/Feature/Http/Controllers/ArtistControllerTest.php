<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Artist;
use App\Models\User;
use App\Services\ArtistService;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArtistControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $this->regularUser = User::factory()->create();
    }

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
        $this->actingAs($this->regularUser)
            ->get(route('artists.create'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(route('artists.create'))
            ->assertOk();
    }

    public function test_admin_can_store_artist(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('artists.store'), [
                'name' => 'New Artist',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('artists', ['name' => 'New Artist']);
    }

    public function test_artist_show_page_includes_songs(): void
    {
        $artist = Artist::factory()->withSongs(3)->create();

        $response = $this->get(route('artists.show', $artist));

        $response->assertInertia(fn ($page) => $page
            ->component('Artists/Show')
            ->has('songs.data', 3)
        );
    }

    public function test_artist_show_page_includes_favorite_status(): void
    {
        $artist = Artist::factory()->create();
        $this->regularUser->favoriteArtists()->attach($artist);

        $response = $this->actingAs($this->regularUser)
            ->get(route('artists.show', $artist));

        $response->assertInertia(fn ($page) => $page
            ->where('isFavorited', true)
        );
    }

    public function test_regular_users_cannot_store_artist(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->post(route('artists.store'), [
                'name' => 'New Artist',
            ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('artists', ['name' => 'New Artist']);
    }

    public function test_artist_creation_requires_valid_data(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('artists.store'), [
                'name' => '',
            ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_admin_can_store_new_artist(): void
    {
        $this->actingAs($this->admin)->post(route('artists.store'), [
            'name' => 'New Artist',
        ])
            ->assertRedirect()
            ->assertSessionHas('flash.message')
            ->assertSessionHas('flash.type', 'success');

        $this->assertDatabaseHas('artists', ['name' => 'New Artist']);
    }

    public function test_handles_exception_during_artist_creation(): void
    {
        $this->mock(ArtistService::class, function ($mock): void {
            $mock->shouldReceive('store')
                ->once()
                ->andThrows(new \Exception('Database error'));
        });

        $data = ['name' => 'New Artists'];

        $this->actingAs($this->admin)->post(route('artists.store'), $data)
            ->assertRedirect()
            ->assertSessionHas('flash.message')
            ->assertSessionHas('flash.type', 'error');
    }

    public function test_guests_cannot_favorite_artists(): void
    {
        $artist = Artist::factory()->create();

        $response = $this->post(route('artists.favorite', $artist));

        $response->assertRedirect(route('login'));
    }

    public function test_users_can_favorite_artists(): void
    {
        $artist = Artist::factory()->create();

        $response = $this->actingAs($this->regularUser)
            ->post(route('artists.favorite', $artist));

        $response->assertRedirect();
        $this->assertDatabaseHas('favorite_artists', [
            'user_id' => $this->regularUser->id,
            'artist_id' => $artist->id,
        ]);
    }

    public function test_favorite_handles_exceptions(): void
    {
        $artist = Artist::factory()->create();

        $this->mock(UserService::class, function ($mock): void {
            $mock->shouldReceive('favoriteArtist')
                ->once()
                ->andThrows(new \Exception('Database error'));
        });

        $this->actingAs($this->regularUser);

        $this->post(route('artists.favorite', $artist))
            ->assertRedirect()
            ->assertSessionHas('flash.message')
            ->assertSessionHas('flash.type', 'error');
    }

    public function test_unfavorite_handles_exceptions(): void
    {
        $artist = Artist::factory()->create();

        $this->mock(UserService::class, function ($mock): void {
            $mock->shouldReceive('unfavoriteArtist')
                ->once()
                ->andThrows(new \Exception('Database error'));
        });

        $this->actingAs($this->regularUser);

        $this->delete(route('artists.favorite', $artist))
            ->assertRedirect()
            ->assertSessionHas('flash.message')
            ->assertSessionHas('flash.type', 'error');
    }

    public function test_users_can_unfavorite_artists(): void
    {
        $artist = Artist::factory()->create();
        $this->regularUser->favoriteArtists()->attach($artist);

        $response = $this->actingAs($this->regularUser)
            ->delete(route('artists.favorite', $artist));

        $response->assertRedirect();
        $this->assertDatabaseMissing('favorite_artists', [
            'user_id' => $this->regularUser->id,
            'artist_id' => $artist->id,
        ]);
    }
}
