<?php

namespace Tests\Feature\Api\Customer;

use App\Enums\AssetStatus;
use App\Jobs\NewPhotoshootRequestJob;
use App\Models\Photoshoot;
use App\Models\PhotoshootAsset;
use App\Models\PhotoshootLocation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PhotoshootTest extends TestCase
{
    protected $customer_role;
    protected $photographer_role;
    protected $photoshoot_location;
    protected $customer;
    protected $photographer;

    public function test_unauthenticated_customer_cannot_access_the_resource()
    {
        $response = $this->getJson('/api/customers/photoshoots');
        $response->assertStatus(401);
        $response->assertJsonFragment([
            'message' => 'Unauthenticated.'
        ]);
    }

    public function test_user_without_customer_role_cannot_access_resource()
    {
        $user = $this->setUpAdminUser();
        $response = $this->actingAs($user)->getJson('/api/customers/photoshoots');
        $response->assertStatus(403);
        $response->assertJsonFragment([
            'message' => 'User does not have the right access'
        ]);
    }

    public function test_user_with_the_right_role_and_logged_in_can_access_the_resource()
    {
        $user = $this->setUpUser();
        $this->setupDependencies($user);
        $photoshoot = Photoshoot::factory()->create([
            'photoshoot_location_id' => $this->photoshoot_location->id,
            'photographer_id' => $this->photographer->id,
            'customer_id' => $this->customer->id
        ]);
        $response = $this->actingAs($user)->getJson('/api/customers/photoshoots');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'product' => $photoshoot->product
        ]);
    }

    public function test_customers_can_only_access_their_photoshoot()
    {
        $user = $this->setUpUser();
        $this->setupDependencies();
        Photoshoot::factory()->create([
            'photoshoot_location_id' => $this->photoshoot_location->id,
            'photographer_id' => $this->photographer->id,
            'customer_id' => $this->customer->id
        ]);
        $response = $this->actingAs($user)->getJson('/api/customers/photoshoots');
        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');
    }

    public function test_customer_can_make_request_for_photoshoot()
    {
        $user = $this->setUpUser();
        $this->setupDependencies();
        $data = [
            'location_id' => $this->photoshoot_location->id,
            'product' => 'Test Product',
            'description' => 'Test description',
            'company' => 'Test Company',
            'photographer_id' => $this->photographer->id,
            'number_of_shots' => 2
        ];
        $response = $this->actingAs($user)->postJson('/api/customers/photoshoots', $data);
        $response->assertStatus(201);
        $response->assertJsonFragment([
            'product' => $data['product']
        ]);
    }

    public function test_photographer_can_get_notification_for_photoshoot()
    {
        Queue::fake();
        $user = $this->setUpUser();
        $this->setupDependencies();
        $data = [
            'location_id' => $this->photoshoot_location->id,
            'product' => 'Test Product',
            'description' => 'Test description',
            'company' => 'Test Company',
            'photographer_id' => $this->photographer->id,
            'number_of_shots' => 2
        ];
        $response = $this->actingAs($user)->postJson('/api/customers/photoshoots', $data);
        $response->assertStatus(201);
        Queue::assertPushed(NewPhotoshootRequestJob::class);
        $response->assertJsonFragment([
            'product' => $data['product']
        ]);
    }

    public function test_customers_that_doesnot_owns_the_photoshoot_request_can_not_update_it()
    {
        $user = $this->setUpUser();
        $this->setupDependencies();
        $photoshoot = Photoshoot::factory()->create([
            'photoshoot_location_id' => $this->photoshoot_location->id,
            'photographer_id' => $this->photographer->id,
            'customer_id' => $this->customer->id
        ]);
        $data = ['product' => 'Updated product'];
        $response = $this->actingAs($user)->patchJson("/api/customers/photoshoots/{$photoshoot->id}", $data);
        $response->assertStatus(403);
        $response->assertSeeText('You are not allowed to update this resource');
    }

    public function test_customers_that_owns_the_photoshoot_request_can_update_it()
    {
        $user = $this->setUpUser();
        $this->setupDependencies($user);
        $photoshoot = Photoshoot::factory()->create([
            'photoshoot_location_id' => $this->photoshoot_location->id,
            'photographer_id' => $this->photographer->id,
            'customer_id' => $this->customer->id
        ]);
        $data = ['product' => 'Updated product'];
        $response = $this->actingAs($user)->patchJson("/api/customers/photoshoots/{$photoshoot->id}", $data);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'product' => $data['product']
        ]);
    }

    public function test_customers_cannot_update_photoshoot_when_photographer_has_uploaded_asset()
    {
        $user = $this->setUpUser();
        $this->setupDependencies($user);
        $photoshoot = Photoshoot::factory()
            ->has(PhotoshootAsset::factory()->count(3), 'photoshoot_assets')
            ->create([
                'photoshoot_location_id' => $this->photoshoot_location->id,
                'photographer_id' => $this->photographer->id,
                'customer_id' => $this->customer->id
            ]);
        $data = ['product' => 'Updated product'];
        $response = $this->actingAs($user)->patchJson("/api/customers/photoshoots/{$photoshoot->id}", $data);
        $response->assertStatus(403);
        $response->assertJsonFragment([
            'message' => 'Your photo shoot is already in progress'
        ]);
    }

    public function test_customer_can_approve_a_photoshoot_asset()
    {
        $user = $this->setUpUser();
        $this->setupDependencies($user);
        $photoshoot = Photoshoot::factory()
            ->create([
                'photoshoot_location_id' => $this->photoshoot_location->id,
                'photographer_id' => $this->photographer->id,
                'customer_id' => $this->customer->id
            ]);
        $assets = PhotoshootAsset::factory()->count(3)->create(['photoshoot_id' => $photoshoot->id]);
        $data = ['status' => AssetStatus::APPROVED];
        $first_asset = $assets->first();
        $response = $this->actingAs($user)->patchJson("/api/customers/photoshoots/{$photoshoot->id}/photoshoot-assets/{$first_asset->id}", $data);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => AssetStatus::APPROVED
        ]);
    }

    public function test_customer_can_reject_a_photoshoot_asset()
    {
        $user = $this->setUpUser();
        $this->setupDependencies($user);
        $photoshoot = Photoshoot::factory()
            ->create([
                'photoshoot_location_id' => $this->photoshoot_location->id,
                'photographer_id' => $this->photographer->id,
                'customer_id' => $this->customer->id
            ]);
        $assets = PhotoshootAsset::factory()->count(3)->create(['photoshoot_id' => $photoshoot->id]);
        $data = ['status' => AssetStatus::REJECTED];
        $first_asset = $assets->first();
        $response = $this->actingAs($user)->patchJson("/api/customers/photoshoots/{$photoshoot->id}/photoshoot-assets/{$first_asset->id}", $data);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => AssetStatus::REJECTED
        ]);
    }

    public function test_customer_can_on_rejecting_an_approved_asset_remains_approved()
    {
        $user = $this->setUpUser();
        $this->setupDependencies($user);
        $photoshoot = Photoshoot::factory()
            ->create([
                'photoshoot_location_id' => $this->photoshoot_location->id,
                'photographer_id' => $this->photographer->id,
                'customer_id' => $this->customer->id
            ]);
        $assets = PhotoshootAsset::factory()->count(3)->create(['photoshoot_id' => $photoshoot->id, 'status' => AssetStatus::APPROVED]);
        $data = ['status' => AssetStatus::REJECTED];
        $first_asset = $assets->first();
        $response = $this->actingAs($user)->patchJson("/api/customers/photoshoots/{$photoshoot->id}/photoshoot-assets/{$first_asset->id}", $data);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => AssetStatus::APPROVED
        ]);
    }

    protected function setupDependencies($user = null)
    {
        $this->setUp();
        $this->customer_role = Role::where('name', \App\Enums\Role::CUSTOMER)->first();
        $this->photographer_role = Role::where('name', \App\Enums\Role::PHOTOGRAPHER)->first();
        $this->customer = $user ? $user : User::where('role_id', $this->customer_role->id)->first();
        $this->photoshoot_location = PhotoshootLocation::first();
        $this->photographer = User::where('role_id', $this->photographer_role->id)->first();
    }
}
