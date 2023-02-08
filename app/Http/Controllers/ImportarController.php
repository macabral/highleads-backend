<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Outbounds;

class ImportarController extends Controller
{
        /**
     * @OA\POST(
     * path="/v1/importar-outbound",
     * summary="Criar um novo registro",
     * description="Criar um novo registro",
     * tags={"Importar"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Dados da Categoria",
     *    @OA\JsonContent(
     *       required={"descricao","ativo"},
     *       @OA\Property(property="descricao", type="string", format="text", example="Governo"),
     *       @OA\Property(property="ativo", type="integer", example="1"),
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
    public function outbound(Request $request)
    {

        try {
          
            $fileName = time() . '_' . $request->file->getClientOriginalName();
            $request->file('file')->storeAs('uploads', $fileName, 'public');
            $categoria_fk = $request->categoria_fk;
            $usuarios_fk = $request->usuarios_fk;

            $fileStor = base_path() . '/storage/app/public/uploads/' . $fileName;

            $file = fopen($fileStor, "r");

            while ( ($data = fgetcsv($file, 250, ",",'"')) !== false) {
                $input = [
                    'nome' => $data[0],
                    'email' => $data[1],
                    'empresa' => $data[2],
                    'posicao' => $data[3],
                    'telefone' => $data[4],
                    'cidade' => $data[5],
                    'categorias_fk' => $categoria_fk,
                    'usuarios_fk' => $usuarios_fk
                ];

                try {

                    Outbounds::create($input);

                } catch (\Exception $e) {
                    // null
                }
             }

            fclose($file);

            unlink($fileStor);

            return response()->json(['messagem' => 'Sucesso!'], 200);

        } catch (\Exception $e) {

            return response()->json(['messagem' => $e], 422);
            
        }
    }

}
