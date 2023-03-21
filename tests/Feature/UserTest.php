<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    use RefreshDatabase;
    public function test_admin_can_view_user_index()
    {
        $user = User::factory()->create();
        Role::create(['name' => 'admin', 'is_admin' => 1]);
        $user->assignRole('admin');

        $this->actingAs($user)->get(route('user.index'))->assertStatus(200);
    }

    public function test_not_admin_can_not_view_user_index()
    {
        $user = User::factory()->create();
        Role::create(['name' => 'user', 'is_admin' => 0]);
        $user->assignRole('user');

        $this->actingAs($user)->get(route('user.index'))->assertRedirect(route('home'));
    }

    public function test_admin_can_create_new_user()
    {
        $user = User::factory()->create();
        Role::create(['name' => 'admin', 'is_admin' => 0]);
        $user->assignRole('admin');

        $roles = 
        [
            ['name' => 'role1', 'is_admin' => 0],
            ['name' => 'role2', 'is_admin' => 0],
            ['name' => 'role3', 'is_admin' => 1]
        ];

        foreach($roles as $role){
            Role::create($role);
        }

        $params = [
            'name'  => 'John',
            'email' => 'JohnDoe@acme.com',
            'password'  =>  'password',
            'password_confirmation' => 'password',
            'selectedRoles' =>  ['role2', 'role3']
        ];

        $response = $this->actingAs($user)->post(route('user.store'), ['user' => $params]);

        $response->assertStatus(200);
        $user = User::find($response->getData()->userId);

        $this->assertDatabaseHas('users', ['name' => 'john']);
        $this->assertTrue(Hash::check('password', $user->password));
        $this->assertSame($params['selectedRoles'], $user->getRoleNames()->toArray());
    }

    public function test_recieve_error_on_password_confirmation_failure()
    {
        $user = User::factory()->create();
        Role::create(['name' => 'admin', 'is_admin' => 0]);
        $user->assignRole('admin');

        $params = [
            'name'  => 'John',
            'email' => 'JohnDoe@acme.com',
            'password'  =>  'password',
            'password_confirmation' => 'password2',
            'selectedRoles' =>  ['role2', 'role3']
        ];

        $response = $this->actingAs($user)->post(route('user.store'), ['user' => $params]);
        $response->assertSessionHasErrors(['user.password']);
    }

    public function test_admin_can_edit_user()
    {
        $user = User::factory()->create();
        Role::create(['name' => 'admin', 'is_admin' => 1]);
        $user->assignRole('admin');

        $role1 = Role::create(['name' => 'role1', 'is_admin' => 0]);
        $role2 = Role::create(['name' => 'role2', 'is_admin' => 0]);

        $user1 = User::factory()
            ->state([
                'name' => 'user1',
                'password' => Hash::make('pass')
                ])
            ->create();

        $user1->assignRole([$role1, $role2]);

        $response = $this->actingAs($user)
            ->put(route('user.update', ['user' => $user1->id]),
                    [
                        'name' => 'newName', 
                        'password' => 'pass2',
                        'passwordConfirm' => 'pass2',
                        'email' => 'safb@test.com',
                        'selectedRoles' => ['role1', 'role2']
                    ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['message']);
        $this->assertDatabaseHas('users', 
            [
                'id' => $user1->id, 
                'name' => 'newName',
            ]);

        $this->assertSame(['role1', 'role2'], $user1->getRoleNames()->toArray());
    }
    
    public function test_admin_can_delete_user()
    {
        $user = User::factory()->create();
        Role::create(['name' => 'admin', 'is_admin' => 1]);
        $user->assignRole('admin');

        $role1 = Role::create(['name' => 'role1', 'is_admin' => 0]);
        $role2 = Role::create(['name' => 'role2', 'is_admin' => 0]);

        $user1 = User::factory()
            ->state([
                'name' => 'user1',
                ])
            ->create();

        $user1->assignRole(['role1', 'role2']);
        // $this->assertNotSame(['role1', 'role2'], $user1->getRoleNames()->toArray());
        // dd($user1->getRoleNames()->toArray());
        $this->assertDatabaseHas('users', ['name' => 'user1']);
        $response = $this->actingAs($user)->delete(route('user.destroy', ['user' => $user1->id]));
        $response->assertStatus(200);
        $this->assertDatabaseMissing('users',['id' => $user1->id]);
        // dd($user1->getRoleNames()->toArray());

        $this->assertNotSame(['role1', 'role2'], $user1->getRoleNames()->toArray());
    }
}
