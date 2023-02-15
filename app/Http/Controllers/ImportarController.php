<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Outbounds;

class ImportarController extends Controller
{

    protected $stream = false;
    protected $port = 587;
    protected $from = 'contato@zoit.com';
    protected $max_connection_timeout = 30;
    protected $stream_timeout = 5;
    protected $stream_timeout_wait = 0;
    protected $exceptions = false; 
    protected $error_count = 0; 
    const CRLF = "\r\n"; 
    public $ErrorInfo = []; 

    /**
     * @OA\POST(
     * path="/v1/importar-outbound",
     * summary="Importa emails para Outbound",
     * description="Importação de emails para Outbound",
     * tags={"Importar"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Dados da Categoria",
     *    @OA\JsonContent(
     *       required={"email","categoria"},
     *       @OA\Property(property="email", type="string", format="text", example="teste@teste.com.br"),
     *       @OA\Property(property="categoria", type="integer", example="1"),
     *    ),
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Importado com sucesso.",
     *    @OA\JsonContent(
     *       @OA\Property(property="created", type="string", example="true")
     *        )
     *     ), 
     *    @OA\Response(
     *      response=422,
     *      description="Erro ao inserir."
     *    )
     * )
     */
    public function outbound(Request $request)
    {

        try {
          
            $fileName = time() . '_' . $request->file->getClientOriginalName();
            $request->file('file')->storeAs('uploads', $fileName, 'public');
            $categoria_fk = $request->categoria_fk;
            $usuarios_fk = $request->usuarios_fk;

            $fileStor = base_path() . '/storage/app/public/uploads/' . $fileName;

            $file = fopen($fileStor, "r");

            while ( ($data = fgetcsv($file, 250, ",")) !== false) {

                $email = utf8_decode($data[1]);

                $isEmailValid = FALSE;

                if (! empty($email)) {
                    $domain = ltrim(stristr($email, '@'), '@') . '.';
                    $user = stristr($email, '@', TRUE);
            
                    // validate email's domain using DNS
                    if (! empty($user) && ! empty($domain)) {
                        $isEmailValid = TRUE;
                    }
               
                    if ($isEmailValid) {

                        if (checkdnsrr($domain, 'MX')) {

                            $input = [
                                'nome' => utf8_decode($data[0]),
                                'email' => $email,
                                'empresa' => utf8_decode($data[2]),
                                'posicao' => utf8_decode($data[3]),
                                'telefone' => utf8_decode($data[4]),
                                'cidade' =>utf8_decode($data[5]),
                                'categorias_fk' => $categoria_fk,
                                'usuarios_fk' => $usuarios_fk
                            ];

                            try {

                                Outbounds::create($input);

                            } catch (\Exception $e) {
                                // null
                            }
                        }
                    }
                }
             }

            fclose($file);

            unlink($fileStor);

            return response()->json(['messagem' => 'Sucesso!'], 200);

        } catch (\Exception $e) {

            return response()->json(['messagem' => $e], 422);
            
        }
    }

