<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Campanhas;
use App\Models\Campanhas_emails;
use App\Models\Emails;

class CampanhasController extends Controller
{

    /**
     * @OA\Get(
     * path="/v1/campanhas-all/",
     * summary="Retorna todos registros cadastrados",
     * description="Retorna os registros cadastrados.",
     * tags={"campanhas"},
     * @OA\Response(response="200", description="Lista dos campanhas"),
     * )
     */
    public function all()
    {

        return Campanhas::all();

    }

    /**
     * @OA\Get(
     * path="/v1/campanhas/",
     * summary="Exibe os registros cadastrados",
     * description="Lista os registros cadastrados.",
     * tags={"campanhas"},
     * @OA\Response(response="200", description="Lista dos campanhas"),
     * )
     */
    public function index($perfil, $usuario)
    {

        if ($perfil == 1) {
            return Campanhas::paginate(perPage: 15);
        } else {
            return Campanhas::where('usuarios_fk', '=', $usuario)->paginate(perPage: 15);
        }

    }

    /**
     * @OA\Get(
     * path="/v1/campanhas/{id}",
     * summary="Exibe um registro",
     * description="Exibe o registro por seu ID.",
     * tags={"campanhas"},
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

            return Campanhas::findOrFail($id);

        } catch (ModelNotFoundException $e) {

            return response()->json(['messagem' => 'Não encontrado'], 404);

        }
    }

    /**
     * @OA\Post(
     * path="/v1/campanhas-search",
     * summary="Procurar registro por site",
     * description="Procurar registro por site",
     * tags={"campanhas"},
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
            'page' => 'required'
        ]);
       
        $input = $request->all();

        $query = Campanhas::select('id','titulo','assunto','emailhtml', 'qtdemails','qtdvisitas','qtdcancelados','dtenvio','hrenvio','enviado');

        try {


            if (! $input['search'] == '') {
                $search = $input['search'];
                $query->where('titulo', 'like',  "%$search%");
            }

            return $query->orderBy('titulo')->paginate(15, ['*'], 'page', $input['page']);
    
            } catch (\Exception $e) {
    
                return response()->json(['messagem' => 'Nada encontrado ' . $e], 404);
                
            }
       
    }
    
    /**
     * @OA\POST(
     * path="/v1/campanhas",
     * summary="Criar um novo registro",
     * description="Criar um novo registro",
     * tags={"campanhas"},
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
            'titulo' => 'required|max:120',
            'assunto' => 'required|max:120'
        ]);

        try {

            $site = Campanhas::create($request->all());

            return response()->json(['created' => true], 201);

        } catch (\Exception $e) {

            return response()->json(['messagem' => $e], 404);
            
        }
    }

    /**
     * @OA\PUT(
     * path="/v1/campanhas/{id}",
     * summary="Alterar um registro",
     * description="Alterar um registro por ID",
     * tags={"campanhas"},
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
            'titulo' => 'required|max:120',
            'assunto' => 'required|max:120'
        ]);

        try {

            $campanhas = Campanhas::findOrFail($id);

        } catch (ModelNotFoundException $e) {

            return response()->json(['messagem' => 'Não encontrado'], 404);

        }

        $input = $request->all();

        try {
            
            $campanhas->fill($input);

        } catch (\Exception $e) {

            return response()->json(['messagem' => $e], 422);
            
        }

        $campanhas->save();

        return response()->json(['updated' => true], 201);

    }

    /**
     * @OA\Delete(
     * path="/v1/campanhas/{id}",
     * summary="Exclui um registro",
     * description="Exclui um registro por seu ID.",
     * tags={"campanhas"},
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

            Campanhas::findOrFail($id)->delete();

            return response()->json(['excluído' => true], 201);

        } catch (ModelNotFoundException $e) {

            return response()->json(['messagem' => 'Não encontrado'], 404);

        }
    }

        /**
     * @OA\Get(
     * path="/v1/campanhas-distribuir/{id}",
     * summary="Distribuir Campanha",
     * description="Insere os emails na tabela de envio de emails",
     * tags={"campanhas"},
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
    public function distribuir($id)
    {
        try {

            $campanhas = Campanhas::findOrFail($id);

            $dtenviar = date('Y-m-d H:i', strtotime($campanhas->dtenvio . ' ' . $campanhas->hrenvio));
        
            $emails = Campanhas_emails::where('campanhas_fk', $campanhas->id)->with('Outbounds')->get();

            if (count($emails) > 0) {

                foreach($emails as $elem) {

                    $assunto = $campanhas->assunto;
                    $assunto = str_replace('[CONTATO_NOME]', $elem->outbounds->nome, $assunto);

                    $texto = $campanhas->emailhtml;
                    $texto = str_replace('[CONTATO_NOME]', $elem->outbounds->nome, $texto);
                    $texto = str_replace('[UNIQUEID]', $elem->uniqueid, $texto);

                    $input = [
                        "para" => $elem->outbounds->email,
                        "assunto" => $assunto,
                        "prioridade" => 10,
                        "texto" => $texto,
                        "dtenviar" => $dtenviar
                    ];

                    try {

                        Emails::create($input);
                        
                    } catch (ModelNotFoundException $e) {
            
                        echo 'Erro ao inserir email.';
                        
                    }
                }

                $campanhas->enviado = 1;
                $campanhas->save();

            }

        } catch (ModelNotFoundException $e) {

            return response()->json(['messagem' => 'Não encontrado'], 404);

        }
    }
}
