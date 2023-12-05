<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObatModel extends Model
{
	protected $table      = 'obat';
	protected $primaryKey = 'id_obat';
	protected $guarded    = [];
	public $timestamps    = false;

	public function obat($id) 
	{
		$db = self::where('id_obat',$id)->firstOrFail()->nama_obat;
		return $db;
	}

	public static function jenisObat($id)
	{
		$db = self::join('jenis_obat','obat.id_jenis_obat','=','jenis_obat.id_jenis_obat')
					->where('id_obat',$id)
					->firstOrFail();

		return $db;
	}

	public static function lastNumCode() {
		$db = self::query();
		if ($db->count() == 0) {
			$num = 1;
		}
		else {
			$explode = explode('-',$db->orderBy('id_obat','desc')->firstOrFail()->kode_obat);
			$num = (int)$explode[2]+1;
		}
		return $num;
	}

	public function obatPaging($offset,$data)
	{
		$get = self::offset($offset)->limit($data)->where('status_delete',0)->get();

		return $get;
	}
}