    /**
     * @OA\Get(
     * path="/v1/valida-email?email={email}",
     * summary="Valida email",
     * description="Valida email",
     * tags={"Importar"},
     * @OA\Response(response="200", description="Retorna dados."),
     * @OA\Parameter(
     *    description="email",
     *    in="query",
     *    name="email",
     *    required=true,
     *    example="teste@teste.com.br",
     *    @OA\Schema(
     *       type="string"
     *    )
     * )
     * )
     */
    public function valida(Request $request)
    {
        $email = $request->get('email');

        $domainPart = explode('@', $email)[1] ?? null;

        if ($domainPart) {
            try {

                $isEmailValid = FALSE;

                if (! empty($email)) {
                    $domain = ltrim(stristr($email, '@'), '@') . '.';
                    $user = stristr($email, '@', TRUE);
            
                    // validate email's domain using DNS
                    if (! empty($user) && ! empty($domain)) {
                        $isEmailValid = TRUE;
                    }
                }
               
                if ($isEmailValid) {

                    if (! checkdnsrr($domain, 'MX')) {
                        return response()->json(['messagem' => $email . ' MX inválido.'], 422);
                    } else {
                        $mxs = $this->getMXrecords(self::parse_email($email));
                        $this->set_error($mxs);
                        $timeout = ceil($this->max_connection_timeout / count($mxs)); 
                        foreach ($mxs as $host) {
                            $this->stream = @stream_socket_client("tcp://" . $host . ":" . $this->port, $errno, $errstr, $timeout); 
                            if ($this->stream === FALSE) {
                                $this->set_error("Problem initializing the socket");
                                return $this->ErrorInfo;
                            } else {
                                stream_set_timeout($this->stream, $this->stream_timeout); 
                                stream_set_blocking($this->stream, 1);
                                if ($this->_streamCode($this->_streamResponse()) == '220') { 
                                    break;
                                } else { 
                                    fclose($this->stream); 
                                    $this->stream = FALSE; 
                                }
                            }
                        }
                        if ($this->stream === FALSE) { 
                            $this->set_error("Problem initializing the socket");
                            return $this->ErrorInfo;
                        }
                        
                        $this->_streamQuery("HELO " . self::parse_email($this->from)); 
                        $this->_streamResponse(); 
                        $this->_streamQuery("MAIL FROM: <{$this->from}>"); 
                        $this->_streamResponse(); 
                        $this->_streamQuery("RCPT TO: <{$email}>"); 
                        $code = $this->_streamCode($this->_streamResponse()); 
                        $this->_streamResponse(); 
                        $this->_streamQuery("RSET"); 
                        $this->_streamResponse();
                        $code2 = $this->_streamCode($this->_streamResponse()); 
                        $this->_streamQuery("QUIT"); 
                        fclose($this->stream);
                        $code = !empty($code2)?$code2:$code;
                        return $code;
                        switch ($code) { 
                            case '250': 
                            /** 
                             * http://www.ietf.org/rfc/rfc0821.txt 
                             * 250 Requested mail action okay, completed 
                             * email address was accepted 
                             */ 
                            case '450': 
                            case '451': 
                            case '452': 
                                /** 
                                 * http://www.ietf.org/rfc/rfc0821.txt 
                                 * 450 Requested action not taken: the remote mail server 
                                 * does not want to accept mail from your server for 
                                 * some reason (IP address, blacklisting, etc..) 
                                 * Thanks Nicht Lieb. 
                                 * 451 Requested action aborted: local error in processing 
                                 * 452 Requested action not taken: insufficient system storage 
                                 * email address was greylisted (or some temporary error occured on the MTA) 
                                 * i believe that e-mail exists 
                                 */ 
                                return TRUE;
                            case '550':
                                return FALSE; 
                            default : 
                                return FALSE; 
                        }
                    }

                } else {
                    return response()->json(['messagem' => $email . ' inválido.'], 422);
                }

            } catch(\Exception $e) {
                return response()->json(['messagem' => $email . ' inválido.' . $e], 422);
            }
        } else {
            return response()->json(['messagem' => $email . ' inválido.'], 422);
        }

    }

    protected function _streamQuery($query) { 
        return stream_socket_sendto($this->stream, $query . self::CRLF); 
    }

    public function getMXrecords($hostname) { 
        $mxhosts = array(); 
        $mxweights = array(); 
        if (getmxrr($hostname, $mxhosts, $mxweights) === FALSE) { 
            $this->set_error('MX records not found or an error occurred'); 
        } else { 
            array_multisort($mxweights, $mxhosts); 
        } 
        /** 
         * Add A-record as last chance (e.g. if no MX record is there). 
         * Thanks Nicht Lieb. 
         * @link http://www.faqs.org/rfcs/rfc2821.html RFC 2821 - Simple Mail Transfer Protocol 
         */ 
        if (empty($mxhosts)) { 
            $mxhosts[] = $hostname; 
        } 
        return $mxhosts; 
    }

    protected function set_error($msg) { 
        $this->error_count++; 
        array_push($this->ErrorInfo, $msg);
    }

    public static function parse_email($email, $only_domain = TRUE) { 
        sscanf($email, "%[^@]@%s", $user, $domain); 
        return ($only_domain) ? $domain : array($user, $domain); 
    } 

    protected function _streamCode($str) { 
        preg_match('/^(?<code>[0-9]{3})(\s|-)(.*)$/ims', $str, $matches); 
        $code = isset($matches['code']) ? $matches['code'] : false; 
        return $code; 
    }

    protected function _streamResponse($timed = 0) { 
        $reply = stream_get_line($this->stream, 1); 
        $status = stream_get_meta_data($this->stream); 

        if ($reply === FALSE && $status['timed_out'] && $timed < $this->stream_timeout_wait) { 
            return $this->_streamResponse($timed + $this->stream_timeout); 
        } 

        if ($reply !== FALSE && $status['unread_bytes'] > 0) { 
            $reply .= stream_get_line($this->stream, $status['unread_bytes'], self::CRLF); 
        } 

        return $reply; 
    }
}
