<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; 
use Illuminate\Support\Facades\File; 
use Illuminate\Support\Facades\DB; 
use App\Models\User;
use App\Models\StudentProduct;
use Illuminate\Support\Facades\Hash; 

class ProfileController extends Controller
{
    public function viewProfile($id) { 


        $user = User::find($id);
    
        if ($user) {
            return response()->json([
                'status'=>200, 
                'user'=>$user,
                'image'=>$user->image, 
              
            ]);

        } else { 

            return response()->json([
                'status'=>404, 
                'message'=>'Profile is not cannot be found',    
              
            ]);



        }
    }
    
    
    public function updateProfile(Request $request, $id) { 
    
        $validator = Validator::make($request->all(), [
            'username'=>'required|max:191', 
            'phoneNo'=>'max:11',
          
        ]);
    
        
        if($validator->fails()) 
        { 
            return response()->json([
                'status'=>422,
                'errors'=>$validator->messages(),
            ]);
    
        } 
        else { 
    
            $user = User::find($id); 
            if($user) { 
    
            $user->username = $request->input('username');
            $user->firstname = $request->input('firstname');
            $user->lastname = $request->input('lastname');
            $user->university = $request->input('university');
            $user->phoneNo = $request->input('phoneNo');
            $user->address = $request->input('address');
            $user->city = $request->input('city'); 
            $user->postcode = $request->input('postcode');
           

            
    
            //this if statement is ignored only if admin does not change the image file
            if($request->hasFile('image')) 
            { 
                //get the image path from the product and check if it exists, if so delete the path and have the path be updated with the new file given.
                $path = $user->image;
                if(File::exists($path)) { 
                    File::delete($path);
                }
                $file = $request->file('image'); 
                $extension = $file->getClientOriginalExtension();
                $filename = time().'.'. $extension;
                $file->move('uploads/profile/', $filename);
                $user->image = 'uploads/profile/'.$filename;
            }
    
    
            $user->update(); 

          $product = StudentProduct::where('student_id', $id)->first();
          
          if($product) { 

            StudentProduct::where('student_id', $id)->update(array('student_name' =>  $request->input('username')));

          }
            
          
            return response()->json([
                'status'=>200,
                'message'=>'Profile Updated Successfully',
                
    
            ]);
    
        } else { 
    
            return response()->json([
                'status'=>404,
                'message'=>'Profile Not Found',  
    
            ]);
    
    
        }
    
        }
    
    
    
    }
}
