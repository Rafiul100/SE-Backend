<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products'; 
    protected $fillable = [
        'name',
        'price',
        'saleprice',
        'stock',
        'category',
        'subcategory',
        'delivery',
        'image',
        'description',
        'featured',
        'sale', 
        'status', 
    ];


    // public function products() { 
    //     return $this->morphMany(related: 'App\Cart', name:'product')
    // }



}
