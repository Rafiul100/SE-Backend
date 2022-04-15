<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\Cart;
use Carbon\Carbon;
use App\Models\Ordereditems;

class CheckoutController extends Controller
{
    public function placeOrder(Request $request) { 

        $validator = Validator::make($request->all(), [
            'firstname' => 'required|max:191',
            'lastname' => 'required|max:191',
            'phoneNo' => 'required|max:191',
            'email' => 'required|max:191',
            'address' => 'required|max:191',
            'city' => 'required|max:191',
            'postcode' => 'required|max:191',

        ]);

        if($validator->fails()) 
        {
            return response()->json([
                'status'=>422, 
                'errors'=>$validator->messages(),
            ]);
        } else {

           
            $student_id =  auth('sanctum')->user()->id; 
            $order = new Order;
            $order->student_id = $student_id; 
            $order->firstname = $request->firstname;
            $order->lastname = $request->lastname;
            $order->phoneNo = $request->phoneNo;
            $order->email = $request->email;
            $order->address = $request->address;
            $order->city = $request->city;
            $order->postcode = $request->postcode;

            $order->payment_type = $request->payment_type;
            $order->payment_id = $request->payment_id;
            $order->tracking_no = 'studentessentials'.rand(1111,9999);
           

          

            //get all products an authorised student has in a cart
            $cart = Cart::where('student_id', $student_id)->get(); 


            
            // //an array is created so that it stores the elements attributes that the ordereditems table can hold.
            // //each product found in the cart are stored as an element in itemsordered array, ready to be passed to ordereditems table.
            $itemsordered = [];
            $delivery = [];
            foreach($cart as $item) { 

                if ($item->product_type == 'adminproduct') { 

                
                $itemsordered[] = [
                    'product_id'=>$item->product_id,
                    'product_type'=>$item->product_type, 
                    'quantity'=>$item->product_quantity,
                    'price'=>$item->product->price,
                    // 'saleprice'=>$item->product->saleprice, 
                    'user_id'=>1, 

                ];

                $delivery[] = $item->product->delivery;

               //gets each item product quantity from cart and deducts it from the available stock for a given product 
                $item->product->update([
                    'stock'=>$item->product->stock - $item->product_quantity, 
                    'purchased'=>$item->product->purchased += 1
                ]); 


                } else { 

                    $itemsordered[] = [
                        'product_id'=>$item->studentproduct_id,
                        'product_type'=>$item->product_type, 
                        'quantity'=>$item->product_quantity,
                        'price'=>$item->studentproduct->price, 
                        // 'saleprice'=> 0, 
                        'user_id'=>$item->studentproduct->student_id, 

                    ];

                    $delivery[] = $item->studentproduct->delivery;
    
                   //gets each item product quantity from cart and deducts it from the available stock for a given product 
                    $item->studentproduct->update([
                        'stock'=>$item->studentproduct->stock - $item->product_quantity,
                        'purchased'=>$item->studentproduct->purchased += 1
                    ]); 


                } 
            }  
             
            $value = (int) max($delivery); 

            $current = Carbon::now();

            $deliveryExpires = $current->addDays($value);

            $order->delivery = $deliveryExpires; 

            $order->save(); 

         
            
            //an order can have many itemsordered so there is a one to many relationship
            $order->ordereditems()->createMany($itemsordered); 
            //destroy all items found in cart once payment has been recieved
            Cart::destroy($cart); 

            $allorders = Order::all(); 

            foreach($allorders as $order) { 

             $orderitems  = Ordereditems::where('order_id', $order->id)->get();

             foreach($orderitems as $orderitem) { 

                $orderitem->delivery = $order->delivery; 

                $orderitem->update(); 

             }

            }


            return response()->json([
                'status'=>200,
                'message'=>'thank you',
                'delivery'=>$delivery,
                'value'=>$value, 
                'day'=>$deliveryExpires, 
            ]); 

      
        }


    }

    public function validateInfo (Request $request) {

        $validator = Validator::make($request->all(), [
            'firstname' => 'required|max:191',
            'lastname' => 'required|max:191',
            'phoneNo' => 'required|max:191',
            'email' => 'required|max:191',
            'address' => 'required|max:191',
            'city' => 'required|max:191',
            'postcode' => 'required|max:191',

        ]);

        if($validator->fails()) 
        {
            return response()->json([
                'status'=>422, 
                'errors'=>$validator->messages(),
            ]);
        } else { 

            return response()->json([
                'status'=>200,  
            ]);


    }
}

} 