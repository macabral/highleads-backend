# HighLeads - Gerenciamento de Leads

Você precisa captar leads por uma campanha de mídias digitais que levem o interessado para uma página na web (landing page) que contém um formulário
para que o interessado informe seus dados de contato (nome, email e telefone).

Geralmente as landing pages são construídas em Word Press e você utiliza um formulário que encaminha os dados do interessado para uma conta de email.

Gerenciar os leads em uma conta de email é muito trabalhoso. Assim, o HighLeads automatiza o processo de captação de leads. Ele lê a conta de email (IMAP), armazena as informações dos leads em um banco de dados e gerencia essas informações para a prospecção de futuros clientes.

Você pode ter várias landing pages apontando para a mesma conta de email.  O HighLeads envia um aviso de 'Novo Contato' para as contas de email que você indicar para cada landing page.

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

Para testar a API, se tiver problema de CORS o middleware CorsMiddleware faz a liberação para que a API possa ser acessada por diferentes clientes. Se quiser pode remover o arquivo CorsMiddleware.php (pasta Http\Middleware) e remova-o do bootstrap/app.php.  (fonte: https://dev.to/tadeubdev/php-curtas-resolvendo-cors-origins-no-lumen-23ih).

### Tabelas

- contatos    : armazena os dados dos formulários das landing pages
- usuarios    : cadastro de usuários
- sites       : lista as páginas (landing pages) associadas aos seus responsáveis
- blacklist   : lista dos emails cadastrados para não serem incluídos como contatos
- emails      : lista os emails a serem enviados pela plataforma HighLeads
- codigos     : armazena os códigos de recuperação de senha (enviador por email para o usuário)
- notes       : armazena as anotações do acompanhamento do contato

Para detalhes das tabelas veja na pasta \database\migrations.

### Status do Contato

Ao Contato pode ser atribuído os seguintes status:

- [1] - Novo: quando o contato é inserido no banco de dados e nenhuma ação foi iniciada
- [2] - Em prospecção: quando uma ação de conversão em vendas foi iniciada
- [3] - Qualificado: quando o contato está em processo de venda efetiva
- [4] - Encerrado - Positivo: quando o contato converteu em venda
- [5] - Encerrado - Negativo: quando o contato não converteu em venda

### Score

Um Score pode ser atribuído ao Contato para identificar aqueles mais propensos para aquisição.
O Score é atribuído com um valor de 0 a 10. 
### Perfil dos Usuários

O Usuário pode ter o perfil:
- [1] - Usuário Administrador
- [2] - Consultor de Vendas
### Instalando o HighLeads no servidor (shared host)

1. Crie um arquivo compactado TAR do HighLeads da sua instalação local
2. Crie um subdominio no shared host. Utilize o nome api-highleads (por exemplo: https://api-highleads.dominio.com.br)
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

O frontend está disponível em https://github.com/macabral/highleads-frontend.

# Lumen PHP Framework

[![Build Status](https://travis-ci.org/laravel/lumen-framework.svg)](https://travis-ci.org/laravel/lumen-framework)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel/lumen-framework)](https://packagist.org/packages/laravel/lumen-framework)
[![Latest Stable Version](https://img.shields.io/packagist/v/laravel/lumen-framework)](https://packagist.org/packages/laravel/lumen-framework)
[![License](https://img.shields.io/packagist/l/laravel/lumen)](https://packagist.org/packages/laravel/lumen-framework)

[Official Documentation](https://lumen.laravel.com/docs)

Laravel Lumen is a stunningly fast PHP micro-framework for building web applications with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Lumen attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as routing, database abstraction, queueing, and caching.

## License

The Lumen framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
