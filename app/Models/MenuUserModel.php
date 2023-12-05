<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuUserModel extends Model
{
    protected $table      = 'menu_user';
    protected $primaryKey = 'id_menu_user';
    protected $guarded    = [];
    public $timestamps    = false;
}
