<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\Role;
use App\Models\User;
use App\Policies\ArtistPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArtistPolicyTest extends TestCase
{
    use RefreshDatabase;

    private ArtistPolicy $policy;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new ArtistPolicy;
    }

    public function test_regular_users_cannot_store(): void
    {
        $user = User::factory()->create(['role_id' => Role::USER]);
        $this->assertFalse($this->policy->store($user));
    }

    public function test_admin_can_store(): void
    {
        $admin = User::factory()->create(['role_id' => Role::ADMIN]);
        $this->assertTrue($this->policy->store($admin));
    }
}
