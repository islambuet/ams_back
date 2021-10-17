<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\UserHelper;

class loggedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user=UserHelper::getLoggedUser();
        if(!$user){
            return response()->json(['error' => "USER_SESSION_EXPIRED",'errorMessage'=>__('response.USER_SESSION_EXPIRED')], 401);
        }
        return $next($request);
    }
}
