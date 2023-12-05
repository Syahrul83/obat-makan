<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersenPpnModel extends Model
{
    protected $table      = 'persen_ppn';
    protected $primaryKey = 'id_persen_ppn';
    protected $guarded    = [];
    public $timestamps    = false;
}
