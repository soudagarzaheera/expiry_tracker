<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_receives_verification_notification(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'New Student', 'email' => 'student@example.test',
            'password' => 'College@123', 'password_confirmation' => 'College@123',
        ]);

        $user = User::where('email', 'student@example.test')->firstOrFail();
        $response->assertRedirect(route('verification.notice'));
        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Hash::check('College@123', $user->password));
        $this->assertNull($user->email_verified_at);
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_verified_active_user_can_login_and_logout(): void
    {
        $user = User::factory()->create(['password' => 'Secret@123']);

        $this->post('/login', ['email' => $user->email, 'password' => 'Secret@123'])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);

        $this->post('/logout')->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_invalid_login_and_inactive_account_show_errors(): void
    {
        $user = User::factory()->create(['password' => 'Secret@123', 'is_active' => false]);

        $this->from('/login')->post('/login', ['email' => $user->email, 'password' => 'wrong'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->from('/login')->post('/login', ['email' => $user->email, 'password' => 'Secret@123'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_login_goes_to_the_separate_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create(['password' => 'Admin@123']);

        $this->post('/login', ['email' => $admin->email, 'password' => 'Admin@123'])
            ->assertRedirect(route('admin.dashboard'));
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
    }

    public function test_unverified_user_is_blocked_until_signed_verification_link_is_opened(): void
    {
        $user = User::factory()->unverified()->create();
        $this->actingAs($user)->get(route('dashboard'))->assertRedirect(route('verification.notice'));

        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(30), [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]);

        $this->actingAs($user)->get($url)->assertRedirect(route('dashboard'));
        $this->assertNotNull($user->fresh()->email_verified_at);
    }
}
