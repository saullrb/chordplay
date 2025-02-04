<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use App\Models\Song;
use App\Models\Artist;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SongControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_view_song_create_form()
    {
        $artist = Artist::factory()->create();
        $this->get(route('artists.songs.create', $artist))
            ->assertRedirect('/login');
    }

    public function test_only_staff_can_access_create_page()
    {
        $user = User::factory()->create(['role_id' => Role::USER]);
        $staff = User::factory()->create(['role_id' => Role::STAFF]);
        $artist = Artist::factory()->create();

        $this->actingAs($user)
            ->get(route('artists.songs.create', $artist))
            ->assertForbidden();

        $this->actingAs($staff)
            ->get(route('artists.songs.create', $artist))
            ->assertOk();
    }

    public function test_only_staff_can_store_song()
    {
        $user = User::factory()->create(['role_id' => Role::USER]);
        $staff = User::factory()->create(['role_id' => Role::STAFF]);
        $artist = Artist::factory()->create();

        $this->actingAs($user)
            ->post(route('artists.songs.store', $artist), [
                'name' => 'Test Song',
                'key' => 'C',
                'content' => '[Am] Test'
            ])
            ->assertForbidden();

        $this->actingAs($staff)
            ->post(route('artists.songs.store', $artist), [
                'name' => 'Test Song',
                'key' => 'C',
                'content' => '[Am] Test'
            ])
            ->assertRedirect();
    }

    public function test_anyone_can_view_songs()
    {
        $artist = Artist::factory()->create();
        $song = Song::factory()->create(['artist_id' => $artist->id]);

        $response = $this->get(route('artists.songs.show', [$artist, $song]));
        $response->assertOk();
    }

    public function test_viewing_song_increments_view_count()
    {
        $artist = Artist::factory()->create();
        $song = Song::factory()->create([
            'artist_id' => $artist->id,
            'views' => 0
        ]);

        $this->get(route('artists.songs.show', [$artist, $song]));

        $this->assertEquals(1, $song->fresh()->views);
    }

    public function test_song_creation_requires_valid_data()
    {
        $staff = User::factory()->create(['role_id' => Role::STAFF]);
        $artist = Artist::factory()->create();

        $response = $this->actingAs($staff)
            ->post(route('artists.songs.store', $artist), [
                'name' => '',
                'key' => 'InvalidKey',
                'content' => ''
            ]);

        $response->assertSessionHasErrors(['name', 'key', 'content']);
    }

    public function test_song_content_must_have_valid_chord_format()
    {
        $staff = User::factory()->create(['role_id' => Role::STAFF]);
        $artist = Artist::factory()->create();

        $response = $this->actingAs($staff)
            ->post(route('artists.songs.store', $artist), [
                'name' => 'Test Song',
                'key' => 'C',
                'content' => '[InvalidChord] [NotAChord]\nTest lyrics'
            ]);

        $response->assertSessionHasErrors(['content']);
    }

    public function test_song_show_page_includes_favorite_status()
    {
        $user = User::factory()->create(['role_id' => Role::USER]);
        $artist = Artist::factory()->create();
        $song = Song::factory()->create(['artist_id' => $artist->id]);
        
        $user->addFavoriteSong($song);
        
        $response = $this->actingAs($user)
            ->get(route('artists.songs.show', [$artist, $song]));

        $response->assertInertia(fn ($assert) => $assert
            ->where('is_favorited', true)
        );
    }

    public function test_staff_can_access_edit_page()
    {
        $staff = User::factory()->create(['role_id' => Role::STAFF]);
        $artist = Artist::factory()->create();
        $song = Song::factory()->create(['artist_id' => $artist->id]);

        $response = $this->actingAs($staff)
            ->get(route('artists.songs.edit', [$artist, $song]));

        $response->assertOk();
    }

    public function test_staff_can_update_song()
    {
        $staff = User::factory()->create(['role_id' => Role::STAFF]);
        $artist = Artist::factory()->create();
        $song = Song::factory()->create(['artist_id' => $artist->id]);

        $response = $this->actingAs($staff)
            ->patch(route('artists.songs.update', [$artist, $song]), [
                'name' => 'Updated Song',
                'key' => 'Am',
                'content' => '[Am] Updated lyrics'
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('songs', [
            'id' => $song->id,
            'name' => 'Updated Song'
        ]);
    }

    public function test_regular_users_cannot_access_edit_page()
    {
        $user = User::factory()->create(['role_id' => Role::USER]);
        $artist = Artist::factory()->create();
        $song = Song::factory()->create(['artist_id' => $artist->id]);

        $response = $this->actingAs($user)
            ->get(route('artists.songs.edit', [$artist, $song]));

        $response->assertForbidden();
    }

    public function test_regular_users_cannot_update_song()
    {
        $user = User::factory()->create(['role_id' => Role::USER]);
        $artist = Artist::factory()->create();
        $song = Song::factory()->create(['artist_id' => $artist->id]);

        $response = $this->actingAs($user)
            ->patch(route('artists.songs.update', [$artist, $song]), [
                'name' => 'Updated Song',
                'key' => 'Am',
                'content' => '[Am] Updated lyrics'
            ]);

        $response->assertForbidden();
    }

    public function test_guests_cannot_favorite_songs()
    {
        $artist = Artist::factory()->create();
        $song = Song::factory()->create(['artist_id' => $artist->id]);

        $response = $this->post(route('songs.favorite', [$artist, $song]));

        $response->assertRedirect('/login');
    }

    public function test_users_can_favorite_songs()
    {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        $song = Song::factory()->create(['artist_id' => $artist->id]);

        $response = $this->actingAs($user)
            ->post(route('songs.favorite', [$artist, $song]));

        $response->assertRedirect();
        $this->assertDatabaseHas('favorite_songs', [
            'user_id' => $user->id,
            'song_id' => $song->id
        ]);
    }

    public function test_users_can_unfavorite_songs()
    {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        $song = Song::factory()->create(['artist_id' => $artist->id]);
        $user->addFavoriteSong($song);

        $response = $this->actingAs($user)
            ->delete(route('songs.favorite', [$artist, $song]));

        $response->assertRedirect();
        $this->assertDatabaseMissing('favorite_songs', [
            'user_id' => $user->id,
            'song_id' => $song->id
        ]);
    }
} 