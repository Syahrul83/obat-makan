<?php

namespace App\Http\Controllers\Kasir;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DokterModel as Dokter;

class DokterController extends Controller
{
    public function index() {
        $title = 'Data Dokter | Kasir';
        $link  = 'data-master';
        $page  = 'data-dokter';
        return view('Kasir.dokter.main',compact('title','link','page'));
    }

    public function edit($id) {
        $title = 'Form Dokter | Kasir';
        $link  = 'data-master';
        $page  = 'data-dokter';
        $row   = Dokter::where('id_dokter',$id)->firstOrFail();
        return view('Kasir.dokter.form-dokter',compact('title','link','page','row'));
    }

    public function delete($id) {
        Dokter::where('id_dokter',$id)->update(['status_delete'=>1]);
        return redirect('/kasir/data-dokter')->with('message','Berhasil Hapus Data Dokter');
    }

    public function save(Request $request) {
        $nama_dokter = $request->nama_dokter;
        $id          = $request->id_dokter;

        $data_dokter = [
            'nama_dokter'   => $nama_dokter
        ];

        if ($id == '') {
            $data_dokter['status_delete'] = 0;
            Dokter::create($data_dokter);
            $message = 'Berhasil Input Dokter';
        }
        else {
            Dokter::where('id_dokter',$id)->update($data_dokter);
            $message = 'Berhasil Update Dokter';
        }

        return redirect('/kasir/data-dokter')->with('message',$message);
    }
}
