<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PabrikObatModel extends Model
{
    protected $table      = 'pabrik_obat';
    protected $primaryKey = 'id_pabrik_obat';
    protected $guarded    = [];
    public $timestamps    = false;
}
