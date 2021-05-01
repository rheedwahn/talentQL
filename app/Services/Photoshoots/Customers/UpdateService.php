<?php

namespace App\Services\Photoshoots\Customers;

use App\Models\Photoshoot;
use App\Services\Base\UpdateService as BaseUpdateService;

class UpdateService extends BaseUpdateService
{
    const UPDATE_FIELDS = ['photoshoot_location_id', 'product', 'description', 'company', 'number_of_shots', 'photographer_id'];

    public function __construct(Photoshoot $photoshoot, array $data)
    {
        if(isset($data['location_id'])) {
            $data['photoshoot_location_id'] = $data['location_id'];
        }
        parent::__construct($photoshoot, $data, self::UPDATE_FIELDS);
    }
}
