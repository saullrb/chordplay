<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\User as SocialUser;
use Mockery;
use Socialite;
use Tests\TestCase;

class SocialAuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_google_callback_redirects_to_login_with_error_when_no_email(): void
    {
        $socialUser = Mockery::mock(SocialUser::class);
        $socialUser->shouldReceive('getEmail')->andReturn(null);

        Socialite::shouldReceive('driver')->with('google')->andReturnSelf();
        Socialite::shouldReceive('user')->andReturn($socialUser);

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('flash.message');
        $response->assertSessionHas('flash.type', 'error');
    }

    public function test_google_callback_creates_new_user_and_logs_in(): void
    {
        $socialUser = Mockery::mock(SocialUser::class);
        $socialUser->shouldReceive('getEmail')->andReturn('test@example.com');
        $socialUser->shouldReceive('getName')->andReturn('Test User');
        $avatarUrl = fake()->imageUrl();
        $socialUser->shouldReceive('getAvatar')->andReturn($avatarUrl);

        Socialite::shouldReceive('driver')->with('google')->andReturnSelf();
        Socialite::shouldReceive('user')->andReturn($socialUser);

        Auth::shouldReceive('login')->once();

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'avatar_url' => $avatarUrl,
        ]);
    }

    public function test_google_callback_logs_in_existing_user(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        $socialUser = Mockery::mock(SocialUser::class);
        $socialUser->shouldReceive('getEmail')->andReturn('test@example.com');
        $socialUser->shouldReceive('getName')->andReturn('Test User');

        Socialite::shouldReceive('driver')->with('google')->andReturnSelf();
        Socialite::shouldReceive('user')->andReturn($socialUser);

        Auth::shouldReceive('login')->withArgs(fn ($arg): bool => $arg->id === $user->id)->once();

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseCount('users', 1);
    }

    public function test_google_callback_handles_exception_and_logs_error(): void
    {
        Socialite::shouldReceive('driver')->with('google')->andReturnSelf();
        Socialite::shouldReceive('user')->andThrow(new \Exception('OAuth error'));

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('flash.message');
        $response->assertSessionHas('flash.type', 'error');
    }
}
