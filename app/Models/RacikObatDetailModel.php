<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RacikObatDetailModel extends Model
{
    protected $table      = 'racik_obat_detail';
    protected $primaryKey = 'id_racik_obat_detail';
    protected $guarded    = [];
    public $timestamps    = false;

    public static function getData($id)
    {
        $get = self::join('obat','racik_obat_detail.id_obat','=','obat.id_obat')
                    ->join('jenis_obat','obat.id_jenis_obat','=','jenis_obat.id_jenis_obat')
                    ->where('id_racik_obat',$id)
                    ->get();

        return $get;
    }
}
