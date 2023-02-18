<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Outbounds;
use App\Models\Campanhas;
use App\Models\Campanhas_emails;

class OutboundController extends Controller
{

    /**
     * @OA\Get(
     * path="/v1/outbound-all/",
     * summary="Retorna todos registros cadastrados",
     * description="Retorna os registros cadastrados.",
     * tags={"outbound"},
     * @OA\Response(response="200", description="Lista dos Outbound"),
     * )
     */
    public function all()
    {

        return Outbounds::all();

    }

    /**
     * @OA\Get(
     * path="/v1/outbound/",
     * summary="Exibe os registros cadastrados",
     * description="Lista os registros cadastrados.",
     * tags={"outbound"},
     * @OA\Response(response="200", description="Lista dos Outbound"),
     * )
     */
    public function index($perfil, $usuario)
    {

        if ($perfil == 1) {
            return Outbounds::paginate(perPage: 15);
        } else {
            return Outbounds::where('usuarios_fk', '=', $usuario)->paginate(perPage: 15);
        }

    }

    /**
     * @OA\Get(
     * path="/v1/outbound/{id}",
     * summary="Exibe um registro",
     * description="Exibe o registro por seu ID.",
     * tags={"outbound"},
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

            return Outbounds::findOrFail($id);

        } catch (ModelNotFoundException $e) {

            return response()->json(['messagem' => 'Não encontrado'], 404);

        }
    }

    /**
     * @OA\Post(
     * path="/v1/outbound-search",
     * summary="Procurar registro por site",
     * description="Procurar registro por site",
     * tags={"outbound"},
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
            'categorias_fk' => 'required',
            'perfil' => 'required',
            'idUsuario' => 'required',
            'page' => 'required'
        ]);
       
        $input = $request->all();

        $query = Outbounds::select();

        try {

            if ($input['perfil'] == '2') {
                $query->where('usuarios_fk', $input['idUsuario']);
            }

            if (! $input['categorias_fk'] == 0) {
                $query->where('categorias_fk', '=', $input['categorias_fk']);
            }

            if (! $input['search'] == '') {
                $search = $input['search'];
                $query->where('nome', 'like',  "%$search%")->orwhere('email', 'like', "%$search%")->orwhere('empresa', 'like',  "%$search%")->orwhere('posicao', 'like',  "%$search%");
            }

            return $query->with('Categorias')->orderBy('nome')->paginate(15, ['*'], 'page', $input['page']);
    
            } catch (\Exception $e) {
    
                return response()->json(['messagem' => 'Nada encontrado ' . $e], 404);
                
            }
       
    }
    
    /**
     * @OA\POST(
     * path="/v1/outbound",
     * summary="Criar um novo registro",
     * description="Criar um novo registro",
     * tags={"outbound"},
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
            'nome' => 'max:80',
            'email' => 'required|max:80',
            'usuarios_fk' => 'max:12',
            'categorias_fk' => 'max:12',
            'iscliente' => 'max:1',
            'iscontato' => 'max:1',
            'isvalid' => 'max:1',
            'ativo' => 'max:1'
        ]);

        try {

            $site = Outbounds::create($request->all());

            return response()->json(['created' => true], 201);

        } catch (\Exception $e) {

            return response()->json(['messagem' => $e], 404);
            
        }
    }

    /**
     * @OA\PUT(
     * path="/v1/outbound/{id}",
     * summary="Alterar um registro",
     * description="Alterar um registro por ID",
     * tags={"outbound"},
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
            'nome' => 'max:80',
            'email' => 'required|max:80',
            'usuarios_fk' => 'max:12',
            'iscliente' => 'max:1',
            'iscontato' => 'max:1',
            'isvalid' => 'max:1',
            'ativo' => 'max:1'
        ]);

        try {

            $Outbound = Outbounds::findOrFail($id);

        } catch (ModelNotFoundException $e) {

            return response()->json(['messagem' => 'Não encontrado'], 404);

        }

        $input = $request->all();

        try {
            
            $Outbound->fill($input);

        } catch (\Exception $e) {

            return response()->json(['messagem' => $e], 422);
            
        }

        $Outbound->save();

        return response()->json(['updated' => true], 201);

    }

    /**
     * @OA\Delete(
     * path="/v1/outbound/{id}",
     * summary="Exclui um registro",
     * description="Exclui um registro por seu ID.",
     * tags={"outbound"},
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

            Outbounds::findOrFail($id)->delete();

            return response()->json(['excluído' => true], 201);

        } catch (ModelNotFoundException $e) {

            return response()->json(['messagem' => 'Não encontrado'], 404);

        }
    }

        /**
     * @OA\Get(
     * path="/v1/cancelar-uniqueid/",
     * summary="Ler dados do contato outbound pelo uniqueid",
     * description="Ler dados do contato outbound pelo uniqueid",
     * tags={"outbound"},
     * @OA\Response(response="200", description="Dados do Contato Outbound"),
     * )
     */
    public function uniqueid($id)
    {

        $campanhas_emails = Campanhas_emails::where('uniqueid',$id)->get();

        if (count($campanhas_emails) > 0) {

            try {

                $appSite = env('APP_SITE');
                $outbounds = Outbounds::select('email')->where('id', $campanhas_emails[0]->outbounds_fk)->get();

                $reg = [
                    "email" => $outbounds[0]->email,
                    "site" => $appSite
                ];

                return response()->json($reg, 200);

            } catch (\Exception $e) {

                return response()->json(['messagem' => $e], 422);
                
            }  
            
        } else {

            return response()->json(['messagem' => 'UniqueId não encontrado.'], 422);

        }

    }

