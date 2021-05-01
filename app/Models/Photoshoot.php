<?php

namespace App\Models;

use App\Enums\AssetStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Photoshoot extends Model
{
    use HasFactory, SoftDeletes;

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function photographer()
    {
        return $this->belongsTo(User::class, 'photographer_id');
    }

    public function photoshoot_assets()
    {
        return $this->hasMany(PhotoshootAsset::class);
    }

    public function photoshoot_location()
    {
        return $this->belongsTo(PhotoshootLocation::class);
    }

    public function haveNotApprovedAllAsset()
    {
        $hasRejected = $this->photoshoot_assets->filter(function ($asset) {
            return in_array($asset->status, [AssetStatus::REJECTED, AssetStatus::PENDING]);
        })->toArray();
        return count($hasRejected) ?: false;
    }
}
