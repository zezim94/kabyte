<?php
session_name("loja_kabyte");

require_once 'config/Config.php';
require_once 'config/Database.php';
require_once 'models/Auth.php';

Auth::iniciarSessao();

// ==================================================
// 1. TRATAMENTO DA URL
// ==================================================
// Rota padrão agora é 'produto/vitrine'
$url = $_GET['rota'] ?? 'produto/vitrine';
$url = rtrim($url, '/');
$partes = explode('/', $url);

// Extrai o ID dinamicamente (ex: produto/detalhes/10 -> $_GET['id'] = 10)
if (count($partes) > 1 && is_numeric(end($partes))) {
    $_GET['id'] = array_pop($partes);
}
$rota = implode('/', $partes);

// ==================================================
// 2. AUTO-ROUTING (Descoberta Automática)
// ==================================================
$partesRota = explode('/', $rota);

// Descobre o Controller (ex: 'cliente_area' vira 'ClienteAreaController')
$prefixoController = array_shift($partesRota);
$prefixoLimpo = str_replace(['-', '_'], ' ', $prefixoController);
$controllerName = str_replace(' ', '', ucwords($prefixoLimpo)) . 'Controller';

// Descobre o Método (se a URL não disser, o padrão é abrir o 'index')
$metodoName = array_shift($partesRota) ?? 'index';

// ==================================================
// 3. INSTANCIAÇÃO E CONTROLE DE SEGURANÇA
// ==================================================
$arquivoController = "controllers/{$controllerName}.php";

if (file_exists($arquivoController)) {
    require_once $arquivoController;

    // Instancia a classe dinamicamente
    $instancia = new $controllerName();

    if (method_exists($instancia, $metodoName)) {

        // --- SEGURANÇA AUTOMÁTICA ---
        $acessoLiberado = false;

        // Se o Controller possuir a variável $livre e o método estiver lá, libera acesso
        if (isset($instancia->livre) && in_array($metodoName, $instancia->livre)) {
            $acessoLiberado = true;
        }

        // Se não for área pública e o ADMIN não estiver logado, bloqueia!
        if (!$acessoLiberado && !isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . 'produto/vitrine');
            exit;
        }

        // Tudo certo! Executa a função mapeada
        $instancia->$metodoName();
        exit;
    } else {
        http_response_code(404);
        die("<b>Erro 404:</b> A função <code>{$metodoName}()</code> não foi encontrada dentro de <code>{$controllerName}</code>.");
    }
} else {
    http_response_code(404);
    die("<b>Erro 404:</b> O arquivo <code>{$arquivoController}</code> não existe no sistema. Verifique a URL.");
}
