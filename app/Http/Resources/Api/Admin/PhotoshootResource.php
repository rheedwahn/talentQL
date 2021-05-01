<?php

namespace App\Http\Resources\Api\Admin;

use App\Http\Resources\Api\Customer\AssetResource;
use App\Http\Resources\Api\Customer\CustomerResource;
use App\Http\Resources\Api\Customer\PhotographerResource;
use App\Http\Resources\Api\PhotoshootLocationResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PhotoshootResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'location' => new PhotoshootLocationResource($this->photoshoot_location),
            'product' => $this->product,
            'description' => $this->description,
            'company' => $this->company,
            'customer' => new CustomerResource($this->customer),
            'photographer' => new PhotographerResource($this->photographer),
            'number_of_shots' => $this->number_of_shots,
            'assets' => AssetResource::collection($this->photoshoot_assets)
        ];
    }
}
