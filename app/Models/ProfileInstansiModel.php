<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileInstansiModel extends Model
{
    protected $table      = 'profile_instansi';
    protected $primaryKey = 'id_profile_instansi';
    protected $guarded    = [];
    public $timestamps    = false;
}
