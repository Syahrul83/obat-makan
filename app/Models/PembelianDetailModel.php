<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PembelianObatModel as PembelianObat;

class PembelianDetailModel extends Model
{
    protected $table      = 'pembelian_detail';
    protected $primaryKey = 'id_pembelian_detail';
    protected $guarded    = [];
    public $timestamps    = false;

    public static function getData($id) 
    {
        $db = self::join('obat','pembelian_detail.id_obat','=','obat.id_obat')
                    ->join('jenis_obat','obat.id_jenis_obat','=','jenis_obat.id_jenis_obat')
                    ->where('id_pembelian_obat',$id)
                    ->get();
        return $db;
    }

    public static function export($from,$to,$ket) 
    {
        if ($ket == 'kredit') {
            $db = self::join('pembelian_obat','pembelian_detail.id_pembelian_obat','=','pembelian_obat.id_pembelian_obat')
                        ->join('supplier_obat','pembelian_obat.id_supplier','=','supplier_obat.id_supplier')
                        ->join('obat','pembelian_detail.id_obat','=','obat.id_obat')
                        ->join('jenis_obat','obat.id_jenis_obat','=','jenis_obat.id_jenis_obat')
                        ->whereBetween('tanggal_terima',[$from,$to])
                        ->where('jenis_beli','kredit')
                        ->orderBy('tanggal_terima','desc')
                        ->get();
        }
        else if($ket == 'cash') {
            $db = self::join('pembelian_obat','pembelian_detail.id_pembelian_obat','=','pembelian_obat.id_pembelian_obat')
                        ->join('supplier_obat','pembelian_obat.id_supplier','=','supplier_obat.id_supplier')
                        ->join('obat','pembelian_detail.id_obat','=','obat.id_obat')
                        ->join('jenis_obat','obat.id_jenis_obat','=','jenis_obat.id_jenis_obat')
                        ->whereBetween('tanggal_terima',[$from,$to])
                        ->where('jenis_beli','cash')
                        ->orderBy('tanggal_terima','desc')
                        ->get();   
        }
        else if($ket == 'jatuh-tempo') {
            $db = self::join('pembelian_obat','pembelian_detail.id_pembelian_obat','=','pembelian_obat.id_pembelian_obat')
                        ->join('supplier_obat','pembelian_obat.id_supplier','=','supplier_obat.id_supplier')
                        ->join('obat','pembelian_detail.id_obat','=','obat.id_obat')
                        ->join('jenis_obat','obat.id_jenis_obat','=','jenis_obat.id_jenis_obat')
                        ->whereBetween('tanggal_jatuh_tempo',[$from,$to])
                        ->orderBy('tanggal_jatuh_tempo','desc')
                        ->get(); 
        }
        else if ($ket == 'konsinyasi') {
            $db = self::join('pembelian_obat','pembelian_detail.id_pembelian_obat','=','pembelian_obat.id_pembelian_obat')
                        ->join('supplier_obat','pembelian_obat.id_supplier','=','supplier_obat.id_supplier')
                        ->join('obat','pembelian_detail.id_obat','=','obat.id_obat')
                        ->join('jenis_obat','obat.id_jenis_obat','=','jenis_obat.id_jenis_obat')
                        ->whereBetween('tanggal_terima',[$from,$to])
                        ->where('jenis_beli','konsinyasi')
                        ->orderBy('tanggal_terima','desc')
                        ->get();
        }
        return $db;
    }

    public static function supplierExport($from,$to,$ket)
    {
        if ($ket == 'kredit' || $ket == 'cash' || $ket == 'konsinyasi') {
            $db = self::join('pembelian_obat','pembelian_detail.id_pembelian_obat','=','pembelian_obat.id_pembelian_obat')
                        ->join('supplier_obat','pembelian_obat.id_supplier','=','supplier_obat.id_supplier')
                        ->whereBetween('tanggal_terima',[$from,$to])
                        ->where('jenis_beli',$ket)
                        ->select('pembelian_obat.id_supplier')
                        ->distinct()
                        ->get();
        }
        else {
            $db = self::join('pembelian_obat','pembelian_detail.id_pembelian_obat','=','pembelian_obat.id_pembelian_obat')
                        ->join('supplier_obat','pembelian_obat.id_supplier','=','supplier_obat.id_supplier')
                        ->whereBetween('tanggal_jatuh_tempo',[$from,$to])
                        ->select('pembelian_obat.id_supplier')
                        ->distinct()
                        ->get();   
        }

        return $db;
    }

