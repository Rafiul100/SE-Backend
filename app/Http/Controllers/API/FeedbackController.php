<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; 
use App\Models\Feedback;

class FeedbackController extends Controller
{
    public function addFeedback(Request $request) { 

        $validator = Validator::make($request->all(), [
            'classified' => 'required', 
            'text'=>'required', 
            ]); 
        
        
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->messages(), 
                ]);
            } else { 

                $student_id =  auth('sanctum')->user()->id; 

                $feedback = new Feedback; 
                $feedback->classified = $request->input('classified'); 
                $feedback->text = $request->input('text'); 
                $feedback->student_id = $student_id; 
                $feedback->product_id = $request->input('product_id');  

                $feedback->save(); 


                return response()->json([
                    'status'=>200, 
                    'message' => 'Feedback has been successfully sent', 
                ]);



            }



    }
}
