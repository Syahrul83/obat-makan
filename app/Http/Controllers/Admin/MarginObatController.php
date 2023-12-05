<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MarginObatModel as MarginObat;
use App\Models\ObatModel as Obat;

class MarginObatController extends Controller
{
    public function index() 
    {
        $title = 'Margin Obat | Admin';
        $link  = 'data-master';
        $page  = 'margin-obat';
        $check = MarginObat::count();
        return view('Admin.margin-obat.main',compact('title','link','page','check'));
    }

    public function tambah() 
    {
        $title = 'Form Margin Obat | Admin';
        $link  = 'data-master';
        $page  = 'margin-obat';
        return view('Admin.margin-obat.form-margin-obat',compact('title','link','page'));
    }

    public function edit($id) 
    {
        $title = 'Form Margin Obat | Admin';
        $link  = 'data-master';
        $page  = 'margin-obat';
        $row   = MarginObat::where('id_margin_obat',$id)->firstOrFail();
        return view('Admin.margin-obat.form-margin-obat',compact('title','link','page','row'));
    }

    public function save(Request $request) 
    {
        $margin_upds   = $request->margin_upds;
        $margin_resep  = $request->margin_resep;
        $margin_relasi = $request->margin_relasi;
        $id            = $request->id;

        $array = [
            'margin_upds'   => $margin_upds,
            'margin_resep'  => $margin_resep,
            'margin_relasi' => $margin_relasi
        ];
        if ($id == '') {
            MarginObat::create($array);
            $message = 'Berhasil Input Margin Obat';
        }
        else {
            $obat = Obat::where('status_delete',0)->get();
            MarginObat::where('id_margin_obat',$id)->update($array);
            foreach ($obat as $key => $value) {
                if ($value->kunci_hja_upds != 1) {
                    $hja_upds   = $value->harga_modal_ppn + (($value->harga_modal_ppn * $margin_upds) / 100);
                }
                if ($value->kunci_hja_resep != 1) {
                    $hja_resep  = $value->harga_modal_ppn + (($value->harga_modal_ppn * $margin_resep) / 100);
                }
                if ($value->kunci_hja_relasi != 1) {
                    $hja_relasi = $value->harga_modal_ppn + (($value->harga_modal_ppn * $margin_relasi) / 100);
                }

                 $data_update = [
                     'hja_upds'     => $hja_upds,
                     'hja_resep'    => $hja_resep,
                     'hja_relasi'   => $hja_relasi 
                 ];

                Obat::where('id_obat',$value->id_obat)->update($data_update);
            }
            $message = 'Berhasil Update Margin Obat';
        }

        return redirect('/admin/margin-obat')->with('message',$message);
    }
}
