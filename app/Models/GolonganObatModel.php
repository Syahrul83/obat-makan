<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GolonganObatModel extends Model
{
    protected $table      = 'golongan_obat';
    protected $primaryKey = 'id_golongan_obat';
    protected $guarded    = [];
    public $timestamps    = false;
}
