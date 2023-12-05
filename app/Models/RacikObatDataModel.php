<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RacikObatDataModel extends Model
{
    protected $table      = 'racik_obat_data';
    protected $primaryKey = 'id_racik_obat_data';
    protected $guarded    = [];
    public $timestamps    = false;
}
