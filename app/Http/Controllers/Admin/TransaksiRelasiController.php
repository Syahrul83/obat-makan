<?php

namespace App\Http\Controllers\Admin;

use App\Models\Debitur;
use Illuminate\Http\Request;
use App\Models\ObatModel as Obat;
use App\Http\Controllers\Controller;
use App\Models\KreditModel as Kredit;

class TransaksiRelasiController extends Controller
{
    public function transaksi()
    {
        $obat      = Obat::where('status_delete', 0)->get();
        $debitur   = Debitur::where('status_delete', 0)->get();
        $pelanggan = Kredit::all();
        return view('Admin.transaksi-relasi', compact('obat', 'pelanggan', 'debitur'));
    }
}
