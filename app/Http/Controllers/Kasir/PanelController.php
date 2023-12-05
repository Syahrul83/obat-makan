<?php

namespace App\Http\Controllers\Kasir;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Auth;

class PanelController extends Controller
{
    public function index() 
    {
		$title = 'Dashboard | Kasir';
		$page  = 'dashboard';
    	return view('Kasir.panel',compact('title','page'));
    }

    public function ubahProfile() {
        $title = 'Ubah Profile | Kasir';
        return view('Kasir.ubah-profile',compact('title'));
    }

    public function saveProfile(Request $request) {
        $name     = $request->nama;
        $username = $request->username;
        $password = $request->password;

        if (User::where('username',$username)->count()>1) {
            return redirect()->back()->withErrors(['log'=>'Username Sudah Ada'])->withInput();
        }
        $data_user = [
            'name'          => $name,
            'username'      => $username,
            'password'      => bcrypt($password)
        ];
        if ($username == '' && $password == '') {
            unset($data_user['username']);
            unset($data_user['password']);
        }
        elseif($username == '') {
            unset($data_user['username']);
        }
        elseif ($password == '') {
            unset($data_user['password']);
        }

        User::where('id_users',Auth::id())->update($data_user);
        $message = 'Berhasil Update Profile';

        return redirect('/kasir/ubah-profile')->with('message',$message);
    }
}
