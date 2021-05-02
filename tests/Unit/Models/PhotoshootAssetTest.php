<?php

namespace Tests\Unit\Models;

use App\Models\Photoshoot;
use App\Models\PhotoshootAsset;
use App\Models\PhotoshootLocation;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class PhotoshootAssetTest extends TestCase
{
    protected $customer_role;
    protected $photographer_role;
    protected $photoshoot_location;
    protected $customer;
    protected $photographer;

    public function test_it_belongs_to_a_photoshoot()
    {
        $this->setupDependencies();
        $photoshoot = Photoshoot::factory()->create([
            'photoshoot_location_id' => $this->photoshoot_location->id,
            'photographer_id' => $this->photographer->id,
            'customer_id' => $this->customer->id
        ]);
        $assets = PhotoshootAsset::factory()
                    ->count(3)
                    ->for($photoshoot)
                    ->create();
        $this->assertEquals($photoshoot->id, $assets->first()->photoshoot->id);
    }

    protected function setupDependencies()
    {
        $this->setUp();
        $this->customer_role = Role::where('name', \App\Enums\Role::CUSTOMER)->first();
        $this->photographer_role = Role::where('name', \App\Enums\Role::PHOTOGRAPHER)->first();
        $this->customer = User::where('role_id', $this->customer_role->id)->first();
        $this->photoshoot_location = PhotoshootLocation::first();
        $this->photographer = User::where('role_id', $this->photographer_role->id)->first();
    }
}
