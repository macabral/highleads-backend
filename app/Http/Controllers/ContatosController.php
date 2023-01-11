<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Contatos;

class ContatosController extends Controller
{
    /**
     * @OA\Get(
     * path="/v1/contatos-satus/",
     * summary="Exibe os Contatos cadastrados filtrado por status",
     * description="Lista os contatos cadastrados filtrados por status",
     * tags={"Contato"},
     * @OA\Response(response="200", description="Retorna dados."),
     * @OA\Response(response="404", description="Item não encontrado."), 
     * @OA\Parameter(
     *    description="status",
     *    in="path",
     *    name="status",
     *    required=true,
     *    example="1",
     *    @OA\Schema(
     *       type="integer",
     *       format="int64"
     *    )
     * )
     * )
     */
    public function index($status)
    {

        try {

            $query = Contatos::where('status', '=', $status)->orderBy('score','desc')->get();
        
        } catch (\Exception $e) {

            return response()->json(['messagem' => $e], 404);
            
        }

        return response()->json($query);
    }

    /**
     * @OA\Get(
     * path="/v1/contatos/{id}",
     * summary="Exibe um contato",
     * description="Exibe o contato por seu ID.",
     * tags={"Contato"},
     * @OA\Response(response="200", description="Retorna dados do contato."),
     * @OA\Response(response="404", description="Contato não encontrado."),
     * @OA\Parameter(
     *    description="ID do contato",
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
    public function show($id)
    {
        try {

            return Contatos::findOrFail($id);

        } catch (ModelNotFoundException $e) {

            return response()->json([ 
                'error' => [
                    'mensagem' => 'Não Encontrado'
                    ]
                ], 404);

        }
        
    }

    /**
     * @OA\Get(
     * path="/v1/contatos-search?email={email}",
     * summary="Procurar contatos por email",
     * description="Procurar contatos por email",
     * tags={"Contato"},
     * @OA\Response(response="200", description="Retorna dados do Contato."),
     * @OA\Parameter(
     *    description="email",
     *    in="query",
     *    name="email",
     *    required=true,
     *    example="teste@dominio.com",
     *    @OA\Schema(
     *       type="string"
     *    )
     * )
     * )
     */
    public function search(Request $request)
    {


        $this->validate($request, [
            'search' => 'required|max:80',
        ]);

        $search = $request->get('search');
               
        try {

            $query = Contatos::where('nome', 'like', "%$search%")->orwhere('email', 'like', "%$search%")->orwhere('empresa', 'like', "%$search%")->orderBy('datahora', 'DESC')->get();

        } catch (\Exception $e) {

            return response()->json(['messagem' => $e], 404);
            
        }

        return response()->json($query);
        
    }

    /**
     * @OA\POST(
     * path="/v1/contatos",
     * summary="Criar um Contato",
     * description="Criar um novo Contato",
     * tags={"Contato"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Dados do Contato",
     *    @OA\JsonContent(
     *       required={"site","nome","email","telefone","remoteip","datahora"},
     *       @OA\Property(property="site", type="string", format="text", example="https://zoit.com.br"),
     *       @OA\Property(property="nome", type="string", format="text", example="José da Silva"),* 
     *       @OA\Property(property="email", type="email", format="text", example="mercedes68@example.org"),
     *       @OA\Property(property="telefone", type="string", format="text", example="2199999999"),
     *       @OA\Property(property="remoteip", type="string", format="text", example="192.168.1.1"),
     *       @OA\Property(property="datahora", type="datetime", example="2022-12-19 09:00:00"),
     *    ),
     * ),
     * @OA\Response(
     *    response=201,
     *    description="Contato salvo com sucesso.",
     *    @OA\JsonContent(
     *       @OA\Property(property="created", type="string", example="true")
     *        )
     *     ), 
     *   @OA\Response(
     *    response=422,
     *    description="Erro ao inserir um Contato."
     *   )
     * )
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'site' => 'required|max:255',
            'remoteip' => 'required|max:15',
            'nome' => 'required|max:80',
            'email' => 'required|max:80',
            'datahora' => 'max:19',
            'telefone' => 'required|max:15'
        ]);

        $input = $request->all();

        try {

            $contatos = Contatos::create($input);

            return response()->json(['created' => true], 201);

        } catch (ModelNotFoundException $e) {

            return response()->json(['messagem' => $e], 404);
            
        }
    }

    /**
     * @OA\PUT(
     * path="/v1/contatos/{id}",
     * summary="Alterar um Contato",
     * description="Alterar um contato por ID",
     * tags={"Contato"},
     * @OA\Parameter(
     *    description="ID do contato",
     *    in="path",
     *    name="id",
     *    required=true,
     *    example="1",
     *    @OA\Schema(
     *       type="integer",
     *       format="int64"
     *    )
     * ),
     * @OA\RequestBody(
     *    required=true,
     *    description="Dados do Contato",
     *    @OA\JsonContent(
     *       required={"site","nome","email","telefone","remoteip","datahora"},
     *       @OA\Property(property="site", type="string", format="text", example="https://zoit.com.br"),
     *       @OA\Property(property="nome", type="string", format="text", example="José da Silva"),* 
     *       @OA\Property(property="email", type="email", format="text", example="mercedes68@example.org"),
     *       @OA\Property(property="telefone", type="string", format="text", example="2199999999"),
     *       @OA\Property(property="remoteip", type="string", format="text", example="192.168.1.1"),
     *       @OA\Property(property="datahora", type="datetime", example="2022-12-19 09:00:00"),
     *    ),
     * ),
     * @OA\Response(
     *    response=201,
     *    description="Contato salvo com sucesso.",
     *    @OA\JsonContent(
     *       @OA\Property(property="updated", type="string", example="true")
     *    ) 
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Erro ao inserir um Contato."
     *   ),
     * @OA\Response(
     *    response=404,
     *    description="Contato não encontrado.",
     *    @OA\JsonContent(
     *       @OA\Property(property="mensagem", type="string", example="Não encontrado")
     *    )  
     *   )
     * )
     */
    public function update($id, Request $request)
    {
        $this->validate($request, [
            'site' => 'max:255',
            'remoteip' => 'max:15',
            'nome' => 'max:80',
            'email' => 'max:80',
            'datahora' => 'max:19',
            'telefone' => 'max:15',
        ]);

        try {

            $contatos = Contatos::findOrFail($id);

        } catch (ModelNotFoundException $e) {

            return response()->json(['messagem' => 'Não encontrado'], 404);

        }

        $input = $request->all();

        try {

            $contatos->fill($input);

        } catch (\Exception $e) {

            return response()->json(['messagem' => $e], 422);
            
        }

        $contatos->save();

        return response()->json(['updated' => true], 201);

    }

    /**
     * @OA\Delete(
     * path="/v1/contatos/{id}",
     * summary="Exclui um contato",
     * description="Exclui o contato por seu ID.",
     * tags={"Contato"},
     * @OA\Response(response="201", description="Contato excluído com sucesso.",
     *    @OA\JsonContent(
     *       @OA\Property(property="excluído", type="string", example="true")
     *    )
     * ),
     * @OA\Response(response="404", description="Contato não encontrado.",
     *    @OA\JsonContent(
     *       @OA\Property(property="mensagem", type="string", example="Não encontrado")
     *    ) 
     * ),
     * @OA\Parameter(
     *    description="ID do contato",
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

            Contatos::findOrFail($id)->delete();

            return response()->json(['excluído' => true], 204);

        } catch (ModelNotFoundException $e) {

            return response()->json(['messagem' => 'Não encontrado'], 404);

        }
    }
}
