<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class EstatController extends Controller
{
    /**
     * @OA\Get(
     * path="/v1/estat",
     * summary="Estatisticas",
     * description="Estatisticas",
     * tags={"Estatisticas"},
     * @OA\Response(response="200", description="Retorna dados."),
     * @OA\Response(response="404", description="Item não encontrado."), 
     * )
     */
    public function index($perfil, $idUsuario)
    {

        try {


            if ($perfil == 1) {
                $sql = "select site, 
                        (select count(*) from contatos as a1 where a1.status = 1 and a1.site = contatos.site) as '1',
                        (select count(*) from contatos as a2 where a2.status = 2 and a2.site = contatos.site) as '2',
                        (select count(*) from contatos as a3 where a3.status = 3 and a3.site = contatos.site) as '3',
                        (select count(*) from contatos as a4 where a4.status = 4 and a4.site = contatos.site) as '4',
                        (select count(*) from contatos as a5 where a5.status = 5 and a5.site = contatos.site) as '5'
                    from contatos
                    group by site";
            } else {
                $sql = "select site, 
                    (select count(*) from contatos as a1 where a1.status = 1 and a1.site = contatos.site and if(a1.usuarios_fk = $idUsuario, true, false)) as '1',
                    (select count(*) from contatos as a2 where a2.status = 2 and a2.site = contatos.site and if(a2.usuarios_fk = $idUsuario, true, false)) as '2',
                    (select count(*) from contatos as a3 where a3.status = 3 and a3.site = contatos.site and if(a3.usuarios_fk = $idUsuario, true, false)) as '3',
                    (select count(*) from contatos as a4 where a4.status = 4 and a4.site = contatos.site and if(a4.usuarios_fk = $idUsuario, true, false)) as '4',
                    (select count(*) from contatos as a5 where a5.status = 5 and a5.site = contatos.site and if(a5.usuarios_fk = $idUsuario, true, false)) as '5'
                from contatos
                where if(contatos.usuarios_fk = $idUsuario, true, false)
                group by site";  
            }

            $cursor = DB::select($sql);
        
        } catch (\Exception $e) {

            return response()->json(['messagem' => $e], 200);
            
        }

        return response()->json($cursor);
    }
}
