<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KreditDetailModel extends Model
{
	protected $table      = 'kredit_det';
	protected $primaryKey = 'id_kredit_det';
	protected $guarded    = [];

	public static function getData($id) {
		$db = self::join('obat','kredit_det.id_obat','=','obat.id_obat')
					->join('kredit_faktur','kredit_det.id_kredit_faktur','=','kredit_faktur.id_kredit_faktur')
					->join('kredit','kredit_faktur.id_kredit','=','kredit.id_kredit')
					->where('kredit_faktur.id_kredit_faktur',$id)
					->where('status_kredit',0)
					->get();
		return $db;
	}

	public static function getKredit($id) {
		$db = self::join('obat','kredit_det.id_obat','=','obat.id_obat')
					->join('supplier_obat','kredit_det.id_supplier','=','supplier_obat.id_supplier')
					->join('kredit_faktur','kredit_det.id_kredit_faktur','=','kredit_faktur.id_kredit_faktur')
					->join('kredit','kredit_faktur.id_kredit','=','kredit.id_kredit')
					->where('kredit_faktur.id_kredit_faktur',$id)
					->get();
		return $db;	
	}

    public static function potonganKredit($from,$to,$jam_shift)
    {
        $db = self::join('kredit_faktur','kredit_det.id_kredit_faktur','=','kredit_faktur.id_kredit_faktur')->whereBetween('tanggal_faktur',[$from,$to])->where('id_jam_shift',$jam_shift)->sum('diskon');

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

        return $db;
    }
}
