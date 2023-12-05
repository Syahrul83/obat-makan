<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RacikObatSementaraDetailModel extends Model
{
    protected $table      = 'racik_obat_sementara_detail';
    protected $primaryKey = 'id_racik_obat_sementara_detail';
    protected $guarded    = [];
    public $timestamps    = false;
}
