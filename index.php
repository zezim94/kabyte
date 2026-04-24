<?php
// index.php

require_once 'config/Config.php';
require_once 'config/Database.php';
require_once 'models/Auth.php';

Auth::iniciarSessao();

// 1. Pega a rota
$url = $_GET['rota'] ?? 'vitrine';

// 2. Limpa URL
$url = rtrim($url, '/');
$partes = explode('/', $url);

// 3. Extrai ID se houver (lógica para rotas estilo cliente/checkout/10)
if (count($partes) > 1 && is_numeric(end($partes))) {
    $_GET['id'] = end($partes);
    array_pop($partes);
}

// 4. Reconstrói a rota
$rota = implode('/', $partes);

// ==================================================
// 1. ROTAS PÚBLICAS GERAIS
// ==================================================

if ($rota == 'logout') {
    require 'controllers/LoginController.php';
    (new LoginController())->logout();
    exit;
}

if ($rota == 'vitrine') {
    require 'controllers/ProdutoController.php';
    (new ProdutoController())->vitrine();
    exit;
}

if ($rota == 'api/enderecos') {
    require 'controllers/VendaController.php'; // Ou onde você colocou
    (new VendaController())->apiEnderecos();
    exit;
}

if ($rota == 'cliente/cadastro') {
    require 'controllers/ClienteController.php';
    (new ClienteController())->cadastro();
    exit;
}

if ($rota == 'cliente/processar_cadastro') {
    require 'controllers/ClienteController.php';
    (new ClienteController())->processar_cadastro();
    exit;
}
if ($rota == 'cliente/esqueci_senha') {
    require 'controllers/ClienteController.php';
    (new ClienteController())->esqueci_senha();
    exit;
}

if ($rota == 'cliente/processar_recuperacao') {
    require 'controllers/ClienteController.php';
    (new ClienteController())->processar_recuperacao();
    exit;
}

if ($rota == 'sobre') {
    // Vamos usar o ProdutoController mesmo, já que ele gerencia a vitrine pública
    require 'controllers/ProdutoController.php';
    (new ProdutoController())->sobre();
    exit;
}

if ($rota == 'produto/detalhes') {
    require 'controllers/ProdutoController.php';
    (new ProdutoController())->detalhes();
    exit;
}

if ($rota == 'login/google_callback') {
    require 'controllers/LoginController.php';
    (new LoginController())->google_callback();
    exit;
}

if ($rota == 'cliente/google_callback') {
    require 'controllers/ClienteAreaController.php';
    (new ClienteAreaController())->google_callback();
    exit;
}

if ($rota == 'login') {
    require 'controllers/LoginController.php';
    (new LoginController())->index();
    exit;
}

if ($rota == 'autenticar') {
    require 'controllers/LoginController.php';
    (new LoginController())->entrar();
    exit;
}

// ==================================================
// 2. ÁREA DO CLIENTE
// ==================================================
// Rota para o Chatbot com IA
if ($rota == 'api/chatbot') {
    require 'controllers/ApiController.php';
    (new ApiController())->chatIa();
    exit;
}
if ($rota == 'cliente/login') {
    require 'controllers/ClienteAreaController.php';
    (new ClienteAreaController())->login();
    exit;
}

if($rota == 'api/enderecos_cliente'){
    require 'controllers/ApiController.php';
    (new ApiController()) ->enderecos_cliente();
    exit;
}

if ($rota == 'cliente/autenticar') {
    require 'controllers/ClienteAreaController.php';
    (new ClienteAreaController())->autenticar();
    exit;
}

if ($rota == 'cliente/salvar_endereco') {
    require 'controllers/ClienteAreaController.php';
    (new ClienteAreaController())->salvar_endereco();
    exit;
}

if ($rota == 'cliente/excluir_endereco') {
    require 'controllers/ClienteAreaController.php';
    (new ClienteAreaController())->excluir_endereco();
    exit;
}
if ($rota == 'cliente/painel') {
    require 'controllers/ClienteAreaController.php';
    (new ClienteAreaController())->painel();
    exit;
}
if ($rota == 'cliente/sair') {
    require 'controllers/ClienteAreaController.php';
    (new ClienteAreaController())->sair();
    exit;
}

