<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Models\ProfileInstansiModel as ProfileInstansi;
use Auth;

class PanelController extends Controller
{
    public function index() 
    {
		$title = 'Dashboard';
		$page  = 'dashboard';
    	return view('Admin.panel',compact('title','page'));
    }

    public function ubahProfile() {
        $title = 'Ubah Profile | Admin';

        if (ProfileInstansi::count() > 0) {
            $profile_instansi = ProfileInstansi::firstOrFail();
            $compact          = compact('title','profile_instansi');
        }
        else {
            $compact = compact('title');
        }

        return view('Admin.ubah-profile',$compact);
    }

    public function saveProfile(Request $request) {
        $name                   = $request->nama;
        $username               = $request->username;
        $password               = $request->password;
        $nama_instansi          = $request->nama_instansi;
        $alamat_instansi        = $request->alamat_instansi;
        $nomor_telepon_instansi = $request->nomor_telepon_instansi;
        $id_profile_instansi    = $request->id_profile_instansi;

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

        $data_instansi = [
            'nama_instansi'          => $nama_instansi,
            'alamat_instansi'        => $alamat_instansi,
            'nomor_telepon_instansi' => $nomor_telepon_instansi
        ];

        User::where('id_users',Auth::id())->update($data_user);

        if ($id_profile_instansi == '') {
            ProfileInstansi::create($data_instansi);
        }
        else {
            ProfileInstansi::where('id_profile_instansi',$id_profile_instansi)->update($data_instansi);
        }

        $message = 'Berhasil Update Profile';
        return redirect('/admin/ubah-profile')->with('message',$message);
    }
}
