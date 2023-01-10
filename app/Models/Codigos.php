<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Codigos extends Model
{
    
    protected $fillable = ['codigo', 'usuarios_fk', 'created_at'];
}
