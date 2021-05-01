<?php

namespace App\Http\Controllers\Api\Select;

use App\Enums\AssetStatus;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Customer\PhotographerResource;
use App\Http\Resources\Api\PhotoshootLocationResource;
use App\Models\PhotoshootLocation;
use App\Models\User;
use Illuminate\Http\Request;

class SelectController extends Controller
{
    public function lists()
    {
        $locations = PhotoshootLocation::all();
        $role = \App\Models\Role::where('name', Role::PHOTOGRAPHER)->first();
        $photographers = User::where('role_id', $role->id)->get();
        return response()->json([
            'locations' => PhotoshootLocationResource::collection($locations),
            'photographers' => PhotographerResource::collection($photographers),
            'asset_statuses' => AssetStatus::$statuses
        ]);
    }
}
