# HighLeads - Gerenciamento de Leads

Você precisa captar leads por uma campanha de mídias digitais que levem seu interessado para uma página na web (landing page) que contém um formulário
para que o interessado informe seus dados de contato (nome, email e telefone).

Geralmente as landing pages são construídas em Word Press e você utiliza um formulário que encaminha os dados do interessado para uma conta de email.

Gerenciar os leads em uma conta de email é muito trabalhoso. Assim, o HighLeads automatiza o processo de captação de leads. Ele lê a conta de email (IMAP), armazena as informações dos leads em um banco de dados e gerencia essas informações para a prospecção de futuros clientes.

Você pode ter várias landing pages apontando para a mesma conta de email.

O Highleds possui um serviço backend para o serviço de API e o frontend - interface para o gerenciamento dos leads.  Esses serviços podem ser executados no mesmo servidor que você publica as suas landing pages.


# HighLeads backend

O serviço backend foi desenvolvido utilizando o framework laravel/lumen. Isso porque geralmente você hospeda sua landing page em servidores "shared hosts" que possuem o Word Press instalado, fornecem conta de email e serviço de banco de dados.  Assim, é mais fácil colocar tudo em um mesmo servidor.

### Setup do ambiente local

O HighLeads-backend roda com o PHP 8.0.27 e laravel/lumen 9.1.5.

1. instale o Composer  (https://getcomposer.org/download/)
2. faça o download do PHP 8 e configure o ambiente (path) (https://www.php.net/downloads.php)
3. verifique se as extensões no php.ini estão habilitadas (extension=imap, extension=mbstring, extension=pdo_mysql, extension=fileinfo)
4. faça o download do HighLeads-backend
5. execute o comando  "composer install"
### Configurações iniciais

Para configurar o HighLeads-backend siga os passos:

1. renomeie o arquivo .env.example para .env
2. edite o arquivo .env e preencha as informações de conexão ao banco de dados, servidor IMAP e SMTP
3. para gerar a chave JWT (JWT_SECRET) execute o comando "php artisan jwt:secret"
4. execute a criação do banco de dados e seed com o comando "php artisan migrate:fresh --seed" - você pode editar os seeders para colocar mais dados para testes.
5. para executar o servidor local execute o comando "php artisan serve"
6. para acessar a aplicação abra seu navegador e digite o endereço: http://localhost:8000


### Documentação da API

Para acessar a documentação da API HighLeads-backend vá ao endereço: http://localhost:8000/api/documentation e altere o endereço para http://localhost:8000/docs

A documentação segue o padrão Swagger.  

Para publicar qualquer alteração na documentação utilize o comando "php artisan swagger-lume:generate" e "php artisan swagger-lume:publish".

Para obter a lista das rotas: "php artisan route:list".

### Serviços Agendados

O HighLeads-backend executa dois serviços cron:

1. para ler os emails (IMAP)
2. para enviar os emails

Para alterar o agendamento dos serviços altere em app/Console/Kernel.php.  Hoje estão configurados para execução de 10 em 10 minutos.

Para executar o serviços "php artisan schedule:run" ou "php artisan schedule:work".

O serviço IMAP lê os emails encaminhados pelo formulário da landing page, recupera as informações do formulário, verifica se o email está na lista negra (tabela blacklist), grava as informações no banco de dados e encaminha um email para o responsável pela landing page (registrado na tabela sites).

O serviço de email é responsável pelo envio dos emails armazenados na tabela emails..

### Executando os testes

Para executar os testes do HighLeads-backend execute o comando "composer tests". Os testes são executados sem o middleware de autenticação (token).  Para a execução dos teste as tabelas devem estar truncadas.

Ainda em desenvolvimento.

### Tabelas

- contatos    : armazena os dados dos formulários das landing pages
- usuarios    : cadastro de usuários
- sites       : lista as páginas (landing pages) associadas aos seus responsáveis
- blacklist   : lista dos emails cadastrados para não serem incluídos como contatos
- emails      : lista os emails a serem enviados pela plataforma HighLeads

Para detalhes das tabelas veja na pasta \database\migrations.

### Instalando o HighLeads no servidor (shared host)

1. Crie um arquivo compactado TAR do HighLeads da sua instalação local
2. Crie um subdominio em seu shared host. Utilize o nome api-highleads (por exemplo: https://api-highleads.dominio.com.br)
2. Copie o arquivo compactado para a pasta do subdominio no shared host
3. Acesse o servidor pelo SSH e vá para a pasta do subdominio
4. Descompacte o arquivo com o comando tar -xvf <nome_do_arquivo_compactado.tar>
5. Certifique que o .htacess foi copiado para a pasta raiz do subdominio
6. Pronto. Teste acessando a url https://api-highleads.dominio.com.br no navegador


### Configurando o serviço cron no servidor (shared host)
1. No cPanel do servidor shared host acesse 'Trabalhos Cron'
2. Crie um serviço cron onde o Comando seja como: /usr/local/bin/php pasta_do_subdominio/artisan schedule:run
3. Configure a frequência do serviço cron. Lembre que no HighLeads está configurado para execução de 10/10 minutos

# HighLeads frontend

O frontend (ainda em desenvolvimento) utilizará o NUXT (Vue) como um SPA (Single Page Application) que também será disponibilizado no mesmo servidor "shared host".


# Lumen PHP Framework

[![Build Status](https://travis-ci.org/laravel/lumen-framework.svg)](https://travis-ci.org/laravel/lumen-framework)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel/lumen-framework)](https://packagist.org/packages/laravel/lumen-framework)
[![Latest Stable Version](https://img.shields.io/packagist/v/laravel/lumen-framework)](https://packagist.org/packages/laravel/lumen-framework)
[![License](https://img.shields.io/packagist/l/laravel/lumen)](https://packagist.org/packages/laravel/lumen-framework)

[Official Documentation](https://lumen.laravel.com/docs)

Laravel Lumen is a stunningly fast PHP micro-framework for building web applications with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Lumen attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as routing, database abstraction, queueing, and caching.

## License

The Lumen framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
