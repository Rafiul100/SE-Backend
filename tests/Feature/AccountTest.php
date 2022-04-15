<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AccountTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_users_can_register()
    {
        $formData = [
            'username' =>'Rafiul', 
            'email'=>'rafiul@gmail.com',
            'password'=>'raf123', 
            'gender'=>'male',  
        ]; 

        $this->withoutExceptionHandling(); 
      
        
        $this->json('POST', 'api/register', $formData)
             ->assertStatus(200); 
    }


    public function test_if_users_can_signin()
    {
        $formData = [
            'email'=>'rafiul@gmail.com',
            'password'=>'raf123',   
        ]; 

        $this->json('POST', 'api/signin', $formData)
             ->assertStatus(200); 
    }
}
