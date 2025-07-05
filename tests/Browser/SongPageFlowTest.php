<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\Artist;
use App\Models\Song;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SongPageFlowTest extends DuskTestCase
{
    protected $artist;

    protected $song;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->artist = Artist::factory()->create(['name' => 'Test Artist']);
        $this->song = Song::factory()->withSongBlocks(4, '[E]        [Cm]          [D]           [A7]')->create([
            'artist_id' => $this->artist->id,
            'name' => 'Test Song Name',
            'key' => 'C',
        ]);
    }

    public function test_complete_song_page_user_journey(): void
    {

        $this->browse(function (Browser $browser): void {
            $song_url = route('artists.songs.show', [$this->artist, $this->song]);

            // Visit homepage and search for song
            $browser->visit(route('home'))
                ->typeSlowly('@search-input', 'test')
                ->keys('@search-input', '{enter}')
                ->waitForRoute('search');

            $this->assertGuest();

            // Assert song page elements
            $browser
                ->click('a[href="'.$song_url.'"]')
                ->waitForTextIn('@song-key', $this->song->key->value, 3)
                ->assertSeeLink($this->artist->name)
                ->assertSee($this->song->name)
                ->assertPresent('@capo-0')
                ->assertPresent('@capo-1')
                ->assertPresent('@capo-2')
                ->assertPresent('@capo-3')
                ->assertPresent('@capo-10')
                ->assertPresent('@capo-11')
                ->assertNotPresent('@edit-song-link')
                ->assertNotPresent('@favorite-button');

            $expectedChords = ['E', 'Cm', 'D', 'A7'];
            $transposedChords = [];

            foreach ($expectedChords as $chord) {
                $transposedChords[] = $browser->text('@chord-'.$chord);
            }

            $this->assertEquals($expectedChords, $transposedChords);

            // Transpose song
            $browser
                ->click('@transpose-up-button')
                ->waitForTextIn('@song-key', 'C#')
                ->click('@transpose-down-button')
                ->waitForTextIn('@song-key', 'C')

                // Clicking 12 times should get you back to the original key
                ->click('@transpose-up-button')
                ->click('@transpose-up-button')
                ->click('@transpose-up-button')
                ->click('@transpose-up-button')
                ->click('@transpose-up-button')
                ->click('@transpose-up-button')
                ->click('@transpose-up-button')
                ->click('@transpose-up-button')
                ->click('@transpose-up-button')
                ->click('@transpose-up-button')
                ->click('@transpose-up-button')
                ->click('@transpose-up-button')
                ->waitForTextIn('@song-key', 'C')
                ->click('@transpose-down-button')
                ->waitForTextIn('@song-key', 'B');

            $expectedChords = ['Eb', 'Bm', 'C#', 'Ab7'];
            $transposedChords = [];

            foreach ($expectedChords as $chord) {
                $transposedChords[] = $browser->text('@chord-'.$chord);
            }

            $this->assertEquals($expectedChords, $transposedChords);

            // Add capo
            $browser
                ->click('@capo-dropdown')
                ->click('@capo-3');

            $expectedChords = ['C', 'Abm', 'Bb', 'F7'];
            $transposedChords = [];

            foreach ($expectedChords as $chord) {
                $transposedChords[] = $browser->text('@chord-'.$chord);
            }

            $this->assertEquals($expectedChords, $transposedChords);

            // Login to be able to favorite song
            $browser->visit(route('test.oauth.callback', $this->user->id).'?intended='.urlencode($song_url));

            // Favorite song
            $browser->waitForLocation($song_url)
                ->assertAuthenticated()
                ->waitFor('@favorite-button')
                ->assertPresent('@edit-song-link')
                ->assertPresent('@favorite-button @empty-star')
                ->click('@favorite-button')
                ->pause(1000)
                ->assertPresent('@favorite-button @filled-star');

            // Unfavorite song
            $browser
                ->click('@favorite-button')
                ->pause(1000)
                ->assertPresent('@favorite-button @empty-star');
        });
    }
}
