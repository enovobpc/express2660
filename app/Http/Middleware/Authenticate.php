<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
     /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {

        //Só para API usada por clientes
        if ($guard == 'api' && Auth::guard('api')->guest())
        {
            return response()->json([
                'error'   => 'unauthenticated customer',
                'message' => 'Your authentication on API is wrong or has failed.',
                'hint'    => 'Check if your bearer access token is valid or generate a new bearer access token.'
            ], 401);
        }

        if ($guard != 'api' && Auth::guard('customer')->guest())
        {
            if ($request->expectsJson()) {
                return response()->json([
                    'error'   => 'unauthenticated',
                    'message' => 'Your authentication on API is wrong or has failed.',
                    'hint'    => 'Check if your bearer access token is valid or generate a new bearer access token.'
                ], 401);
            } elseif ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest(route('account.login')); // <--- note this
            }
        }

        return $next($request);
    }
}
