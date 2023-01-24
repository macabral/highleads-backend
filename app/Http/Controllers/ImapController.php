<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Contatos;
use App\Models\Blacklistas;
use App\Models\Sites;
use App\Models\Emails;
use App\Models\Outbounds;
class ImapController extends Controller
{
    public function index() 

    {
        $meses =  ['janeiro', 'fevereiro', 'março', 'abril', 'maio','junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];
        $appSigla = env('APP_SIGLA');

        // ****** Obtém as informações de conexão ao servidor IMAP do .env

        $host = env('IMAP_SERVIDOR', '');
        $usuario = env('IMAP_USUARIO', '');
        $pass = env('IMAP_SENHA', '');

        // ****** Conecta ao servidor IMAP
        
        // $mbox = imap_open($host, $usuario, $pass, OP_READONLY);
        $mbox = imap_open($host, $usuario, $pass, OP_READONLY);

        if ($mbox) {

            $emails = imap_search($mbox,'UNSEEN');
            if($emails) {
                rsort($emails);

                foreach($emails as $email_number) {
            
                    $texto = imap_fetchbody($mbox,$email_number,1);
                    $message = imap_qprint($texto);

                    $pieces = explode(":", $message);

                    // ****** Obtém as informações do body da mensagem de email removendo o \r\n
                    // ****** as informações a serem obtidas são: Nome, Telefone, E-mail, site, datahora, remoteIP

                    $nome = substr($pieces[2], 0, strpos($pieces[2], 'Telefone') - 2);
                    $nome = substr($nome, 1, strpos($nome, '\r\n')-3);
                    $telefone = substr($pieces[3], 0, strpos($pieces[3], 'E-mail') - 2);
                    $telefone = substr($telefone, 1, strpos($telefone, '\r\n')-3);
                    $email =  substr($pieces[4], 0, strpos($pieces[4], '---') - 2);
                    $email = substr($email, 1, strpos($email, '\r\n')-6);
                    $dt = substr($pieces[5], 0, strpos($pieces[5], 'Time') - 2);
                    $hora = $pieces[6] . ':' . substr($pieces[7], 0, strpos($pieces[7], 'Page') - 2);
                    $hora = substr($hora, 0, strpos($hora, '\r\n')-2);
                    $url = substr($pieces[9], 0, strpos($pieces[9], 'User') - 2);
                    $url = substr($url, 2, strpos($url, '\r\n')-3);
                    $remoteIP = substr($pieces[11], 0, strpos($pieces[11], 'Po') - 2);
                    $remoteIP = substr($remoteIP, 1, strpos($remoteIP, '\r\n')-3);

                    // ****** transforma da data em YYYY-MM_dd HH:MM
                    $dt2 = explode(' ', $dt);
                    $dia = $dt2[1]; 
                    $mes = 0;
                    for ($i = 0; $i < count($meses); $i++) {
                        $pos = strpos($dt, $meses[$i]);
                        if ($pos != false) {
                            $mes = $i +1;
                        }
                    }
                    $ano = substr($dt2[5], 0, strpos($dt, '\r\n')-2);
                    $dataH =  $ano . '-' . $mes . '-' . $dia . ' '. $hora;
                    $dataEmail = $dia . '/' . $mes . '/' . $ano . ' ' . $hora;

                    // ***** Verifica se o email do contato está na lista negra (blacklist)

                    $ret = Blacklistas::where('texto', $email)->get();

                    if (count($ret) == 0) {

                        // ****** procura a landing page
                        
                        $ret = Sites::where('pagina', $url)->get();

                        if (count($ret) > 0) {

                            // ****** verifica se o contato está em outbound

                            $ret = Outbounds::where('email', $email)->with('Usuarios')->get();

                            if (count($ret) == 0) {
                                $usuarios_fk = null;
                                $outboundEmail = '';
                            } else {
                                $usuarios_fk = $ret[0]->usuarios_fk;
                                $outboundEmail = $ret[0]->usuarios->email;

                                $outbound = Outbounds::findOrFail($ret[0]->id);

                                $input = [
                                    'iscontato' => 1
                                ];

                                // atualiza outbound marcando iscontato
                                $outbound->fill($input);
                                $outbound->save();

                            }

                            // ***** Insere o contato na tabela de Contatos

                            $input = [
                                'nome' => $nome,
                                'email' => $email,
                                'telefone' => $telefone,
                                'site' => $url,
                                'datahora' => $dataH,
                                'remoteip' => $remoteIP,
                                'usuarios_fk' => $usuarios_fk,
                                'sites_fk' => $ret[0]->id
                            ];

                            try {

                                Contatos::create($input);

                                $destino = $ret[0]->email;
                                if ($outboundEmail !== '') {
                                    $destino = $destino . ';' . $outboundEmail;
                                }

                                if ($destino !== '') {
                                
                                    // ****** envia email para o responsável pela landing page

                                    $input = [
                                        "para" => $destino,
                                        "assunto" => "[$appSigla] Novo Contato",
                                        "prioridade" => 0,
                                        "texto" => "Prezado(a) Sr(a),<p>Um novo contato foi recebido.</p>Nome: $nome <br>Email: $email <br>Telefone:  $telefone <br>Página: $url <br> Data: $dataEmail"
                                    ];

                                    try {

                                        Emails::create($input);
                                        
                                    } catch (ModelNotFoundException $e) {
                            
                                        echo 'Erro ao inserir email.';
                                        
                                    }
                                }                             
                
                            } catch (ModelNotFoundException $e) {
                    
                                echo $input['nome'] . ' =>Erro ao inserir o contato.';
                                
                            }
                            
                        }

                    }

                   
                    // marca como lido
                    // imap_setflag_full($mbox,imap_uid($mbox,$email_number),'\\SEEN');

                }

            }
       
            imap_close($mbox);
        } else {
            echo "Não foi possíve abrir a caixa de email.";
        }
    }
}
