<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTime;

class JamShiftModel extends Model
{
    protected $table      = 'jam_shift';
    protected $primaryKey = 'id_jam_shift';
    protected $guarded    = [];
    public $timestamps    = false;

    public static function getIdJamShift()
    {
        $count  = self::where('jam_awal','<=',date('H:i'))
                        ->where('jam_akhir','>=',date('H:i'))
                        ->where('status_delete',0)
                        ->count();
                        
        if ($count > 0) { 
            $get  = self::where('jam_awal','<=',date('H:i'))
                        ->where('jam_akhir','>=',date('H:i'))
                        ->where('status_delete',0)
                        ->firstOrFail()->id_jam_shift;
        }
        else {
            $get = self::orderBy('jam_awal','DESC')->firstOrFail()->id_jam_shift;
        }

        return $get;
    }

    public static function getData()
    {
        $get = self::where('status_delete',0)->get();

        return $get;
    }
}
