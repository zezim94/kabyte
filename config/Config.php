<?php
require_once __DIR__ . '/../vendor/autoload.php'; // caminho do autoload do Composer

// Carrega variáveis do .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../'); // raiz do projeto
$dotenv->load();

// Define constantes usando variáveis de ambiente
define('BASE_URL', $_ENV['BASE_URL'] ?? 'http://localhost/kabyte/');

// URL de callback do Google (mantém dinâmica)
define('GOOGLE_REDIRECT_URL', BASE_URL . 'index.php?rota=login/google_callback');