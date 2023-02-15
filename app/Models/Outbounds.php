<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outbounds extends Model
{
    protected $fillable = ['nome','email','empresa','posicao','telefone','cidade','iscliente','iscontato','usuarios_fk','categorias_fk','ativo', 'isvalid'];
    
    public function usuarios()
    {
        return $this->belongsTo(Usuarios::class,'usuarios_fk','id');

    }

    public function categorias()
    {
        return $this->belongsTo(Categorias::class,'categorias_fk','id');
    }

}
