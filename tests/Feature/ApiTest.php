<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class ApiTest extends TestCase
{
    use RefreshDatabase;//run fresh migration every time

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_login_check()
    {
        $loginFormData = [
            'phone' => '1234567890'
        ];
        $response = $this->json('POST','/api/v1/login',  $loginFormData);
        $response->assertStatus(200);
        $response->assertJson([ 'status' => false ]);
    }

    public function test_login_reponse()
    {
        $user = factory(User::class)->create();
        $phone = $user->phone;

        $loginFormData = [
            'phone' => $phone
        ];

        $response2 = $this->post('/api/v1/login',  $loginFormData);
        $response2->assertStatus(200);
        $response2->assertJson([ 'status' => true ]);
    }

}
