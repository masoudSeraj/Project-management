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
        Role::create(['name' => 'admin']);
        $user->assignRole('admin');

        $this->actingAs($user)->get('admin/permission')->assertStatus(200);
    }

    public function test_not_admin_can_not_visit_permissions_page()
    {
        $user = User::factory()->create();
        Role::create(['name' => 'user']);
        $user->assignRole('user');

        $this->actingAs($user)->get('admin/permission')->assertRedirect(route('home'));
    }

    public function test_admin_can_add_new_permission()
    {
        $user = User::factory()->create();
        Role::create(['name' => 'admin']);
        $user->assignRole('admin');

        Permission::create(['name' => 'creation', 'guard_name' => 'web']);

        $this->assertDatabaseHas('permissions', ['name' => 'creation', 'guard_name' => 'web']);
    }

    public function test_permissions_are_displayed_in_page()
    {
        $user = User::factory()->create();
        Role::create(['name' => 'admin']);
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

    public function test_permission_can_be_edited()
    {
        $user = User::factory()->create();
        Role::create(['name' => 'admin']);
        $user->assignRole('admin');

        $permission = Permission::create(['name' => 'creation', 'guard_name' => 'web']);
        Permission::find($permission->id)->update(['name' => 'deletion']);
        $this->assertDatabaseHas('permissions',  ['name' => 'deletion']);
    }
}
