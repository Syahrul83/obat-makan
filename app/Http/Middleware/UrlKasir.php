<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UrlKasir
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
        $explode_url = explode('/', $request->path());
        if (menu_user_check(auth()->user()->id_users,'child',$explode_url[1]) == 'false') {
            return redirect('/kasir/panel')->with('log','Akun Anda Tidak Diberi Akses Ke Url '.$request->path());
        }
        return $next($request);
    }
}
