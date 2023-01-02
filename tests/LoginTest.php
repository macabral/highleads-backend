<?php

namespace Tests;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class LoginTest extends TestCase
{

    public function test_registrar_usuario()
    {
        $this
            ->post('/api/register', [
                "nome" => "Teste",
                "email" => "teste@teste.com",
                "password" => "123456",

            ]);

        $this
            ->seeStatusCode(201);
    }

    public function test_login_com_sucesso()
    {
       
        $this
            ->SeeInDatabase('usuarios',['email' => 'teste@teste.com']);

        $this
            ->post('/api/login', [
                "email" => "teste@teste.com",
                "password" => "123456",

            ]);

        $this
            ->seeStatusCode(200);

        $data = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('token', $data);

    }
}
