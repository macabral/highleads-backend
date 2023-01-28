<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categorias extends Model
{
    protected $fillable = ['descricao','ativo'];
    
    public function outbounds()
    {
        return $this->belongsTo(Categorias::class,'categorias_fk','id');
    }

}
