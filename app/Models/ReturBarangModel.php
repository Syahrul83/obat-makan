<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturBarangModel extends Model
{
    protected $table      = 'retur_barang';
    protected $primaryKey = 'id_retur_barang';
    protected $guarded    = [];

    public static function lastNumCode() {
        $db = self::query();
        if ($db->count() == 0) {
            $num = 1;
        }
        else {
            $explode = explode('-',$db->orderBy('nomor_retur','desc')->firstOrFail()->nomor_retur);
            $num = (int)$explode[1]+1;
        }
        return $num;
    }
}
