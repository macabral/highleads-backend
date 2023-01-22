<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Usuarios extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable;

    protected $fillable = [
        'nome', 'email', 'password','ativo','perfil','usuarios_fk'
    ];

    // protected $hidden = [
    //     'password',
    // ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function notes()
    {
        return $this->hasMany(Notes::class);
    }

    public function outbounds()
    {
        return $this->hasMany(Outbounds::class);
    }
}
