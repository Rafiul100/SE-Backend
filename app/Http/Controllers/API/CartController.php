<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\StudentProduct;
use App\Models\Cart;
use App\Models\User;



class CartController extends Controller
{
    public function addToCart(Request $request, $type) { 

        $student_id = auth('sanctum')->user()->id; 
        $product_id = $request->product_id; 
        $product_quantity = $request->product_quantity; 

        if ($type == 'new') {
            $checkProduct = Product::where('id', $product_id)->where('status', '0')->first(); 
            $product_type = 'adminproduct';

        } else { 
            $checkProduct = StudentProduct::where('id', $product_id)->where('status', '0')->first();  
            $product_type = 'studentproduct'; 
        }
        
        
        //checks if product exists in the product table
        if($checkProduct && $checkProduct->stock > 0) { 
            //checks if student has already added the product to the cart
            if(Cart::where('product_id', $product_id)->where('student_id', $student_id)->where('product_type', $product_type)->exists() || Cart::where('studentproduct_id', $product_id)->where('student_id', $student_id)->where('product_type', $product_type)->exists()) { 

                return response()->json([
                    'status'=> 409, 
                    'message'=>$checkProduct->name. ' has already been added to cart', 
                ]); 

            } else { 
                //creates new cart row for the new item to add 
                $cart = new Cart; 
                $cart->student_id = $student_id; 
                if ($product_type == 'adminproduct') { 
                    $cart->product_id = $checkProduct->id; 

                } else { 

                    $cart->studentproduct_id = $checkProduct->id; 


                }
           
                $cart->product_type = $product_type; 
                $cart->product_quantity = $product_quantity;
                $cart->save();
                
                return response()->json([
                    'status'=> 200, 
                    'message'=>'Added to cart', 
                ]); 

            }

            } else { 

                return response()->json([
                    'status'=> 404, 
                    'message'=>'Product is out of stock at the moment or does not exist', 
                ]); 

            }

    }


    public function viewCart() { 

        $student_id = auth('sanctum')->user()->id; 
        $items = Cart::where('student_id', $student_id)->get(); 


        $user = User::find($student_id); 



        return response()->json([
            'status'=> 200, 
            'cart'=>$items,
            'user'=>$user, 
        ]); 
    }


    public function updateCart($id, $type, $stock) 
    {
        
        $student_id = auth('sanctum')->user()->id; 

        $item = Cart::where('id', $id)->where('student_id', $student_id)->first();
    
        
        //checks which button the student has clicked, increment or decrement and do different operations for each type.
        //the cart item in the cart table has a maximum and minimum range it can be updated to. 
        //the maximum would be the stock number for the product, where as the minium will always be 1. 
        if($type == 'inc' && $item->product_quantity < $stock) { 
            $item->product_quantity += 1; 
        } else if ($type == 'dec' && $item->product_quantity > 1) { 
            $item->product_quantity -= 1;  
        }; 

        $item->update(); 
    }

    public function deleteCart($id) {

        $student_id = auth('sanctum')->user()->id; 
        $item = Cart::where('id', $id)->where('student_id', $student_id)->first();
        if($item) { 

            $item->delete(); 

            return response()->json([
                'status'=> 200, 
                'message'=>'Item removed successfully from cart', 
            ]);
        } else {
            
            return response()->json([
                'status'=> 404, 
                'message'=>'Item is not found in cart', 
            ]);


        }  

    }
}
