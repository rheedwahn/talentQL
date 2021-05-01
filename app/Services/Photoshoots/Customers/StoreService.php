<?php

namespace App\Services\Photoshoots\Customers;

use App\Models\Photoshoot;
use Illuminate\Support\Facades\DB;

class StoreService
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function run()
    {
        return DB::transaction(function () {
            $photoshoot = new Photoshoot();
            $photoshoot->photoshoot_location_id = $this->data['location_id'];
            $photoshoot->product = $this->data['product'];
            $photoshoot->description = $this->data['description'];
            $photoshoot->photographer_id = $this->data['photographer_id'];
            $photoshoot->number_of_shots = $this->data['number_of_shots'];
            $photoshoot->company = $this->data['company'];
            $photoshoot->customer_id = request()->user()->id;
            $photoshoot->save();

            return $photoshoot;
        });
    }
}
