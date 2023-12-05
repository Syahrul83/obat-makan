<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KomposisiObatModel extends Model
{
    protected $table      = 'komposisi_obat';
    protected $primaryKey = 'id_komposisi_obat';
    protected $guarded    = [];
    public $timestamps    = false;
}
