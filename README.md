# HighLeads - Gerenciamento de Leads

Você precisa captar leads por uma campanha de mídias digitais que levem seu interessado para uma página na web (landing page) que contém um formulário
para que o interessado informe seus dados de contato (nome, email e telefone).

Geralmente as landing pages são construídas em Word Press e você utiliza um formulário que encaminha os dados do interessado para uma conta de email.

Gerenciar os leads em uma conta de email é muito difícil. Assim, o Highleads tem essa função, ler essa conta de email (IMAP), guardar as informações dos interessados em um banco de dados e gerenciar essas informações para a prospecção de futuros clientes.

O Highleds possui um serviço backend e o gerencimanto dos leads é realizado no frontend.  Tudo isso vai rodar no mesmo servidor que você publica as suas landing pages.


## highleads backend

O serviço backend foi desenvolvido utilizando o framework laravel/lumen. Isso porque geralmente você hospeda sua landing page em servidores "shared hosts" que possuem o Word Press instalado, fornecem conta de email e serviço de banco de dados.  Assim, é mais fácil colocar tudo em um mesmo servidor.

### Setup do ambiente local

O highleads-backend roda com o PHP 8 e laravel/lumen 9.

1. instale o Composer  (https://getcomposer.org/download/)
2. faça o download do PHP 8 e configure o ambiente (path) (https://www.php.net/downloads.php)
3. verifique se as extensões no php.ini estão habilitadas (extension=imap, extension=mbstring)
4. faça o download do highleads-backend
5. execute o comando  "composer install"
### Configurações iniciais

Para configurar o highleads-backend siga os passos:

1. renomeie o arquivo .env.example para .env
2. edite o arquivo .env e preencha as informações de conexão ao banco de dados, servidor IMAP e SMTP
3. para gerar a chave JWT (JWT_SECRET) execute o comando "php artisan jwt:secret"
4. execute a criação do banco de dados e seed com o comando "php artisan migrate:fresh --seed" - você pode editar os seeders para colocar mais dados para testes.
5. para executar o servidor local execute o comando "php artisan serve"
6. para acessar a aplicação abra seu navegador e digite o endereço: http://localhost:8000


### Documentação da API

Para acessar a documentação da API highleads-backend vá ao endereço: http://localhost:8000/api/documentation e altere o endereço para http://localhost:8000/docs

A documentação segue o padrão Swagger.  

Para publicar qualquer alteração na documentação utilize o comando "php artisan swagger-lume:generate" e "php artisan swagger-lume:publish".

Para obter as rotas "php artisan route:list".

### Serviços Agendados

O Highleads-backend executa dois serviços cron:

1. para ler os emails (IMAP)
2. para enviar os emails

Para alterar o agendamento dos serviços altere em app/Console/Kernel.php.  Hoje estão configurados para execução de 10 em 10 minutos.

Para executar o serviços "php artisan schedule:run" ou "php artisan schedule:work".

O serviço IMAP lê os emails encaminhados pelo formulário da landing page, recupera as informações do formulário, verifica se o email está na lista negra (tabela blacklist), grava as informações no banco de dados e encaminha um email para o reponsável pela landing page (registrado na tabela sites).

O serviço de emails é responsável pelo envio de emais do highleads. Todos os emails gerados são armazenados na tabela emails e são enviados por esse serviço.


## highleads frontend

O frontend (ainda em desenvolvimento) utilizará o NUXT (Vue) como um SPA (Single Page Application) que também será disponibilizado no mesmo servidor "shared host".


# Lumen PHP Framework

[![Build Status](https://travis-ci.org/laravel/lumen-framework.svg)](https://travis-ci.org/laravel/lumen-framework)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel/lumen-framework)](https://packagist.org/packages/laravel/lumen-framework)
[![Latest Stable Version](https://img.shields.io/packagist/v/laravel/lumen-framework)](https://packagist.org/packages/laravel/lumen-framework)
[![License](https://img.shields.io/packagist/l/laravel/lumen)](https://packagist.org/packages/laravel/lumen-framework)

Laravel Lumen is a stunningly fast PHP micro-framework for building web applications with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Lumen attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as routing, database abstraction, queueing, and caching.

## Official Documentation

Documentation for the framework can be found on the [Lumen website](https://lumen.laravel.com/docs).

## Contributing

Thank you for considering contributing to Lumen! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Security Vulnerabilities

If you discover a security vulnerability within Lumen, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

## License

The Lumen framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).


