<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokterModel extends Model
{
	protected $table      = 'dokter';
	protected $primaryKey = 'id_dokter';
	protected $guarded    = [];
	public $timestamps    = false;
}