<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class StudentSellMiddleware
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

        if(Auth::check()) 
        { 
        //When handling an incoming request authenticated by Sanctum, you may determine if the token has a given ability using the tokenCan method.
        //only a user with the ability of an admin can access admin functionalities. 
        if(auth()->user()->tokenCan('server:studentsell')) {
            return $next($request);   
        
        }

        else { 
            return response()->json([
                'message' => 'Access Denied!', 
            ], 403); 
        }
    }
        
         else 
         {
                return response()->json([
                    'status' => 401, 
                    'message' => 'Please Login First.',

                ]);


            }
        }

    }


