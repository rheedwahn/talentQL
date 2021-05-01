<?php

namespace Database\Seeders;

use App\Models\PhotoshootLocation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhotoshootLocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('photoshoot_locations')->truncate();

        PhotoshootLocation::factory()->count(5)->create();
    }
}
