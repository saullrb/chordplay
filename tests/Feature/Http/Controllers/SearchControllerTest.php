<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Artist;
use App\Models\Song;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_index_returns_limited_results()
    {
        $artist = Artist::factory()->create(['name' => 'Test Artist']);
        Artist::factory()->count(5)->create();
        Song::factory()->create([
            'name' => 'Test Song',
            'artist_id' => $artist->id,
        ]);
        Song::factory()->count(5)->create([
            'artist_id' => $artist->id,
        ]);

        $response = $this->get(route('search', ['query' => 'test']));

        $response->assertInertia(fn ($page) => $page
            ->component('Home')
            ->has('results.songs', 1)
            ->has('results.artists', 1)
            ->where('query', 'test')
            ->where('has_more_songs', false)
            ->where('has_more_artists', false)
        );
    }

    public function test_search_with_empty_query_returns_empty_results()
    {
        $response = $this->get(route('search'));

        $response->assertInertia(fn ($page) => $page
            ->component('Home')
            ->has('results.songs', 0)
            ->has('results.artists', 0)
            ->where('query', '')
        );
    }
}
