<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KreditFakturModel extends Model
{
    protected $table      = 'kredit_faktur';
    protected $primaryKey = 'id_kredit_faktur';
    protected $guarded    = [];
    public $timestamps    = false;

    public static function lastNumCode() {
        $db = self::query();
        if ($db->count() == 0) {
            $num = 1;
        }
        else {
            $explode = explode('-',$db->orderBy('nomor_faktur','desc')->firstOrFail()->nomor_faktur);
            $num = (int)$explode[1]+1;
        }
        return $num;
    }
}
