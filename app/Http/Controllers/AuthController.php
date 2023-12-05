<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;

class AuthController extends Controller
{
    public function index() {
    	return view('login');
    }

    public function login(Request $request) {
    	$username = $request->username;
    	$password = $request->password;
    	if (Auth::attempt(['username'=>$username,'password'=>$password,'status_delete'=>0],true)) {
            if (Auth::user()->level_user == 2 && Auth::user()->active == 1) {
                return redirect('/admin/panel');
            }
            elseif(Auth::user()->level_user == 1 && Auth::user()->active == 1) {
                return redirect('/kasir/panel');
            }
            elseif (Auth::user()->active == 0) {
                Auth::logout();
                return redirect('/login')->with('log','Maaf Akun Anda Non Aktif');
            }
    	}
        else {
            return redirect('/login')->with('log','Kredensial Salah');
        }
    }

    public function user() {
        User::insert([
                0=>[
                    'name'          => 'Petugas Inventory',
                    'username'      => 'inventory',
                    'password'      => bcrypt('inventory'),
                    'level_user'    => 1,
                    'active'        => 1,
                    'status_delete' => 0
                ],
                1=>[
                    'name'          => 'Petugas Resep',
                    'username'      => 'resep',
                    'password'      => bcrypt('resep'),
                    'level_user'    => 0,
                    'active'        => 1,
                    'status_delete' => 0
                ]
            ]
        );
    }

    public function logout() {
    	Auth::logout();
    	return redirect('/');
    }
}
