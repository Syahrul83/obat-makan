<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Models\MenuUserModel as MenuUser;

class UsersController extends Controller
{
    public function index() 
    {
        $title = 'Data Users';
        $page  = 'data-users';
        return view('Admin.users.main',compact('title','page'));
    }

    public function tambah() 
    {
        $title     = 'Tambah Users';
        $page      = 'data-users';
        return view('Admin.users.form-users',compact('title','page'));
    }

    public function edit($id) 
    {
        $title     = 'Edit Users';
        $page      = 'data-users';
        $row       = User::where('id_users',$id)->whereNotIn('level_user',[2])->firstOrFail();
        $menu_user = MenuUser::where('id_users',$id)->get();
        return view('Admin.users.form-users',compact('title','page','row','menu_user'));
    }

    public function statusUser($id) 
    {
        $active = User::where('id_users',$id)->firstOrFail()->active;
        if ($active == 1) {
            User::where('id_users',$id)->update(['active'=>0]);
            $message = 'Berhasil Non Aktifkan';
        }
        else {
            User::where('id_users',$id)->update(['active'=>1]);
            $message = 'Berhasil Aktifkan'; 
        }
        return redirect('/admin/data-users')->with('message',$message);
    }

    public function delete($id) 
    {
        User::where('id_users',$id)
            ->whereNotIn('level_user',[2])
            ->update(['status_delete'=>1]);
        return redirect('/admin/data-users')->with('message','Berhasil Hapus User');
    }

    public function save(Request $request) 
    {
        $name      = $request->nama;
        $username  = $request->username;
        $menu_user = $request->menu_user;
        if (User::where('username',$username)->count()>1) {
            return redirect()->back()->withErrors(['log'=>'Username Sudah Ada'])->withInput();
        }
        $password      = $request->password;
        $id            = $request->id;
        $array = [
            'name'          => $name,
            'username'      => $username,
            'password'      => bcrypt($password),
            'level_user'    => 1,
            'active'        => 1,
        ];
        if ($id == '') {
            $id_user = User::insertGetId($array);

            foreach ($menu_user as $key => $value) {
                if (str_contains($value,'|')) {
                    $explode     = explode('|', $value);
                    $menu_child  = $explode[0];
                    $menu_parent = $explode[1];
                }
                else {
                    $menu_child  = $value;
                    $menu_parent = '-';
                }
                $menu_user__[] = [
                    'id_users'    => $id_user,
                    'menu_child'  => $menu_child,
                    'menu_parent' => $menu_parent
                ];
            }

            MenuUser::insert($menu_user__);
            $message = 'Berhasil Input User';
        }
        else {
            if ($username == '' && $password == '') {
                unset($array['username']);
                unset($array['password']);
            }
            elseif($username == '') {
                unset($array['username']);
            }
            elseif ($password == '') {
                unset($array['password']);
            }
            unset($array['level_user']);
            unset($array['active']);

            MenuUser::where('id_users',$id)->delete();
            User::where('id_users',$id)->update($array);

            foreach ($menu_user as $key => $value) {
                if (str_contains($value,'|')) {
                    $explode     = explode('|', $value);
                    $menu_child  = $explode[0];
                    $menu_parent = $explode[1];
                }
                else {
                    $menu_child  = $value;
                    $menu_parent = '-';
                }
                $menu_user__[] = [
                    'id_users'    => $id,
                    'menu_child'  => $menu_child,
                    'menu_parent' => $menu_parent
                ];
            }

            MenuUser::insert($menu_user__);

            $message = 'Berhasil Update User';
        }
        return redirect('/admin/data-users')->with('message',$message);
    }
}
