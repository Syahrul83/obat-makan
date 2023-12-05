<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasienModel extends Model
{
	protected $table      = 'pasien';
	protected $primaryKey = 'id_pasien';
	protected $guarded    = [];
	public $timestamps    = false;
}