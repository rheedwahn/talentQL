<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Customer\Photoshoot\ListRequest;
use App\Http\Resources\Api\Admin\PhotoshootResource;
use App\Models\Photoshoot;

class PhotoshootController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware(Role::ADMIN);
    }

    public function lists(ListRequest $request)
    {
        $photoshoots = Photoshoot::orderBy('created_at', 'desc')->paginate(20);
        return PhotoshootResource::collection($photoshoots);
    }
}
