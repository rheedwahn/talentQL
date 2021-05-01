<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RolesTableSeeder::class);
        User::factory()
            ->count(10)
            ->state(new Sequence(
                function () {
                    return ['role_id' => Role::all()->random()];
                }
            ))
            ->create();
        $this->call(PhotoshootLocationsTableSeeder::class);
    }
}
