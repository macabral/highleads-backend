<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outbounds extends Model
{
    protected $fillable = ['nome','email','iscliente','iscontato','usuarios_fk','ativo'];
    
    public function usuarios()
    {
        return $this->belongsTo(Usuarios::class,'usuarios_fk','id');
    }

}
