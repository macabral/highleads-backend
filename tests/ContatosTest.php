<?php

namespace Tests;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ContatosTest extends TestCase
{

    public function test_incluir_novo_contato()
    {
        $this->withoutMiddleware();

        $this
            ->post('/api/contatos', [
                "site" => "https://teste.com/teste/",
                "remoteip" => "127.0.0.1",
                "datahora" => "2022-12-13 14:50:00",
                "nome" => "José da Silva",
                "email" => "jsl@gmail.com",
                "telefone" => "219999999"
            ]);
            
        $this
            ->seeJson(['created' => true]);

    }

    public function test_retorna_lista_de_contatos()
    {

        $this->withoutMiddleware();

        $this
            ->get('/api/contatos')
            ->seeStatusCode(200);
            
    }

    public function test_retorna_um_contato_valido()
    {

        $this->withoutMiddleware();

        $this
            ->get('/api/contatos/1')
            ->seeStatusCode(200)
            ->seeJson([
                "id" =>  1,
                "site" => "https://teste.com/teste/",
                "remoteip" =>  "127.0.0.1",
                "datahora" =>  "2022-12-13 14:50:00",
                "nome" =>  "José da Silva",
                "email" =>  "jsl@gmail.com",
                "telefone" =>  "219999999"
            ]);
            $data = json_decode($this->response->getContent(), true);
            $this->assertArrayHasKey('created_at', $data);
            $this->assertArrayHasKey('updated_at', $data);
      
    }

    public function test_retorna_um_contato_invalido()
    {

        $this->withoutMiddleware();

        $this
            ->get('/api/contatos/0')
            ->seeStatusCode(404)
            ->seeJson([
                'error' => [
                    'mensagem' => 'Não Encontrado'
                    ]
            ]);

    }

    public function test_alterar_contato()
    {

        $this->withoutMiddleware();
        
        $this
            ->notSeeInDatabase('contatos',['site' => 'https://zoit.com.br/outro9/']);

        $this
            ->put('/api/contatos/1', [
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

        $this->withoutMiddleware();

        $this
            ->delete('/api/contatos/1')
            ->seeStatusCode(204)
            ->isEmpty();

        $this->notSeeInDatabase('contatos', ['id' => 7]);
    }

    public function test_rota_invalida_excluir()
    {

        $this->withoutMiddleware();

        $this
            ->delete('/api/contatos/invalido')
            ->seeStatusCode(404);
    }

}
