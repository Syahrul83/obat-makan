<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiKasirModel extends Model
{
	protected $table      = 'transaksi_kasir';
	protected $primaryKey = 'id_transaksi';
	protected $guarded    = [];

	public static function export($from,$to,$id) {
		$db = self::join('users','transaksi_kasir.id_users','=','users.id_users')->whereBetween('tanggal_transaksi',[$from,$to])
					->where('id_jam_shift',$id)
					->orderBy('tanggal_transaksi','DESC')
					->get();
		return $db;
	}
}
