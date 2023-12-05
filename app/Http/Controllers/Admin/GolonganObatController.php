<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\GolonganObatModel as GolonganObat;

class GolonganObatController extends Controller
{
    public function index() 
    {
        $title = 'Golongan Obat | Admin';
        $link  = 'obat';
        $page  = 'data-golongan-obat';
        return view('Admin.golongan-obat.main',compact('title','link','page'));
    }

    public function tambah()
    {
        $title = 'Form Golongan Obat | Admin';
        $link  = 'obat';
        $page  = 'data-golongan-obat';
        return view('Admin.golongan-obat.form-golongan-obat',compact('title','link','page'));
    }

    public function edit($id)
    {
        $title = 'Form Golongan Obat | Admin';
        $link  = 'obat';
        $page  = 'data-golongan-obat';
        $row   = GolonganObat::where('id_golongan_obat',$id)->firstOrFail();
        return view('Admin.golongan-obat.form-golongan-obat',compact('title','link','page','row'));
    }

    public function save(Request $request)
    {
        $nama_golongan = $request->nama_golongan;
        $id            = $request->id_golongan_obat;

        $data_golongan_obat = [
            'nama_golongan' => $nama_golongan,
            'status_delete' => 0
        ];

        if ($id == '') {
            GolonganObat::create($data_golongan_obat);
            $message = 'Berhasil Input Golongan Obat';
        }
        else {
            GolonganObat::where('id_golongan_obat',$id)->update($data_golongan_obat);
            $message = 'Berhasil Update Golongan Obat';
        }
        return redirect('/admin/data-golongan-obat')->with('message',$message);
    }

    public function delete($id)
    {
        GolonganObat::where('id_golongan_obat',$id)->update(['status_delete'=>1]);
        return redirect('/admin/data-golongan-obat')->with('message','Berhasil Hapus Data');
    }
}
