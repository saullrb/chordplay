<?php

use App\Enums\SongKeyEnum;
use App\Models\Artist;
use App\Models\Song;
use App\Models\User;
use App\Models\Role;

test('guests cannot view song create form', function () {
    $artist = Artist::factory()->create();
    
    $response = $this->get(route('artists.songs.create', $artist));
    
    $response->assertRedirect('/login');
});

test('normal users cannot view song create form', function () {
    $user = User::factory()->create(['role_id' => Role::USER]);
    $artist = Artist::factory()->create();
    
    $response = $this->actingAs($user)
        ->get(route('artists.songs.create', $artist));
    
    $response->assertForbidden();
});

test('staff can view song create form', function () {
    $user = User::factory()->create(['role_id' => Role::STAFF]);
    $artist = Artist::factory()->create();
    
    $response = $this->actingAs($user)
        ->get(route('artists.songs.create', $artist));
    
    $response->assertOk();
});

test('staff can create songs', function () {
    $user = User::factory()->create(['role_id' => Role::STAFF]);
    $artist = Artist::factory()->create();
    
    $response = $this->actingAs($user)
        ->post(route('artists.songs.store', $artist), [
            'name' => 'Test Song',
            'key' => 'C',
            'content' => "[Am] [C] [G]\nTest lyrics here"
        ]);
    
    $this->assertDatabaseHas('songs', [
        'name' => 'Test Song',
        'key' => 'C',
        'artist_id' => $artist->id
    ]);
    
    $song = Song::latest()->first();
    $response->assertRedirect(route('artists.songs.show', [$artist, $song]));
});

test('staff can edit songs', function () {
    $user = User::factory()->create(['role_id' => Role::STAFF]);
    $artist = Artist::factory()->create();
    $song = Song::factory()->create(['artist_id' => $artist->id]);
    
    $response = $this->actingAs($user)
        ->patch(route('artists.songs.update', [$artist, $song]), [
            'name' => 'Updated Song',
            'key' => 'Am',
            'content' => "[Dm] [F] [C]\nUpdated lyrics here"
        ]);
    
    $this->assertDatabaseHas('songs', [
        'id' => $song->id,
        'name' => 'Updated Song',
        'key' => 'Am'
    ]);
    
    $response->assertRedirect(route('artists.songs.show', [$artist, $song->fresh()]));
});

test('users can view songs', function () {
    $user = User::factory()->create(['role_id' => Role::USER]);
    $artist = Artist::factory()->create();
    $song = Song::factory()->create(['artist_id' => $artist->id]);
    
    $response = $this->actingAs($user)
        ->get(route('artists.songs.show', [$artist, $song]));
    
    $response->assertOk();
});

test('users can favorite songs', function () {
    $user = User::factory()->create(['role_id' => Role::USER]);
    $artist = Artist::factory()->create();
    $song = Song::factory()->create(['artist_id' => $artist->id]);
    
    $response = $this->actingAs($user)
        ->post(route('songs.favorite', [$artist, $song]));
    
    $this->assertDatabaseHas('favorite_songs', [
        'user_id' => $user->id,
        'song_id' => $song->id
    ]);
});

test('users can unfavorite songs', function () {
    $user = User::factory()->create();
    $artist = Artist::factory()->create();
    $song = Song::factory()->create(['artist_id' => $artist->id]);
    
    $user->addFavoriteSong($song);
    
    $response = $this->actingAs($user)
        ->delete(route('songs.favorite', [$artist, $song]));
    
    $this->assertDatabaseMissing('favorite_songs', [
        'user_id' => $user->id,
        'song_id' => $song->id
    ]);
});

test('song creation requires valid data', function () {
    $user = User::factory()->create(['role_id' => Role::STAFF]);
    $artist = Artist::factory()->create();
    
    $response = $this->actingAs($user)
        ->post(route('artists.songs.store', $artist), [
            'name' => '',  
            'key' => 'InvalidKey',  
            'content' => ''  
        ]);
    
    $response->assertSessionHasErrors(['name', 'key', 'content']);
});

test('song content must have valid chord format', function () {
    $user = User::factory()->create(['role_id' => Role::STAFF]);
    $artist = Artist::factory()->create();
    
    $response = $this->actingAs($user)
        ->post(route('artists.songs.store', $artist), [
            'name' => 'Test Song',
            'key' => 'C',
            'content' => "[InvalidChord] [NotAChord]\nTest lyrics"
        ]);
    
    $response->assertSessionHasErrors(['content']);
});

test('viewing song increments view count', function () {
    $artist = Artist::factory()->create();
    $song = Song::factory()->create([
        'artist_id' => $artist->id,
        'views' => 0
    ]);
    
    $this->get(route('artists.songs.show', [$artist, $song]));
    
    $this->assertEquals(1, $song->fresh()->views);
});

test('guests cannot edit songs', function () {
    $artist = Artist::factory()->create();
    $song = Song::factory()->create(['artist_id' => $artist->id]);
    
    $response = $this->get(route('artists.songs.edit', [$artist, $song]));
    
    $response->assertRedirect('/login');
});

test('regular users cannot edit songs', function () {
    $user = User::factory()->create(['role_id' => Role::USER]);
    $artist = Artist::factory()->create();
    $song = Song::factory()->create(['artist_id' => $artist->id]);
    
    $response = $this->actingAs($user)
        ->get(route('artists.songs.edit', [$artist, $song]));
    
    $response->assertForbidden();
});

test('song show page includes favorite status', function () {
    $user = User::factory()->create(['role_id' => Role::USER]);
    $artist = Artist::factory()->create();
    $song = Song::factory()->create(['artist_id' => $artist->id]);
    
    $user->addFavoriteSong($song);
    
    $response = $this->actingAs($user)
        ->get(route('artists.songs.show', [$artist, $song]));
    
    $response->assertInertia(fn ($assert) => $assert
        ->where('is_favorited', true)
    );
});
