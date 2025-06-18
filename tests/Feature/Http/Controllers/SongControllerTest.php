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

    public function test_only_authenticated_user_can_access_edit_page(): void
    {
        $user = User::factory()->create(['role_id' => Role::USER]);
        $artist = Artist::factory()->create();
        $song = Song::factory()->create(['artist_id' => $artist->id]);

        $this->assertGuest();
        $this->get(route('artists.songs.edit', [$artist, $song]))
            ->assertRedirect(route('login'));

        $response = $this->actingAs($user)
            ->get(route('artists.songs.edit', [$artist, $song]));

        $response->assertOk();
    }

    public function test_guests_cannot_favorite_songs(): void
    {
        $artist = Artist::factory()->create();
        $song = Song::factory()->create(['artist_id' => $artist->id]);

        $response = $this->post(route('songs.favorite', [$artist, $song]));

        $response->assertRedirect(route('login'));
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
