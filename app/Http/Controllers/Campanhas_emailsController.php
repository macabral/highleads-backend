<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Campanhas_emails;
use App\Models\Campanhas;
use App\Models\Outbounds;

class Campanhas_emailsController extends Controller
{

    /**
     * @OA\Get(
     * path="/v1/campanhas/",
     * summary="Exibe os registros cadastrados",
     * description="Lista os registros cadastrados.",
     * tags={"campanhas"},
     * @OA\Response(response="200", description="Lista dos campanhas"),
     * )
     */
    public function index($id)
    {
 
        $emails = Campanhas_emails::where('campanhas_fk', $id)->with('Outbounds')->get();

        if (count($emails) > 0) {

            $id = $emails[0]->campanhas_fk;

            $input = [
                'qtdemails' => count($emails)
            ];
         
            try {

                $campanhas = Campanhas::findOrFail($id);

                $campanhas->qtdemails = count($emails);

                $campanhas->save();

            } catch (\Exception $e) {
    
                return response()->json(['messagem' => $e], 200);
                
            }

        }

        return $emails;

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

            return Campanhas_emails::findOrFail($id);

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

        $query = Campanhas_emails::select('id','uniqueid');

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
     * path="/v1/campanhas-emails",
     * summary="Criar um novo registro",
     * description="Criar um novo registro",
     * tags={"campanhas"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Inclui um novo email na campanha",
     *    @OA\JsonContent(
     *       required={"campanhas_fk","outbounds_fk","uniqueid"},
     *       @OA\Property(property="campanhas_fk", type="integer", example="1"),
     *       @OA\Property(property="outbounds_fk", type="integer", example="1"),* 
     *       @OA\Property(property="uniqueid", type="text", format="text", example="dfwqer342re")
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
            'campanhas_fk' => 'required',
            'outbounds_fk' => 'required',
            'uniqueid' => 'required|max:120',
        ]);

        try {

            $site = Campanhas_emails::create($request->all());

            return response()->json(['created' => true], 201);

        } catch (\Exception $e) {

            return response()->json(['messagem' => $e], 404);
            
        }
    }

    /**
     * @OA\PUT(
     * path="/v1/campanhas-emails/{id}",
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
            'campanhas_fk' => 'required',
            'outbounds_fk' => 'required',
            'uniqueid' => 'required|max:120',
        ]);

        try {

            $campanhas = Campanhas_emails::findOrFail($id);

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

            Campanhas_emails::findOrFail($id)->delete();

            return response()->json(['excluído' => true], 201);

        } catch (ModelNotFoundException $e) {

            return response()->json(['messagem' => 'Não encontrado'], 404);

        }
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
    public function destroy_all($id)
    {
        try {

            Campanhas_emails::where('campanhas_fk',$id)->delete();

            return response()->json(['excluído' => true], 201);

        } catch (ModelNotFoundException $e) {

            return response()->json(['messagem' => 'Não encontrado'], 404);

        }
    }

    /**
     * @OA\Post(
     * path="/v1/campanhas-emails-filtrar",
     * summary="Filtrar emails para a campanha",
     * description="Filtrar emails para a campanha",
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
    public function filtrar(Request $request)
    {
     
        $input = $request->all();
        $query = Outbounds::select()->where('isvalid',1)->where('ativo',1);

        try {

            if (! $input['nome'] == '') {
                $search = $input['nome'];
                $query->where('nome', 'like',  "%$search%");
            }

            if (! $input['email'] == '') {
                $search = $input['email'];
                $query->where('email', 'like',  "%$search%");
            }

            if (! $input['empresa'] == '') {
                $search = $input['empresa'];
                $query->where('empresa', 'like',  "%$search%");
            }

            if (! $input['posicao'] == '') {
                $search = $input['posicao'];
                $query->where('posicao', 'like',  "%$search%");
            }

            if (! $input['categorias_fk'] == 0) {
                $search = $input['categorias_fk'];
                $query->where('categorias_fk', $search);
            }

            if (! $input['iscliente'] == 0) {
                $search = $input['iscliente'];
                $query->where('iscliente', $search);
            }

            if (! $input['iscontato'] == 0) {
                $search = $input['iscontato'];
                $query->where('iscontato', $search);
            }

            return $query->orderBy('nome')->get();
    
        } catch (\Exception $e) {
    
            return response()->json(['messagem' => 'Nada encontrado ' . $e], 404);
                
        }
       
    }

    /**
     * @OA\Post(
     * path="/v1/campanhas-emails-filtrar",
     * summary="Filtrar emails para a campanha",
     * description="Filtrar emails para a campanha",
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
    public function incluir(Request $request)
    {
     
        $input = $request->all();

        $outbounds = $input['emails'];

        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvxywz';
        $n = 30;

        foreach($outbounds as $item) {

            try {

                $randomString = '';
                
                for ($i = 0; $i < $n; $i++) {
                    $index = rand(0, strlen($characters) - 1);
                    $randomString .= $characters[$index];
                }

                $req = [
                    'outbounds_fk' => $item,
                    'campanhas_fk' => $input['idcampanha'],
                    'uniqueid' => $input['idcampanha'].$randomString
                ];
            
                Campanhas_emails::create($req);
    
            } catch (\Exception $e) {
    
                // ignore
            }

        }

        return response()->json(['messagem' => 'emails incluídos '], 200);

    }    
}
