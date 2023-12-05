<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TransaksiKasirModel as TransaksiKasir;
use App\Models\TransaksiKasirDetailModel as TransaksiKasirDetail;
use App\Models\ProfileInstansiModel as ProfileInstansi;

class DataTransaksiController extends Controller
{
    public function index() 
    {
        $title = 'Data Transaksi';
        $link  = 'penjualan';
        $page  = 'data-penjualan';
        return view('Admin.transaksi.main',compact('title','link','page'));
    }

    public function delete($id) 
    {
        TransaksiKasir::where('id_transaksi',$id)->delete();
        return redirect('/admin/data-penjualan')->with('message','Berhasil Hapus Data');
    }

    public function transaksiDetail($id) 
    {
        $title = 'Data Detail Transaksi';
        $link  = 'penjualan';
        $page  = 'data-penjualan';
        $nomor_transaksi = TransaksiKasir::where('id_transaksi',$id)->firstOrFail()->kode_transaksi;
        return view('Admin.transaksi.detail',compact('id','title','link','page','nomor_transaksi'));
    }

    public function deleteTransaksiDetail($id,$id_detail) 
    {
        TransaksiKasirDetail::where('id_transaksi',$id)->where('id_transaksi_det',$id_detail)->delete();
        return redirect('/admin/data-penjualan/detail/'.$id)->with('message','Berhasil Hapus Data');
    }

    public function cetakInvoice($id)
    {
        $profile_instansi = ProfileInstansi::firstOrFail();
        $transaksi        = TransaksiKasir::join('users','transaksi_kasir.id_users','=','users.id_users')
                                                ->where('id_transaksi',$id)->firstOrFail();
        $transaksi_detail = TransaksiKasirDetail::join('obat','transaksi_kasir_det.id_obat','=','obat.id_obat')->where('id_transaksi',$id)->get();

        $sum_diskon         = TransaksiKasirDetail::where('id_transaksi',$id)->sum('diskon');
        $sum_sub_total_obat = TransaksiKasirDetail::where('id_transaksi',$id)->sum('sub_total_obat');

        return view('Admin.print-bayar',compact('transaksi','transaksi_detail','profile_instansi','sum_diskon','sum_sub_total_obat'));
    }
}
