<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObatDetailModel extends Model
{
	protected $table      = 'obat_detail';
	protected $primaryKey = 'id_obat_detail';
	protected $guarded    = [];
	public $timestamps    = false;

    public static function getShuffleByObat($id)
    {
        $get = self::where('id_obat',$id)->orderByRaw('RAND()')->limit(1)->firstOrFail();

        return $get;
    }

	public static function cekSupplier($id,$id_supp)
	{
		$get = self::where('id_obat',$id)->where('id_supplier',$id_supp)->count();
		if ($get > 0) {
			$statement = 'true';
		}
		else {
			$statement = 'false';
		}

		return $statement;
	}
}