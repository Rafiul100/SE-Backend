<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\StudentProduct;
use App\Models\Ordereditems;
use App\Models\Subscription; 
use Carbon\Carbon;

class OrderController extends Controller
{
    public function adminOrders() { 

        // $orders = Order::all(); 

        // $products = []; 
       

        $orders = []; 
        $id = [];

        $todaysdate = Carbon::now();
        $todayssubdate = Carbon::now();

        $items = Ordereditems::all();

            foreach ($items as $item) { 


                if ($item->product_type == 'adminproduct') {

                    //if the id does not already exist in the array then add that id to the orders array
                    if (in_array($item->order_id, $id)) {


                    } else {
                    
                    $order = Order::where('id', $item->order_id)->first(); 

                    if ($order->status != 1) { 

                        $orders[]= $order; 

                    }

                    $id[] = $item->order_id; 

                    }

                }

            }

            $subs = Subscription::where('owner_id', '1')->get(); 


            $subscription = [];   
            $itemsordered = [];             

            foreach ($subs as $sub) {

                 $product = Product::where('id', $sub->product_id)->first();  

                 $user = User::where('id', $sub->student_id)->first();


                 if ($todaysdate > $sub->delivery && $product->stock >= $sub->quantity) {

                    $deliveryExpires = $todaysdate->addDays($product->delivery);

                    $suborder = new Order;
                    $suborder->student_id = $sub->student_id; 
                    $suborder->firstname = $user->firstname;
                    $suborder->lastname = $user->lastname;
                    $suborder->phoneNo = $user->phoneNo;
                    $suborder->email = $user->email;
                    $suborder->address = $user->address;
                    $suborder->city = $user->city;
                    $suborder->postcode = $user->postcode;
                    $suborder->payment_type = 'Cash on delivery';
                    $suborder->tracking_no = 'studentessentials'.rand(1111,9999);
                    $suborder->delivery = $deliveryExpires;
                    $suborder->save(); 


                    $itemsordered[] = [
                        'product_id'=>$product->id,
                        'product_type'=>'adminproduct', 
                        'quantity'=>$sub->quantity,
                        'price'=>$product->price, 
                        'user_id'=>$sub->owner_id, 
                        'delivery'=>$deliveryExpires
                    ];

                    $suborder->ordereditems()->createMany($itemsordered); 

        
                    $product->update([
                        'stock'=>$product->stock - $sub->quantity, 
                        'purchased'=>$product->purchased += 1
                    ]); 


                if ($sub->interval == '1w') {

                    $sub->delivery = $todayssubdate->addWeek();
    
                } else if ($sub->interval == '2w') {
    
                    $sub->delivery = $todayssubdate->addWeeks(2);
    
                } else if ($sub->interval == '3w') {
    
                    $sub->delivery = $todayssubdate->addWeeks(3);
    
                } else if ($sub->interval == '4w') {
    
                    $sub->delivery = $todayssubdate->addWeeks(4);
    
                } else if ($sub->interval == '2m') {
    
                    $sub->delivery = $todayssubdate->addMonths(2);
    
                } else if ($sub->interval == '3m') {
    
                   $sub->delivery = $todayssubdate->addMonths(3);
    
                } else if ($sub->interval == '4m') {
    
                    $sub->delivery = $todayssubdate->addMonths(4);
    
                }

                $sub->update(); 

                $orders[]= $suborder; 

                }


                $subscription[] = [
                    'name'=>$product->name,
                    'productimage'=>$product->image,
                    'price'=>$product->price,
                    'quantity'=>$sub->quantity,
                    'interval'=>$sub->interval, 
                    'user'=>$user->username,
                    'userimage'=>$user->image, 
                  
                ];
                
            
            }
            

            return response()->json([
                'status'=> 200, 
                'orders'=> $orders, 
                'subscription' => $subscription
            
            ]); 
    

        // foreach($products as $product) {

        //     if ($product->product_type == 'adminproduct') { 

        //       $actual_orders = Order::find($product->order_id);


        //     } 
            
        // }

  

    }


