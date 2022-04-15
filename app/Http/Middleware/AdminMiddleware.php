<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {


        //check if user is logged in before checking thier privileges
        if(Auth::check()) 
        { 
        //When handling an incoming request authenticated by Sanctum, you may determine if the token has a given ability using the tokenCan method.
        //only a user that is an admin can access admin functionalities. 
        if(auth()->user()->tokenCan('server:admin')) {
            return $next($request);   
        
        }

        //if user does not have the token ability of an admin, send this response back to react.js
        else { 
            return response()->json([
                'message' => 'Access Denied!', 
            ], 403); 
        }
    }
        
         else 
         {
             //if not logged send this response back to react.js
                return response()->json([
                    'status' => 401, 
                    'message' => 'Please Login First.',

                ]);


            }
        }

    }


