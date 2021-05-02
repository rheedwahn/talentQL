<?php

namespace Tests\Feature\Authenticate;

use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_user_can_login()
    {
        Session::start();
        $user = $this->setUpUser();
        $this->getXsrfTokes();
        $data = ['email' => $user->email, 'password' => 'password'];
        $response = $this->makePost('/login', $data);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'logged in successfully'
        ]);
    }

    public function test_logged_in_user_can_get_profile()
    {
        $user = $this->setUpUser();
        $response = $this->actingAs($user)->getJson('/api/me');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    public function test_an_unauthenticated_user_cannot_access_authenticated_resource()
    {
        $response = $this->getJson('/api/me');
        $response->assertStatus(401);
        $response->assertJsonFragment([
            'message' => 'Unauthenticated.'
        ]);
    }

}
