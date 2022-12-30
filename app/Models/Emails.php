<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Emails extends Model
{
    protected $fillable = ['para','assunto','texto','erro','anexos','enviado'];
}