if ($rota == 'cliente/dados') {
    require 'controllers/ClienteAreaController.php';
    (new ClienteAreaController())->meusDados();
    exit;
}

if ($rota == 'cliente/salvar_dados') {
    require 'controllers/ClienteAreaController.php';
    (new ClienteAreaController())->salvarDados();
    exit;
}

if ($rota == 'cliente/processar_pagamento') {
    require 'controllers/ClienteAreaController.php';
    (new ClienteAreaController())->processarPagamento();
    exit;
}

if ($rota == 'api/itens_venda') {
    require 'controllers/ApiController.php';
    (new ApiController())->itensVenda();
    exit;
}

if ($rota == 'api/usuarios') {
    require 'controllers/ApiController.php';
    (new ApiController())->index();
    exit;
}

if ($rota == 'usuarios/editar') {
    require 'controllers/ApiController.php';
    (new ApiController())->editar();
    exit;
}

if ($rota == 'api/salvar_edicao') {
    require 'controllers/ApiController.php';
    (new ApiController())->salvar_edicao();
    exit;
}

if ($rota == 'api/excluir') {
    require 'controllers/ApiController.php';
    (new ApiController())->excluir();
    exit;
}

if ($rota == 'usuarios/novo') {
    require 'controllers/ApiController.php';
    (new ApiController())->novo();
    exit;
}

if ($rota == 'api/salvar') {
    require 'controllers/ApiController.php';
    (new ApiController())->salvar();
    exit;
}

if ($rota == 'admin/imprimir_pedido') {
    require 'controllers/VendaController.php';
    (new VendaController())->imprimir_pedido();
    exit;
}

// index.php

if ($rota == 'cliente/pedido_confirmado') {
    require_once 'controllers/ClienteAreaController.php';
    (new ClienteAreaController())->telaPedidoConfirmado();
    exit;
}

// --- ROTAS DO CARRINHO (IMPORTANTE: FICAM ANTES DO CHECKOUT ANTIGO) ---

if ($rota == 'carrinho/salvar') {
    require_once 'controllers/ClienteAreaController.php';
    $controller = new ClienteAreaController();
    $controller->salvarSessaoCarrinho();
    exit;
}

// ROTA ESPECÍFICA: CHECKOUT DO CARRINHO (NOVO)
// Deve vir antes do checkout genérico abaixo
if ($rota == 'cliente/checkout_carrinho') {
    require_once 'controllers/ClienteAreaController.php';
    $controller = new ClienteAreaController();
    $controller->telaCheckoutCarrinho();
    exit;
}

// ROTA PARA PROCESSAR O PAGAMENTO DO CARRINHO
if ($rota == 'carrinho/processar_pagamento') {
    require_once 'controllers/ClienteAreaController.php';
    $controller = new ClienteAreaController();
    $controller->processarPedidoCompleto();
    exit;
}

// --- CHECKOUT GENÉRICO (PAGAR PEDIDOS ANTIGOS) ---
// Usa strpos, então ele pega qualquer coisa que comece com cliente/checkout
// Por isso colocamos as rotas específicas acima dele
if (strpos($rota, 'cliente/checkout') === 0) {
    if (!isset($_GET['id'])) {
        $_GET['id'] = 0;
    }
    require 'controllers/ClienteAreaController.php';
    (new ClienteAreaController())->checkout();
    exit;
}

// ==================================================
// 3. SEGURANÇA GERAL (ÁREA RESTRITA/ADMIN)
// ==================================================
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . 'vitrine');
    exit;
}

