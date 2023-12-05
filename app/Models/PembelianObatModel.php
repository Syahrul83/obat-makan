<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembelianObatModel extends Model
{
    protected $table      = 'pembelian_obat';
    protected $primaryKey = 'id_pembelian_obat';
    protected $guarded    = [];
    public $timestamps    = false;

    public static function getData($id = '',$request) 
    {
        if ($id == '') {
            $db = self::join('supplier_obat','pembelian_obat.id_supplier','=','supplier_obat.id_supplier')
                        ->join('users','pembelian_obat.id_users','=','users.id_users')
                        ->where('kode_pembelian','like','%'.$request->kode_pembelian_cari.'%')
                        ->where('nomor_faktur','like','%'.$request->nomor_faktur_cari.'%')
                        ->where('nama_supplier','like','%'.$request->supplier_cari.'%');
                        // ->whereBetween('tanggal_terima',[$request->tanggal_dari_cari,$request->tanggal_sampai_cari])

            if ($request->tanggal_dari_cari != '' && $request->tanggal_sampai_cari != '') {
                $db->whereBetween('tanggal_terima',[reverse_date($request->tanggal_dari_cari),reverse_date($request->tanggal_sampai_cari)]);
            }
        }
        else {
            $db = self::join('supplier_obat','pembelian_obat.id_supplier','=','supplier_obat.id_supplier')
                        ->join('users','pembelian_obat.id_users','=','users.id_users')
                        ->where('kode_pembelian','like','%'.$request->kode_pembelian_cari.'%')
                        ->where('nomor_faktur','like','%'.$request->nomor_faktur_cari.'%')
                        ->where('nama_supplier','like','%'.$request->supplier_cari.'%')
                        // ->whereBetween('tanggal_terima',[$request->tanggal_dari_cari,$request->tanggal_sampai_cari])
                        ->where('pembelian_obat.id_users',$id);
            if ($request->tanggal_dari_cari != '' && $request->tanggal_sampai_cari != '') {
                $db->whereBetween('tanggal_terima',[reverse_date($request->tanggal_dari_cari),reverse_date($request->tanggal_sampai_cari)]);
            }
        }
        
        return $db->get();
    }

    public static function lastNumCode() {
        $db = self::query();
        if ($db->count() == 0) {
            $num = 1;
        }
        else {
            $explode = explode('-',$db->orderBy('kode_pembelian','desc')->firstOrFail()->kode_pembelian);
            $num = (int)$explode[1]+1;
        }
        return $num;
    }

    public static function getIdSupplierDistinct($from,$to)
    {
        $get = self::whereBetween('tanggal_jatuh_tempo',[$from,$to])
                    ->select('pembelian_obat.id_supplier')
                    ->distinct()
                    ->get();

        return $get;
    }

    public static function getIdSupplierKonsinyasi($from,$to,$ket)
    {
        if ($ket == 'terima') {
            $get = self::whereBetween('tanggal_terima',[$from,$to])
                        ->where('jenis_beli','konsinyasi')
                        ->select('pembelian_obat.id_supplier')
                        ->distinct()
                        ->get();
        }
        else {
            $get = self::whereBetween('tanggal_jatuh_tempo',[$from,$to])
                        ->where('jenis_beli','konsinyasi')
                        ->select('pembelian_obat.id_supplier')
                        ->distinct()
                        ->get();
        }

        return $get;
    }

    public static function exportJatuhTempo($from,$to,$id)
    {
        $get = self::whereBetween('tanggal_jatuh_tempo',[$from,$to])
                    ->where('id_supplier',$id)
                    ->get();

        return $get;
    }

    public static function exportKonsinyasi($from,$to,$id,$ket)
    {
        if ($ket == 'terima') {
            $get = self::whereBetween('tanggal_terima',[$from,$to])
                        ->where('id_supplier',$id)
                        ->where('jenis_beli','konsinyasi')
                        ->get();
        }
        else {
            $get = self::whereBetween('tanggal_jatuh_tempo',[$from,$to])
                        ->where('id_supplier',$id)
                        ->where('jenis_beli','konsinyasi')
                        ->get();
        }

        return $get;
    }
}
