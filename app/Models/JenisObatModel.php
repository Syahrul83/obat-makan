<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisObatModel extends Model
{
	protected $table      = 'jenis_obat';
	protected $primaryKey = 'id_jenis_obat';
	protected $guarded    = [];
	public $timestamps 	  = false;
}
