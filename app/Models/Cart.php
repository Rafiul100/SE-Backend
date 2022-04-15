<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\StudentProduct;






class Cart extends Model
{
    use HasFactory;

    protected $table = 'cart'; 
    
    protected $fillable = [ 
        'user_id', 
        'product_id', 
        'product_type', 
        'product_quantity', 
    ]; 

 
    //this is used to call the relationship through react.js
    //product will now have the ability to call product table columns from the cart table
    protected $with = ['product', 'studentproduct']; 
    //creates a relationship between product_id cart column and unique id from product table.
    public function product() 
    { 
       return $this->belongsTo(Product::class, 'product_id', 'id'); 
       //these two id values are similar so it is seen as a foriegn key
    }


    public function studentproduct() 
    { 
       return $this->belongsTo(StudentProduct::class, 'studentproduct_id', 'id'); 
       //these two id values are similar so it is seen as a foriegn key
    }


}
