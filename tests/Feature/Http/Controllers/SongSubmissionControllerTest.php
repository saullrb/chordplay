<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\SongLineContentType;
use App\Models\Artist;
use App\Models\Song;
use App\Models\SongSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SongSubmissionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $author;

    protected User $regular_user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $this->author = User::factory()->create();
        $this->regular_user = User::factory()->create();
    }

    public function test_guest_user_cannot_visit_submissions_page(): void
    {
        $this->get(route('song_submissions.index'))->assertRedirect(route('login'));
    }

    public function test_admin_sees_all_submissions(): void
    {
        $artist = Artist::factory()->create();
        SongSubmission::factory(25)->create([
            'artist_id' => $artist->id, 'user_id' => $this->author->id,
        ]);

        $this->actingAs($this->admin)
            ->get(route('song_submissions.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('SongSubmissions/Index')
                ->has('submissions.data', 10)
                ->where('submissions.total', 25)
            );
    }

    public function test_user_sees_own_submissions(): void
    {
        $artist = Artist::factory()->create();
        SongSubmission::factory()->count(5)->create(['user_id' => $this->author->id, 'artist_id' => $artist->id]);
        SongSubmission::factory()->count(5)->create(['user_id' => $this->admin->id, 'artist_id' => $artist->id]);

        $this->actingAs($this->author)
            ->get(route('song_submissions.index'))
            ->assertStatus(200)
            ->assertInertia(fn ($page) => $page->component('SongSubmissions/Index')
                ->has('submissions.data', 5)
            );
    }

    public function test_only_admin_or_author_can_view_submission(): void
    {
        $artist = Artist::factory()->create();
        $submission = SongSubmission::factory()->create([
            'artist_id' => $artist->id, 'user_id' => $this->author->id,
        ]);

        // Guest user
        $this->get(route('song_submissions.show', $submission))
            ->assertRedirect(route('login'));

        // Regular user
        $this->actingAs($this->regular_user)
            ->get(route('song_submissions.show', $submission))
            ->assertForbidden();

        // Author
        $this->actingAs($this->author)
            ->get(route('song_submissions.show', $submission))
            ->assertOk();

        // Admin
        $this->actingAs($this->admin)
            ->get(route('song_submissions.show', $submission))
            ->assertOk();
    }

    public function test_show_displays_song_submission_show_page(): void
    {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        $submission = SongSubmission::factory()
            ->for($artist)
            ->for($user, 'user')
            ->create();

        $submission->lines()->createMany([
            ['line_number' => 1, 'content' => '[Am] [C]', 'content_type' => SongLineContentType::CHORDS],
            ['line_number' => 2, 'content' => 'Line 2', 'content_type' => SongLineContentType::LYRICS],
        ]);

        $this->actingAs($user)
            ->get(route('song_submissions.show', $submission))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('SongSubmissions/Show')
                ->where('song_submission.id', $submission->id)
                ->where('song_submission.artist.id', $artist->id)
                ->where('song_submission.lines.0.content', '[Am] [C]')
                ->where('song_submission.lines.1.content', 'Line 2')
                ->has('valid_chords')
            );
    }

    public function test_only_authenticated_user_can_acess_create_page(): void
    {
        $auth_user = User::factory()->create();
        $artist = Artist::factory()->create();

        // Guest user
        $this->get(route('artists.songs.create', $artist))->assertRedirect(route('login'));

        // Authenticated user
        $this->actingAs($auth_user)->get(route('artists.songs.create', $artist))->assertOk();
    }

    public function test_create_displays_song_submission_create_page(): void
    {
        $artist = Artist::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('artists.songs.create', $artist))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('SongSubmissions/Create')
                ->where('artist.id', $artist->id)
                ->has('available_keys')
                ->has('valid_chords')
            );
    }

    public function test_only_authenticated_user_can_store_a_new_submission(): void
    {
        $auth_user = User::factory()->create();
        $artist = Artist::factory()->create();

        $this->post(route('song_submissions.store', $artist))
            ->assertRedirect(route('login'));

        $response = $this->actingAs($auth_user)
            ->post(route('song_submissions.store', $artist), [
                'name' => 'Test Song',
                'key' => 'C',
                'content' => '[Am] Test',
            ]);

        $songSubmission = SongSubmission::where('name', 'Test Song')->first();

        $response->assertRedirect(route('song_submissions.show', $songSubmission));
    }

    public function test_edit_displays_song_submission_edit_page(): void
    {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        $submission = SongSubmission::factory()
            ->for($artist)
            ->for($user, 'user')
            ->create();

        $submission->lines()->createMany([
            ['line_number' => 1, 'content' => '[Am] [C]', 'content_type' => SongLineContentType::CHORDS],
            ['line_number' => 2, 'content' => 'Line 2', 'content_type' => SongLineContentType::LYRICS],
        ]);

        $this->actingAs($user)
            ->get(route('song_submissions.edit', $submission))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('SongSubmissions/Edit')
                ->where('song_submission.id', $submission->id)
                ->where('song_submission.content', "[Am] [C]\nLine 2")
                ->has('available_keys')
            );
    }

    public function test_only_admin_or_author_can_edit_submission(): void
    {
        $this->regular_user = User::factory()->create();
        $this->author = User::factory()->create();
        $this->admin = User::factory()->admin()->create();

        $artist = Artist::factory()->create();
        $submission = SongSubmission::factory()->create([
            'artist_id' => $artist->id, 'user_id' => $this->author->id,
        ]);

        // Guest user
        $this->get(route('song_submissions.edit', $submission))
            ->assertRedirect(route('login'));

        // Regular user
        $this->actingAs($this->regular_user)
            ->get(route('song_submissions.edit', $submission))
            ->assertForbidden();

        // Author
        $this->actingAs($this->author)
            ->get(route('song_submissions.edit', $submission))
            ->assertOk();

        // Admin
        $this->actingAs($this->admin)
            ->get(route('song_submissions.edit', $submission))
            ->assertOk();
    }

    public function test_only_authenticated_user_can_update_song_submission(): void
    {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        $submission = SongSubmission::factory()
            ->for($user, 'user')
            ->for($artist)
            ->create();

        $requestData = [
            'name' => 'Updated Song',
            'key' => 'D',
            'content' => "[D] [G]\nUpdated line 2",
        ];

        // Guest user
        $this->patch(route('song_submissions.update', $submission), $requestData)
            ->assertRedirect(route('login'));

        // Authenticated user
        $this->actingAs($user)
            ->patch(route('song_submissions.update', $submission), $requestData)
            ->assertRedirect(route('song_submissions.show', $submission));

        $submission->refresh();

        $this->assertEquals('Updated Song', $submission->name);
        $this->assertEquals('D', $submission->key);
        $this->assertCount(2, $submission->lines);
        $this->assertEquals('[D] [G]', $submission->lines[0]->content);
        $this->assertEquals('Updated line 2', $submission->lines[1]->content);
    }

    public function test_only_admin_or_author_can_delete_song_submission(): void
    {
        $this->regular_user = User::factory()->create();
        $this->author = User::factory()->create();
        $this->admin = User::factory()->admin()->create();

        $artist = Artist::factory()->create();
        $submission = SongSubmission::factory()->create([
            'artist_id' => $artist->id, 'user_id' => $this->author->id,
        ]);

        // Guest user
        $this->get(route('song_submissions.destroy', $submission))
            ->assertRedirect(route('login'));

        // Regular user
        $this->actingAs($this->regular_user)
            ->get(route('song_submissions.destroy', $submission))
            ->assertForbidden();

        // Author
        $this->actingAs($this->author)
            ->get(route('song_submissions.destroy', $submission))
            ->assertOk();

        // Admin
        $this->actingAs($this->admin)
            ->get(route('song_submissions.destroy', $submission))
            ->assertOk();
    }

    public function test_destroy_song_submission_and_redirect(): void
    {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        $submission = SongSubmission::factory()->for($artist)->for($user, 'user')->create();

        $this->actingAs($user)
            ->delete(route('song_submissions.destroy', $submission))
            ->assertRedirect(route('song_submissions.index'));

        $this->assertDatabaseMissing('song_submissions', ['id' => $submission->id]);
    }

    public function test_only_admin_can_approve_song_submission(): void
    {
        $this->regular_user = User::factory()->create();
        $this->author = User::factory()->create();
        $this->admin = User::factory()->admin()->create();

        $artist = Artist::factory()->create();
        $submission = SongSubmission::factory()->for($artist)->for($this->author, 'user')->create();

        // Guest user
        $this->post(route('song_submissions.approve', $submission))
            ->assertRedirect(route('login'));

        // Regular user
        $this->actingAs($this->regular_user)
            ->post(route('song_submissions.approve', $submission))
            ->assertForbidden();

        // Author
        $this->actingAs($this->author)
            ->post(route('song_submissions.approve', $submission))
            ->assertForbidden();

        // Admin
        $response = $this->actingAs($this->admin)->post(route('song_submissions.approve', $submission));
        $song = Song::where('name', $submission->name)->first();
        $response->assertRedirect(route('artists.songs.show', [$artist, $song]));
    }

    public function test_approve_song_submission_creates_song_and_redirects(): void
    {
        $this->admin = User::factory()->admin()->create();
        $artist = Artist::factory()->create();
        $submission = SongSubmission::factory()->for($artist)->for($this->admin, 'user')->create();

        $submission->lines()->createMany([
            ['line_number' => 1, 'content' => '[Am] [C]', 'content_type' => SongLineContentType::CHORDS],
            ['line_number' => 2, 'content' => 'Some lyrics', 'content_type' => SongLineContentType::LYRICS],
        ]);

        $response = $this->actingAs($this->admin)->post(route('song_submissions.approve', $submission));

        $song = Song::where('name', $submission->name)->first();

        $response->assertRedirect(route('artists.songs.show', [$artist, $song]));

        $this->assertDatabaseMissing('song_submissions', ['id' => $submission->id]);

        $this->assertDatabaseHas('songs', [
            'name' => $submission->name,
            'key' => $submission->key,
            'artist_id' => $artist->id,
        ]);

        $this->assertEquals(2, $song->lines()->count());
        $this->assertDatabaseHas('song_lines', [
            'song_id' => $song->id,
            'line_number' => 1,
            'content' => '[Am] [C]',
            'content_type' => SongLineContentType::CHORDS,
        ]);
    }
}
