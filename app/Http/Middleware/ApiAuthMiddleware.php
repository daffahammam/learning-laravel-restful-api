<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');
        $authenticated = true;

        if(!$token){
            $authenticated = false;
        }

        $user = User::where('token', $token)->first();
        if(!$user){
            $authenticated = false;
        }else {
            Auth::login($user);
        }



        if($authenticated){
            return $next($request);
        }else {
            return response()->json([
                'errors' => [
                    'message' =>[
                        'Unauthorized'
                        ]
                    ]
            ])->setStatusCode( 401);
        }
    }
}
