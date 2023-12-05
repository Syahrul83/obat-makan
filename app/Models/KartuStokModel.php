<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KartuStokModel extends Model
{
    protected $table      = 'kartu_stok';
    protected $primaryKey = 'id_kartu_stok';
    protected $guarded    = [];
    public $timestamps    = false;
}
