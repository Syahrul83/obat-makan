<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RacikObatModel extends Model
{
    protected $table      = 'racik_obat';
    protected $primaryKey = 'id_racik_obat';
    protected $guarded    = [];
    public $timestamps    = false;
}
