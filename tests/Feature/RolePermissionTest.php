<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function admin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'status' => true,
            'email_verified_at' => now(),
            'permissions' => [],
        ]);
    }

    protected function staff(array $permissions = [], bool $active = true): User
    {
        return User::factory()->create([
            'role' => 'staff',
            'status' => $active,
            'email_verified_at' => now(),
            'permissions' => $permissions,
        ]);
    }

    public function test_admin_can_access_staff_management(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->get('/staff');

        $response->assertStatus(200);
    }

    public function test_staff_cannot_access_staff_management(): void
    {
        $staff = $this->staff();

        $response = $this->actingAs($staff)->get('/staff');

        $response->assertStatus(403);
    }

    public function test_staff_with_view_permission_can_browse_products(): void
    {
        $staff = $this->staff(['products.view']);

        $response = $this->actingAs($staff)->get('/products');

        $response->assertStatus(200);
    }

    public function test_staff_without_view_permission_cannot_browse_products(): void
    {
        $staff = $this->staff([]);

        $response = $this->actingAs($staff)->get('/products');

        $response->assertStatus(403);
    }

    public function test_staff_with_add_permission_can_create_product(): void
    {
        $staff = $this->staff(['products.add']);

        $response = $this->actingAs($staff)->get('/products/create');

        $response->assertStatus(200);
    }

    public function test_staff_without_add_permission_cannot_create_product(): void
    {
        $staff = $this->staff([]);

        $response = $this->actingAs($staff)->get('/products/create');

        $response->assertStatus(403);
    }

    public function test_staff_with_edit_permission_can_edit_product(): void
    {
        $staff = $this->staff(['products.edit']);
        $product = Product::factory()->create();

        $response = $this->actingAs($staff)->get('/products/'.$product->id.'/edit');

        $response->assertStatus(200);
    }

    public function test_staff_without_edit_permission_cannot_edit_product(): void
    {
        $staff = $this->staff([]);
        $product = Product::factory()->create();

        $response = $this->actingAs($staff)->get('/products/'.$product->id.'/edit');

        $response->assertStatus(403);
    }

    public function test_staff_with_delete_permission_can_delete_product(): void
    {
        $staff = $this->staff(['products.delete']);
        $product = Product::factory()->create();

        $response = $this->actingAs($staff)->delete('/products/'.$product->id);

        $response->assertRedirect(route('products.index'));
    }

    public function test_staff_without_delete_permission_cannot_delete_product(): void
    {
        $staff = $this->staff([]);
        $product = Product::factory()->create();

        $response = $this->actingAs($staff)->delete('/products/'.$product->id);

        $response->assertStatus(403);
    }

    public function test_inactive_staff_cannot_login(): void
    {
        $staff = $this->staff([], active: false);

        $response = $this->post('/login', [
            'name' => $staff->name,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    public function test_admin_bypasses_permission_checks(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->get('/products/create');

        $response->assertStatus(200);
    }

    public function test_admin_can_create_admin_via_staff_form(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->post('/staff', [
            'name' => 'New Admin',
            'email' => 'newadmin@example.com',
            'phone' => '0123456789',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'admin',
            'status' => '1',
            'permissions' => [],
        ]);

        $response->assertRedirect(route('staff.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'newadmin@example.com',
            'role' => 'admin',
        ]);
    }

    public function test_admin_can_promote_staff_to_admin(): void
    {
        $admin = $this->admin();
        $staff = $this->staff(['products.view']);

        $response = $this->actingAs($admin)->put('/staff/'.$staff->id, [
            'name' => $staff->name,
            'email' => $staff->email,
            'phone' => $staff->phone,
            'role' => 'admin',
            'status' => '1',
            'permissions' => [],
        ]);

        $response->assertRedirect(route('staff.index'));

        $staff->refresh();
        $this->assertTrue($staff->isAdmin());
    }
}
