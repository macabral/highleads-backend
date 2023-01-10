<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contatos extends Model
{
    
    protected $fillable = ['site','remoteip','datahora','nome','email','telefone','sites_fk','status','score','empresa','usuarios_fk'];

}
