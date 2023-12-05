<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PemakaianModel extends Model
{
    protected $table      = 'pemakaian_obat';
    protected $primaryKey = 'id_pemakaian_obat';
    protected $guarded    = [];
    public $timestamps    = false;

    public static function getExport($from,$to,$id_dokter = '',$id_supplier = '',$ket_data) 
    {
        if ($ket_data == 'supplier' && $id_dokter == '') {
            $db = self::join('obat','pemakaian_obat.id_obat','=','obat.id_obat')
                        ->join('jenis_obat','obat.id_jenis_obat','=','jenis_obat.id_jenis_obat')
                        ->join('supplier_obat','pemakaian_obat.id_supplier','=','supplier_obat.id_supplier')
                        ->where('pemakaian_obat.id_supplier',$id_supplier)
                        ->whereBetween('tanggal_pemakaian',[$from,$to])
                        ->orderBy('tanggal_pemakaian','desc')
                        ->get();
        }
        else if ($ket_data == 'dokter' && $id_supplier == '') {
            $db = self::join('obat','pemakaian_obat.id_obat','=','obat.id_obat')
                        ->join('jenis_obat','obat.id_jenis_obat','=','jenis_obat.id_jenis_obat')
                        ->join('dokter','pemakaian_obat.id_dokter','=','dokter.id_dokter')
                        ->where('pemakaian_obat.id_dokter',$id_dokter)
                        ->whereBetween('tanggal_pemakaian',[$from,$to])
                        ->orderBy('tanggal_pemakaian','desc')
                        ->get();
        }

        return $db;
    }

    public static function getExportByPabrik($from,$to,$id_pabrik) 
    {
        $db = self::join('obat','pemakaian_obat.id_obat','=','obat.id_obat')
                    ->join('jenis_obat','obat.id_jenis_obat','=','jenis_obat.id_jenis_obat')
                    ->where('obat.id_pabrik_obat',$id_pabrik)
                    ->whereBetween('tanggal_pemakaian',[$from,$to])
                    ->orderBy('tanggal_pemakaian','desc')
                    ->get();

        return $db;
    }

    public static function getSumExportObat($from,$to,$id)
    {
        $db = self::join('obat','pemakaian_obat.id_obat','=','obat.id_obat')
                    ->where('pemakaian_obat.id_obat',$id)
                    ->whereBetween('tanggal_pemakaian',[$from,$to])
                    ->sum('stok_pakai');

        return $db;
    }

    public static function getSumExportObatPerDokter($from,$to,$id,$id_dokter)
    {
        $db = self::join('obat','pemakaian_obat.id_obat','=','obat.id_obat')
                    ->where('pemakaian_obat.id_obat',$id)
                    ->where('id_dokter',$id_dokter)
                    ->whereBetween('tanggal_pemakaian',[$from,$to])
                    ->sum('stok_pakai');

        return $db;
    }

    public static function getSumExportObatPerSupplier($from,$to,$id,$id_supplier)
    {
        $db = self::join('obat','pemakaian_obat.id_obat','=','obat.id_obat')
                    ->where('pemakaian_obat.id_obat',$id)
                    ->where('id_supplier',$id_supplier)
                    ->whereBetween('tanggal_pemakaian',[$from,$to])
                    ->sum('stok_pakai');

        return $db;
    }

    public static function getSumExportObatPerPabrik($from,$to,$id,$id_pabrik)
    {
        $db = self::join('obat','pemakaian_obat.id_obat','=','obat.id_obat')
                    ->where('pemakaian_obat.id_obat',$id)
                    ->where('id_pabrik_obat',$id_pabrik)
                    ->whereBetween('tanggal_pemakaian',[$from,$to])
                    ->sum('stok_pakai');

        return $db;
    }
}
