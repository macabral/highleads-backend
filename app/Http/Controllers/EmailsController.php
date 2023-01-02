<?php

namespace App\Http\Controllers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use App\Models\Emails;

class EmailsController extends Controller
{

    /**
     * @OA\Get(
     * path="/api/emails/",
     * summary="Exibe os registros cadastrados",
     * description="Lista os registros cadastrados.",
     * tags={"Emails"},
     * @OA\Response(response="200", description="Lista dos emails"),
     * )
     */
    public function index()
    {
        return Emails::all();
    }

    /**
     * @OA\Get(
     * path="/api/emails/{id}",
     * summary="Exibe um registro",
     * description="Exibe o registro por seu ID.",
     * tags={"Emails"},
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

            return Emails::findOrFail($id);
        } catch (ModelNotFoundException $e) {

            return response()->json(['messagem' => 'Não encontrado'], 404);
        }
    }

    /**
     * @OA\Get(
     * path="/api/emails-search?email={email}",
     * summary="Procurar registro",
     * description="Procurar registro",
     * tags={"Emails"},
     * @OA\Response(response="200", description="Retorna dados."),
     * @OA\Parameter(
     *    description="email",
     *    in="query",
     *    name="email",
     *    required=true,
     *    example="teste",
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

            $query = DB::table('emails')->where('para',  $search)->get();

        } catch (\Exception $e) {

            return response()->json(['messagem' => $e], 404);
        }

        return response()->json($query);
    }

    /**
     * @OA\POST(
     * path="/api/emails",
     * summary="Criar um novo registro",
     * description="Criar um novo registro",
     * tags={"Emails"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Dados do emails",
     *    @OA\JsonContent(
     *       required={"para","cc","bcc","assunto","texto","erro","enviado","anexos"},
     *       @OA\Property(property="para", type="email", format="text", example="mercedes68@example.org"),
     *       @OA\Property(property="cc", type="email", format="text", example=""),* 
     *       @OA\Property(property="bcc", type="email", format="text", example=""),
     *       @OA\Property(property="assunto", type="string", format="text", example="Assunto"),
     *       @OA\Property(property="texto", type="string", format="text", example="Texto do email"),
     *       @OA\Property(property="erro", type="integer", example="0"),
     *       @OA\Property(property="enviado", type="integer", example="0")
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
            'para' => 'required|max:255',
            'cc' => 'max:255',
            'bcc' => 'max:255',
            'assunto' => 'required|max:80',
            'texto' => 'required|max:80',
            'erro' => 'max:1',
            'enviado' => 'max:1'
        ]);

        try {

            $emails= Emails::create($request->all());

            return response()->json(['created' => true], 201);

        } catch (\Exception $e) {

            return response()->json(['messagem' => $e], 404);
        }
    }

        /**
     * @OA\PUT(
     * path="/api/emails/{id}",
     * summary="Alterar um registro",
     * description="Alterar um registro por ID",
     * tags={"Emails"},
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
     *    description="Dados do emails",
     *    @OA\JsonContent(
     *       required={"para","cc","bcc","assunto","texto","erro","enviado","anexos"},
     *       @OA\Property(property="para", type="email", format="text", example="mercedes68@example.org"),
     *       @OA\Property(property="cc", type="email", format="text", example=""),* 
     *       @OA\Property(property="bcc", type="email", format="text", example=""),
     *       @OA\Property(property="assunto", type="string", format="text", example="Assunto"),
     *       @OA\Property(property="texto", type="string", format="text", example="Texto do email"),
     *       @OA\Property(property="erro", type="integer", example="0"),
     *       @OA\Property(property="enviado", type="integer", example="0")
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

        try {

            $emails = Emails::findOrFail($id);
            
        } catch (ModelNotFoundException $e) {

            return response()->json([
                'error' => [
                    'messagem' => 'Não encontrado'
                ]
            ], 404);
        }

        $input = $request->all();

        try {

            $emails->fill($input);
        } catch (\Exception $e) {

            return response()->json([
                'error' => [
                    'messagem' => $e
                ]
            ], 404);
        }

        $emails->save();

        return response()->json($emails, 200);
    }

    /**
     * @OA\Delete(
     * path="/api/emails/{id}",
     * summary="Exclui um registro",
     * description="Exclui um registro por seu ID.",
     * tags={"Emails"},
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

            Emails::findOrFail($id)->delete();

            return response()->json(['excluído' => true], 201);

        } catch (ModelNotFoundException $e) {

            return response()->json(['messagem' => 'Não encontrado'], 404);
        }
    }

    /**
     * @OA\Get(
     * path="/api/emails-send",
     * summary="Enviar emails",
     * description="Enviar emails.",
     * tags={"Emails"},
     * @OA\Response(response="200", description="Emails enviados"),
     * )
     */
    public function send()
    {
        require base_path("vendor/autoload.php");


        $mail = new PHPMailer(true);

        //Server settings
        $mail->SMTPDebug = 0;                                       //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = env('SMTP_HOST', '');                   //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = env('SMTP_USERNAME', '');               //SMTP username
        $mail->Password   = env('SMTP_PASSWORD', '');               //SMTP password
        $mail->SMTPSecure = true;                                   //Enable implicit TLS encryption
        $mail->Port       = env('SMTP_PORT', '');                   //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        $cursor = Emails::all()->where('enviado','=',0)->take(10)->sortBy('prioridade');

        $mail->addReplyTo($mail->Username);
        $mail->setFrom($mail->Username);
        $mail->isHTML(true);

        foreach ($cursor as $item) {
            $para = "";
            $cc = "";
            $bcc = "";
            $arquivos = "";

            $para = explode(";", $item->para);
            foreach ($para as $dest) {
                $mail->addAddress(trim($dest));
            }

            if ($item->cc !== '') {
                $cc = explode(";", $item->cc);
                foreach ($cc as $dest) {
                    $mail->addCC(trim($dest));
                }
            }

            if ($item->bcc !== '') {
                $bcc = explode(";", $item->bcc);
                foreach ($bcc as $dest) {
                    $mail->addBCC(trim($dest));
                }
            }

            // Email subject
            $mail->Subject = utf8_decode($item->assunto);

            // Email body content
            $content = '<font face="verdana" size="2">' . $item->texto . '  <p>Atenciosamente,<br><br>HighLeads - Gerenciamento de Leads';
            $mail->Body = utf8_decode($content);

            // Anexos
            if (!empty($item->anexo)) {
                $arquivos = $item->anexo;
                $arqs = explode(';', $arquivos);
                foreach ($arqs as $e) {
                    $file = trim($e);
                    if (file_exists($file)) {
                        $mail->AddAttachment($file, basename($file));
                    }
                }
            }

            try {
                $mail->send();
                echo 'Mensagem enviada.';

                $emails = Emails::findOrFail($item->id);
                $emails->enviado = 1;
                $emails->save();

            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
            $mail->clearAddresses();
            $mail->clearAttachments();
        }
    }
}
