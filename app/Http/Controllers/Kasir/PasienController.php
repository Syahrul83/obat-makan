<?php

namespace App\Http\Controllers\Kasir;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PasienModel as Pasien;

class PasienController extends Controller
{
    public function index() {
        $title = 'Data Pasien';
        $link  = 'data-master';
        $page  = 'data-pasien';
        return view('Kasir.pasien.main',compact('title','link','page'));
    }

    public function edit($id) {
        $title = 'Form Data Pasien';
        $link  = 'data-master';
        $page  = 'data-pasien';
        $row   = Pasien::where('id_pasien',$id)->firstOrFail();
        return view('Kasir.pasien.form-pasien',compact('title','link','page','row'));
    }

    public function delete($id) {
        Pasien::where('id_pasien',$id)->update(['status_delete'=>1]);
        return redirect('/kasir/data-pasien')->with('message','Berhasil Hapus Pasien');
    }

    public function save(Request $request) {
        $nama_pasien          = $request->nama_pasien;
        $nomor_telepon_pasien = $request->nomor_telepon_pasien;
        $alamat_pasien        = $request->alamat_pasien;
        $id                   = $request->id;

        $array = [
            'nama_pasien'          => $nama_pasien,
            'nomor_telepon_pasien' => $nomor_telepon_pasien,
            'alamat_pasien'        => $alamat_pasien,
        ];

        if ($id == '') {
            $array['status_delete'] = 0;
            Pasien::create($array);
            $message = 'Berhasil Input Data Pasien';            
        }
        else {
            Pasien::where('id_pasien',$id)->update($array);
            $message = 'Berhasil Update Data Pasien';
        }
        return redirect('/kasir/data-pasien')->with('message',$message);
    }
}
