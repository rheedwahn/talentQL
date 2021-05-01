<?php

namespace App\Services\Users;

use App\Models\User;
use Illuminate\Support\Str;

class StoreService
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function run()
    {
        $user = new User();
        $user->name = $this->data['name'];
        $user->email = $this->data['email'];
        $user->password = $this->data['password'];
        $user->remember_token = Str::random(10);
        $user->role_id = $this->data['role_id'];
        $user->save();
        return $user;
    }
}
