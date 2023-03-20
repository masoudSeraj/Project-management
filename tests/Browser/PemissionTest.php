<?php

namespace Tests\Browser;

use App\Models\Role;
use App\Models\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PemissionTest extends DuskTestCase
{
    use DatabaseMigrations;
    /**
     * A Dusk test example.
     */
    public function test_click_on_add_permission(): void
    {
        $user = User::factory()->create();
        Role::create(['name' => 'admin']);
        $user->assignRole('admin');

        $this->browse(function (Browser $browser) use($user){
            $browser
                ->loginAs($user)
                ->visit(route('permission.index'))
                ->waitUntilMissing('#nprogress')
                ->pause(2000)
                ->assertSee('Search...');
        });
    }
}
