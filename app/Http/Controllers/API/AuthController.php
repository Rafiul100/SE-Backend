<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Validator; 

class AuthController extends Controller
{


public function register(Request $request) { 

//all users data is validated to check if required information is given while meeting certain constraints.  
    $validator =   Validator::make($request->all(), [
        'name'=> 'required|max:191',
        'email' => 'required|email|max:191|unique:users,email',
        'password' => 'required|min:6', //minimum of 6 characters 
        'password2' => 'required|same:password', //passord2 should match password  
        'gender'=> 'required',


    ]);

    //if contraints are not met, validator messages are sent back to react. 
    if($validator->fails()) { 
        return response()->json([
            'email_error' => $validator->messages(), 
        ]);
    }
else { 

    //if constraints are met, a new user is created, storing the requested data given by user to new fields in the user table. 
        $user = new User; 
        $user->username = $request->input('name'); 
        $user->email = $request->input('email'); 
        $user->password = Hash::make($request->input('password')); 
        $user->gender = $request->input('gender'); 
        $user->university = $request->input('university') ? : '';
        //$user->address = $request->input('address'); 
        $user->firstname = '';
        $user->lastname = '';
        $user->phoneNo = '';
        $user->address = '';
        $user->city = '';
        $user->postcode = '';
        $user->image = 'noimage'; 
      
     
        $user->save(); 

        //token is used to authienticate API requests to your application
        //the token is created with a users email address since its unique. 
        //therefore a unique token is created all the time.
        $token = $user->createToken($user->email.'_Token')->plainTextToken;


        //users name, account and token is sent back to react which will be saved in local storage
        //when signing in. This information will be used to determine what pages users are authorised to view and whether a user is logged in or not.  
        return response()->json([
            'status' => 200, 
            'name' => $user->name, 
            'role' => $user->role, 
            'token' =>$token, 

        ]); 
    }
}



public function signin(Request $request) { 

    $validator = Validator::make($request->all(), [
    'email' => 'required|max:191', 
    'password'=>'required', 
    ]); 


    if ($validator->fails()) {
        return response()->json([
            'validation_errors' => $validator->messages(), 
        ]);
    }

    else 
    {

        //a user variable is created when the email user sent matches an email found in the user table
        $user = User::where('email', $request->email)->first();

        //if a user variable is not created because the email was not found in the database, 
        //or the password user sent does not match the password found in the database for that email. send a invalid message back to react for users to see. 
        if (! $user || ! Hash::check($request->password, $user->password)) { 
            return response()->json([
                'status' => 401,
                'message' => 'Invalid Credentials', 
            ]);

        }
        else  
        {
          
                 //Sanctum allows you to assign "abilities" to tokens. 
            if($user->role == 1 ) { 
                //if the role column in the database is equal to 1, then give user the token ability of being an admin 
                $token = $user->createToken($user->email.'_AdminToken', ['server:admin'] )->plainTextToken;
            } else if ($user->role == 0) {
                 //if the role column in the database is equal to 0, then give user the token ability of being a student that can buy. 
                $token = $user->createToken($user->email.'_Token', ['server:studentbuy'])->plainTextToken;
                  
                /*if the role column in the database is equal to 2, then give user the token ability of being a student that can sell. 
            } else if ($user->role == 2) { 

                $token = $user->createToken($user->email.'_Token', ['server:studentsell'])->plainTextToken;
              */
            }

           

            //if sign in information is provided while meeting constraints, the email is found in the database 
            //and the password sent by the user matches the password for that email, send users information back to react. 
            return response()->json([
                'status' => 200, 
                'name' => $user->username, 
                'id' => $user->id, 
                'token' =>$token,
                'role' => $user->role,
                'message' => 'Logged in Successfully', 
    
            ]); 

        } 
    }

}


public function signout() { 

    //get the token of the user that sent the logout request and delete it from the personal_access_tokens table. 
    auth()->user()->tokens()->delete(); 
    return response()->json([
        'status' => 200, 
        'message' => 'logged out'
    ]); 

}


}









