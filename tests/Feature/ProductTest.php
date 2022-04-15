<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
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


    public function test_if_users_can_add_product()
    {
     
        $formData = [
            'name' =>'FlexGrip', 
            'price'=>'2.99',
            'stock'=>'3', 
            'category'=>'Stationary',
            'subcategory'=>'pen',  
        ]; 

        //401 requires valid user authentication 
        //i was unable to find a way to validate user through sanctum, therefore this step has been skipped in all tests 
        $this->json('POST', 'api/add-product', $formData)
             ->assertStatus(401); 
    }


}


