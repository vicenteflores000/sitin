<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AllowedDomain;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(string $email = 'admin@example.com'): User
    {
        AllowedDomain::create(['domain' => 'example.com']);

        return User::factory()->create([
            'role' => 'user',
            'email' => $email,
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
    }

    public function test_profile_page_is_displayed(): void
    {
        $user = $this->createUser();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertStatus(200);
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = $this->createUser();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response->assertRedirect(route('profile.edit'));

        $this->assertSame('test@example.com', $user->refresh()->email);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = $this->createUser();
        $verifiedAt = $user->email_verified_at;

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response->assertRedirect(route('profile.edit'));
        $this->assertSame($verifiedAt?->format('c'), $user->refresh()->email_verified_at?->format('c'));
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = $this->createUser();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response->assertRedirect('/');

        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = $this->createUser();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response->assertRedirect('/profile');
        $response->assertSessionHasErrors('password', null, 'userDeletion');

        $this->assertNotNull($user->fresh());
    }
}
