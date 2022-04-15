<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class StudentProduct extends Model
{
    use HasFactory;
    protected $table = 'studentproducts'; 
    protected $fillable = [
        'student_id', 
        'student_name', 
        'name',
        'price',
        'stock',
        'category',
        'subcategory',
        'delivery',
        'image',
        'description',
        'status',
     
    ];



    // public function products() { 
    //     return $this->morphMany(related: 'App\Cart', name:'productable');
    // }
 
}
