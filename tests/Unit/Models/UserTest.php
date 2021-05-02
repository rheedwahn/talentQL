<?php

namespace Tests\Unit\Models;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_it_belongs_to_a_role()
    {
        $role = Role::factory()->create([
            'name' => \App\Enums\Role::CUSTOMER,
        ]);
        $users = User::factory()
                ->count(3)
                ->for($role)
                ->create();

        $this->assertEquals($role->id, $users->first()->role->id);
    }

    public function test_it_hash_password_correctly()
    {
        $password = 'password';

        $role = Role::factory()->create([
            'name' => \App\Enums\Role::CUSTOMER,
        ]);

        $user = User::factory()
            ->count(1)
            ->for($role)
            ->create();
        $this->assertTrue(Hash::check($password, $user->first()->password));
    }
}
