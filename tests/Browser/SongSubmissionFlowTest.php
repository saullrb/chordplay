<?php

namespace Tests\Browser;

use App\Models\Artist;
use App\Models\SongSubmission;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SongSubmissionFlowTest extends DuskTestCase
{
    protected $artist;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artist = Artist::factory()->create(['name' => 'Test Artist']);
        $this->user = User::factory()->create();
    }

    public function test_song_submission_flow(): void
    {
        $this->browse(function (Browser $browser): void {
            // Visit artist page as guest user
            $browser
                ->visit(route('artists.show', $this->artist))
                ->pause(500)
                ->assertSee($this->artist->name)
                ->assertGuest()
                ->assertNotPresent('@add-song-link');

            // Login and click add song link
            $browser
                ->loginAs($this->user)
                ->refresh()
                ->pause(500)
                ->assertAuthenticatedAs($this->user)
                ->click('@add-song-link')
                ->pause(500)
                ->assertRouteIs('artists.songs.create', $this->artist);

            // Test chords validation
            $browser
                ->type('#name', 'New Song')
                ->select('#key', 'C')
                ->type('#content', '[C]  [D]   [Em]   [T]')
                ->click('@submit-button')
                ->waitFor('@content-errors', 2)
                ->assertSeeIn('@content-errors', 'These chords are invalid: T');

            $song_content = '[C]  [D]   [Em]   [D]'.PHP_EOL.
               'The first song line'.PHP_EOL.
               '[C]  [D]   [Em]   [D]'.PHP_EOL.
               'The second song line'.PHP_EOL.
               PHP_EOL.
               '[C]  [D]   [Em]   [D]';

            // Type valid content and submit
            $browser
                ->type('#content', $song_content)
                ->click('@submit-button')
                ->pause(500);

            $songSubmission = SongSubmission::firstWhere('name', 'New Song');

            // SongSubmission show page
            $browser
                ->waitForTextIn('@song-key', $songSubmission->key->value, 3)
                ->assertRouteIs('song-submissions.show', $songSubmission)
                ->assertPresent('@preview-notice')
                ->assertPresent('@flash-message')
                ->assertSeeLink($songSubmission->artist->name)
                ->assertSee($songSubmission->name)
                ->assertNotPresent('@approve-song-button')
                ->assertPresent('@edit-song-link')
                ->assertPresent('@reject-song-button');

            // Edit song submission
            $browser
                ->click('@edit-song-link')
                ->pause(500)
                ->assertInputValue('@song-name-input', $songSubmission->name)
                ->assertSelected('@song-key-select', $songSubmission->key->value)
                ->assertInputValue('@song-content-textarea', $song_content)
                ->type('@song-name-input', 'Updated Song Name')
                ->click('@submit-button')
                ->pause(500);

            $songSubmission->refresh();

            // Check if song submission was updated
            $browser
                ->waitForTextIn('@song-key', $songSubmission->key->value, 3)
                ->assertSee($songSubmission->name)
                ->assertPresent('@flash-message');

            // Delete song submission
            $browser
                ->click('@reject-song-button')
                ->waitFor('@confirm-modal-button')
                ->click('@confirm-modal-button')
                ->pause(1000)
                ->assertRouteIs('song-submissions.index');
        });
    }
}
