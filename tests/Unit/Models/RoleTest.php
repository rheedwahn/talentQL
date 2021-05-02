<?php

namespace Tests\Unit\Models;

use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class RoleTest extends TestCase
{
    public function test_it_has_many_users()
    {
        $role = Role::factory()
                    ->has(User::factory()->count(5))
                    ->create(['name' => \App\Enums\Role::CUSTOMER]);
        $this->assertCount(5, $role->users);
    }
}
