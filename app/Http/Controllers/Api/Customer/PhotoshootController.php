<?php

namespace App\Http\Controllers\Api\Customer;

use App\Enums\AssetStatus;
use App\Enums\PhotoshootTrackerName;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Customer\Asset\UpdateStatusRequest;
use App\Http\Requests\Api\Customer\Photoshoot\ListRequest;
use App\Http\Requests\Api\Customer\Photoshoot\StoreRequest;
use App\Http\Requests\Api\Customer\Photoshoot\UpdateRequest;
use App\Http\Resources\Api\Customer\PhotoshootResource;
use App\Jobs\NewPhotoshootRequestJob;
use App\Models\Photoshoot;
use App\Models\PhotoshootAsset;
use App\Models\User;
use App\Services\Photoshoots\Customers\StoreService;
use App\Services\Photoshoots\Customers\UpdateService;

class PhotoshootController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware(Role::CUSTOMER);
    }

    public function lists(ListRequest $request)
    {
        $photos = Photoshoot::where('customer_id', $request->user()->id)->orderBy('created_at', 'desc')->paginate(20);
        return PhotoshootResource::collection($photos);
    }

    public function store(StoreRequest $request)
    {
        $photographer = User::where('id', $request->photographer_id)->first();
        $photoshoot = (new StoreService($request->all()))->run();
        NewPhotoshootRequestJob::dispatch($photographer);
        return new PhotoshootResource($photoshoot);
    }

    public function update(UpdateRequest $request, Photoshoot $photoshoot)
    {
        $this->authorize('customerUpdate', $photoshoot);
        if($photoshoot->photoshoot_assets->count() === 0) {
            (new UpdateService($photoshoot, $request->all()))->run();
            return new PhotoshootResource(Photoshoot::findOrFail($photoshoot->id));
        }
        return response()->json(['status' => 'error', 'message' => 'Your photo shoot is already in progress'], 403);
    }

    public function updateAssetStatus(UpdateStatusRequest $request, Photoshoot $photoshoot, PhotoshootAsset $photoshootAsset)
    {
        $this->authorize('updateAssetStatus', $photoshoot);
        if($photoshootAsset->status !== AssetStatus::APPROVED) {
            $photoshootAsset->status = $request->status;
            $photoshootAsset->save();
        }
        return new PhotoshootResource(Photoshoot::findOrFail($photoshoot->id));
    }

    public function delete(Photoshoot $photoshoot)
    {
        $photoshoot->delete();
        return $this->deleteResource();
    }
}
