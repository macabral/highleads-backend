<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Configuracoes;

class ConfiguracoesController extends Controller
{

    public function index()
    {
        return Configuracoes::all();
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
    }


    public function show($id)
    {
        try {

            return Configuracoes::findOrFail($id);

        } catch (ModelNotFoundException $e) {

            return response()->json([
                'error' => [
                    'messagem' => 'Não encontrado'
                ]
            ], 404);

        }
    }


    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