    public static function dppSupplier($from,$to,$ket,$id)
    {
        if ($ket == 'kredit' || $ket == 'cash' || $ket == 'konsinyasi') {
            $sum1 = self::join('pembelian_obat','pembelian_detail.id_pembelian_obat','=','pembelian_obat.id_pembelian_obat')
                        ->join('supplier_obat','pembelian_obat.id_supplier','=','supplier_obat.id_supplier')
                        ->whereBetween('tanggal_terima',[$from,$to])
                        ->where('jenis_beli',$ket)
                        ->where('pembelian_obat.id_supplier',$id)
                        ->where('total_dpp','=',0)
                        ->sum('sub_total');

            $sum2 = PembelianObat::join('supplier_obat','pembelian_obat.id_supplier','=','supplier_obat.id_supplier')
                        ->whereBetween('tanggal_terima',[$from,$to])
                        ->where('jenis_beli',$ket)
                        ->where('pembelian_obat.id_supplier',$id)
                        ->whereNotIn('total_dpp',[0])
                        ->sum('total_dpp');

            // $sum1 = 0;
            // foreach ($db as $key => $value) {
            //     if ($value->ppn == 10) {
            //         $sum1 = $sum1 + ((100/110) * $value->sub_total);
            //     }
            //     else {
            //         $sum1 = $sum1 + $value->sub_total;
            //     }
            // }
            $sum = $sum1+$sum2;
        }
        // else {
        //     $db = self::join('pembelian_obat','pembelian_detail.id_pembelian_obat','=','pembelian_obat.id_pembelian_obat')
        //                 ->join('supplier_obat','pembelian_obat.id_supplier','=','supplier_obat.id_supplier')
        //                 ->join('obat','pembelian_detail.id_obat','=','obat.id_obat')
        //                 ->whereBetween('tanggal_jatuh_tempo',[$from,$to])
        //                 ->where('pembelian_obat.id_supplier',$id)
        // }

        return $sum;
    }

    public static function ppnSupplier($from,$to,$ket,$id)
    {
        if ($ket == 'kredit' || $ket == 'cash' || $ket == 'konsinyasi') {
            $db = self::join('pembelian_obat','pembelian_detail.id_pembelian_obat','=','pembelian_obat.id_pembelian_obat')
                        ->join('supplier_obat','pembelian_obat.id_supplier','=','supplier_obat.id_supplier')
                        ->join('obat','pembelian_detail.id_obat','=','obat.id_obat')
                        ->whereBetween('tanggal_terima',[$from,$to])
                        ->where('jenis_beli',$ket)
                        ->where('pembelian_obat.id_supplier',$id)
                        ->where('total_dpp','=',0)
                        ->get();

            $sum2 = PembelianObat::join('supplier_obat','pembelian_obat.id_supplier','=','supplier_obat.id_supplier')
                                ->whereBetween('tanggal_terima',[$from,$to])
                                ->where('jenis_beli',$ket)
                                ->where('pembelian_obat.id_supplier',$id)
                                ->whereNotIn('total_dpp',[0])
                                ->sum('total_ppn');

            $sum1 = 0;
            foreach ($db as $key => $value) {
                // if ($value->ppn == 10) {
                //     $un_ppn = (100/110) * $value->sub_total;
                //     $sum = $sum + (($un_ppn * 10) / 100);
                // }
                // else {
                //     $sum = $sum + 0;
                // }
                $sum1 = $sum1 + ($value->total_semua - $value->sub_total);
            }
            $sum = $sum1+$sum2;
        }
        // else {
        //     $db = self::join('pembelian_obat','pembelian_detail.id_pembelian_obat','=','pembelian_obat.id_pembelian_obat')
        //                 ->join('supplier_obat','pembelian_obat.id_supplier','=','supplier_obat.id_supplier')
        //                 ->join('obat','pembelian_detail.id_obat','=','obat.id_obat')
        //                 ->whereBetween('tanggal_jatuh_tempo',[$from,$to])
        //                 ->where('pembelian_obat.id_supplier',$id)
        // }

        return $sum;
    }

    public static function sumJumlahBeli($id)
    {
        $db = self::where('id_pembelian_obat',$id)->sum('jumlah');

        return $db;
    }

    public static function sumTotalBeli($id)
    {
        $db = self::where('id_pembelian_obat',$id)->sum('sub_total');

        return $db;
    }
}
