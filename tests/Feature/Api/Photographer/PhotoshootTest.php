<?php

namespace Tests\Feature\Api\Photographer;

use App\Enums\AssetStatus;
use App\Models\Photoshoot;
use App\Models\PhotoshootAsset;
use App\Models\PhotoshootLocation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhotoshootTest extends TestCase
{
    protected $customer_role;
    protected $photographer_role;
    protected $photoshoot_location;
    protected $customer;
    protected $photographer;

    public function test_unauthenticated_photographer_cannot_access_the_resource()
    {
        $response = $this->getJson('/api/photographers/photoshoots');
        $response->assertStatus(401);
        $response->assertJsonFragment([
            'message' => 'Unauthenticated.'
        ]);
    }

    public function test_user_without_photographer_role_cannot_access_resource()
    {
        $user = $this->setUpUser();
        $response = $this->actingAs($user)->getJson('/api/photographers/photoshoots');
        $response->assertStatus(403);
        $response->assertJsonFragment([
            'message' => 'User does not have the right access'
        ]);
    }

    public function test_user_with_the_photographer_role_and_logged_in_can_access_the_resource()
    {
        $user = $this->setUpPhotographerUser();
        $this->setupDependencies($user);
        $photoshoot = Photoshoot::factory()->create([
            'photoshoot_location_id' => $this->photoshoot_location->id,
            'photographer_id' => $user->id,
            'customer_id' => $this->customer->id
        ]);
        $response = $this->actingAs($user)->getJson('/api/photographers/photoshoots');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'product' => $photoshoot->product
        ]);
    }

    public function test_photographer_can_upload_assets()
    {
        $user = $this->setUpPhotographerUser();
        $this->setupDependencies($user);
        $photoshoot = Photoshoot::factory()->create([
            'photoshoot_location_id' => $this->photoshoot_location->id,
            'photographer_id' => $user->id,
            'customer_id' => $this->customer->id,
            'number_of_shots' => 2
        ]);
        $data = [
            'images' => [UploadedFile::fake()->image('random.jpg'), UploadedFile::fake()->image('random1.jpg')]
        ];
        $response = $this->actingAs($user)->postJson("/api/photographers/photoshoots/{$photoshoot->id}/assets", $data);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => AssetStatus::PENDING
        ]);
        $asset = PhotoshootAsset::first();
        $full_link = $asset->asset_link;
        $thumbnail_link = $asset->thumbnail_link;
        Storage::disk('talentql')->assertExists($full_link);
        Storage::disk('talentql')->assertExists($thumbnail_link);
    }

    public function test_photographer_can_update_an_asset_after_rejected()
    {
        $user = $this->setUpPhotographerUser();
        $this->setupDependencies($user);
        $photoshoot = Photoshoot::factory()
            ->create([
                'photoshoot_location_id' => $this->photoshoot_location->id,
                'photographer_id' => $user->id,
                'customer_id' => $this->customer->id
            ]);
        $data = [
            'image' => UploadedFile::fake()->image('random.jpg')
        ];
        $assets = PhotoshootAsset::factory()->count(3)->create([
            'photoshoot_id' => $photoshoot->id,
            'status' => AssetStatus::REJECTED,
            'asset_link' => '/images/ihdhrbdhed.png',
            'thumbnail_link' => '/thumbnail/ihdhrbdhed.png'
        ]);
        $first_asset = $assets->first();
        $response = $this->actingAs($user)->postJson("/api/photographers/photoshoots/{$photoshoot->id}/photoshoot-assets/{$first_asset->id}", $data);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => AssetStatus::PENDING
        ]);
        $asset = PhotoshootAsset::first();
        $full_link = $asset->asset_link;
        $thumbnail_link = $asset->thumbnail_link;
        Storage::disk('talentql')->assertExists($full_link);
        Storage::disk('talentql')->assertExists($thumbnail_link);

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
