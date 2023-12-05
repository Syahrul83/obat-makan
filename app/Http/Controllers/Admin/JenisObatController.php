<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\JenisObatModel as JenisObat;

class JenisObatController extends Controller
{
    public function index() 
    {
        $title = 'Data Jenis Obat | Admin';
        $link  = 'obat';
        $page  = 'data-jenis-obat';
        return view('Admin.jenis-obat.main',compact('title','link','page'));
    }

    public function tambah() 
    {
        $title = 'Form Jenis Obat | Admin';
        $link  = 'obat';
        $page  = 'data-jenis-obat';
        return view('Admin.jenis-obat.form-jenis-obat',compact('title','link','page'));
    }

    public function edit($id) 
    {
        $title = 'Form Jenis Obat | Admin';
        $link  = 'obat';
        $page  = 'data-jenis-obat';
        $row   = JenisObat::where('id_jenis_obat',$id)->firstOrFail();
        return view('Admin.jenis-obat.form-jenis-obat',compact('title','link','page','row'));
    }

    public function delete($id) 
    {
        JenisObat::where('id_jenis_obat',$id)->update(['status_delete'=>1]);
        return redirect('/admin/data-jenis-obat')->with('message','Berhasil Hapus Jenis Obat');
    }

    public function save(Request $request) 
    {
        $nama_jenis_obat = $request->nama_jenis_obat;
        $id              = $request->id;

        $array = [
            'nama_jenis_obat' => $nama_jenis_obat,
            'status_delete'   => 0
        ];
        if ($id == '') {
            JenisObat::create($array);
            $message = 'Berhasil Input Jenis Obat';
        }
        else {
            JenisObat::where('id_jenis_obat',$id)->update($array);
            $message = 'Berhasil Update Jenis Obat';
        }

        return redirect('/admin/data-jenis-obat')->with('message',$message);
    }
}
