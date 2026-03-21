<?php
require_once __DIR__ . '/../vendor/autoload.php'; // caminho do autoload do Composer

// Carrega variáveis do .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../'); // raiz do projeto
$dotenv->load();

// Define constantes usando variáveis de ambiente
define('BASE_URL', $_ENV['BASE_URL'] ?? 'http://localhost/kabyte/');

define('MP_PUBLIC_KEY', $_ENV['MP_PUBLIC_KEY'] ?? '');
define('MP_ACCESS_TOKEN', $_ENV['MP_ACCESS_TOKEN'] ?? '');

define('GOOGLE_CLIENT_ID', $_ENV['GOOGLE_CLIENT_ID'] ?? '');
define('GOOGLE_CLIENT_SECRET', $_ENV['GOOGLE_CLIENT_SECRET'] ?? '');

// URL de callback do Google (mantém dinâmica)
define('GOOGLE_REDIRECT_URL', BASE_URL . 'index.php?rota=login/google_callback');