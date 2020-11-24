<?php

namespace App\Http\Middleware;

use Closure;

class edadmiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->edad<=60 &&$request->edad>=18){
        return $next($request);
        }
        else 
        return abort(403,"no es el rango de edad");

    }
}
