<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\StudentProduct;
use Illuminate\Support\Facades\DB; 
use App\Models\Ordereditems;
use App\Models\Feedback;
use Carbon\Carbon;


class PurchaseController extends Controller
{
    


    public function viewAllAdmin() {
    
        $products = Product::all();
    
        return response()->json([
            'status'=>200, 
            'products'=>$products,
            'message'=>'all products recieved', 
        ]);
    
    
    }


    public function viewAllStudent() {
    
        $products = StudentProduct::all();

    
        return response()->json([
            'status'=>200, 
            'products'=>$products,
            'message'=>'all products recieved', 
        ]);
    
    
    }

    public function getCategory(Request $request) { 

        if ($request->input('type') == 'new') { 

        $items = DB::table('products')->where('subcategory', $request->subcategory)->get();
        $items_count = DB::table('products')->where('subcategory', $request->subcategory)->count();


        //checks if category selected by student has product in it or not
        if($items_count != '0') { 

            return response()->json([
                'status'=>200,
                'products'=>$items, 

            ]);


        } else { 

            return response()->json([
                'status'=>404,
                'message'=> 'No products available for selected category',
                'products'=>'0', 

            ]);


        }
        }  else { 

            $items = DB::table('studentproducts')->where('subcategory', $request->subcategory)->get();
            $items_count = DB::table('studentproducts')->where('subcategory', $request->subcategory)->count();
    
    
            //checks if category selected by student has product in it or not
            if($items_count != '0') { 
    
                return response()->json([
                    'status'=>200,
                    'products'=>$items, 
    
                ]);
    
    
            } else { 
    
                return response()->json([
                    'status'=>404,
                    'message'=> 'No products available for selected category',
                    'products'=>'0', 
    
                ]);
    
    
            }





        }
}



public function getDetails($type, $product, $id) { 

    if ($type == 'new') {

    $product = Product::where('name', $product)
                        ->where('id', $id)
                        ->where('status', '0')
                        ->first();
                        
    if($product) { 
    
    return response()->json([
        'status'=>200, 
        'product'=>$product, 
        ]); 

    } else { 

        return response()->json([
            'status'=>400, 
            'message'=>'Product has been removed at this time',
        ]); 

    }


    } else { 

        $studentproduct  = StudentProduct::where('name', $product)
        ->where('id', $id)
        ->where('status', '0')
        ->first();  

    }

    if ($studentproduct) { 

        $user_id = $studentproduct->student_id;

        $user = User::find($user_id);

        return response()->json([
            'status'=>200, 
            'product'=>$studentproduct,
            'user'=>$user,
            'image'=>$user->image,
       
        ]); 

    }

    else { 

        return response()->json([
            'status'=>401, 
            'message'=>'Product has been removed at this time',
        ]); 


    }




}


public function changeBuyFilter(Request $request) {

    if ($request->input('type') == 'new') { 

    if ($request->input('filter') == "popularity" && $request->input('subcategory') == "all") {
        
        $items = Product::orderBy('purchased', 'desc')->get(); 


    } else if ($request->input('filter') == "price-high" && $request->input('subcategory') == "all") { 

        $items =  Product::orderBy('price', 'desc')->get();
        

    } else if ($request->input('filter') == "price-low" && $request->input('subcategory') == "all")  { 

        $items =  Product::orderBy('price', 'asc')->get();
          

    } else if ($request->input('filter') == "latest" && $request->input('subcategory') == "all") {
        
        $items = Product::orderBy('created_at','desc')->get();

    } else if ($request->input('filter') == "oldest" && $request->input('subcategory') == "all") { 


        $items = Product::orderBy('created_at','asc')->get();

    }

    else if ($request->input('filter') == "popularity") { 

        //gets the product name in alphabetical order 

        $items = Product::orderBy('purchased', 'desc')->where('subcategory', $request->subcategory)->get(); 
    

    } else if ($request->input('filter') == 'price-high') {

      $items =  Product::orderBy('price', 'desc')->where('subcategory', $request->subcategory)->get();
        

    } else if ($request->input('filter') == 'price-low') {

        $items =  Product::orderBy('price', 'asc')->where('subcategory', $request->subcategory)->get();
          

    } else if  ($request->input('filter') == 'latest') { 

        $items = Product::orderBy('created_at','desc')->where('subcategory', $request->subcategory)->get();
  
          
    } else if  ($request->input('filter') == 'oldest') { 

        $items = Product::orderBy('created_at','asc')->where('subcategory', $request->subcategory)->get();
  
} 

    } else { 

        if ($request->input('filter') == "popularity" && $request->input('subcategory') == "all") {
        
            $items = StudentProduct::orderBy('purchased', 'desc')->get(); 
    
    
        } else if ($request->input('filter') == "price-high" && $request->input('subcategory') == "all") { 
    
            $items =  StudentProduct::orderBy('price', 'desc')->get();
            
    
        } else if ($request->input('filter') == "price-low" && $request->input('subcategory') == "all")  { 
    
            $items =  StudentProduct::orderBy('price', 'asc')->get();
              
    
        } else if ($request->input('filter') == "latest" && $request->input('subcategory') == "all") {
            
            $items = StudentProduct::orderBy('created_at','desc')->get();
    
        } else if ($request->input('filter') == "oldest" && $request->input('subcategory') == "all") { 
    
    
            $items = StudentProduct::orderBy('created_at','asc')->get();
    
        }
    
        else if ($request->input('filter') == "popularity") { 
    
            //gets the Studentproduct name in alphabetical order 
    
            $items = StudentProduct::orderBy('purchased', 'desc')->where('subcategory', $request->subcategory)->get(); 
        
    
        } else if ($request->input('filter') == 'price-high') {
    
          $items =  StudentProduct::orderBy('price', 'desc')->where('subcategory', $request->subcategory)->get();
            
    
        } else if ($request->input('filter') == 'price-low') {
    
            $items =  StudentProduct::orderBy('price', 'asc')->where('subcategory', $request->subcategory)->get();
              
    
        } else if  ($request->input('filter') == 'latest') { 
    
            $items = StudentProduct::orderBy('created_at','desc')->where('subcategory', $request->subcategory)->get();
      
              
        } else if  ($request->input('filter') == 'oldest') { 
    
            $items = StudentProduct::orderBy('created_at','asc')->where('subcategory', $request->subcategory)->get();
      
    } 


    }


return response()->json([
    'status'=>200, 
    'product'=>$items,  
]); 


}


public function getHistory() { 

    $student_id = auth('sanctum')->user()->id; 

     $orders = Order::orderBy('created_at','desc')->where('student_id', $student_id)->get();
     
     $items = [];

     foreach ($orders as $order) { 

        $ordereditems  = Ordereditems::where('order_id', $order->id)->get(); 

        foreach ($ordereditems as $ordereditem) {

        if ($ordereditem->product_type == 'adminproduct') { 


         $product =  Product::where('id', $ordereditem->product_id)->first(); 

         $items [] = [
            'id'=>$product->id, 
            'image' => $product->image,
            'name' => $product->name,
            'price'=>$product->price, 
            'saleprice'=>$product->saleprice, 
            'category'=>$product->category,
            'subcategory'=>$product->subcategory, 
            'delivery'=>$ordereditem->delivery, 
            'created'=>$ordereditem->created_at->diffForHumans(), 
        ]; 


        } else if ($ordereditem->product_type == 'studentproduct') {

            $product =  StudentProduct::where('id', $ordereditem->product_id)->first(); 


            $items [] = [
                'id'=>$product->id, 
                'image' => $product->image,
                'name' => $product->name,
                'price'=>$product->price, 
                'delivery'=>$product->delivery,
                'description'=>$product->description,
                'category'=>$product->category,
                'subcategory'=>$product->subcategory, 
                'delivery'=>$ordereditem->delivery, 
                'created'=>$ordereditem->created_at->diffForHumans(),
    
            ]; 
   
        }

    }

     }
        

    return response()->json([
        'status'=>200, 
        'order'=>$items,  
    ]); 

}


public function getFeedback($id) {

    $feedbacks = Feedback::orderBy('created_at','desc')->where('product_id', $id)->get(); 

    $reviews = []; 
    
    foreach ($feedbacks as $feedback) { 

        

      $student = User::where('id', $feedback->student_id)->first();



      $reviews [] = [
          'classified' => $feedback->classified, 
          'text' => $feedback->text, 
          'created_at'=>$feedback->created_at->diffForHumans(), 
          'username'=>$student->username,
          'image'=>$student->image, 


      ]; 

    }

    return response()->json([
        'status'=>200, 
        'feedback'=>$reviews,  
    ]); 


}


public function homepageProducts() {

 $featured  = Product::where('featured', '1')->get();

 $sale  = Product::where('sale', '1')->get();

 $adminProducts = Product::orderBy('purchased','DESC')->limit(2)->get();



 $popular = []; 

 foreach ($adminProducts as $adminProduct) {


    
    $popular [] = [
        'id'=>$adminProduct->id, 
        'image' => $adminProduct->image,
        'name' => $adminProduct->name,
        'price'=>$adminProduct->price, 
        'category'=>$adminProduct->category,
        'subcategory'=>$adminProduct->subcategory, 
        'type'=>'adminproduct', 
     
    ]; 



 }

 $studentProducts = StudentProduct::orderBy('purchased','DESC')->limit(2)->get();


 foreach ($studentProducts as $studentProduct) {


    
    $popular [] = [
        'id'=>$studentProduct->id, 
        'image' => $studentProduct->image,
        'name' => $studentProduct->name,
        'price'=>$studentProduct->price, 
        'category'=>$studentProduct->category,
        'subcategory'=>$studentProduct->subcategory, 
        'type'=>'studentproduct', 
    ]; 



 }

    return response()->json([
        'status'=>200, 
        'featured'=>$featured,  
        'popular'=>$popular, 
        'sale'=>$sale, 
    ]); 


}



} 
