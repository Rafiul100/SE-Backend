<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\StudentProduct;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\Subscription; 
use App\Models\Ordereditems;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function subscriptionDetails($type, $id) { 

        $student_id = auth('sanctum')->user()->id; 
        $user = User::find($student_id); 

        $alreadysubscribed  = Subscription::where('student_id', $student_id)->where('product_id', $id)->first();


        if ($user) {

            if ($type == 'new') {

              $product = Product::find($id); 


              if ($product) {
                
                return response()->json([
                    'status'=> 200, 
                    'user'=>$user, 
                    'product'=>$product, 
                    'subscribed'=>$alreadysubscribed ? 'subbed' : 'notsubbed', 
                ]); 

              } 

            } else {

                $product = StudentProduct::find($id); 

                if ($product) {

                    return response()->json([
                        'status'=> 200, 
                        'user'=>$user, 
                        'product'=>$product, 
                        'subscribed'=>$alreadysubscribed ? 'subbed' : 'notsubbed', 
                    ]); 
                }

            }  

            return response()->json([
                'status'=> 404, 
                'user'=>'product has not been found' 
            ]); 


    } else {

        return response()->json([
            'status'=> 401, 
            'user'=>'user information cannot be found' 
        ]); 


    }

    }



    public function subscriptionOrder(Request $request) {

        $current = Carbon::now();

        $validator = Validator::make($request->all(), [
            'firstname' => 'required|max:191',
            'lastname' => 'required|max:191',
            'phoneNo' => 'required|max:191',
            'email' => 'required|max:191',
            'address' => 'required|max:191',
            'city' => 'required|max:191',
            'postcode' => 'required|max:191',
            'interval' => 'required',
            'quantity' => 'required',

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

            
            $alreadysubscribed  = Subscription::where('student_id', $student_id)->where('product_id', $request->product_id)->first();

            if ($alreadysubscribed) {

                return response()->json([
                    'status'=>401, 
                ]);

            }


            $sub = new Subscription; 
            $sub->student_id = $student_id; 
            $sub->product_id = $request->product_id;
            $sub->interval = $request->interval;
            $sub->quantity = $request->quantity;


            if ($request->interval == '1w') {

                $sub->delivery = $current->addWeek();

            } else if ($request->interval == '2w') {

                $sub->delivery = $current->addWeeks(2);

            } else if ($request->interval == '3w') {

                $sub->delivery = $current->addWeeks(3);

            } else if ($request->interval == '4w') {

                $sub->delivery = $current->addWeeks(4);

            } else if ($request->interval == '2m') {

                $sub->delivery = $current->addMonths(2);

            } else if ($request->interval == '3m') {

               $sub->delivery = $current->addMonths(3);

            } else if ($request->interval == '4m') {

                $sub->delivery = $current->addMonths(4);

            }

     
            

            $itemsordered = [];
      
            
            if ($request->type == 'new') {

                $sub->owner_id = 1; 

                $product = Product::find($request->product_id); 

                if ($product->stock < $request->quantity) {

                    return response()->json([
                        'status'=>404, 
                    ]);

                }

                $itemsordered[] = [
                    'product_id'=>$product->id,
                    'product_type'=>'adminproduct', 
                    'quantity'=>$request->quantity,
                    'price'=>$product->price, 
                    'user_id'=>1, 

                ];


               //gets each item product quantity from cart and deducts it from the available stock for a given product 
                $product->update([
                    'stock'=>$product->stock - $request->quantity, 
                    'purchased'=>$product->purchased += 1
                ]); 


            } else {
                
                
                $product = StudentProduct::find($request->product_id); 


                $sub->owner_id = $product->student_id; 


                if ($product->stock < $request->quantity) {

                    return response()->json([
                        'status'=>404, 
                    ]);

                }

                $itemsordered[] = [
                    'product_id'=>$product->id,
                    'product_type'=>'studentproduct', 
                    'quantity'=>$request->quantity,
                    'price'=>$product->price, 
                    'user_id'=>$product->student_id, 
                ];

               

                //gets each item product quantity from cart and deducts it from the available stock for a given product 
                 $product->update([
                     'stock'=>$product->stock - $request->quantity, 
                     'purchased'=>$product->purchased += 1
                 ]); 


            }

            $currentfororder = Carbon::now();
            $deliveryExpires = $currentfororder->addDays($product->delivery);
            $order->delivery = $deliveryExpires;  
            $order->save(); 
            $order->ordereditems()->createMany($itemsordered); 
            $sub->save(); 


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
            ]);

    }

}


public function studentSubscriptions() { 


    $student_id =  auth('sanctum')->user()->id;

    $subs = Subscription::where('student_id', $student_id)->get(); 

    $subscriptions = [];             

    foreach ($subs as $sub) {

        if ($sub->owner_id == '1') {

         $product = Product::where('id', $sub->product_id)->first();  
 
        $subscriptions[] = [
            'name'=>$product->name,
            'id'=>$product->id, 
            'image'=>$product->image,
            'price'=>$product->price,
            'saleprice'=>$product->saleprice, 
            'quantity'=>$sub->quantity,
            'interval'=>$sub->interval, 
            'stock'=>$product->stock, 
            'type' =>$sub->owner_id == '1' ? 'adminproduct' : 'studentproduct',  
            
          
        ];

    } else {


        $product = StudentProduct::where('id', $sub->product_id)->first();  
 
        $subscriptions[] = [
            'name'=>$product->name,
            'id'=>$product->id, 
            'image'=>$product->image,
            'price'=>$product->price,
            'quantity'=>$sub->quantity,
            'interval'=>$sub->interval, 
            'stock'=>$product->stock, 
            'type' =>$sub->owner_id == '1' ? 'adminproduct' : 'studentproduct',  
            
          
        ];
    }
      
    }

    return response()->json([
        'status'=>200, 
        'subscriptions' => $subscriptions
    ]);

}

public function removeSubscriptions($id, $type) {

    $student_id = auth('sanctum')->user()->id; 

    if ($type == 'adminproduct') {

        $item = Subscription::where('product_id', $id)->where('owner_id', '1')->first();

    } else {
 
        $item = Subscription::where('product_id', $id)->where('owner_id', '<>', '1')->first();

    }

    if($item) { 

        $item->delete(); 

        return response()->json([
            'status'=> 200, 
            'message'=>'Successfully unsubscribed from item.', 
        ]);

    } else {
        
        return response()->json([
            'status'=> 404, 
            'message'=> 'You are not subscribed to this item' 
        ]);

    }  

}

}
