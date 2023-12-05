<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokOpnemDetailModel extends Model
{
    protected $table      = 'stok_opnem_detail';
    protected $primaryKey = 'id_stok_opnem_detail';
    protected $guarded    = [];
    public $timestamps    = false;
}
