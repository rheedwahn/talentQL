<?php

namespace Tests\Unit\Models;

use App\Enums\AssetStatus;
use App\Models\Photoshoot;
use App\Models\PhotoshootAsset;
use App\Models\PhotoshootLocation;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class PhotoshootTest extends TestCase
{
    protected $customer_role;
    protected $photographer_role;
    protected $photoshoot_location;
    protected $customer;
    protected $photographer;

    public function test_it_can_belong_to_a_customer()
    {
        $this->setupDependencies();
        $photoshoot = Photoshoot::factory()->create([
            'photoshoot_location_id' => $this->photoshoot_location->id,
            'photographer_id' => $this->photographer->id,
            'customer_id' => $this->customer->id
        ]);
        $this->assertEquals($this->customer->id, $photoshoot->customer->id);
    }

    public function test_it_can_belong_to_a_photographer()
    {
        $this->setupDependencies();
        $photoshoot = Photoshoot::factory()->create([
            'photoshoot_location_id' => $this->photoshoot_location->id,
            'photographer_id' => $this->photographer->id,
            'customer_id' => $this->customer->id
        ]);
        $this->assertEquals($this->photographer->id, $photoshoot->photographer->id);
    }

    public function test_it_can_have_many_assets()
    {
        $this->setupDependencies();
        $photoshoot = Photoshoot::factory()
            ->has(PhotoshootAsset::factory()->count(3), 'photoshoot_assets')
            ->create([
            'photoshoot_location_id' => $this->photoshoot_location->id,
            'photographer_id' => $this->photographer->id,
            'customer_id' => $this->customer->id
        ]);
        $this->assertCount(3, $photoshoot->photoshoot_assets);
    }

    public function test_it_can_belong_to_one_location()
    {
        $this->setupDependencies();
        $photoshoot = Photoshoot::factory()->create([
            'photoshoot_location_id' => $this->photoshoot_location->id,
            'photographer_id' => $this->photographer->id,
            'customer_id' => $this->customer->id
        ]);
        $this->assertEquals($this->photoshoot_location->id, $photoshoot->photoshoot_location->id);
    }

    public function test_it_checks_customer_have_not_approved_all_assets()
    {
        $this->setupDependencies();
        $photoshoot = Photoshoot::factory()
            ->has(
                PhotoshootAsset::factory()
                            ->count(3)
                            ->state(function (array $attributes) {
                                return ['status' => AssetStatus::REJECTED];
                            }),
                'photoshoot_assets'
            )
            ->create([
                'photoshoot_location_id' => $this->photoshoot_location->id,
                'photographer_id' => $this->photographer->id,
                'customer_id' => $this->customer->id
            ]);
        $this->assertTrue($photoshoot->haveNotApprovedAllAsset());
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
