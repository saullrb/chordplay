<?php

use App\Models\Artist;
use App\Models\Role;
use App\Models\User;
use App\Models\Song;

test('anyone can view artists index', function() {
    $artists = Artist::factory(3)->create();

    $response = $this->get('/artists');

    $response->assertInertia(fn ($assert) => $assert
        ->component('Artists/Index')
        ->has('artists.data', 3)
    );
});

test('artists index displays all artists', function() {
    $artists = Artist::factory(3)->create();

    $response = $this->get('/artists');

    foreach ($artists as $artist) {
        $response->assertSee($artist->name);
    }
});

test('regular user cannot see add artist button', function() {
    $user = User::factory()->create(['role_id' => Role::USER]);
    
    $response = $this->actingAs($user)
        ->get('/artists');

    $response->assertInertia(fn ($assert) => $assert
        ->component('Artists/Index')
        ->where('can.create_artist', false)
    );
});

test('staff can see add artist button', function() {
    $user = User::factory()->create(['role_id' => Role::STAFF]);
    
    $response = $this->actingAs($user)
        ->from('/artists')
        ->get('/artists');

    $response->assertInertia(fn ($assert) => $assert
        ->component('Artists/Index')
        ->where('can.create_artist', true)
    );
});

test('admin can see add artist button', function() {
    $user = User::factory()->create(['role_id' => Role::ADMIN]);
    
    $response = $this->actingAs($user)
        ->from('/artists')
        ->get('/artists');

    $response->assertInertia(fn ($assert) => $assert
        ->component('Artists/Index')
        ->where('can.create_artist', true)
    );
});

test('regular user cannot create artist', function() {
    $user = User::factory()->create(['role_id' => Role::USER]);
    
    $response = $this->actingAs($user)
        ->get(route('artists.create'));
    
    $response->assertStatus(403);
});

test('staff can access create artist page', function() {
    $user = User::factory()->create(['role_id' => Role::STAFF]);
    
    $response = $this->actingAs($user)
        ->get(route('artists.create'));
    
    $response->assertInertia(fn ($assert) => $assert
        ->component('Artists/Create')
    );
});

test('can view artist details', function() {
    $artist = Artist::factory()
        ->has(Song::factory()->count(3))
        ->create();
    
    $response = $this->get(route('artists.show', $artist));
    
    $response->assertInertia(fn ($assert) => $assert
        ->component('Artists/Show')
        ->has('artist')
        ->has('artist.songs', 3)
        ->where('artist.name', $artist->name)
        ->where('is_favorited', false)
    );
});

test('viewing artist increments view count', function() {
    $artist = Artist::factory()->create(['views' => 0]);
    
    $this->get(route('artists.show', $artist));
    
    $this->assertEquals(1, $artist->fresh()->views);
});

test('staff can store new artist', function() {
    $user = User::factory()->create(['role_id' => Role::STAFF]);
    
    $response = $this->actingAs($user)
        ->post(route('artists.store'), [
            'name' => 'New Artist'
        ]);
    
    $response->assertRedirect();
    $this->assertDatabaseHas('artists', ['name' => 'New Artist']);
});

test('regular user cannot store artist', function() {
    $user = User::factory()->create(['role_id' => Role::USER]);
    
    $response = $this->actingAs($user)
        ->post(route('artists.store'), [
            'name' => 'New Artist'
        ]);
    
    $response->assertStatus(403);
    $this->assertDatabaseMissing('artists', ['name' => 'New Artist']);
});

test('user can favorite artist', function() {
    $user = User::factory()->create();
    $artist = Artist::factory()->create();
    
    $response = $this->actingAs($user)
        ->post(route('artists.favorite', $artist));
    
    $response->assertRedirect();
    $this->assertTrue($user->favoriteArtists()->where('artist_id', $artist->id)->exists());
});

test('user can unfavorite artist', function() {
    $user = User::factory()->create();
    $artist = Artist::factory()->create();
    $user->addFavoriteArtist($artist);
    
    $response = $this->actingAs($user)
        ->delete(route('artists.favorite', $artist));
    
    $response->assertRedirect();
    $this->assertFalse($user->favoriteArtists()->where('artist_id', $artist->id)->exists());
});

test('artist name is required when creating', function() {
    $user = User::factory()->create(['role_id' => Role::STAFF]);
    
    $response = $this->actingAs($user)
        ->post(route('artists.store'), [
            'name' => ''
        ]);
    
    $response->assertSessionHasErrors(['name']);
});

test('show returns 404 for non-existent artist', function() {
    $response = $this->get(route('artists.show', 'non-existent-slug'));
    $response->assertStatus(404);
});

test('songs are ordered by title in show page', function() {
    $artist = Artist::factory()->create();
    $songC = Song::factory()->create(['artist_id' => $artist->id, 'name' => 'C Song']);
    $songA = Song::factory()->create(['artist_id' => $artist->id, 'name' => 'A Song']);
    $songB = Song::factory()->create(['artist_id' => $artist->id, 'name' => 'B Song']);
    
    $response = $this->get(route('artists.show', $artist));
    
    $response->assertInertia(fn ($assert) => $assert
        ->where('artist.songs.0.name', 'A Song')
        ->where('artist.songs.1.name', 'B Song')
        ->where('artist.songs.2.name', 'C Song')
    );
});

test('guest cannot favorite artist', function() {
    $artist = Artist::factory()->create();
    
    $response = $this->post(route('artists.favorite', $artist));
    
    $response->assertRedirect(route('login'));
});