    /**
     * @OA\Get(
     * path="/v1/cancelar-inscricao/",
     * summary="Cancelar contato outbound",
     * description="Marca o contato outbound como inativo",
     * tags={"outbound"},
     * @OA\Response(response="200", description="Contato Outbound marcado como inativo."),
     * )
     */
    public function cancelar($id)
    {

        $campanhas_emails = Campanhas_emails::where('uniqueid',$id)->get();

        if (count($campanhas_emails) > 0) {

            try {

                $outbounds = Outbounds::findOrFail($campanhas_emails[0]->outbounds_fk);

                if ($outbounds->ativo == 1) {
                    
                    $outbounds->ativo = 0;
                    $outbounds->save();

                    $campanhas = Campanhas::findOrFail($campanhas_emails[0]->campanhas_fk);
                    $campanhas->qtdcancelados = $campanhas->qtdcancelados + 1;
                    $campanhas->save();
                }

            } catch (\Exception $e) {

                return response()->json(['messagem' => $e], 422);
                
            }  
            
            return response()->json(['mensagem' => 'Contato inativo'], 200);

        } else {

            return response()->json(['messagem' => 'UniqueId não encontrado.'], 422);

        }

    }

    /**
     * @OA\Get(
     * path="/v1/campanha-clicou/",
     * summary="Clicou para abrir o link da campanha",
     * description="Clicou para abrir o link da campanha",
     * tags={"outbound"},
     * @OA\Response(response="200", description="Clicou para abrir o link da campanha."),
     * )
     */
    public function clicou($id)
    {

        $campanhas_emails = Campanhas_emails::where('uniqueid',$id)->get();

        if (count($campanhas_emails) > 0) {

            try {

                $outbounds = Outbounds::findOrFail($campanhas_emails[0]->outbounds_fk);

                if ($outbounds->ativo == 1) {
                    
                    $campanhas = Campanhas::findOrFail($campanhas_emails[0]->campanhas_fk);
                    $campanhas->qtdvisitas = $campanhas->qtdvisitas + 1;
                    $campanhas->save();
                }

            } catch (\Exception $e) {

                return response()->json(['messagem' => $e], 422);
                
            }  
            
            return response()->json(['mensagem' => 'Contato inativo'], 200);

        } else {

            return response()->json(['messagem' => 'UniqueId não encontrado.'], 422);

        }

    }
}