// --- ROTAS PROTEGIDAS ---
switch ($rota) {

    case 'vitrine':
        require 'controllers/ProdutoController.php';
        (new ProdutoController())->vitrine();
        break;

    case 'home':
        require 'controllers/HomeController.php';
        (new HomeController())->index();
        break;

    case 'admin':
        require 'controllers/LoginController.php';
        (new LoginController())->painelAdmin();
        break;

    case 'master':
        require 'controllers/LoginController.php';
        (new LoginController())->painelMaster();
        break;

    case 'api/buscar':
        require 'controllers/ApiController.php';
        (new ApiController())->buscarProduto();
        break;

    case 'api/finalizar':
        require 'controllers/ApiController.php';
        (new ApiController())->finalizarVenda();
        break;

    case 'cadastro':
        Auth::verificar(['admin', 'master']);
        require 'controllers/ProdutoController.php';
        (new ProdutoController())->index();
        break;

    case 'salvar_produto':
        Auth::verificar(['admin', 'master']);
        require 'controllers/ProdutoController.php';
        (new ProdutoController())->salvar();
        break;

    // --- GESTÃO DE PRODUTOS ---
    case 'produtos':
        require 'controllers/ProdutoController.php';
        (new ProdutoController())->index();
        break;

    case 'produto/novo':
        require 'controllers/ProdutoController.php';
        (new ProdutoController())->criar();
        break;

    case 'produto/salvar':
        require 'controllers/ProdutoController.php';
        (new ProdutoController())->salvar();
        break;

    case 'produto/editar':
        require 'controllers/ProdutoController.php';
        (new ProdutoController())->editar();
        break;

    case 'produto/excluir':
        require 'controllers/ProdutoController.php';
        (new ProdutoController())->excluir();
        break;

    case 'api/pesquisar':
        require 'controllers/ApiController.php';
        (new ApiController())->pesquisarProdutos();
        break;

    case 'api/pesquisar_cliente':
        require 'controllers/ApiController.php';
        (new ApiController())->pesquisarCliente();
        break;

    // --- CLIENTES ---
    case 'clientes':
        require 'controllers/ClienteController.php';
        (new ClienteController())->index();
        break;
    case 'cliente/novo':
        require 'controllers/ClienteController.php';
        (new ClienteController())->criar();
        break;
    case 'cliente/editar':
        require 'controllers/ClienteController.php';
        (new ClienteController())->editar();
        break;
    case 'cliente/salvar':
        require 'controllers/ClienteController.php';
        (new ClienteController())->salvar();
        break;
    case 'cliente/excluir':
        require 'controllers/ClienteController.php';
        (new ClienteController())->excluir();
        break;

    // --- VENDAS / RELATÓRIOS ---
    case 'vendas':
        require 'controllers/VendaController.php';
        (new VendaController())->index();
        break;

    case 'api/detalhes_admin':
        require 'controllers/ApiController.php';
        (new ApiController())->detalhesVendaAdmin();
        break;

    case 'relatorios':
        require 'views/relatorios/index.php';
        break;

    case 'api/relatorio_dados':
        require 'controllers/ApiController.php';
        (new ApiController())->dadosRelatorio();
        break;

    case 'entregas':
        require_once 'controllers/ApiController.php';
        $controller = new ApiController();
        $controller->entregas();
        break;

    case 'admin/confirmar_entrega':
        require_once 'controllers/ApiController.php';
        (new ApiController())->confirmarEntrega();
        break;

    // --- CATEGORIAS ---
    case 'categorias':
        require 'controllers/CategoriaController.php';
        (new CategoriaController())->index();
        break;

    case 'categoria/nova':
        require 'controllers/CategoriaController.php';
        (new CategoriaController())->criar();
        break;

    case 'categoria/editar':
        require 'controllers/CategoriaController.php';
        (new CategoriaController())->editar();
        break;

    case 'categoria/salvar':
        require 'controllers/CategoriaController.php';
        (new CategoriaController())->salvar();
        break;

    case 'categoria/excluir':
        require 'controllers/CategoriaController.php';
        (new CategoriaController())->excluir();
        break;

    // --- CONFIGURAÇÃO ---
    case 'configuracao':
        require 'controllers/ConfigController.php';
        (new ConfigController())->index();
        break;
    case 'configuracao/salvar':
        require 'controllers/ConfigController.php';
        (new ConfigController())->salvar();
        break;

    default:
        echo "Página não encontrada";
        break;
}