<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  App\Models\Usuarios;

class UsuariosController extends Controller
{

    /**
     * @OA\Get(
     * path="/api/usuarios/",
     * summary="Exibe os usuarios cadastrados",
     * description="Exibe os usuarios cadastrados.",
     * tags={"Usuários"},
     * @OA\Response(response="200", description="Usuários."),
     * )
     */
    public function index()
    {
        return Usuarios::all();
    }

}
