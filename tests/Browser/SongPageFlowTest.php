<?php

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
                ->pause(500);

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
                ->assertNotPresent('@edit-song-link');

            // Transpose song
            $browser
                ->click('@transpose-up-button')
                ->pause(500)
                ->assertSeeIn('@song-key', 'Db')
                ->click('@transpose-up-button')
                ->pause(500)
                ->click('@transpose-up-button')
                ->pause(500)
                ->assertSeeIn('@song-key', 'Eb')
                ->click('@transpose-down-button')
                ->pause(500)
                ->assertSeeIn('@song-key', 'D');

            $transposed_chords = $browser->text('[dusk="chord-line"]:first-of-type');
            $expected_chords = 'F#        Dm          E           B7';

            $this->assertEquals($expected_chords, $transposed_chords);

            // Add capo
            $browser
                ->click('@capo-dropdown')
                ->click('@capo-3');

            $transposed_chords = $browser->text('[dusk="chord-line"]:first-of-type');
            $expected_chords = 'D#        Bm          C#           G#7';

            $this->assertEquals($expected_chords, $transposed_chords);

            // Clicking on favorite button as a guest user should redirect to login page
            $browser
                ->click('@favorite-button')
                ->waitForLocation(route('login'))

                ->assertSee('Login with Google');

            $browser->visit(route('test.oauth.callback', $this->user->id).'?intended='.urlencode($song_url));

            // Favorite song
            $browser->waitForLocation($song_url)
                ->assertAuthenticated()
                ->waitFor('@favorite-button')
                ->assertPresent('@edit-song-link')
                ->assertNotChecked('@favorite-button input')
                ->click('@favorite-button')
                ->pause(1000)
                ->assertChecked('@favorite-button input');

            // Unfavorite song
            $browser
                ->click('@favorite-button')
                ->pause(1000)
                ->assertNotChecked('@favorite-button input');
        });
    }
}
