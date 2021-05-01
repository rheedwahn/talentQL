<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotoshootAsset extends Model
{
    use HasFactory;

    public function photoshoot()
    {
        return $this->belongsTo(Photoshoot::class);
    }
}
