<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PersenPpnModel as PersenPpn;
use App\Models\ObatModel as Obat;
use App\Models\MarginObatModel as MarginObat;

class PersenPpnController extends Controller
{
    public function index()
    {
        $title = 'Persen PPN | Admin';
        $link  = 'data-master';
        $page  = 'data-ppn';

        return view('Admin.data-ppn.main',compact('title','link','page'));
    }

    public function edit($id)
    {
        $title = 'Form Persen PPN | Admin';
        $link  = 'data-master';
        $page  = 'data-ppn';
        $row   = PersenPpn::where('id_persen_ppn',$id)->firstOrFail();

        return view('Admin.data-ppn.form-data-ppn',compact('title','link','page','row'));
    }

    public function save(Request $request)
    {
        $ppn           = $request->ppn;
        $id_persen_ppn = $request->id_persen_ppn;

        $data_persen_ppn = [
            'ppn' => $ppn
        ];

        PersenPpn::where('id_persen_ppn',$id_persen_ppn)->update($data_persen_ppn);

        $obat        = Obat::where('status_delete',0)->get();
        $margin_obat = MarginObat::firstOrFail();

        foreach ($obat as $key => $value) {
            $harga_modal_ppn = $value->harga_modal + (($value->harga_modal * $ppn) / 100);
            if ($value->kunci_hja_upds != 1) {
                $hja_upds        = $harga_modal_ppn + (($harga_modal_ppn * $margin_obat->margin_upds) / 100);
            }
            if ($value->kunci_hja_resep != 1) {
                $hja_resep       = $harga_modal_ppn + (($harga_modal_ppn * $margin_obat->margin_resep) / 100);
            }
            if ($value->kunci_hja_relasi != 1) {
                $hja_relasi      = $harga_modal_ppn + (($harga_modal_ppn * $margin_obat->margin_relasi) / 100);
            }

            $update_obat = [
                'harga_modal_ppn' => $harga_modal_ppn,
                'hja_upds'        => $hja_upds,
                'hja_resep'       => $hja_resep,
                'hja_relasi'      => $hja_relasi
            ];

            Obat::where('id_obat',$value->id_obat)->update($update_obat);
        }

        return redirect('/admin/data-ppn')->with('message','Berhasil Update PPn');
    }
}
