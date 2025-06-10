<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Artist;
use App\Models\Role;
use App\Models\Song;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SongControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_view_song_create_form(): void
    {
        $artist = Artist::factory()->create();
        $this->get(route('artists.songs.create', $artist))
            ->assertRedirect(route('google.redirect'));
    }

    public function test_only_admin_can_access_create_page(): void
    {
        $user = User::factory()->create(['role_id' => Role::USER]);
        $admin = User::factory()->create(['role_id' => Role::ADMIN]);
        $artist = Artist::factory()->create();

        $this->actingAs($user)
            ->get(route('artists.songs.create', $artist))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('artists.songs.create', $artist))
            ->assertOk();
    }

    public function test_only_admin_can_store_song(): void
    {
        $user = User::factory()->create(['role_id' => Role::USER]);
        $admin = User::factory()->create(['role_id' => Role::ADMIN]);
        $artist = Artist::factory()->create();

        $this->actingAs($user)
            ->post(route('artists.songs.store', $artist), [
                'name' => 'Test Song',
                'key' => 'C',
                'content' => '[Am] Test',
            ])
            ->assertForbidden();

        $this->actingAs($admin)
            ->post(route('artists.songs.store', $artist), [
                'name' => 'Test Song',
                'key' => 'C',
                'content' => '[Am] Test',
            ])
            ->assertRedirect();
    }

    public function test_anyone_can_view_songs(): void
    {
        $artist = Artist::factory()->create();
        $song = Song::factory()->create(['artist_id' => $artist->id]);

        $response = $this->get(route('artists.songs.show', [$artist, $song]));
        $response->assertOk();
    }

    public function test_viewing_song_increments_view_count(): void
    {
        $artist = Artist::factory()->create();
        $song = Song::factory()->create([
            'artist_id' => $artist->id,
            'views' => 0,
        ]);

        $this->get(route('artists.songs.show', [$artist, $song]));

        $this->assertEquals(1, $song->fresh()->views);
    }

    public function test_song_creation_requires_valid_data(): void
    {
        $admin = User::factory()->create(['role_id' => Role::ADMIN]);
        $artist = Artist::factory()->create();

        $response = $this->actingAs($admin)
            ->post(route('artists.songs.store', $artist), [
                'name' => '',
                'key' => 'InvalidKey',
                'content' => '',
            ]);

        $response->assertSessionHasErrors(['name', 'key', 'content']);
    }

    public function test_song_content_must_have_valid_chord_format(): void
    {
        $admin = User::factory()->create(['role_id' => Role::ADMIN]);
        $artist = Artist::factory()->create();

        $response = $this->actingAs($admin)
            ->post(route('artists.songs.store', $artist), [
                'name' => 'Test Song',
                'key' => 'C',
                'content' => '[InvalidChord] [NotAChord]\nTest lyrics',
            ]);

        $response->assertSessionHasErrors(['content']);
    }

    public function test_song_show_page_includes_favorite_status(): void
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

    public function test_admin_can_access_edit_page(): void
    {
        $admin = User::factory()->create(['role_id' => Role::ADMIN]);
        $artist = Artist::factory()->create();
        $song = Song::factory()->create(['artist_id' => $artist->id]);

        $response = $this->actingAs($admin)
            ->get(route('artists.songs.edit', [$artist, $song]));

        $response->assertOk();
    }

    public function test_admin_can_update_song(): void
    {
        $admin = User::factory()->create(['role_id' => Role::ADMIN]);
        $artist = Artist::factory()->create();
        $song = Song::factory()->create(['artist_id' => $artist->id]);

        $response = $this->actingAs($admin)
            ->patch(route('artists.songs.update', [$artist, $song]), [
                'name' => 'Updated Song',
                'key' => 'Am',
                'content' => '[Am] Updated lyrics',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('songs', [
            'id' => $song->id,
            'name' => 'Updated Song',
        ]);
    }

    public function test_regular_users_cannot_access_edit_page(): void
    {
        $user = User::factory()->create(['role_id' => Role::USER]);
        $artist = Artist::factory()->create();
        $song = Song::factory()->create(['artist_id' => $artist->id]);

        $response = $this->actingAs($user)
            ->get(route('artists.songs.edit', [$artist, $song]));

        $response->assertForbidden();
    }

    public function test_regular_users_cannot_update_song(): void
    {
        $user = User::factory()->create(['role_id' => Role::USER]);
        $artist = Artist::factory()->create();
        $song = Song::factory()->create(['artist_id' => $artist->id]);

        $response = $this->actingAs($user)
            ->patch(route('artists.songs.update', [$artist, $song]), [
                'name' => 'Updated Song',
                'key' => 'Am',
                'content' => '[Am] Updated lyrics',
            ]);

        $response->assertForbidden();
    }

    public function test_guests_cannot_favorite_songs(): void
    {
        $artist = Artist::factory()->create();
        $song = Song::factory()->create(['artist_id' => $artist->id]);

        $response = $this->post(route('songs.favorite', [$artist, $song]));

        $response->assertRedirect(route('google.redirect'));
    }

    public function test_users_can_favorite_songs(): void
    {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        $song = Song::factory()->create(['artist_id' => $artist->id]);

        $response = $this->actingAs($user)
            ->post(route('songs.favorite', [$artist, $song]));

        $response->assertRedirect();
        $this->assertDatabaseHas('favorite_songs', [
            'user_id' => $user->id,
            'song_id' => $song->id,
        ]);
    }

    public function test_users_can_unfavorite_songs(): void
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
            'song_id' => $song->id,
        ]);
    }
}
