<?php

namespace Tests\Unit\Policies;

use App\Models\Artist;
use App\Models\Role;
use App\Models\Song;
use App\Models\User;
use App\Policies\SongPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SongPolicyTest extends TestCase
{
    use RefreshDatabase;

    private SongPolicy $policy;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new SongPolicy;
    }

    public function test_regular_users_cannot_create(): void
    {
        $user = User::factory()->create(['role_id' => Role::USER]);
        $this->assertFalse($this->policy->create($user));
    }

    public function test_regular_users_cannot_update(): void
    {
        $user = User::factory()->create(['role_id' => Role::USER]);
        $artist = Artist::factory()->create();
        Song::factory()->create(['artist_id' => $artist->id]);

        $this->assertFalse($this->policy->update($user));
    }

    public function test_admin_can_create(): void
    {
        $admin = User::factory()->create(['role_id' => Role::ADMIN]);
        $this->assertTrue($this->policy->create($admin));
    }

    public function test_admin_can_update(): void
    {
        $admin = User::factory()->create(['role_id' => Role::ADMIN]);
        $artist = Artist::factory()->create();
        Song::factory()->create(['artist_id' => $artist->id]);

        $this->assertTrue($this->policy->update($admin));
    }
}
