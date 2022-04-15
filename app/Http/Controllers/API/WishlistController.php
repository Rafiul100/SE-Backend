<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\StudentProduct;
use App\Models\Wishlist;

class WishlistController extends Controller
{
    public function addWishlist(Request $request, $type) {

    $student_id =  auth('sanctum')->user()->id; 


    $item = Wishlist::where('product_id', $request->product_id)->where('student_id', $student_id)->first();
    
if ($item) {

    return response()->json([
        'status'=>404, 
        'message'=>'Item already in wishlist'
    ]);


} else {

       $wishlist = new Wishlist;
       $wishlist->product_id = $request->product_id; 
       $wishlist->student_id = $student_id; 
    
    
        if ($type == 'new') {

       $wishlist->product_type = 'adminproduct'; 
       
       
        } else {
 
       $wishlist->product_type = 'studentproduct'; 

        }

        $wishlist->save(); 

        return response()->json([
            'status'=>200, 
            'message'=>'Added to wishlist'
        ]);

    }


    }


    public function getWishlist() { 


        $student_id =  auth('sanctum')->user()->id; 

        $items = Wishlist::where('student_id', $student_id)->get();

        $wishlist = [];

        foreach($items as $item) { 

            if ($item->product_type == 'adminproduct') {

              $product = Product::where('id', $item->product_id)->first();


              $wishlist[] = [
                'name'=>$product->name,
                'stock'=>$product->stock, 
                'image'=>$product->image,
                'price'=>$product->price, 
                'saleprice'=>$product->saleprice,
                'id'=>$product->id, 
                'type'=>$item->product_type,
            ];


            } else {


                $product = StudentProduct::where('id', $item->product_id)->first(); 

                $wishlist[] = [
                  'name'=>$product->name,
                  'stock'=>$product->stock, 
                  'image'=>$product->image,
                  'price'=>$product->price, 
                  'id'=>$product->id, 
                  'type'=>$item->product_type,
              ];


            }

        }

        return response()->json([
            'status'=>200, 
            'wishlist'=>$wishlist
        ]);


    }




    public function deleteWishlist($id) {

        $student_id = auth('sanctum')->user()->id; 

    
        $item = Wishlist::where('product_id', $id)->where('student_id', $student_id)->first();
        if($item) { 

            $item->delete(); 

            return response()->json([
                'status'=> 200, 
                'message'=>'Item removed successfully from wishlist', 
            ]);
        } else {
            
            return response()->json([
                'status'=> 404, 
                'message'=> 'Item is not found in wishlist' 
            ]);


        }  

    }
}
