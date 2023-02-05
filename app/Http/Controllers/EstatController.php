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
                        (select count(*) from contatos as a1 where a1.status = 1 and a1.site = contatos.site) as 'st1',
                        (select count(*) from contatos as a2 where a2.status = 2 and a2.site = contatos.site) as 'st2',
                        (select count(*) from contatos as a3 where a3.status = 3 and a3.site = contatos.site) as 'st3',
                        (select count(*) from contatos as a4 where a4.status = 4 and a4.site = contatos.site) as 'st4',
                        (select count(*) from contatos as a5 where a5.status = 5 and a5.site = contatos.site) as 'st5'
                    from contatos
                    group by site";
            } else {
                $sql = "select site,
                    (select count(*) from contatos as a1 where a1.status = 1 and a1.site = contatos.site and if(a1.usuarios_fk = $idUsuario, true, false)) as 'st1',
                    (select count(*) from contatos as a2 where a2.status = 2 and a2.site = contatos.site and if(a2.usuarios_fk = $idUsuario, true, false)) as 'st2',
                    (select count(*) from contatos as a3 where a3.status = 3 and a3.site = contatos.site and if(a3.usuarios_fk = $idUsuario, true, false)) as 'st3',
                    (select count(*) from contatos as a4 where a4.status = 4 and a4.site = contatos.site and if(a4.usuarios_fk = $idUsuario, true, false)) as 'st4',
                    (select count(*) from contatos as a5 where a5.status = 5 and a5.site = contatos.site and if(a5.usuarios_fk = $idUsuario, true, false)) as 'st5'
                from contatos
                where if(contatos.usuarios_fk = $idUsuario, true, false)
                group by site";  
            }

            $cursor = DB::select($sql);

            $result = [];
            $somaTotal = 0; $soma1 = 0; $soma2 = 0; $soma3 = 0; $soma4 = 0; $soma5 = 0;
            foreach($cursor as $d) {
                $soma = $d->st1 + $d->st2 + $d->st3 + $d->st4 + $d->st5;
                $somaTotal = $somaTotal + $soma;
                $soma1 = $soma1 + $d->st1;
                $soma2 = $soma2 + $d->st2;
                $soma3 = $soma3 + $d->st3;
                $soma4 = $soma4 + $d->st4;
                $soma5 = $soma5 + $d->st5;
                $arr = [
                    'site' => $d->site,
                    'total' => $soma,
                    '1' => $d->st1,
                    '2' => $d->st2,
                    '3' => $d->st3,
                    '4' => $d->st4,
                    '5' => $d->st5
                ];
                array_push($result, $arr);
            }
            $arr = [
                'site' => 'TOTAL',
                'total' => $somaTotal,
                '1' => $soma1,
                '2' => $soma2,
                '3' => $soma3,
                '4' => $soma4,
                '5' => $soma5
            ];
            array_push($result, $arr);
        
        } catch (\Exception $e) {

            return response()->json(['messagem' => $e], 200);
            
        }

        return response()->json($result);
    }
}
