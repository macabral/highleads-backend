<?php

namespace Tests;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ContatosTest extends TestCase
{

    public function test_retorna_lista_de_contatos()
    {

        $this
            ->get('/contatos')
            ->seeStatusCode(200);
            
    }

    public function test_retorna_um_contato_valido()
    {

        $this
            ->get('/contatos/1')
            ->seeStatusCode(200)
            ->seeJson([
                "id" => 1,
                "site" => "https://asdasd-one",
                "remoteip" => "127.0.0.1",
                "datahora" => "2022-12-13 14:50:00",
                "nome"  => "José da Silva",
                "email"  => "marcoascabral@gmail.com",
                "telefone"  => "21998045272",
            ]);
            $data = json_decode($this->response->getContent(), true);
            $this->assertArrayHasKey('created_at', $data);
            $this->assertArrayHasKey('updated_at', $data);
      
    }

    public function test_retorna_um_contato_invalido()
    {

        $this
            ->get('contatos/0')
            ->seeStatusCode(404)
            ->seeJson([
                'error' => [
                    'mensagem' => 'Não Encontrado'
                    ]
            ]);

    }

    public function test_incluir_novo_contato()
    {

        $this
            ->post('/contatos', [
                "site" => "https://zoit.com.br/teste/",
                "remoteip" => "127.0.0.1",
                "datahora" => "2022-12-13 14:50:00",
                "nome" => "José da Silva",
                "email" => "marcoascabral@gmail.com",
                "telefone" => "21998045272",
                "created_at" => "2022-12-23T16:53:08.000000Z",
                "updated_at" => "2022-12-23T16:53:08.000000Z",

            ]);
            
        $this
            ->seeJson(['created' => true])
            ->seeInDatabase('contatos', ['site' => 'https://zoit.com.br/teste/']);

    }

    public function test_alterar_contato()
    {


        $this
            ->notSeeInDatabase('contatos',['site' => 'https://zoit.com.br/outro9/']);

        $this
            ->put('/contatos/4', [
                "site" => "https://zoit.com.br/outro9/",
                "remoteip" => "127.0.0.1",
                "datahora" => "2022-12-13 14:50:00",
                "nome" => "José da Silva",
                "email" => "marcoascabral@gmail.com",
                "telefone" => "21998045272",

            ]);
            
        $this
            ->seeStatusCode(201)
            ->seeInDatabase('contatos', ['site' => 'https://zoit.com.br/outro9/']);

    }

    public function test_exclui_um_contato()
    {

        $this
            ->delete('contatos/7')
            ->seeStatusCode(204)
            ->isEmpty();

        $this->notSeeInDatabase('contatos', ['id' => 7]);
    }

    public function test_rota_invalida_excluir()
    {
        $this
            ->delete('/contatos/invalido')
            ->seeStatusCode(404);
    }

}
