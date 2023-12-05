<?php

namespace App\Http\Controllers\Kasir;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ObatModel as Obat;
use App\Models\KartuStokModel as KartuStok;
use App\Models\ReturBarangModel as ReturBarang;
use App\Models\ReturBarangDetailModel as ReturBarangDetail;

class ReturBarangController extends Controller
{
    public function index()
    {
        $title = 'Retur Barang | Kasir';
        $page  = 'retur-barang';
        $link  = 'penjualan';

        return view('Kasir.retur-barang.main',compact('title','page','link'));
    }

    public function tambah()
    {
        $title       = 'Form Retur Barang | Kasir';
        $page        = 'retur-barang';
        $link        = 'penjualan';
        $obat        = Obat::where('status_delete',0)->get();
        $nomor_retur = generateCodeTrx('retur-barang');

        return view('Kasir.retur-barang.form-retur-barang',compact('title','page','link','obat','nomor_retur'));
    }

    public function save(Request $request)
    {
        $nomor_retur       = $request->nomor_retur;
        $tanggal_retur     = reverse_date($request->tanggal_retur);
        $nomor_transaksi   = $request->nomor_transaksi;
        $tanggal_transaksi = reverse_date($request->tanggal_transaksi);
        $keterangan        = $request->keterangan;
        $obat              = $request->obat;
        $stok              = $request->stok_retur;
        $stok_transaksi    = $request->stok_transaksi;
        $total_harga       = $request->total_harga;
        $harga_retur       = $request->harga_retur;
        $harga_fleksibel   = $request->harga_fleksibel;

        $data_retur = [
            'nomor_retur'         => $nomor_retur,
            'tanggal_retur'       => $tanggal_retur,
            'nomor_transaksi'     => $nomor_transaksi,
            'tanggal_transaksi'   => $tanggal_transaksi,
            'total_nominal_retur' => $total_harga,
            'keterangan_retur'    => '-'
        ];

        $id_retur_barang = ReturBarang::insertGetId($data_retur);
        
        foreach ($obat as $key => $value) {
            if (isset($stok[$key]) || $stok[$key] != null || $stok[$key] != '') {
                if (isset($harga_fleksibel[$key]) || $harga_retur[$key] == 0) {
                    $nominal_retur = $harga_fleksibel[$key];
                }
                else {
                    $nominal_retur = $harga_retur[$key];
                }
                $data_retur_detail = [
                    'id_retur_barang' => $id_retur_barang,
                    'id_obat'         => $obat[$key],
                    'stok_transaksi'  => $stok_transaksi[$key],
                    'stok_retur'      => $stok[$key],
                    'nominal_retur'   => $nominal_retur
                ];

                ReturBarangDetail::create($data_retur_detail);

                $stok_obat_old = Obat::where('id_obat',$obat[$key])
                                    ->firstOrFail()->stok_obat;

                $kartu_stok = [
                    'nomor_stok'    => $nomor_retur,
                    'tanggal_pakai' => $tanggal_retur,
                    'layanan'       => 'Retur Barang',
                    'id_obat'       => $obat[$key],
                    'beli'          => 0,
                    'jual'          => 0,
                    'retur_barang'  => $stok[$key],
                    'saldo'         => $stok[$key]+$stok_obat_old,
                    'keterangan'    => 'Retur Barang'
                ];

                KartuStok::create($kartu_stok);

                Obat::where('id_obat',$obat[$key])
                    ->update(['stok_obat' => $stok[$key] + $stok_obat_old]);
            }
        }

        return redirect('/kasir/retur-barang')->with('message','Berhasil Input Retur Barang');
    }

    public function detail($id)
    {
        $title = 'Retur Barang Detail | Kasir';
        $page  = 'retur-barang';
        $link  = 'penjualan';

        return view('Kasir.retur-barang.retur-barang-detail',compact('title','page','link','id'));
    }

    public function delete($id)
    {
        ReturBarang::where('id_retur_barang',$id)->delete();

        return redirect('/kasir/retur-barang')->with('message','Berhasil Hapus Data');
    }

    // public function deleteDetail($id,$id_detail)
    // {
    //     ReturBarangDetail::where('id_retur_barang_detail',$id_detail)->delete();

    //     return redirect('/kasir/retur-barang/detail/'.$id)->with('message','Berhasil Hapus Data');
    // }
}
