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

}