    public function studentOrders() { 

        //$orders = Order::all(); 

        $orders = []; 
        $id = [];

        $todaysdate = Carbon::now();
    
        $todayssubdate = Carbon::now();

        $items = Ordereditems::all();

        $student_id = auth('sanctum')->user()->id; 

            foreach ($items as $item) { 

                if ($item->product_type == 'studentproduct' && $student_id == $item->user_id ) {

                if (in_array($item->order_id, $id)) {

                } else {
                
                $order = Order::where('id', $item->order_id)->first(); 

                if ($order->status != 2) { 

                    $orders[]= $order; 

                }

                $id[] = $item->order_id; 

                }

            }

            }


            $subs = Subscription::where('owner_id', $student_id)->get(); 

    
            $subscription = [];  
            $itemsordered = [];            

            foreach ($subs as $sub) {

                 $product = StudentProduct::where('id', $sub->product_id)->first();  

                 $user = User::where('id', $sub->student_id)->first();

                if ($todaysdate > $sub->delivery && $product->stock >= $sub->quantity) {

                    $deliveryExpires = $todaysdate->addDays($product->delivery);


                    $suborder = new Order;
                    $suborder->student_id = $sub->student_id; 
                    $suborder->firstname = $user->firstname;
                    $suborder->lastname = $user->lastname;
                    $suborder->phoneNo = $user->phoneNo;
                    $suborder->email = $user->email;
                    $suborder->address = $user->address;
                    $suborder->city = $user->city;
                    $suborder->postcode = $user->postcode;
                    $suborder->payment_type = 'Cash on delivery';
                    $suborder->tracking_no = 'studentessentials'.rand(1111,9999);
                    $suborder->delivery = $deliveryExpires; 
                    $suborder->save(); 


                    $itemsordered[] = [
                        'product_id'=>$product->id,
                        'product_type'=>'studentproduct', 
                        'quantity'=>$sub->quantity,
                        'price'=>$product->price, 
                        'user_id'=>$sub->owner_id, 
                        'delivery'=>$deliveryExpires
                    ];

                    $suborder->ordereditems()->createMany($itemsordered); 

        
                    $product->update([
                        'stock'=>$product->stock - $sub->quantity, 
                        'purchased'=>$product->purchased += 1
                    ]); 



                if ($sub->interval == '1w') {

                    $sub->delivery = $todayssubdate->addWeek();
    
                } else if ($sub->interval == '2w') {
    
                    $sub->delivery = $todayssubdate->addWeeks(2);
    
                } else if ($sub->interval == '3w') {
    
                    $sub->delivery = $todayssubdate->addWeeks(3);
    
                } else if ($sub->interval == '4w') {
    
                    $sub->delivery = $todayssubdate->addWeeks(4);
    
                } else if ($sub->interval == '2m') {
    
                    $sub->delivery = $todayssubdate->addMonths(2);
    
                } else if ($sub->interval == '3m') {
    
                   $sub->delivery = $todayssubdate->addMonths(3);
    
                } else if ($sub->interval == '4m') {
    
                    $sub->delivery = $todayssubdate->addMonths(4);
    
                }

                $sub->update(); 

                $orders[]= $suborder; 

                }

                 
                $subscription[] = [
                    'name'=>$product->name,
                    'productimage'=>$product->image,
                    'price'=>$product->price,
                    'quantity'=>$sub->quantity,
                    'interval'=>$sub->interval, 
                    'user'=>$user->username,
                    'userimage'=>$user->image, 
                    
                  
                ];
                
            
            }


        return response()->json([
            'status'=> 200, 
            'orders'=> $orders, 
            'subscription' => $subscription
        ]); 


    }


    public function orderedItems($id) { 

        $orders = Ordereditems::where('order_id', $id)->get(); 


        $adminitems = [];
        $studentitems = [];

        $quantity = [];
        foreach($orders as $item) { 

            if ($item->product_type == 'adminproduct') {

                $products = Product::where('id', $item->product_id)->first(); 


                $adminitems[] = [
                    'name'=>$products->name,
                    'image'=>$products->image,
                    'price'=>$products->price,
                    'saleprice'=>$products->saleprice, 
                    'quantity'=>$item->quantity,
                  
                ];


            } else {

                $student_id = auth('sanctum')->user()->id; 
                $products = StudentProduct::where('id', $item->product_id)->where('student_id', $student_id)->first(); 

                if ($products) { 

                    $studentitems[] = [
                        'name'=>$products->name,
                        'image'=>$products->image,
                        'price'=>$products->price,
                        'quantity'=>$item->quantity,
                      
                    ];
                }
               
             

            }

        }

        // $product_id = $order->product_id; 

      
        return response()->json([
            'status'=> 200, 
            'adminorders'=> $adminitems, 
            'studentorders'=> $studentitems, 
            
        ]); 


    }


    public function deleteOrder(Request $request, $id) { 


        $order = Order::where('id', $id)->first();


        if($order) { 

            //deleted by admin 

            if ($request->type == 'adminorder') { 


                $order->status = 1; 


            
            } else { 

                $order->status = 2; 

            }
          
            $order->update();
         
            return response()->json([
                'status'=> 200, 
                'message'=>'Order has been removed successfully', 
               
            ]);
        } else {
            
            return response()->json([
                'status'=> 404, 
                'message'=>'Item is not found in order table', 
            ]);


        }  


    }
 }
