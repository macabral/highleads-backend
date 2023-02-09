<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campanhas extends Model
{
    protected $fillable = ['titulo', 'assunto', 'emailhtml'];
}
