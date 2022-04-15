<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\StudentProduct;
use Illuminate\Support\Facades\Validator; 
use Illuminate\Support\Facades\File; 
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Str;

class ProductController extends Controller
{

public function view() { 

    $products = Product::all();
    return response()->json([
        'status'=>200, 
        'products'=>$products
    ]);


}


public function studentview() { 

    $products = StudentProduct::all();
    return response()->json([
        'status'=>200, 
        'products'=>$products
    ]);


}

    public function store(Request $request) { 

        $validator = Validator::make($request->all(), [
            'name'=>'required|max:25', 
            'price'=>'required|numeric|min:1|max:100', 
            'stock'=>'required|numeric|min:1|max:100', 
            'saleprice'=>'numeric|min:0|max:100', 
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

            $product = new Product; 

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
            $product->saleprice = $request->input('saleprice') ? $request->input('saleprice') : 0;

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


            $count = DB::table('products')->where('featured',  '=', '1')->count();
            
            $countsale = DB::table('products')->where('sale',  '=', '1')->count();
      
       
        if ($count >= '4' && $request->input('featured') == true) {

            return response()->json([
                'status'=>404,
                'message'=>'You can only have 4 featured products. uncheck other featured products to check this one.',  

            ]); 

        }
        
        if ($request->input('sale') == true && $request->saleprice == '0'  ||  $request->saleprice == '') {

            return response()->json([
                'status'=>404,
                'message'=>'require a sale price to check the sale check box',  

            ]); 
  

        }  else if ($request->saleprice > $request->price) {

            return response()->json([
                'status'=>404,
                'message'=>'sale price cannot be greater than actual price',  

            ]); 


        } else if ($countsale >= '4' && $request->input('sale') == true) {

                return response()->json([
                    'status'=>404,
                    'message'=>'you can only have 4 products on sale on the home page',  
    
                ]); 

            } else {

                $product->featured = $request->input('featured') == true ? '1':'0';
                $product->sale = $request->input('sale') == true ? '1':'0';

            }
            
            $product->save(); 

          
            return response()->json([
                'status'=>200,
                'message'=>'product Added Successfully', 
                'count'=> $count,  

            ]);

        }

    }


    public function edit($id) { 

        $product = Product::find($id);

        if($product) { 
            return response()->json([
                'status'=>200,
                'product'=>$product,
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
            'saleprice'=>'numeric|min:0|max:100', 
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

            $product = Product::find($id); 
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
            $product->saleprice =  $request->input('saleprice') ? $request->input('saleprice') : 0;

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
            $product->status = $request->input('status') == true ? '1':'0';

            $count = DB::table('products')->where('featured',  '=', '1')->count();
            $countsale = DB::table('products')->where('sale',  '=', '1')->count();

            if ($count >= '4' && $request->input('featured') == true) {

            return response()->json([
                'status'=>405,
                'message'=>'You can only have 4 featured products. uncheck other featured products to check this one.',  

            ]);

        }

            if ($request->input('sale') == true && $request->saleprice == '0' ||  $request->saleprice == '') {

                return response()->json([
                    'status'=>405,
                    'message'=>'require a sale price to check the sale check box',  
    
                ]); 
      
    
            } else if ($request->saleprice > $request->price) {

                return response()->json([
                    'status'=>405,
                    'message'=>'sale price cannot be greater than actual price',  
    
                ]); 
    
    
            } else if ($countsale == '4' && $request->input('sale') == true) {

                return response()->json([
                    'status'=>405,
                    'message'=>'you can only have 4 sale products on home page',  
    
                ]);


            } else {

                $product->featured = $request->input('featured') == true ? '1':'0';
                $product->sale = $request->input('sale') == true ? '1':'0';

            } 

            $product->update(); 


            return response()->json([
                'status'=>200,
                'message'=>'Product Updated Successfully', 
                'count'=>$count,  

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

$item = DB::table('products')->where('category', $request->category)->get();

  
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



public function adminEdit($id) { 

    $product = StudentProduct::find($id);

    if($product) { 
        return response()->json([
            'status'=>200,
            'product'=>$product,
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



public function adminUpdate(Request $request, $id) { 


    $validator = Validator::make($request->all(), [
        'name'=>'required|max:191', 
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

        $product = StudentProduct::find($id); 
        if($product) { 

        $product->name = $request->input('name');
        $product->price = $request->input('price');
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
        $product->status = $request->input('status'); 

        if ($request->status == '1') { 

            $product->admindeleted = '1';

        } else { 
            $product->admindeleted = '0';
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


public function deleteProduct($id) {

  
    $item = Product::where('id', $id)->first();
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
