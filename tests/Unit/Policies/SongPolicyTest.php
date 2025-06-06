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

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new SongPolicy;
    }

    public function test_staff_can_create()
    {
        $staff = User::factory()->create(['role_id' => Role::STAFF]);
        $this->assertTrue($this->policy->create($staff));
    }

    public function test_staff_can_update()
    {
        $staff = User::factory()->create(['role_id' => Role::STAFF]);
        $artist = Artist::factory()->create();
        $song = Song::factory()->create(['artist_id' => $artist->id]);

        $this->assertTrue($this->policy->update($staff, $song));
    }

    public function test_regular_users_cannot_create()
    {
        $user = User::factory()->create(['role_id' => Role::USER]);
        $this->assertFalse($this->policy->create($user));
    }

    public function test_regular_users_cannot_update()
    {
        $user = User::factory()->create(['role_id' => Role::USER]);
        $artist = Artist::factory()->create();
        $song = Song::factory()->create(['artist_id' => $artist->id]);

        $this->assertFalse($this->policy->update($user, $song));
    }

    public function test_admin_can_create()
    {
        $admin = User::factory()->create(['role_id' => Role::ADMIN]);
        $this->assertTrue($this->policy->create($admin));
    }

    public function test_admin_can_update()
    {
        $admin = User::factory()->create(['role_id' => Role::ADMIN]);
        $artist = Artist::factory()->create();
        $song = Song::factory()->create(['artist_id' => $artist->id]);

        $this->assertTrue($this->policy->update($admin, $song));
    }
}
