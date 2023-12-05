<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class isKasir
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
            if (Auth::user()->level_user == 1 && Auth::user()->active == 1) {
                true;
            }
            elseif (Auth::user()->level_user != 1) {
                return redirect('/login');
            }
            elseif (Auth::user()->status_delete == 1 || Auth::user()->active == 0) {
                Auth::logout();
                return redirect('/login')->with('log','Silahkan Login Terlebih Dahulu');
            }
        }
        else {
            return redirect('/login')->with('log','Silahkan Login Terlebih Dahulu');
        }
        return $next($request);
    }
}
