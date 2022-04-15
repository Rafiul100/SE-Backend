<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;


class ApiRoutesTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    // public function test_example()
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    public function testIfRegisterRouteExists()
    {
        $this->json('post', 'api/register')
        ->assertStatus(200); 
    }

    public function testIfSignInRouteExists()
    {
        $this->json('post', 'api/signin')
        ->assertStatus(200); 
    }

    public function testIfSignOutRouteExists()
    {
        //401 requires valid user authentication 
        //i was unable to find a way to validate user through sanctum, therefore this step has been skipped in all tests 
        $this->json('post', 'api/signout')
        ->assertStatus(401);
    }


    public function testIfAddProductRouteExists()
    {
        //401 requires valid user authentication 
        //i was unable to find a way to validate user through sanctum, therefore this step has been skipped in all tests 
        $this->json('post', 'api/add-product')
        ->assertStatus(401);
    }


    public function testIfViewProductRouteExists()
    {
        //401 requires valid user authentication 
        //i was unable to find a way to validate user through sanctum, therefore this step has been skipped in all tests 
        $this->json('get', 'api/view-product')
        ->assertStatus(401);
    }

    public function testIfViewCartRouteExists()
    {
        //401 requires valid user authentication 
        //i was unable to find a way to validate user through sanctum, therefore this step has been skipped in all tests 
        $this->json('get', 'api/view-cart')
        ->assertStatus(401);
    }
}

