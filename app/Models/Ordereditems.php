<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ordereditems extends Model
{
    use HasFactory;
    protected $table = 'ordereditems'; 
    protected $fillable = [
        'order_id',
        'user_id',
        'product_type',
        'product_id',
        'quantity',
        'price',
        'delivery',
    ]; 
}
