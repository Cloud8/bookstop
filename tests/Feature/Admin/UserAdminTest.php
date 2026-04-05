<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAdminTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Access control
    // -------------------------------------------------------------------------

    public function test_non_admin_cannot_access_users_index(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/admin/users')->assertStatus(404);
    }

    public function test_guest_cannot_access_users_index(): void
    {
        $this->get('/admin/users')->assertRedirect('/login');
    }

    // -------------------------------------------------------------------------
    // Index
    // -------------------------------------------------------------------------

    public function test_admin_can_view_users_list(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->count(3)->create();

        $this->actingAs($admin)->get('/admin/users')->assertStatus(200);
    }

    public function test_admin_can_search_users_by_email(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['email' => 'findme@example.com']);
        User::factory()->create(['email' => 'other@example.com']);

        $response = $this->actingAs($admin)->get('/admin/users?search=findme');

        $response->assertStatus(200);

        $users = $response->viewData('users');
        $this->assertCount(1, $users);
        $this->assertEquals('findme@example.com', $users->first()->email);
    }

    // -------------------------------------------------------------------------
    // Show
    // -------------------------------------------------------------------------

    public function test_admin_can_view_user_detail(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $this->actingAs($admin)->get("/admin/users/{$user->id}")->assertStatus(200);
    }

    // -------------------------------------------------------------------------
    // Ban
    // -------------------------------------------------------------------------

    public function test_admin_can_ban_a_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $this->actingAs($admin)
            ->patch("/admin/users/{$user->id}/ban")
            ->assertRedirect();

        $this->assertNotNull($user->fresh()->banned_at);
    }

    public function test_admin_cannot_ban_another_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $otherAdmin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->patch("/admin/users/{$otherAdmin->id}/ban");

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertNull($otherAdmin->fresh()->banned_at);
    }

    public function test_admin_cannot_ban_themselves(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->patch("/admin/users/{$admin->id}/ban");

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertNull($admin->fresh()->banned_at);
    }

    // -------------------------------------------------------------------------
    // Unban
    // -------------------------------------------------------------------------

    public function test_admin_can_unban_a_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->banned()->create();

        $this->actingAs($admin)
            ->patch("/admin/users/{$user->id}/unban")
            ->assertRedirect();

        $this->assertNull($user->fresh()->banned_at);
    }

    // -------------------------------------------------------------------------
    // Send password reset
    // -------------------------------------------------------------------------

    public function test_admin_can_send_password_reset_link(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        // Password::sendResetLink enqueues a notification; we just verify the
        // action completes without error and flashes a success message.
        $response = $this->actingAs($admin)
            ->post("/admin/users/{$user->id}/send-password-reset");

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    // -------------------------------------------------------------------------
    // Verify email
    // -------------------------------------------------------------------------

    public function test_admin_can_verify_user_email(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->unverified()->create();

        $this->assertNull($user->email_verified_at);

        $this->actingAs($admin)
            ->post("/admin/users/{$user->id}/verify-email")
            ->assertRedirect();

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_banned_user_receives_403_on_web_route(): void
    {
        $user = User::factory()->banned()->create();

        $this->actingAs($user)->get(route('home'))->assertForbidden();
    }

    public function test_verify_email_is_idempotent_for_already_verified_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create(); // already verified by default

        $verifiedAt = $user->email_verified_at;

        $this->actingAs($admin)
            ->post("/admin/users/{$user->id}/verify-email")
            ->assertRedirect();

        // Timestamp should not change
        $this->assertEquals(
            $verifiedAt->timestamp,
            $user->fresh()->email_verified_at->timestamp
        );
    }
}
