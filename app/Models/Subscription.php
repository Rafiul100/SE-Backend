<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;
    
    protected $table = 'subscriptions'; 
    protected $fillable = [
        'student_id',
        'product_id',
        'quantity',
        'interval', 
        'delivery', 
    ];
}
