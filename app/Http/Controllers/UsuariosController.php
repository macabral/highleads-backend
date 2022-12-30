<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use  App\Models\Usuarios;

class UsuariosController extends Controller
{
    public function profile()
    {
        return response()->json(['user' => Auth::usuarios()], 200);
    }

    public function lista()
    {
         return response()->json(['users' =>  Usuarios::all()], 200);
    }
}
