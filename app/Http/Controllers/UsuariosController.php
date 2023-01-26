<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Models\Usuarios;
use App\Models\Emails;
use App\Models\Codigos;
class UsuariosController extends Controller
{

    /**
     * @OA\Get(
     * path="/v1/usuarios/",
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

    /**
     * @OA\Get(
     * path="/v1/usuarios-search?nome={nome}",
     * summary="Procurar registro",
     * description="Procurar registro",
     * tags={"Usuários"},
     * @OA\Response(response="200", description="Retorna dados."),
     * @OA\Parameter(
     *    description="site",
     *    in="query",
     *    name="site",
     *    required=true,
     *    example="João da Silva",
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

            $query = Usuarios::where('nome', 'like',  "%$search%")->get();

        } catch (\Exception $e) {

            return response()->json(['messagem' => $e], 404);
            
        }

        return response()->json($query);
        
    }

    /**
     * @OA\PUT(
     * path="/v1/usuarios/{id}",
     * summary="Alterar um registro",
     * description="Alterar um registro por ID",
     * tags={"Usuários"},
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
     *       required={"nome","email"},
     *       @OA\Property(property="nome", type="string", format="text", example="José da Silva"),* 
     *       @OA\Property(property="email", type="email", format="text", example="mercedes68@example.org"),
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
            'email' => 'max:80'
        ]);

        try {

            $usuario = Usuarios::findOrFail($id);

        } catch (ModelNotFoundException $e) {

            return response()->json(['messagem' => 'Não encontrado'], 404);

        }

        $input = $request->all();

        try {
            
            $usuario->fill($input);

        } catch (\Exception $e) {

            return response()->json(['messagem' => $e], 422);
            
        }

        $usuario->save();

        return response()->json(['updated' => true], 201);

    }

    /**
     * @OA\Get(
     * path="/v1/envia-codigo?email={email}",
     * summary="Envia código de confirmação",
     * description="Envia o código de confirmação para o email informado",
     * tags={"Usuários"},
     * @OA\Response(response="200", description="Confirma que o código foi encaminhado."),
     * @OA\Response(response="404", description="Email não encontrado."), 
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
    public function enviaCodigo(Request $request)
    {

        $this->validate($request, [
            'email' => 'required|max:80',
        ]);

        $search = $request->get('email');      
         
        try {

            $query = Usuarios::select('id','nome','email')->where('email', '=', $search)->get();

        } catch (\Exception $e) {

            return response()->json(['messagem' => $e], 404);
            
        }

        if (count($query) === 0) {
            return response()->json(['messagem' => false], 404);
        }

        $user = json_decode($query[0]);

        // gera código de confirmação

        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        $n = 4;
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        $token = strtoupper($randomString);

        // armazena o código na tabela de codigos

        $input = [
            "codigo" => $token,
            "usuarios_fk" => $user->id
        ];

        try {

            Codigos::create($input);

        } catch (ModelNotFoundException $e) {

            return response()->json(['messagem' => $e], 400);
            
        }

        // envia email para o usuário
        
        $appSigla = env('APP_SIGLA');

        //lê template resources/template/altera-senha.html
        $path = base_path() . '/resources/templates/alterar-senha.html';
        $texto = file_get_contents($path);
        $texto = str_replace('[NOME]', $user->nome, $texto);
        $texto = str_replace('[CODIGO]', $token, $texto );
        $texto = str_replace('[EMPRESA]', $appSigla, $texto);

        $input = [
            "para" => $user->email,
            "assunto" => "[HighLeads ] Código de Confirmação",
            "prioridade" => 0,
            "texto" => $texto
        ];

        try {

            Emails::create($input);
            
        } catch (ModelNotFoundException $e) {

            return response()->json(['messagem' => true], 404);
            
        }

        app('App\Http\Controllers\EmailsController')->send();

        return response()->json(['messagem' => true], 200);

    }

    /**
     * @OA\PUT(
     * path="/v1/altera-senha",
     * summary="Altera senha do usuário",
     * description="Alterar a senha do usuário cadastrado",
     * tags={"Usuários"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Dados do Usuario",
     *    @OA\JsonContent(
     *       required={"email","codigo","senha"},
     *       @OA\Property(property="email", type="email", format="text", example="mercedes68@example.org"),
     *       @OA\Property(property="codigo", type="string", format="text", example="JAVA"),
     *       @OA\Property(property="senha", type="string",  format="text", example="******"),
     *    ),
     * ),
     * @OA\Response(
     *    response=201,
     *    description="Senha Alterada com sucesso.",
     *    @OA\JsonContent(
     *       @OA\Property(property="updated", type="string", example="true")
     *    ) 
     * ),
     * @OA\Response(response=422,description="Erro ao alterar a senha."),
     * @OA\Response(response=404,description="Usuário não encontrado.",@OA\JsonContent(
     *       @OA\Property(property="mensagem", type="string", example="Não encontrado")
     *    )  
     *   )
     * )
     */
    public function alteraSenha(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|max:80',
            'codigo' => 'required|max:5',
            'senha' => 'required|max:50'
        ]);

        // --- Verifica se o email está cadastrado
        try {

            $usuario = Usuarios::select('id','nome','email')->where('email', '=', $request->email)->get();

        } catch (\Exception $e) {

            return response()->json(['messagem' => $e], 404);
            
        }

        if (count($usuario) === 0) {
            return response()->json(['messagem' => false], 404);
        }

        // --- Verifica o Código de Confirmação
        try {

            $codigo = Codigos::where('codigo', '=', $request->codigo)->get();

        } catch (ModelNotFoundException $e) {

            return response()->json(['messagem' => 'Não encontrado'], 404);

        }

        if (count($codigo) === 0) {
            return response()->json(['messagem' => false], 404);
        }

        // --- Verifica a validade do Código de Confirmação

        $dataCodigo = $codigo[0]->created_at->setTimezone('UTC')->addHour(1);

        $now = Carbon::now()->setTimezone('UTC');

        if ($now > $dataCodigo) {
            return response()->json(['messagem' => false], 404);
        }

        // --- Verifica se o Código de Confirmação está associado ao usuário do email cadastrado

        $usuario_id = $usuario[0]->id;
        $id = $codigo[0]->usuarios_fk;

        if ($usuario_id != $id) {
            return response()->json(['messagem' => 'Não encontrado'], 404);
        }

        // --- Altera a senha

        $input = [
            'password' => app('hash')->make($request->senha)
        ];

        try {

            $usuarios = Usuarios::findOrFail($id);

            $usuarios->fill($input);

        } catch (\Exception $e) {

            return response()->json(['messagem' => $e], 422);
            
        }

        $usuarios->save();

        // --- exclui o código de confirmação

        Codigos::findOrFail($codigo[0]->id)->delete();

        return response()->json(['updated' => true], 201);

    }

    /**
     * @OA\Delete(
     * path="/v1/usuarios/{id}",
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

            Usuarios::findOrFail($id)->delete();

            return response()->json(['excluído' => true], 201);

        } catch (ModelNotFoundException $e) {

            return response()->json(['messagem' => 'Não encontrado'], 404);

        }
    }
}
