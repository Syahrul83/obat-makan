<?php

namespace App\Http\Controllers\Kasir;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\KreditModel as Kredit;
use App\Models\KreditFakturModel as KreditFaktur;
use App\Models\KreditDetailModel as KreditDetail;
use App\Models\ProfileInstansiModel as ProfileInstansi;

class DataKreditController extends Controller
{
    public function index() 
    {
        $title = 'Data Kredit';
        $link  = 'penjualan';
        $page  = 'data-kredit';
        return view('Kasir.kredit.main',compact('title','link','page'));
    }

    public function delete($id) 
    {
        Kredit::where('id_kredit',$id)->delete();
        return redirect('/kasir/data-kredit')->with('message','Berhasil Hapus Data');
    }

    public function kreditFaktur($id)
    {
        $title = 'Data Faktur Kredit';
        $link  = 'penjualan';
        $page  = 'data-kredit';

        return view('Kasir.kredit.kredit-faktur',compact('id','link','title','page'));
    }

    public function deleteKreditFaktur($id,$id_faktur) 
    {
        KreditFaktur::where('id_kredit_faktur',$id_faktur)->delete();
        return redirect('/kasir/data-kredit/detail/'.$id)->with('message','Berhasil Hapus Data');
    }

    public function kreditDetail($id,$id_faktur) 
    {
        $title = 'Data Detail Kredit';
        $link  = 'penjualan';
        $page  = 'data-kredit';
        return view('Kasir.kredit.detail',compact('id','id_faktur','link','title','page'));
    }

    public function cetakInvoice($id,$id_faktur)
    {
        $profile_instansi = ProfileInstansi::firstOrFail();
        $nama_pelanggan   = Kredit::where('id_kredit',$id)->firstOrFail()->nama_pelanggan;
        $kredit_faktur    = KreditFaktur::join('users','kredit_faktur.id_users','=','users.id_users')
                                        ->where('id_kredit_faktur',$id_faktur)->firstOrFail();
        $data             = KreditDetail::join('obat','kredit_det.id_obat','=','obat.id_obat')->where('status_kredit',0)->where('id_kredit_faktur',$id_faktur)->get();
        $total_kredit     = KreditDetail::where('id_kredit_faktur',$id_faktur)->where('status_kredit',0)->sum('sub_total');

        return view('Kasir.print-kredit',compact('total_kredit','nama_pelanggan','data','profile_instansi','kredit_faktur'));
    }

    public function cetakKreditRange(Request $request,$id,$id_faktur)
    {   
        $from = reverse_date($request->from);
        $to   = reverse_date($request->to);

        $profile_instansi = ProfileInstansi::firstOrFail();
        $nama_pelanggan   = Kredit::where('id_kredit',$id)->firstOrFail()->nama_pelanggan;
        $kredit_faktur    = KreditFaktur::where('id_kredit_faktur',$id_faktur)->firstOrFail();
        $data             = KreditDetail::join('obat','kredit_det.id_obat','=','obat.id_obat')->whereBetween('tanggal_jatuh_tempo',[$from,$to])->where('status_kredit',0)->where('id_kredit_faktur',$id_faktur)->get();
        $total_kredit     = KreditDetail::where('id_kredit_faktur',$id_faktur)->whereBetween('tanggal_jatuh_tempo',[$from,$to])->where('status_kredit',0)->sum('sub_total');

        return view('Kasir.print-kredit',compact('total_kredit','nama_pelanggan','data','profile_instansi','kredit_faktur'));   
    }

    public function deleteKreditDetail($id,$id_faktur,$id_detail) 
    {
        KreditDetail::where('id_kredit_faktur',$id_faktur)->where('id_kredit_det',$id_detail)->delete();
        return redirect('/kasir/data-kredit/detail/'.$id.'/lihat-hutang/'.$id_faktur)->with('message','Berhasil Hapus Data');
    }

    public function lunasSemua($id,$id_faktur)
    {
        KreditDetail::where('id_kredit_faktur',$id_faktur)->update(['status_kredit' => 1]);

        return redirect('/kasir/data-kredit/detail/'.$id.'/lihat-hutang/'.$id_faktur)->with('message','Berhasil Lunaskan Semua Hutang');
    }

    public function lunasHutang($id,$id_faktur,$id_kredit_det)
    {
        KreditDetail::where('id_kredit_faktur',$id_faktur)->where('id_kredit_det',$id_kredit_det)->update(['status_kredit' => 1]);

        return redirect('/kasir/data-kredit/detail/'.$id.'/lihat-hutang/'.$id_faktur)->with('message','Berhasil Lunaskan Hutang');
    }
}
