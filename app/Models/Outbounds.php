<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outbounds extends Model
{
    protected $fillable = ['nome','email','iscliente','iscontato','usuarios_fk'];
}
