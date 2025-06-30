<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('profile.edit'));

        $response->assertOk();
    }

    public function test_user_can_update_name_with_valid_input(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => 'John Doe',
            ])->assertRedirect(route('profile.edit'))
            ->assertSessionHas('flash.message')
            ->assertSessionHas('flash.type', 'success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'John Doe',
        ]);
    }

    public function test_user_cannot_update_name_with_invalid_input(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => '',
            ])->assertRedirectBack()
            ->assertSessionHasErrors(['name']);

        $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => str_repeat('a', 256),
            ])->assertRedirectBack()
            ->assertSessionHasErrors(['name']);
    }

    public function test_update_handles_errors(): void
    {
        $user = User::factory()->create();

        $this->mock(UserService::class, function ($mock): void {
            $mock->shouldReceive('update')
                ->once()
                ->andThrow(new \Exception('Database error'));
        });

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => 'New Name',
        ])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHas('flash.message')
            ->assertSessionHas('flash.type', 'error');
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->delete(route('profile.destroy'));

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_delete_handles_errors(): void
    {
        $user = User::factory()->create();

        $this->mock(UserService::class, function ($mock): void {
            $mock->shouldReceive('destroy')
                ->once()
                ->andThrow(new \Exception('Database error'));
        });

        $this->actingAs($user)->delete(route('profile.destroy'))
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHas('flash.message')
            ->assertSessionHas('flash.type', 'error');
    }
}
