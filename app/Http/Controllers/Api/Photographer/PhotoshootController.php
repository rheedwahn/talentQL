<?php

namespace App\Http\Controllers\Api\Photographer;

use App\Enums\AssetStatus;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Customer\Photoshoot\ListRequest;
use App\Http\Requests\Api\Photographer\Photoshoot\UpdateAssetRequest;
use App\Http\Requests\Api\Photographer\Photoshoot\UploadRequest;
use App\Http\Resources\Api\Photographer\PhotoshootResource;
use App\Models\Photoshoot;
use App\Models\PhotoshootAsset;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class PhotoshootController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware(Role::PHOTOGRAPHER);
    }

    public function lists(ListRequest $request)
    {
        $photoshoots = Photoshoot::where('photographer_id', $request->user()->id)->orderBy('created_at', 'desc')->paginate(20);
        return PhotoshootResource::collection($photoshoots);
    }

    public function addUpload(UploadRequest $request, Photoshoot $photoshoot)
    {
        $this->authorize('uploadPhoto', $photoshoot);
        $fileNames = $this->uploadImage($request);
        foreach ($fileNames as $fileName) {
            $asset = new PhotoshootAsset();
            $asset->photoshoot_id = $photoshoot->id;
            $asset->asset_link = "/images/{$fileName}";
            $asset->thumbnail_link = "/thumbnail/{$fileName}";
            $asset->status = AssetStatus::PENDING;
            $asset->save();
        }
        return new PhotoshootResource(Photoshoot::findOrFail($photoshoot->id));
    }

    public function updatePhotoshootAsset(UpdateAssetRequest $request, Photoshoot $photoshoot, PhotoshootAsset $photoshootAsset)
    {
        $this->authorize('updateAsset', $photoshoot);
        if(in_array($photoshootAsset->status, [AssetStatus::REJECTED, AssetStatus::PENDING])) {
            $oldThumbnail = $photoshootAsset->thumbnail_link;
            $oldAsset = $photoshootAsset->asset_link;
            $fileName = $this->handleUpload($request->file('image'));
            $photoshootAsset->thumbnail_link = "/thumbnail/{$fileName}";
            $photoshootAsset->asset_link = "/images/{$fileName}";
            $photoshootAsset->save();
            $this->cleanupDirectories($oldAsset, $oldThumbnail);
            return new PhotoshootResource(Photoshoot::findOrFail($photoshoot->id));
        }
        return response()->json(['status' => 'error', 'message' => 'You cannot update an approved asset'], 403);
    }

    private function uploadImage($request)
    {
        $fileNames = [];
        foreach ($request->file('images') as $image) {
            $fileNames[] = $this->handleUpload($image);
        }
        return $fileNames;
    }

    private function handleUpload($image)
    {
        $fileName = time().'.'.$image->extension();
        $destinationPath = public_path('/thumbnail');
        $img = Image::make($image->path());
        $img->resize(100, 100, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$fileName);
        $destinationPath = public_path('/images');
        $image->move($destinationPath, $fileName);
        return $fileName;
    }

    private function cleanupDirectories($image, $thumbnail)
    {
        if(Storage::disk('talentql')->exists($image)) {
            Storage::disk('talentql')->delete($image);
        }

        if(Storage::disk('talentql')->exists($thumbnail)) {
            Storage::disk('talentql')->delete($thumbnail);
        }
    }
}
