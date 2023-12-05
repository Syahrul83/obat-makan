<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarginObatModel extends Model
{
    protected $table      = 'margin_obat';
    protected $primaryKey = 'id_margin_obat';
    protected $guarded    = [];
    public $timestamps    = false;
}
