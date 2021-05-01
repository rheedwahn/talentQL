<?php

namespace App\Policies;

use App\Models\Photoshoot;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class PhotoshootPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function customerUpdate(User $user, Photoshoot $photoshoot)
    {
        return $photoshoot->customer_id === $user->id ? Response::allow()
            : Response::deny('You are not allowed to update this resource');
    }

    public function uploadPhoto(User $user, Photoshoot $photoshoot)
    {
        return $photoshoot->photographer_id === $user->id ? Response::allow()
            : Response::deny('You are not allowed to update this resource');
    }

    public function updateAssetStatus(User $user, Photoshoot $photoshoot)
    {
        return $photoshoot->customer_id === $user->id ? Response::allow()
            : Response::deny('You are not allowed to update this resource');
    }

    public function updateAsset(User $user, Photoshoot $photoshoot)
    {
        return $photoshoot->photographer_id === $user->id ? Response::allow()
            : Response::deny('You are not allowed to update this resource');
    }
}
