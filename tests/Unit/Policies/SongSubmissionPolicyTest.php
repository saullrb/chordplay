<?php

namespace Tests\Unit\Policies;

use App\Models\Artist;
use App\Models\SongSubmission;
use App\Models\User;
use App\Policies\SongSubmissionPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SongSubmissionPolicyTest extends TestCase
{
    use RefreshDatabase;

    private SongSubmissionPolicy $policy;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new SongSubmissionPolicy;
    }

    public function test_only_author_or_admin_can_view_submission(): void
    {
        $regularUser = User::factory()->create();
        $author = User::factory()->create();
        $admin = User::factory()->admin()->create();

        $artist = Artist::factory()->create();
        $songSubmission = SongSubmission::factory()->create([
            'artist_id' => $artist->id,
            'user_id' => $author->id,
        ]);

        $this->assertFalse($this->policy->view($regularUser, $songSubmission));
        $this->assertTrue($this->policy->view($author, $songSubmission));
        $this->assertTrue($this->policy->view($admin, $songSubmission));
    }

    public function test_only_author_or_admin_can_update_submission(): void
    {
        $regularUser = User::factory()->create();
        $author = User::factory()->create();
        $admin = User::factory()->admin()->create();

        $artist = Artist::factory()->create();
        $songSubmission = SongSubmission::factory()->create([
            'artist_id' => $artist->id,
            'user_id' => $author->id,
        ]);

        $this->assertFalse($this->policy->update($regularUser, $songSubmission));
        $this->assertTrue($this->policy->update($author, $songSubmission));
        $this->assertTrue($this->policy->update($admin, $songSubmission));
    }

    public function test_only_author_or_admin_can_delete_submission(): void
    {
        $regularUser = User::factory()->create();
        $author = User::factory()->create();
        $admin = User::factory()->admin()->create();

        $artist = Artist::factory()->create();
        $songSubmission = SongSubmission::factory()->create([
            'artist_id' => $artist->id,
            'user_id' => $author->id,
        ]);

        $this->assertFalse($this->policy->delete($regularUser, $songSubmission));
        $this->assertTrue($this->policy->delete($author, $songSubmission));
        $this->assertTrue($this->policy->delete($admin, $songSubmission));
    }

    public function test_only_admin_can_approve_submission(): void
    {
        $regularUser = User::factory()->create();
        $author = User::factory()->create();
        $admin = User::factory()->admin()->create();

        $artist = Artist::factory()->create();
        SongSubmission::factory()->create([
            'artist_id' => $artist->id,
            'user_id' => $author->id,
        ]);

        $this->assertFalse($this->policy->approve($regularUser));
        $this->assertFalse($this->policy->approve($author));
        $this->assertTrue($this->policy->approve($admin));
    }
}
