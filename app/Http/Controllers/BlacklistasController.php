<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use App\Models\Blacklistas;


class BlacklistasController extends Controller
{

    /**
     * @OA\Get(
     * path="/api/blacklist/",
     * summary="Exibe os itens da lista negra",
     * description="Exibe os itens da lista negra.",
     * operationId="blacklistIndex",
     * tags={"Blacklist"},
     * @OA\Response(response="200", description="Blacklist."),
     * )
     */
    public function index()
    {
        return Blacklistas::all();
    }

    /**
     * @OA\Get(
     * path="/api/blacklist/{id}",
     * summary="Exibe um registro da blacklist",
     * description="Exibe um registro da blacklist por seu ID.",
     * operationId="blacklistShow",
     * tags={"Blacklist"},
     * @OA\Response(response="200", description="Retorna dados da blacklist."),
     * @OA\Response(response="404", description="Item não encontrado."),
     * @OA\Parameter(
     *    description="ID da blacklist",
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

            return Blacklistas::findOrFail($id);

        } catch (ModelNotFoundException $e) {

            return response()->json([
                'error' => [
                    'messagem' => 'Não encontrado'
                ]
            ], 404);

        }
        
    }

    /**
     * @OA\Get(
     * path="/api/blacklist-search?email{email}",
     * summary="Procurar o item da blacklist por email",
     * description="Procurar o item da blacklist por email",
     * operationId="blacklistSearch",
     * tags={"Blacklist"},
     * @OA\Response(response="200", description="Retorna dados da blacklist."),
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
            'email' => 'required|max:80',
        ]);

        $search = $request->get('email');
      
         
        try {

            $query = Blacklistas::where('texto', $search)->get();

        } catch (\Exception $e) {

            return response()->json(['messagem' => $e], 404);
            
        }

        return response()->json($query);
        
    }

        /**
     * @OA\POST(
     * path="/api/blacklist",
     * summary="Criar uma nova entrada na blacklist",
     * description="Criar uma nova entrada na blacklist",
     * tags={"Blacklist"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Dados da blacklista",
     *    @OA\JsonContent(
     *       required={"texto"},
     *       @OA\Property(property="texto", type="string", format="text", example="mercedes68@example.org"),
     *    ),
     * ),
     * @OA\Response(
     *    response=201,
     *    description="Blacklist salva com sucesso.",
     *    @OA\JsonContent(
     *       @OA\Property(property="created", type="string", example="true")
     *        )
     *     ), 
     *   @OA\Response(
     *    response=422,
     *    description="Erro ao inserir o registro."
     *   )
     * )
     */
    public function store(Request $request)
    {
        try {

            $blacklist = Blacklistas::create($request->all());

            return response()->json(['created' => true], 201);

        } catch (\Exception $e) {

            return response()->json(['messagem' => $e], 404);
            
        }
    }

        /**
     * @OA\PUT(
     * path="/api/blacklist/{id}",
     * summary="Alterar um registro da blacklist",
     * description="Alterar um registro da blacklist",
     * tags={"Blacklist"},
     * @OA\Parameter(
     *    description="ID da entrada na blacklist",
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
     *    description="Dados do registro da blacklist",
     *    @OA\JsonContent(
     *       required={"texto"},
     *       @OA\Property(property="texto", type="string", format="text", example="https://zoit.com.br")
     *    ),
     * ),
     * @OA\Response(
     *    response=201,
     *    description="Salvo com sucesso.",
     *    @OA\JsonContent(
     *       @OA\Property(property="updated", type="string", example="true")
     *    ) 
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Erro ao inserir."
     *   ),
     * @OA\Response(
     *    response=404,
     *    description="Não encontrado.",
     *    @OA\JsonContent(
     *       @OA\Property(property="mensagem", type="string", example="Não encontrado")
     *    )  
     *   )
     * )
     */
    public function update($id, Request $request)
    {

        try {

            $blacklist = Blacklistas::findOrFail($id);

        } catch (ModelNotFoundException $e) {

            return response()->json(['messagem' => 'Não encontrado'], 404);

        }

        $input = $request->all();

        try {

            $blacklist->fill($input);

        } catch (\Exception $e) {

            return response()->json(['messagem' => $e], 422);
            
        }

        $blacklist->save();

        return response()->json(['updated' => true], 201);

    }

    /**
     * @OA\Delete(
     * path="/api/blacklist/{id}",
     * summary="Exclui uma entrada da blacklist",
     * description="Exclui a entrada na blacklist por seu ID.",
     * tags={"Blacklist"},
     * @OA\Response(response="201", description="Excluído com suceso.",
     *    @OA\JsonContent(
     *       @OA\Property(property="excluído", type="string", example="true")
     *    )
     * ),
     * @OA\Response(response="404", description="Não encontrado.",
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

            Blacklistas::findOrFail($id)->delete();

            return response()->json(['excluído' => true], 200);

        } catch (ModelNotFoundException $e) {

            return response()->json(['messagem' => 'Não encontrado'], 404);

        }
    }
}
