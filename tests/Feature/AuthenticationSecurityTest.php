<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_weak_registration_password_is_rejected(): void
    {
        $this->from(route('register'))
            ->post(route('register.store'), [
                'name' => 'Example User',
                'email' => 'example@example.com',
                'password' => 'short',
                'password_confirmation' => 'short',
            ])
            ->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', ['email' => 'example@example.com']);
    }

    public function test_password_confirmation_is_required(): void
    {
        $this->from(route('register'))
            ->post(route('register.store'), [
                'name' => 'Example User',
                'email' => 'example@example.com',
                'password' => 'StrongPassword!2026',
                'password_confirmation' => 'DifferentPassword!2026',
            ])
            ->assertSessionHasErrors('password');
    }

    public function test_valid_registration_hashes_password_and_authenticates_user(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Example User',
            'email' => 'example@example.com',
            'password' => 'StrongPassword!2026',
            'password_confirmation' => 'StrongPassword!2026',
        ])->assertRedirect(route('listing.index'));

        $user = User::query()->where('email', 'example@example.com')->firstOrFail();
        $this->assertNotSame('StrongPassword!2026', $user->password);
        $this->assertAuthenticatedAs($user);
    }

    public function test_authenticated_users_cannot_reopen_guest_authentication_pages(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('login'))
            ->assertRedirect(route('listing.index'));

        $this->actingAs($user)
            ->get(route('register'))
            ->assertRedirect(route('listing.index'));
    }
}
