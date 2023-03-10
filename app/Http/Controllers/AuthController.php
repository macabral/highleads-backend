<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuarios;
use App\Models\Emails;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{

    public function register(Request $request)
    {
        $this->validate($request, [
            'nome' => 'required|string',
            'email' => 'required|email|unique:usuarios',
            'password' => 'required',
        ]);

        try {

            $user = new Usuarios;
            $user->nome = $request->input('nome');
            $user->email = $request->input('email');
            $user->perfil = $request->input('perfil');
            $user->ativo = $request->input('ativo');
            $plainPassword = $request->input('password');
            $user->password = app('hash')->make($plainPassword);

            $user->save();

            // envia email para o usuário
        
            $appSigla = env('APP_SIGLA');

            //lê template resources/template/altera-senha.html
            $path = base_path() . '/resources/templates/novo-usuario.html';
            $texto = file_get_contents($path);
            $texto = str_replace('[NOME]', $user->nome, $texto);
            $texto = str_replace('[EMPRESA]', $appSigla, $texto);

            $input = [
                "para" => $user->email,
                "assunto" => "[HighLeads ] Novo Usuário",
                "prioridade" => 1,
                "texto" => $texto
            ];

            try {

                Emails::create($input);
                
            } catch (\Exception $e) {

                return response()->json(['message' => $e], 409);
                
            }

            return response()->json(['user' => $user, 'message' => 'CREATED'], 201);

        } catch (\Exception $e) {

            return response()->json(['message' => $e], 409);

        }

    }

    public function login(Request $request)
    {

        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);


        $credentials = $request->only(['email', 'password']);

        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }


        $usuarios = Usuarios::where('email', $request->email)->where('ativo', 1)->get();

        if (count($usuarios) === 0) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $usu = $usuarios[0];

        $ret = [
            'id' => $usu['id'],
            'nome' => $usu['nome'],
            'email' => $usu['email'],
            'perfil' => $usu['perfil'],
            'token' => $token
        ];

        return $ret;

        // return response()->json($ret, 200);

    }

    public function logout()
    {
        auth()->logout();
        return response()->json([
            'message' => 'Logout with success!'
        ], 200);
    }

    // public function GenerateToken($user)
    // {
    //     $secretKey  = env('JWT_KEY');
    //     $tokenId    = base64_encode(random_bytes(16));
    //     $issuedAt   = new DateTimeImmutable();
    //     $expire     = $issuedAt->modify('+6 minutes')->getTimestamp();     
    //     $serverName = "your.server.name";
    //     $userID   = $user->id;                                    

    //     // Create the token as an array
    //     $data = [
    //         'iat'  => $issuedAt->getTimestamp(),    
    //         'jti'  => $tokenId,                     
    //         'iss'  => $serverName,                  
    //         'nbf'  => $issuedAt->getTimestamp(),    
    //         'exp'  => $expire,                      
    //         'data' => [                             
    //             'userID' => $userID,            
    //         ]
    //     ];

    //     // Encode the array to a JWT string.
    //     $token = JWT::encode(
    //         $data,      
    //         $secretKey, 
    //         'HS512'     
    //     );

    //     return $token;
    // }

    public function refreshToken(Request $request)
    {
        try {
           
            $token = auth()->refresh();

        } catch (\Exception $e) {

            return response()->json(['messagem' => $e], 401);
            
        }

        return response()->json(['token' => $token]);

    }
    

}