<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Factories\Sequence;

class RolesTest extends TestCase
{
    use RefreshDatabase;
    use HasRoles;

    public function test_admin_can_visit_roles_page()
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'admin', 'is_admin' => 1]);
        $user->assignRole([$role->name]);

        $this->actingAs($user)->get('admin/role')->assertStatus(200);
    }

    public function test_not_admin_can_not_visit_roles_page()
    {
        $user = User::factory()->create();
        Role::create(['name' => 'user', 'is_admin' => 1]);
        $user->assignRole('user');

        $this->actingAs($user)->get('admin/role')->assertRedirect(route('home'));
    }

    public function test_admin_can_create_new_role_without_assigning_permission()
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'admin', 'is_admin' => 1]);
        $user->assignRole($role->name);

        $params = ['roleName' => 'administrator', 'isAdmin' => 1];
        $response = $this->actingAs($user)->post(route('role.store', $params));

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Role successfully added'
        ]);
        $this->assertDatabaseHas('roles', ['name' => 'administrator', 'is_admin' => 1]);
    }
    public function test_admin_can_create_new_role_with_assigning_permissions()
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'admin', 'is_admin' => 1]);
        $user->assignRole($role->name);

        $permissions = Permission::factory()->state(
            new Sequence(
                ['name' => 'perm1'],
                ['name' => 'perm2']
            )
        )->create();

        $params = ['roleName' => 'administrator', 'isAdmin' => 1, 'selectedPermissions' => $permissions];
        $response = $this->actingAs($user)->post(route('role.store', $params));
        $response->assertStatus(200);
        // dd($response->getData()->role_id);
        $permissions->each(function ($permission) use ($response) {
            $this->assertDatabaseHas(
                'role_has_permissions',
                [
                    'permission_id' => $permission->id,
                    'role_id' => $response->getData()->roleId
                ]
            );
        });
    }

    public function test_admin_can_edit_roles_without_assigning_permissions()
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'admin', 'is_admin' => 1]);
        $user->assignRole($role->name);

        $role1 = Role::create(['name' => 'role1', 'is_admin' => 1]);
        $params = ['roleName' => 'role2', 'isAdmin' => 0];
        $response = $this->actingAs($user)->put(route('admin.role.updateRole', ['role' => $role1->id]), $params);
        $response->assertStatus(200);

        $this->assertDatabaseHas('roles', [
            'id'    => $role1->id,
            'name' => 'role2',
            'is_admin' => 0
        ]);
    }

    public function test_admin_can_edit_roles_with_assigning_permissions()
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'admin', 'is_admin' => 1]);
        $user->assignRole($role->name);

        $permissions = Permission::factory()->state(
            new Sequence(
                ['name' => 'perm1'],
                ['name' => 'perm2']
            )
        )->create();
        $role1 = Role::create(['name' => 'role1', 'is_admin' => 1]);

        $params = ['roleName' => 'role2', 'isAdmin' => 0, 'selectedPermissions' => $permissions];
        $response = $this->actingAs($user)->put(route('admin.role.updateRole', ['role' => $role1->id]), $params);
        $response->assertStatus(200);

        $this->assertDatabaseHas('roles', [
            'id'    =>  $role1->id,
            'name' => 'role2',
            'is_admin' => 0
        ]);

        $permissions->each(function ($permission) use ($response) {
            $this->assertDatabaseHas(
                'role_has_permissions',
                [
                    'permission_id' => $permission->id,
                    'role_id' => $response->getData()->roleId
                ]
            );
        });
    }


    public function test_admin_can_remove_role()
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'admin', 'is_admin' => 1]);
        $user->assignRole($role->name);

        $role1 = Role::create(['name' => 'role1', 'is_admin' => 1]);

        $permissions = Permission::factory()->state(
            new Sequence(
                ['name' => 'perm1'],
                ['name' => 'perm2']
            )
        )->create();
        
        $role1->givePermissionTo($permissions);

        $response = $this->actingAs($user)->delete(route('role.destroy', ['role' => $role1->id]));
        $response->assertStatus(200);

        $this->assertDatabaseMissing('roles', [
            'id'    => $role1->id
        ]);

        $permissions->each(function ($permission) use ($role1) {
            $this->assertDatabaseMissing(
                'role_has_permissions',
                [
                    'permission_id' => $permission->id,
                    'role_id' => $role1->id
                ]
            );
        });
    }

    public function test_roles_are_displayed_in_index_page()
    {
        $user = User::factory()->create();
        Role::create(['name' => 'admin', 'is_admin' => 1]);
        $user->assignRole('admin');

        Role::create(['name' => 'helper', 'guard_name' => 'web', 'is_admin' => 1]);
        Role::create(['name' => 'user', 'guard_name' => 'web', 'is_admin' => 0]);

        $this->actingAs($user)->get('admin/role')
            ->assertInertia(function(AssertableInertia $page) {
                $page->component('Admin/Role/Index') 
                    ->has('roles', function(AssertableInertia $page){
                        $page
                            ->has('data', 3)
                            ->has('data.0', function(AssertableInertia $page){
                                $page
                                    ->where('name', 'admin')
                                    ->where('is_admin', 1)
                                    ->etc();

                            })
                            ->has('data.1', function(AssertableInertia $page){
                                $page
                                    ->where('name', 'helper')
                                    ->where('is_admin', 1)
                                    ->etc();

                            })
                            ->has('data.2', function(AssertableInertia $page){
                                $page
                                    ->where('name', 'user')
                                    ->where('is_admin', 0)
                                    ->etc();

                            })
                            ->etc();                            
                    });
            });
    }
}