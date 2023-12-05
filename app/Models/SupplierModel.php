<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierModel extends Model
{
    protected $table      = 'supplier_obat';
    protected $primaryKey = 'id_supplier_obat';
    protected $guarded    = [];
    public $timestamps    = false;
}