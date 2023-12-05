<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RacikObatSementaraModel extends Model
{
    protected $table      = 'racik_obat_sementara';
    protected $primaryKey = 'id_racik_obat_sementara';
    protected $guarded    = [];
    public $timestamps    = false;
}
