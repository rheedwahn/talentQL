<?php

namespace Tests\Feature\Api\Admin;

use App\Models\Photoshoot;
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

    public function test_unauthenticated_user_cannot_access_the_resource()
    {
        $response = $this->getJson('/api/admins/photoshoots');
        $response->assertStatus(401);
        $response->assertJsonFragment([
            'message' => 'Unauthenticated.'
        ]);
    }

    public function test_user_with_the_wrong_role_cannot_access_the_resource()
    {
        $user = $this->setUpUser();
        $response = $this->actingAs($user)->getJson('/api/admins/photoshoots');
        $response->assertStatus(403);
        $response->assertJsonFragment([
            'message' => 'User does not have the right access'
        ]);
    }

    public function test_user_with_the_right_role_and_logged_in_can_access_the_resource()
    {
        $user = $this->setUpAdminUser();
        $this->setupDependencies();
        $photoshoot = Photoshoot::factory()->create([
            'photoshoot_location_id' => $this->photoshoot_location->id,
            'photographer_id' => $this->photographer->id,
            'customer_id' => $this->customer->id
        ]);
        $response = $this->actingAs($user)->getJson('/api/admins/photoshoots');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'product' => $photoshoot->product
        ]);
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
