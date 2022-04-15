<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentProduct;
use Illuminate\Support\Facades\Validator; 
use Illuminate\Support\Facades\File; 
use Illuminate\Support\Facades\DB; 
use App\Models\User;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Str;


class StudentProductController extends Controller
{


    public function view(Request $request) { 

       

        $products = DB::table('studentproducts')->where('student_id', $request->student_id)->get();
        return response()->json([
            'status'=>200, 
            'products'=>$products,
          
        ]);
    
    
    }
    

    public function store(Request $request) { 

        $validator = Validator::make($request->all(), [
            'name'=>'required|max:25', 
            'price'=>'required|numeric|min:1|max:100', 
            'stock'=>'required|numeric|min:1|max:100', 
            'category'=>'required|max:191', 
            'subcategory'=>'required|max:191', 
            'delivery'=>'required|numeric|min:1|max:14',
            'image'=>'required|image|mimes:jpeg,png,jpg|max:2048', 
           
        ]);

        
        if($validator->fails()) 
        { 
            return response()->json([
                'status'=>422,
                'errors'=>$validator->messages(),
            ]);

        } 
        else { 

            

            $count = DB::table('studentproducts')->where('student_id', $request->student_id)->where('status',  '<>', '1')->count();

            if ($count != '10') { 
                
            $product = new StudentProduct; 

        
          $digit = '.00'; 
          $price = $request->input('price'); 

          $contains = Str::contains($price, '.'); 

          if ($contains) {

            $price = $request->input('price'); 

          } else {

            $price = $price . $digit; 

          }


            $product->name = $request->input('name');
            $product->student_id = $request->input('student_id');
            $product->student_name = $request->input('student_name'); 
            $product->price = $price; 
            $product->stock = $request->input('stock');
            $product->category = $request->input('category');
            $product->subcategory = $request->input('subcategory');
            $product->delivery = $request->input('delivery');

            if($request->hasFile('image')) 
            { 
                $file = $request->file('image'); 
                $extension = $file->getClientOriginalExtension();
                $filename = time().'.'. $extension;
                $file->move('uploads/product/', $filename);
                $product->image = 'uploads/product/'.$filename;
            }

            
            $product->type = $request->input('type') ? : 'No type given'; 
            $product->description = $request->input('description') ? : 'A product description is not given';   
            $product->status = $request->input('status') == true ? '1':'0';

            $product->save(); 


            return response()->json([
                'status'=>200,
                'message'=>'Product Added Successfully',  
                'count'=> $count, 
               

            ]);

        } else  {


            return response()->json([
                'status'=>404,
                'message'=>'Product limit reached. Delete or hide products to add more.',  
               

            ]);

        }

        }

    }


    public function edit($id) 
    { 

        $product = StudentProduct::find($id);

        if($product) { 
            return response()->json([
                'status'=>200,
                'product'=>$product,
                'admindeleted'=>$product->admindeleted, 
            ]);
        } 
        else 
        { 
            return response()->json([
                'status'=>404,
                'message'=>'Product Was Not Found',
            ]);
        }
    }

 

    public function update(Request $request, $id) { 


        $validator = Validator::make($request->all(), [
            'name'=>'required|max:25', 
            'price'=>'required|numeric|min:1|max:100', 
            'stock'=>'required|numeric|min:1|max:100', 
            'category'=>'required|max:191', 
            'subcategory'=>'required|max:191', 
            'delivery'=>'required|numeric|min:1|max:14',
           
        ]);

        
        if($validator->fails()) 
        { 
            return response()->json([
                'status'=>422,
                'errors'=>$validator->messages(),
            ]);

        } 
        else { 

            
        $count = DB::table('studentproducts')->where('student_id', $request->student_id)->where('status',  '<>', '1')->count();

       

            $product = StudentProduct::find($id); 
            if($product) { 

                $digit = '.00'; 
                $price = $request->input('price'); 
      
                $contains = Str::contains($price, '.'); 
      
                if ($contains) {
      
                  $price = $request->input('price'); 
      
                } else {
      
                  $price = $price . $digit; 
      
                }


            $product->name = $request->input('name');
            $product->price = $price; 
            $product->stock = $request->input('stock');
            $product->category = $request->input('category');
            $product->subcategory = $request->input('subcategory');
            $product->delivery = $request->input('delivery');

            //this if statement is ignored only if admin does not change the image file
            if($request->hasFile('image')) 
            { 
                //get the image path from the product and check if it exists, if so delete the path and have the path be updated with the new file given.
                $path = $product->image;
                if(File::exists($path)) { 
                    File::delete($path);
                }
                $file = $request->file('image'); 
                $extension = $file->getClientOriginalExtension();
                $filename = time().'.'. $extension;
                $file->move('uploads/product/', $filename);
                $product->image = 'uploads/product/'.$filename;
            }

            $product->type = $request->input('type') ? : 'No type given'; 
            $product->description = $request->input('description') ? : 'A product description is not given';  

            if ($count == '10' && $product->status == '1' && $request->input('status') == '0') { 
                return response()->json([
                    'status'=>400,
                    'message'=>'Must hide another product to unhide this',  
    
                ]);
          
            } else { 

                $product->status = $request->input('status'); 

            }

            $product->update(); 


            return response()->json([
                'status'=>200,
                'message'=>'Product Updated Successfully',  

            ]);

        } else { 

            return response()->json([
                'status'=>404,
                'message'=>'Product Not Found',  

            ]);


        }

        }

}


    
public function filter(Request $request) {
    
    //$item = Product::where('category', 'fevava'); 

    //$filter = Product::find()->value('Toiletry');

    $item = StudentProduct::where([
        ['student_id',$request->student_id],
        ['category', $request->category]
    ])->get(); 

    //$products = DB::table('studentproducts')->where('student_id', $request->student_id)->get();
    //$item = DB::table('studentproducts')->where($products, $request->category)->get();

  
        if($item) { 

            return response()->json([
                'status'=>200,
                'product'=>$item, 

            ]);


        } else { 

            return response()->json([
                'status'=>404,
                'message'=> 'Product Not Found',

            ]);


        }


}


public function deleteProduct($id) {

  
    $item = StudentProduct::where('id', $id)->first();
    if($item) { 

        $item->delete(); 

        return response()->json([
            'status'=> 200, 
            'message'=>'Item removed successfully from cart', 
        ]);
    } else {
        
        return response()->json([
            'status'=> 404, 
            'message'=>'Item cannot be deleted', 
        ]);


    }  

}



}
  