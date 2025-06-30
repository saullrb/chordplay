<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticatedSessionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_guest_can_access_login_page(): void
    {
        $user = User::factory()->create();

        $this->get('/login')->assertOk();

        $this->actingAs($user)
            ->get('/login')
            ->assertRedirect(route('dashboard'));
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/');

        $this->assertGuest();
    }
}
