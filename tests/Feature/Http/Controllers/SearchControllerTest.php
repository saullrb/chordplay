<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Artist;
use App\Models\Song;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_validates_query(): void
    {
        $response = $this->get('/search?query=');

        $response->assertSessionHasErrors(['query' => 'The query field is required.']);
    }

    public function test_search_unauthenticated_returns_results_without_favorites(): void
    {
        Artist::factory()->create(['name' => 'Artist1']);
        Song::factory()->create([
            'name' => 'Test Song',
        ]);

        $this->get('/search?query=test')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Search')
                ->has('songs.data', 1)
                ->has('artists.data', 0)
                ->where('songs.data.0.name', 'Test Song')
                ->where('songs.data.0.is_favorited', false)
                ->where('query', 'test')
            );
    }

    public function test_search_authenticated_returns_favorited_results(): void
    {
        $user = User::factory()->create();
        Artist::factory()->create(['name' => 'Artist1']);
        $song = Song::factory()->create([
            'name' => 'Test Song',
        ]);
        $user->favoriteSongs()->attach($song);

        $this->actingAs($user)->get('/search?query=test')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Search')
                ->has('songs.data', 1)
                ->has('artists.data', 0)
                ->where('songs.data.0.name', 'Test Song')
                ->where('songs.data.0.is_favorited', true)
                ->where('query', 'test')
            );
    }
}
