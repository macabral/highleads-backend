<?php

/** @var \Laravel\Lumen\Routing\Router $router */


$router->group(['prefix' => 'v1', 'middleware' => 'auth'], function () use ($router) {

    $router->get('/config', ['uses' => 'ConfiguracoesController@index','as' => 'config']);
    $router->get('/config/{id}', ['uses' => 'ConfiguracoesController@show','as' => 'find_config']);

    $router->get('/sites', ['uses' => 'SitesController@index','as' => 'sites']);
    $router->get('/sites/{id}', ['uses' => 'SitesController@show','as' => 'find_sites']);
    $router->get('/sites-search', ['uses' => 'SitesController@search','as' => 'search_sites']);
    $router->post('/sites', ['uses' => 'SitesController@store','as' => 'new_site']);
    $router->put('/sites/{id}', ['uses' => 'SitesController@update','as' => 'update_site']);
    $router->delete('/sites/{id}', ['uses' => 'SitesController@destroy','as' => 'delete_site']);

    $router->get('/contatos-status/{status}', ['uses' => 'ContatosController@index','as' => 'contatos']);
    $router->get('/contatos/{id}', ['uses' => 'ContatosController@show','as' => 'find_contatos']);
    $router->get('/contatos-search', ['uses' => 'ContatosController@search','as' => 'search_contatos']);
    $router->post('/contatos', ['uses' => 'ContatosController@store','as' => 'new_contatos']);
    $router->put('/contatos/{id}', ['uses' => 'ContatosController@update','as' => 'update_contatos']);
    $router->delete('/contatos/{id}', ['uses' => 'ContatosController@destroy','as' => 'delete_contatos']);

    $router->get('/blacklist', ['uses' => 'BlacklistasController@index','as' => 'blacklista']);
    $router->get('/blacklist/{id}', ['uses' => 'BlacklistasController@show','as' => 'find_blacklista']);
    $router->get('/blacklist-search', ['uses' => 'BlacklistasController@search','as' => 'search_blacklista']);
    $router->post('/blacklist', ['uses' => 'BlacklistasController@store','as' => 'new_blacklista']);
    $router->put('/blacklist/{id}', ['uses' => 'BlacklistasController@update','as' => 'update_blacklista']);
    $router->delete('/blacklist/{id}', ['uses' => 'BlacklistasController@destroy','as' => 'delete_blacklista']);
    
    $router->get('/emails', ['uses' => 'EmailsController@index','as' => 'emails']);
    $router->get('/emails/{id}', ['uses' => 'EmailsController@show','as' => 'find_emails']);
    $router->get('/emails-search', ['uses' => 'EmailsController@search','as' => 'search_emails']);
    $router->get('/emails-send', ['uses' => 'EmailsController@send','as' => 'send_email']);
    $router->post('/emails', ['uses' => 'EmailsController@store','as' => 'new_emails']);
    $router->put('/emails/{id}', ['uses' => 'EmailsController@update','as' => 'update_emails']);
    $router->delete('/emails/{id}', ['uses' => 'EmailsController@destroy','as' => 'delete_emails']);

    $router->post('/register', ['uses' =>  'AuthController@register', 'as' => 'auth_register']);

    $router->get('/usuarios', ['uses' =>  'UsuariosController@index', 'as' => 'usuarios_lista',]);
    $router->get('/usuarios-profile', ['uses' =>  'UsuariosController@profile', 'as' => 'usuarios_profile', 'middleware' => 'auth']);
    $router->post('/usuarios', ['uses' =>  'AuthController@register', 'as' => 'auth_register']);
    $router->put('/usuarios/{id}', ['uses' =>  'UsuariosController@update', 'as' => 'atualiza_usuario']);
    $router->get('/usuarios-search', ['uses' => 'UsuariosController@search','as' => 'search_usuarios']);

    $router->get('/notes/{contato}', ['uses' => 'NotesController@index','as' => 'litar_notas']);
    $router->post('/notes', ['uses' => 'NotesController@store','as' => 'salvar_notas']);
    $router->delete('/notes/{id}', ['uses' => 'NotesController@destroy','as' => 'excluir_notas']);

    $router->get('/imap-reader', ['uses' =>  'ImapController@index', 'as' => 'imap_reader']);

});

$router->post('/v1/login', ['uses' =>  'AuthController@login', 'as' => 'auth_login']);
$router->get('/v1/verifica-email', ['uses' =>  'UsuariosController@verificaEmail', 'as' => 'verifica_email']);
$router->get('/v1/envia-codigo', ['uses' =>  'UsuariosController@enviaCodigo', 'as' => 'envia_codigo']);
$router->put('/v1/altera-senha', ['uses' =>  'UsuariosController@alteraSenha', 'as' => 'altera_senha']);

$router->get('/', function () use ($router) {
    return "Highleads-backend - " . $router->app->version();
});

