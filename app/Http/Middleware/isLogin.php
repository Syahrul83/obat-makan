<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class isLogin
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
        if (Auth::check()) {
            if (Auth::user()->level_user == 1) {
                return redirect('/kasir/panel');
            }
            elseif (Auth::user()->level_user == 2) {
                return redirect('/admin/panel');
            }
        }
        return $next($request);
    }
}