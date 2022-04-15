<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders'; 
    protected $fillable = [
        'firstname', 
        'lastname',
        'PhoneNo',
        'email',
        'address',
        'city',
        'postcode',
        'payment_id',
        'payment_type',
        'tracking_no',
        'delivery', 
        'status',
        'remark'
    ]; 



    public function ordereditems() 
    {
        return $this->hasMany(Ordereditems::class, 'order_id', 'id');
    }                                
                                                                 
}
