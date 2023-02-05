<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Sites;

class SitesController extends Controller
{

    /**
     * @OA\Get(
     * path="/v1/sites-all/",
     * summary="Retorna todos registros cadastrados",
     * description="Retorna os registros cadastrados.",
     * tags={"Sites"},
     * @OA\Response(response="200", description="Lista dos sites"),
     * )
     */
    public function all()
    {

        return Sites::all();

    }

    /**
     * @OA\Get(
     * path="/v1/sites/",
     * summary="Exibe os registros cadastrados",
     * description="Lista os registros cadastrados.",
     * tags={"Sites"},
     * @OA\Response(response="200", description="Lista dos sites"),
     * )
     */
    public function index()
    {

        return Sites::paginate(perPage: 15);

    }

    /**
     * @OA\Get(
     * path="/v1/sites/{id}",
     * summary="Exibe um registro",
     * description="Exibe o registro por seu ID.",
     * tags={"Sites"},
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

            return Sites::findOrFail($id);

        } catch (ModelNotFoundException $e) {

            return response()->json(['messagem' => 'Não encontrado'], 404);

        }
    }

    /**
     * @OA\Get(
     * path="/v1/sites-search?site={site}",
     * summary="Procurar registro por site",
     * description="Procurar registro por site",
     * tags={"Sites"},
     * @OA\Response(response="200", description="Retorna dados."),
     * @OA\Parameter(
     *    description="site",
     *    in="query",
     *    name="site",
     *    required=true,
     *    example="http://teste.com",
     *    @OA\Schema(
     *       type="string"
     *    )
     * )
     * )
     */
    public function search(Request $request)
    {

        $this->validate($request, [
            'site' => 'required|max:80',
        ]);

        $search = $request->get('site');
      
        try {

            $query = Sites::where('pagina', 'like',  "%$search%")->get();

        } catch (\Exception $e) {

            return response()->json(['messagem' => $e], 404);
            
        }

        return response()->json($query);
        
    }
    
    /**
     * @OA\POST(
     * path="/v1/sites",
     * summary="Criar um novo registro",
     * description="Criar um novo registro",
     * tags={"Sites"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Dados do Site",
     *    @OA\JsonContent(
     *       required={"pagina","email","telefone","nome"},
     *       @OA\Property(property="pagina", type="string", format="text", example="https://zoit.com.br"),
     *       @OA\Property(property="responsavel", type="string", format="text", example="José da Silva"),* 
     *       @OA\Property(property="email", type="email", format="text", example="mercedes68@example.org"),
     *       @OA\Property(property="telefone", type="string", format="text", example="2199999999"),
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
            'pagina' => 'required|max:80',
            'email' => 'required|max:80',
            'telefone' => 'max:1024',
            'nome' => 'max:80',
            'ativo' => 'max:1'
        ]);

        try {

            $site = Sites::create($request->all());

            return response()->json(['created' => true], 201);

        } catch (\Exception $e) {

            return response()->json(['messagem' => $e], 404);
            
        }
    }

    /**
     * @OA\PUT(
     * path="/v1/sites/{id}",
     * summary="Alterar um registro",
     * description="Alterar um registro por ID",
     * tags={"Sites"},
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
     *       required={"pagina","responsavel","email","telefone"},
     *       @OA\Property(property="pagina", type="string", format="text", example="https://zoit.com.br"),
     *       @OA\Property(property="responsavel", type="string", format="text", example="José da Silva"),* 
     *       @OA\Property(property="email", type="email", format="text", example="mercedes68@example.org"),
     *       @OA\Property(property="telefone", type="string", format="text", example="2199999999"),
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
            'pagina' => 'max:255',
            'responsavel' => 'max:80',
            'email' => 'max:1024',
            'telefone' => 'max:15',
            'ativo' => 'max:1'
        ]);

        try {

            $sites = Sites::findOrFail($id);

        } catch (ModelNotFoundException $e) {

            return response()->json(['messagem' => 'Não encontrado'], 404);

        }

        $input = $request->all();

        try {
            
            $sites->fill($input);

        } catch (\Exception $e) {

            return response()->json(['messagem' => $e], 422);
            
        }

        $sites->save();

        return response()->json(['updated' => true], 201);

    }

    /**
     * @OA\Delete(
     * path="/v1/sites/{id}",
     * summary="Exclui um registro",
     * description="Exclui um registro por seu ID.",
     * tags={"Sites"},
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

            Sites::findOrFail($id)->delete();

            return response()->json(['excluído' => true], 201);

        } catch (ModelNotFoundException $e) {

            return response()->json(['messagem' => 'Não encontrado'], 404);

        }
    }
}
