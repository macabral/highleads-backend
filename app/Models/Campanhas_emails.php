<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campanhas_emails extends Model
{
    protected $fillable = ['campanhas_fk', 'outbounds_fk', 'uniqueid','qtdemails','qtdvisitas','qtdcancelados'];

    public function campanhas()
    {
        return $this->belongsTo(Campanhas::class,'campanhas_fk','id');

    }

    public function outbounds()
    {
        return $this->belongsTo(Outbounds::class,'outbounds_fk','id');

    }
}
