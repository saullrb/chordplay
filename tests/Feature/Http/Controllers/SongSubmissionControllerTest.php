<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\SongKeyEnum;
use App\Enums\SongLineContentType;
use App\Models\Artist;
use App\Models\Song;
use App\Models\SongSubmission;
use App\Models\User;
use App\Services\SongSubmissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SongSubmissionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $author;

    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $this->author = User::factory()->create();
        $this->regularUser = User::factory()->create();
    }

    public function test_guest_user_cannot_visit_submissions_page(): void
    {
        $this->get(route('song-submissions.index'))->assertRedirect(route('login'));
    }

    public function test_admin_sees_all_submissions(): void
    {
        $artist = Artist::factory()->create();
        SongSubmission::factory(25)->create([
            'artist_id' => $artist->id, 'user_id' => $this->author->id,
        ]);

        $this->actingAs($this->admin)
            ->get(route('song-submissions.index'))
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
            ->get(route('song-submissions.index'))
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
        $this->get(route('song-submissions.show', $submission))
            ->assertRedirect(route('login'));

        // Regular user
        $this->actingAs($this->regularUser)
            ->get(route('song-submissions.show', $submission))
            ->assertForbidden();

        // Author
        $this->actingAs($this->author)
            ->get(route('song-submissions.show', $submission))
            ->assertOk();

        // Admin
        $this->actingAs($this->admin)
            ->get(route('song-submissions.show', $submission))
            ->assertOk();
    }

    public function test_show_displays_song_submission_show_page(): void
    {
        $artist = Artist::factory()->create();
        $submission = SongSubmission::factory()
            ->for($artist)
            ->for($this->author, 'user')
            ->create();

        $submission->lines()->createMany([
            ['line_number' => 1, 'content' => '[Am] [C]', 'content_type' => SongLineContentType::CHORDS],
            ['line_number' => 2, 'content' => 'Line 2', 'content_type' => SongLineContentType::LYRICS],
        ]);

        $this->actingAs($this->author)
            ->get(route('song-submissions.show', $submission))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('SongSubmissions/Show')
                ->where('song.id', $submission->id)
                ->where('song.artist.id', $artist->id)
                ->where('song.lines.0.content', '[Am] [C]')
                ->where('song.lines.1.content', 'Line 2')
            );
    }

    public function test_only_authenticated_user_can_acess_create_page(): void
    {
        $artist = Artist::factory()->create();

        // Guest user
        $this->get(route('artists.songs.create', $artist))->assertRedirect(route('login'));

        // Authenticated user
        $this->actingAs($this->regularUser)->get(route('artists.songs.create', $artist))->assertOk();
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
                ->has('availableKeys')
                ->has('validChords')
            );
    }

    public function test_only_authenticated_user_can_store_a_new_submission(): void
    {
        $artist = Artist::factory()->create();

        $this->post(route('song-submissions.store', $artist))
            ->assertRedirect(route('login'));

        $response = $this->actingAs($this->author)
            ->post(route('song-submissions.store', $artist), [
                'name' => 'Test Song',
                'key' => 'C',
                'content' => '[Am] Test',
            ]);

        $songSubmission = SongSubmission::where('name', 'Test Song')->first();

        $response
            ->assertRedirect(route('song-submissions.show', $songSubmission))
            ->assertSessionHas('flash.message')
            ->assertSessionHas('flash.type', 'success');
    }

    public function test_store_handle_validation_errors(): void
    {
        $artist = Artist::factory()->create();

        $invalid_data = [
            'name' => '',
            'key' => '',
            'content' => '[Wrong] chord',
        ];

        $this->actingAs($this->author)
            ->post(route('song-submissions.store', $artist), $invalid_data)
            ->assertRedirectBack()
            ->assertSessionHasErrors(['name', 'key', 'content']);
    }

    public function test_store_handles_errors(): void
    {
        $artist = Artist::factory()->create();

        $this->mock(SongSubmissionService::class, function ($mock): void {
            $mock->shouldReceive('store')
                ->once()
                ->andThrow(new \Exception('Database error'));
        });

        $this->actingAs($this->author)
            ->post(route('song-submissions.store', $artist), [
                'name' => 'Test Song',
                'key' => 'C',
                'content' => '[Am] Test',
            ])
            ->assertRedirectBack()
            ->assertSessionHas('flash.message')
            ->assertSessionHas('flash.type', 'error');
    }

    public function test_edit_displays_song_submission_edit_page(): void
    {
        $artist = Artist::factory()->create();
        $submission = SongSubmission::factory()
            ->for($artist)
            ->for($this->regularUser, 'user')
            ->create();

        $submission->lines()->createMany([
            ['line_number' => 1, 'content' => '[Am] [C]', 'content_type' => SongLineContentType::CHORDS],
            ['line_number' => 2, 'content' => 'Line 2', 'content_type' => SongLineContentType::LYRICS],
        ]);

        $this->actingAs($this->regularUser)
            ->get(route('song-submissions.edit', $submission))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('SongSubmissions/Edit')
                ->where('song.id', $submission->id)
                ->where('song.content', "[Am] [C]\nLine 2")
                ->has('availableKeys')
            );
    }

    public function test_only_admin_or_author_can_edit_submission(): void
    {
        $artist = Artist::factory()->create();
        $submission = SongSubmission::factory()->create([
            'artist_id' => $artist->id, 'user_id' => $this->author->id,
        ]);

        // Guest user
        $this->get(route('song-submissions.edit', $submission))
            ->assertRedirect(route('login'));

        // Regular user
        $this->actingAs($this->regularUser)
            ->get(route('song-submissions.edit', $submission))
            ->assertForbidden();

        // Author
        $this->actingAs($this->author)
            ->get(route('song-submissions.edit', $submission))
            ->assertOk();

        // Admin
        $this->actingAs($this->admin)
            ->get(route('song-submissions.edit', $submission))
            ->assertOk();
    }

    public function test_only_admin_or_author_can_update_submission(): void
    {
        $artist = Artist::factory()->create();
        $submission = SongSubmission::factory()
            ->for($this->author, 'user')
            ->for($artist)
            ->create();

        $requestData = [
            'name' => 'Updated Song',
            'key' => 'D',
            'content' => "[D] [G]\nUpdated line 2",
        ];

        // Guest user
        $this->patch(route('song-submissions.update', $submission), $requestData)
            ->assertRedirect(route('login'));

        // Regular user
        $this->actingAs($this->regularUser)
            ->patch(route('song-submissions.update', $submission), $requestData)
            ->assertForbidden();

        // Author
        $this->actingAs($this->author)
            ->patch(route('song-submissions.update', $submission), $requestData)
            ->assertRedirect(route('song-submissions.show', $submission));

        $submission->refresh();

        $this->assertEquals('Updated Song', $submission->name);
        $this->assertEquals(SongKeyEnum::D, $submission->key);
        $this->assertCount(2, $submission->lines);
        $this->assertEquals('[D] [G]', $submission->lines[0]->content);
        $this->assertEquals('Updated line 2', $submission->lines[1]->content);
    }

    public function test_update_handles_errors(): void
    {
        $artist = Artist::factory()->create();
        $submission = SongSubmission::factory()
            ->for($this->author, 'user')
            ->for($artist)
            ->create();
        $requestData = [
            'name' => 'Updated Song',
            'key' => 'D',
            'content' => "[D] [G]\nUpdated line 2",
        ];

        $this->mock(SongSubmissionService::class, function ($mock): void {
            $mock->shouldReceive('update')
                ->once()
                ->andThrow(new \Exception('Database error'));
        });

        $this->actingAs($this->author)
            ->patch(route('song-submissions.update', $submission), $requestData)
            ->assertRedirectBack()
            ->assertSessionHas('flash.message')
            ->assertSessionHas('flash.type', 'error');
    }

    public function test_only_admin_or_author_can_delete_song_submission(): void
    {
        $artist = Artist::factory()->create();
        $submission = SongSubmission::factory()->create([
            'artist_id' => $artist->id, 'user_id' => $this->author->id,
        ]);

        // Guest user
        $this->get(route('song-submissions.destroy', $submission))
            ->assertRedirect(route('login'));

        // Regular user
        $this->actingAs($this->regularUser)
            ->get(route('song-submissions.destroy', $submission))
            ->assertForbidden();

        // Author
        $this->actingAs($this->author)
            ->get(route('song-submissions.destroy', $submission))
            ->assertOk();

        // Admin
        $this->actingAs($this->admin)
            ->get(route('song-submissions.destroy', $submission))
            ->assertOk();
    }

    public function test_destroy_song_submission_and_redirect(): void
    {
        $artist = Artist::factory()->create();
        $submission = SongSubmission::factory()
            ->for($artist)
            ->for($this->author, 'user')
            ->create();

        $this->actingAs($this->author)
            ->delete(route('song-submissions.destroy', $submission))
            ->assertRedirect(route('song-submissions.index'));

        $this->assertDatabaseMissing('song_submissions', ['id' => $submission->id]);
    }

    public function test_only_admin_can_approve_song_submission(): void
    {
        $artist = Artist::factory()->create();
        $submission = SongSubmission::factory()->for($artist)->for($this->author, 'user')->create();

        // Guest user
        $this->post(route('song-submissions.approve', $submission))
            ->assertRedirect(route('login'));

        // Regular user
        $this->actingAs($this->regularUser)
            ->post(route('song-submissions.approve', $submission))
            ->assertForbidden();

        // Author
        $this->actingAs($this->author)
            ->post(route('song-submissions.approve', $submission))
            ->assertForbidden();

        // Admin
        $response = $this->actingAs($this->admin)->post(route('song-submissions.approve', $submission));
        $song = Song::where('name', $submission->name)->first();
        $response->assertRedirect(route('artists.songs.show', [$artist, $song]));
    }

    public function test_approve_song_submission_creates_song_and_redirects(): void
    {
        $artist = Artist::factory()->create();
        $submission = SongSubmission::factory()->for($artist)->for($this->admin, 'user')->create();

        $submission->lines()->createMany([
            ['line_number' => 1, 'content' => '[Am] [C]', 'content_type' => SongLineContentType::CHORDS],
            ['line_number' => 2, 'content' => 'Some lyrics', 'content_type' => SongLineContentType::LYRICS],
        ]);

        $response = $this->actingAs($this->admin)->post(route('song-submissions.approve', $submission));

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

    public function test_approve_handles_errors(): void
    {
        $artist = Artist::factory()->create();
        $submission = SongSubmission::factory()->for($artist)->for($this->admin, 'user')->create();

        $submission->lines()->createMany([
            ['line_number' => 1, 'content' => '[Am] [C]', 'content_type' => SongLineContentType::CHORDS],
            ['line_number' => 2, 'content' => 'Some lyrics', 'content_type' => SongLineContentType::LYRICS],
        ]);

        $this->mock(SongSubmissionService::class, function ($mock): void {
            $mock->shouldReceive('approve')
                ->once()
                ->andThrow(new \Exception('Database error'));
        });

        $this->actingAs($this->admin)->post(route('song-submissions.approve', $submission))
            ->assertRedirectBack()
            ->assertSessionHas('flash.message')
            ->assertSessionHas('flash.type', 'error');
    }

    public function test_destroy_handles_errors(): void
    {
        $artist = Artist::factory()->create();
        $submission = SongSubmission::factory()->for($artist)->for($this->admin, 'user')->create();

        $this->mock(SongSubmissionService::class, function ($mock): void {
            $mock->shouldReceive('destroy')
                ->once()
                ->andThrow(new \Exception('Database error'));
        });

        $this->actingAs($this->admin)->delete(route('song-submissions.destroy', $submission))
            ->assertRedirectBack()
            ->assertSessionHas('flash.message')
            ->assertSessionHas('flash.type', 'error');
    }
}
