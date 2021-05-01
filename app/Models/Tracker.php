<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Tracker extends Pivot
{
    protected $table = 'photoshoot_photoshoot_tracker';
}
