<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Categorias;

class CategoriasController extends Controller
{

    /**
     * @OA\Get(
     * path="/v1/categorias-all/",
     * summary="Retorna todos registros cadastrados",
     * description="Retorna os registros cadastrados.",
     * tags={"Categorias"},
     * @OA\Response(response="200", description="Lista das Categorias"),
     * )
     */
    public function all()
    {

        return Categorias::where("ativo", 1)->get();

    }

    /**
     * @OA\Get(
     * path="/v1/categorias/",
     * summary="Exibe os registros cadastrados",
     * description="Lista os registros cadastrados.",
     * tags={"Categorias"},
     * @OA\Response(response="200", description="Lista das Categorias"),
     * )
     */
    public function index()
    {

        return Categorias::paginate(perPage: 15);

    }

    /**
     * @OA\Get(
     * path="/v1/categorias/{id}",
     * summary="Exibe um registro",
     * description="Exibe o registro por seu ID.",
     * tags={"Categorias"},
     * @OA\Response(response="200", description="Retorna dados."),
     * @OA\Response(response="404", description="Não encontrado."),
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
    public function show($id)
    {
        try {

            return Categorias::findOrFail($id);

        } catch (ModelNotFoundException $e) {

            return response()->json(['messagem' => 'Não encontrado'], 404);

        }
    }

    /**
     * @OA\Get(
     * path="/v1/categorias-search?site={site}",
     * summary="Procurar registro por categoria",
     * description="Procurar registro por categoria",
     * tags={"Categorias"},
     * @OA\Response(response="200", description="Retorna dados."),
     * @OA\Parameter(
     *    description="categoria",
     *    in="query",
     *    name="site",
     *    required=true,
     *    example="Governo",
     *    @OA\Schema(
     *       type="string"
     *    )
     * )
     * )
     */
    public function search(Request $request)
    {

        $this->validate($request, [
            'texto' => 'required|max:80',
        ]);

        $search = $request->get('texto');
      
        try {

            $query = Categorias::where('descricao', 'like',  "%$search%")->get();

        } catch (\Exception $e) {

            return response()->json(['messagem' => $e], 404);
            
        }

        return response()->json($query);
        
    }
    
    /**
     * @OA\POST(
     * path="/v1/categorias",
     * summary="Criar um novo registro",
     * description="Criar um novo registro",
     * tags={"Categorias"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Dados da Categoria",
     *    @OA\JsonContent(
     *       required={"descricao","ativo"},
     *       @OA\Property(property="descricao", type="string", format="text", example="Governo"),
     *       @OA\Property(property="ativo", type="integer", example="1"),
     *    ),
     * ),
     * @OA\Response(
     *    response=201,
     *    description="Salvo com sucesso.",
     *    @OA\JsonContent(
     *       @OA\Property(property="created", type="string", example="true")
     *        )
     *     ), 
     *    @OA\Response(
     *      response=422,
     *      description="Erro ao inserir."
     *    ),
     *    @OA\Response(
     *      response=404,
     *      description="Não encontrado ou duplicado."
     *    )* 
     * )
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'descricao' => 'required|max:254',
            'ativo' => 'required|max:1'
        ]);

        try {

            $site = Categorias::create($request->all());

            return response()->json(['created' => true], 201);

        } catch (\Exception $e) {

            return response()->json(['messagem' => $e], 404);
            
        }
    }

    /**
     * @OA\PUT(
     * path="/v1/categorias/{id}",
     * summary="Alterar um registro",
     * description="Alterar um registro por ID",
     * tags={"Categorias"},
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
     * ),
     * @OA\RequestBody(
     *    required=true,
     *    description="Dados do registro",
     *    @OA\JsonContent(
     *       required={"escricao","ativo"},
     *       @OA\Property(property="descricao", type="string", format="text", example="Governo"),
     *       @OA\Property(property="ativo", type="integer", example="1"), 
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
     *    description="Nnão encontrado.",
     *    @OA\JsonContent(
     *       @OA\Property(property="mensagem", type="string", example="Não encontrado")
     *    )  
     *   )
     * )
     */
    public function update($id, Request $request)
    {

        $this->validate($request, [
            'descricao' => 'max:254',
            'ativo' => 'max:1'
        ]);

        try {

            $Categorias = Categorias::findOrFail($id);

        } catch (ModelNotFoundException $e) {

            return response()->json(['messagem' => 'Não encontrado'], 404);

        }

        $input = $request->all();

        try {
            
            $Categorias->fill($input);

        } catch (\Exception $e) {

            return response()->json(['messagem' => $e], 422);
            
        }

        $Categorias->save();

        return response()->json(['updated' => true], 201);

    }

    /**
     * @OA\Delete(
     * path="/v1/categorias/{id}",
     * summary="Exclui um registro",
     * description="Exclui um registro por seu ID.",
     * tags={"Categorias"},
     * @OA\Response(response="201", description="Excluído com sucesso.",
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

            Categorias::findOrFail($id)->delete();

            return response()->json(['excluído' => true], 201);

        } catch (ModelNotFoundException $e) {

            return response()->json(['messagem' => 'Não encontrado'], 404);

        }
    }
}
