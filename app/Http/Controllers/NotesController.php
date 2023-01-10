<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Notes;

class NotesController extends Controller
{
    /**
     * @OA\Get(
     * path="/v1/notes/{contato}",
     * summary="Retorna as notas inseridas para um Contato",
     * description="Retorna as notas inseridas para um Contato",
     * tags={"Notes"},
     * @OA\Response(response="200", description="Retorna dados."),
     * @OA\Response(response="404", description="Item não encontrado."), 
     * @OA\Parameter(
     *    description="contato",
     *    in="path",
     *    name="contato",
     *    required=true,
     *    example="1",
     *    @OA\Schema(
     *       type="integer",
     *       format="int64"
     *    )
     * )
     * )
     */
    public function index($contato)
    {

        try {

            $query = Notes::where('contatos_fk', '=', $contato)->with('Usuarios')->select(['id','created_at','texto','usuarios_fk'])->orderBy('created_at','desc')->get();
        
        } catch (\Exception $e) {

            return response()->json(['messagem' => $e], 404);
            
        }

        return response()->json($query);
    }

    /**
     * @OA\POST(
     * path="/v1/notes",
     * summary="Criar um novo registro",
     * description="Criar um novo registro de Notas",
     * tags={"Notes"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Dados da Nota",
     *    @OA\JsonContent(
     *       required={"contatos_fk","usuarios_fk","notas"},
     *       @OA\Property(property="contatos_fk", type="integer",  example="1"),
     *       @OA\Property(property="usuarios_fk", type="integer",  example="1"),* 
     *       @OA\Property(property="notas", type="string", format="text", example="orgi urbi sanctum distro"),
     *       @OA\Property(property="telefone", type="string", format="text", example="2199999999"),
     *    ),
     * ),
     * @OA\Response(
     *    response=201,
     *    description="Nota salva com sucesso.",
     *    @OA\JsonContent(
     *       @OA\Property(property="created", type="string", example="true")
     *        )
     *     ), 
     *   @OA\Response(
     *    response=422,
     *    description="Erro ao inserir."
     *   )
     * )
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'contatos_fk' => 'required',
            'usuarios_fk' => 'required',
            'texto' => 'required',
        ]);

        $input = $request->all();

        try {

            $notas = Notes::create($input);

            return response()->json(['created' => true], 201);

        } catch (ModelNotFoundException $e) {

            return response()->json(['messagem' => $e], 404);
            
        }
    }

    /**
     * @OA\Delete(
     * path="/v1/notes/{id}",
     * summary="Exclui uma nota",
     * description="Exclui uma nota por seu ID.",
     * tags={"Notes"},
     * @OA\Response(response="201", description="Nota excluída com sucesso.",
     *    @OA\JsonContent(
     *       @OA\Property(property="excluído", type="string", example="true")
     *    )
     * ),
     * @OA\Response(response="404", description="Nota não encontrado.",
     *    @OA\JsonContent(
     *       @OA\Property(property="mensagem", type="string", example="Não encontrado")
     *    ) 
     * ),
     * @OA\Parameter(
     *    description="ID",
     *    in="path",
     *    name="id",
     *    required=true,
     *    example="1",
     *    @OA\Schema(
     *       type="integer",
     *       format="int64"
     *    )
     * ) 
     * )
     */
    public function destroy($id)
    {
        try {

            Notes::findOrFail($id)->delete();

            return response()->json(['excluído' => true], 204);

        } catch (ModelNotFoundException $e) {

            return response()->json(['messagem' => 'Não encontrado'], 404);

        }
    }    
}
