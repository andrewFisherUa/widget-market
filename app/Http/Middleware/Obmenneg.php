<?php

namespace App\Http\Middleware;

use Closure;

class Obmenneg
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
		if (\Auth::user()->id!=1 and \Auth::user()->id!=2 and \Auth::user()->id!=37)
        {
			return abort(404);
        }
        return $next($request);
    }
}
