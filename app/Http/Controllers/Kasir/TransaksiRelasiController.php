<?php

namespace App\Http\Controllers\Kasir;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ObatModel as Obat;
use App\Models\KreditModel as Kredit;

class TransaksiRelasiController extends Controller
{
    public function transaksi()
    {
        $obat      = Obat::where('status_delete',0)->get();
        $pelanggan = Kredit::all();
        return view('Kasir.transaksi-relasi',compact('obat','pelanggan'));
    }
}
