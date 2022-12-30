<?php

namespace Tests;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class SitesTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_sites_lista_sem_autenticacao()
    {
        
        $this->get('/api/sites');

        $this->seeStatusCode(401);

    }


    // public function test_sites_lista_com_autenticacao()
    // {

    //     $client = $this->post( '/api/login', [], [],
    //         ['CONTENT_TYPE' => 'application/json'],
    //         json_encode(
    //             [
    //                 "email" => "mascabral@gmail.com",
    //                 "password" => "teste",
    //             ]
    //         )
    //     );


    //     echo $client;

    //     $this->get('/api/sites', [], [], [
    //         'headers' => [
    //             'Authorization' => 'bearer '. $token
    //         ]
    //     ]);

    //     $this->seeStatusCode(200);

    // } 

}
