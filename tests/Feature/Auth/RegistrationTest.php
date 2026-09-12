<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/');
    }

    public function test_first_registered_user_becomes_admin(): void
    {
        $this->post('/register', [
            'name' => 'First Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::first();
        $this->assertNotNull($user);
        $this->assertTrue($user->isAdmin());
        $this->assertTrue((bool) $user->status);
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_second_registered_user_becomes_staff_with_view_permissions(): void
    {
        User::factory()->create(['role' => 'admin']);

        $this->post('/register', [
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'staff@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->isStaff());
        $this->assertFalse($user->isAdmin());
        $this->assertContains('dashboard.view', $user->permissions ?? []);
        $this->assertContains('products.view', $user->permissions ?? []);
    }
}
