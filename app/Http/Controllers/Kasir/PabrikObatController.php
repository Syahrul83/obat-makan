<?php

namespace App\Http\Controllers\Kasir;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PabrikObatModel as PabrikObat;

class PabrikObatController extends Controller
{
    public function index() 
    {
        $title = 'Data Pabrik Obat | Kasir';
        $link  = 'obat';
        $page  = 'data-pabrik-obat';
        return view('Kasir.pabrik-obat.main',compact('title','link','page'));
    }

    public function tambah() 
    {
        $title = 'Form Pabrik Obat | Kasir';
        $link  = 'obat';
        $page  = 'data-pabrik-obat';
        return view('Kasir.pabrik-obat.form-pabrik-obat',compact('title','link','page'));
    }

    public function edit($id) 
    {
        $title = 'Form Pabrik Obat | Kasir';
        $link  = 'obat';
        $page  = 'data-pabrik-obat';
        $row   = PabrikObat::where('id_pabrik_obat',$id)->firstOrFail();
        return view('Kasir.pabrik-obat.form-pabrik-obat',compact('title','link','page','row'));
    }

    public function delete($id) 
    {
        PabrikObat::where('id_pabrik_obat',$id)->update(['status_delete' => 1]);
        return redirect('/kasir/data-pabrik-obat')->with('message','Berhasil Hapus PabrikObat');
    }

    public function save(Request $request) 
    {
        $nama_pabrik   = $request->nama_pabrik;
        $nomor_hp      = $request->nomor_hp;
        $alamat_pabrik = $request->alamat_pabrik;
        $id            = $request->id;

        $array = [
            'nama_pabrik'          => $nama_pabrik,
            'nomor_telepon_pabrik' => $nomor_hp,
            'alamat_pabrik'        => $alamat_pabrik,
            'status_delete'        => 0,
        ];

        if ($id == '') {
            PabrikObat::create($array);
            $message = 'Berhasil Input Pabrik Obat';
        }
        else {
            PabrikObat::where('id_pabrik_obat',$id)->update($array);
            $message = 'Berhasil Update Pabrik Obat';
        }

        return redirect('/kasir/data-pabrik-obat')->with('message',$message);
    }
}
