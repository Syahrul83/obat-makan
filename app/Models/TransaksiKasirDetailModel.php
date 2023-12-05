<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiKasirDetailModel extends Model
{
	protected $table      = 'transaksi_kasir_det';
	protected $primaryKey = 'id_transaksi_det';
	protected $guarded    = [];

	public static function getByIdTransaksi($id) {
		$db = self::join('obat','transaksi_kasir_det.id_obat','=','obat.id_obat')
					->join('supplier_obat','transaksi_kasir_det.id_supplier','=','supplier_obat.id_supplier')
					->where('id_transaksi',$id)
					->get();
		return $db;
	}

    public static function potonganUpds($from,$to,$jam_shift)
    {
        // if ($jam_shift == 2) {
        $db = self::join('transaksi_kasir','transaksi_kasir_det.id_transaksi','=','transaksi_kasir.id_transaksi')->whereBetween('tanggal_transaksi',[$from,$to])->where('id_jam_shift',$jam_shift)->sum('diskon');
        // dd($db);
        return $db;
        // $disc_calc = 0;
        // foreach ($db as $key => $value) {
        // 	if ($value->diskon != 0) {
	       //      if ($value->jenis_diskon == 'rupiah') {
		      //       $disc       = $value->diskon / 100;
		      //       $real_price = $value->sub_total / $disc;
		      //       $calc       = ($real_price * $value->diskon) / 100;
		      //       $disc_calc  = $disc_calc + $calc;
	       //      }
	       //      else {
	       //          $disc_calc = $disc_calc + $value->diskon;
	       //      }
        // 	}
        // 	else {
        // 		$disc_calc = $disc_calc + 0;
        // 	}
        // }

        // return $disc_calc;
    	// }
    }
}
