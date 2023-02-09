<?php

/** @var \Laravel\Lumen\Routing\Router $router */


$router->group(['prefix' => 'v1', 'middleware' => 'auth'], function () use ($router) {

    $router->get('/estat/{perfil}/{idUsuario}', ['uses' => 'EstatController@index','as' => 'estat']);

    $router->get('/contatos-status/{status}', ['uses' => 'ContatosController@index','as' => 'contatos']);
    $router->get('/contatos/{id}', ['uses' => 'ContatosController@show','as' => 'find_contatos']);
    $router->post('/contatos', ['uses' => 'ContatosController@store','as' => 'new_contatos']);
    $router->put('/contatos/{id}', ['uses' => 'ContatosController@update','as' => 'update_contatos']);
    $router->delete('/contatos/{id}', ['uses' => 'ContatosController@destroy','as' => 'delete_contatos']);
    $router->post('/contatos-search', ['uses' => 'ContatosController@search','as' => 'search_contatos']);
    
    $router->get('/notes/{contato}', ['uses' => 'NotesController@index','as' => 'litar_notas']);
    $router->post('/notes', ['uses' => 'NotesController@store','as' => 'salvar_notas']);
    $router->delete('/notes/{id}', ['uses' => 'NotesController@destroy','as' => 'excluir_notas']);

    $router->get('/sites', ['uses' => 'SitesController@index','as' => 'sites']);
    $router->get('/sites-all', ['uses' => 'SitesController@all','as' => 'sites']);
    $router->get('/sites/{id}', ['uses' => 'SitesController@show','as' => 'find_sites']);
    $router->get('/sites-search', ['uses' => 'SitesController@search','as' => 'search_sites']);
    $router->post('/sites', ['uses' => 'SitesController@store','as' => 'new_site']);
    $router->put('/sites/{id}', ['uses' => 'SitesController@update','as' => 'update_site']);
    $router->delete('/sites/{id}', ['uses' => 'SitesController@destroy','as' => 'delete_site']);
    
    $router->get('/outbound/{perfil}/{usuario}', ['uses' => 'OutboundController@index','as' => 'outbound']);
    $router->get('/outbound-all', ['uses' => 'OutboundController@all','as' => 'outbound_all']);
    $router->get('/outbound/{id}', ['uses' => 'OutboundController@show','as' => 'find_outbound']);
    $router->post('/outbound-search', ['uses' => 'OutboundController@search','as' => 'search_outbound']);
    $router->post('/outbound', ['uses' => 'OutboundController@store','as' => 'new_outbound']);
    $router->put('/outbound/{id}', ['uses' => 'OutboundController@update','as' => 'update_outbound']);
    $router->delete('/outbound/{id}', ['uses' => 'OutboundController@destroy','as' => 'delete_outbound']);
    $router->post('/importar-outbound', ['uses' => 'ImportarController@outbound','as' => 'importar_outbound']);

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

    $router->get('/usuarios', ['uses' =>  'UsuariosController@index', 'as' => 'usuarios_lista',]);
    $router->get('/usuarios-profile', ['uses' =>  'UsuariosController@profile', 'as' => 'usuarios_profile', 'middleware' => 'auth']);
    $router->post('/usuarios', ['uses' =>  'AuthController@register', 'as' => 'auth_register']);
    $router->put('/usuarios/{id}', ['uses' =>  'UsuariosController@update', 'as' => 'atualiza_usuario']);
    $router->get('/usuarios-search', ['uses' => 'UsuariosController@search','as' => 'search_usuarios']);
    $router->delete('/usuarios/{id}', ['uses' =>  'UsuariosController@destroy', 'as' => 'usuarios_delete',]);

    $router->get('/categorias', ['uses' => 'CategoriasController@index','as' => 'categorias']);
    $router->get('/categorias-all', ['uses' => 'CategoriasController@all','as' => 'categorias_all']);
    $router->get('/categorias/{id}', ['uses' => 'CategoriasController@show','as' => 'find_categorias']);
    $router->get('/categorias-search', ['uses' => 'CategoriasController@search','as' => 'search_categorias']);
    $router->post('/categorias', ['uses' => 'CategoriasController@store','as' => 'new_categorias']);
    $router->put('/categorias/{id}', ['uses' => 'CategoriasController@update','as' => 'update_categorias']);
    $router->delete('/categorias/{id}', ['uses' => 'CategoriasController@destroy','as' => 'delete_categorias']);

    $router->get('/campanhas/{perfil}/{usuario}', ['uses' => 'CampanhasController@index','as' => 'campanhas']);
    $router->get('/campanhas-all', ['uses' => 'CampanhasController@all','as' => 'campanhas_all']);
    $router->get('/campanhas/{id}', ['uses' => 'CampanhasController@show','as' => 'find_campanhas']);
    $router->post('/campanhas-search', ['uses' => 'CampanhasController@search','as' => 'search_campanhas']);
    $router->post('/campanhas', ['uses' => 'CampanhasController@store','as' => 'new_campanhas']);
    $router->put('/campanhas/{id}', ['uses' => 'CampanhasController@update','as' => 'update_campanhas']);
    $router->delete('/campanhas/{id}', ['uses' => 'CampanhasController@destroy','as' => 'delete_campanhas']);

    $router->get('/imap-reader', ['uses' =>  'ImapController@index', 'as' => 'imap_reader']);

    $router->post('/register', ['uses' =>  'AuthController@register', 'as' => 'auth_register']);

    $router->get('/logout', ['uses' =>  'AuthController@logout', 'as' => 'auth_logout']);

});

$router->post('/v1/login', ['uses' =>  'AuthController@login', 'as' => 'auth_login']);
$router->get('/v1/verifica-email', ['uses' =>  'UsuariosController@verificaEmail', 'as' => 'verifica_email']);
$router->get('/v1/envia-codigo', ['uses' =>  'UsuariosController@enviaCodigo', 'as' => 'envia_codigo']);
$router->put('/v1/altera-senha', ['uses' =>  'UsuariosController@alteraSenha', 'as' => 'altera_senha']);
$router->get('/v1/refresh-token', ['uses' =>  'AuthController@refreshToken', 'as' => 'refresh_auth']);

$router->get('/', function () use ($router) {
    return "Highleads-backend - " . $router->app->version();
});

