<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Inertia\Testing\Assert;
use Spatie\Permission\Models\Role;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Permission;
use Database\Factories\PermissionFactory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;
    use HasRoles;

    public function test_admin_can_visit_permissions_page()
    {
        $user = User::factory()->create();
        Role::create(['name' => 'admin', 'is_admin' => 1]);
        $user->assignRole('admin');

        $this->actingAs($user)->get('admin/permission')->assertStatus(200);
    }

    public function test_not_admin_can_not_visit_permissions_page()
    {
        $user = User::factory()->create();
        Role::create(['name' => 'user', 'is_admin' => 1]);
        $user->assignRole('user');

        $this->actingAs($user)->get('admin/permission')->assertRedirect(route('home'));
    }

    public function test_admin_can_add_new_permission()
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'admin', 'is_admin' => 1]);
        $user->assignRole($role->name);

        $params = ['permissionName' => 'perm1'];
        $response = $this->actingAs($user)->post(route('permission.store', $params));
        $response->assertStatus(200);

        $this->assertDatabaseHas('permissions', ['name' => 'perm1', 'guard_name' => 'web']);
    }

    public function test_admin_can_edit_permission()
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'admin', 'is_admin' => 1]);
        $user->assignRole($role->name);

        $permission = Permission::create(['name' => 'perm1']);
        $params = ['permissionId' => $permission->id,'permissionName' => 'perm2'];
        $response = $this->actingAs($user)->put(route('permission.update', ['permission' => $permission->id]), $params);
        $response->assertStatus(200);

        $this->assertDatabaseHas('permissions', [
            'id'    => $permission->id,
            'name'  =>  'perm2'
        ]);
    }
    public function test_admin_can_delete_permission()
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'admin', 'is_admin' => 1]);
        $user->assignRole($role->name);

        $permission = Permission::create(['name' => 'perm1']);
        $response = $this->actingAs($user)->delete(route('permission.destroy', ['permission' => $permission->id]));
        $response->assertStatus(200);
        $this->assertDatabaseMissing('permissions', ['name' => 'perm1']);
    }

    public function test_permissions_are_displayed_in_page()
    {
        $user = User::factory()->create();
        Role::create(['name' => 'admin', 'is_admin' => 1]);
        $user->assignRole('admin');

        Permission::create(['name' => 'creation', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit', 'guard_name' => 'web']);

        $this->actingAs($user)->get('admin/permission')
            ->assertInertia(function(AssertableInertia $page) {
                $page->component('Admin/Permission/Index')
                    ->has('permissions', function(AssertableInertia $page){

                        $page
                            ->has('data', 2)
                            ->has('data.0', function(AssertableInertia $page){
                                $page
                                    ->where('guard_name', 'web')
                                    ->where('name', 'creation')
                                    ->etc();
                            })
                            ->has('data.1', function(AssertableInertia $page){
                                $page
                                    ->where('guard_name', 'web')
                                    ->where('name', 'edit')
                                    ->etc();
                            })
                            ->etc();
                    });
        });
    }
}
