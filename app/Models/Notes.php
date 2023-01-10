<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notes extends Model
{
    protected $fillable = ['usuarios_fk','contatos_fk','texto'];

    public function usuarios()
    {
        return $this->belongsTo(Usuarios::class,'usuarios_fk','id');
    }

}
