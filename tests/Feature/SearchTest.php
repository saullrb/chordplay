<?php

namespace Tests\Feature;

use App\Models\Artist;
use App\Models\Song;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_index_returns_limited_results()
    {
        $artist = Artist::factory()->create(['name' => 'Test Artist']);
        Artist::factory()->count(5)->create();
        Song::factory()->create([
            'name' => 'Test Song',
            'artist_id' => $artist->id
        ]);
        Song::factory()->count(5)->create([
            'artist_id' => $artist->id
        ]);

        $response = $this->get('/search?query=test');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Home')
            ->has('results.songs', 1)
            ->has('results.artists', 1)
            ->where('query', 'test')
            ->where('has_more_songs', false)
            ->where('has_more_artists', false)
        );
    }

    public function test_search_show_returns_all_results()
    {
        $artist = Artist::factory()->create(['name' => 'Test Artist']);
        Artist::factory()->count(2)->create(['name' => 'Test Artist']);
        Song::factory()->count(3)->create([
            'name' => 'Test Song',
            'artist_id' => $artist->id
        ]);

        $response = $this->get('/search/results?query=test');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Search')
            ->has('results.songs', 3)
            ->has('results.artists', 3)
            ->where('query', 'test')
        );
    }

    public function test_empty_search_returns_empty_results()
    {
        $artist = Artist::factory()->create(['name' => 'Random Artist']);
        Artist::factory()->count(4)->create();
        Song::factory()->count(5)->create([
            'artist_id' => $artist->id,
            'name' => 'Random Song'
        ]);

        $response = $this->get('/search');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Home')
            ->has('results.songs', 0)
            ->has('results.artists', 0)
            ->where('query', '')
            ->where('has_more_songs', false)
            ->where('has_more_artists', false)
        );
    }

    public function test_search_is_case_insensitive()
    {
        $artist = Artist::factory()->create(['name' => 'TEST Artist']);
        Song::factory()->create([
            'name' => 'TEST Song',
            'artist_id' => $artist->id
        ]);

        $response = $this->get('/search?query=test');

        $response->assertInertia(fn ($page) => $page
            ->has('results.songs', 1)
            ->has('results.artists', 1)
        );
    }

    public function test_search_results_are_ordered_by_views()
    {
        $artist = Artist::factory()->create();
        $lowViews = Song::factory()->create([
            'name' => 'Test Song',
            'artist_id' => $artist->id,
            'views' => 10
        ]);
        $highViews = Song::factory()->create([
            'name' => 'Test Song',
            'artist_id' => $artist->id,
            'views' => 100
        ]);

        $response = $this->get('/search/results?query=test');

        $response->assertInertia(fn ($page) => $page
            ->where('results.songs.0.name', $highViews->name)
            ->where('results.songs.1.name', $lowViews->name)
        );
    }
}
