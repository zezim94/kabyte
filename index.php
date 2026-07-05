<?php
// index.php

require_once 'config/Config.php';
require_once 'config/Database.php';
require_once 'models/Auth.php';

Auth::iniciarSessao();

// 1. Pega e limpa a rota
$url = $_GET['rota'] ?? 'vitrine';
$url = rtrim($url, '/');
$partes = explode('/', $url);

// 2. Extrai ID se houver (lógica para rotas estilo cliente/checkout/10)
if (count($partes) > 1 && is_numeric(end($partes))) {
    $_GET['id'] = end($partes);
    array_pop($partes);
}
// Reconstrói a rota limpa (ex: cliente/checkout)
$rota = implode('/', $partes);

// ==================================================
// 3. MAPA DE ROTAS (O SEU NOVO SISTEMA AUTOMÁTICO)
// ==================================================
// Formato: 'rota_url' => ['NomeDoController', 'nome_da_funcao', Requer_Login_Admin(true/false)]

$mapaRotas = [
    // --- PÚBLICAS GERAIS ---
    'vitrine'                       => ['ProdutoController', 'vitrine', false],
    'sobre'                         => ['ProdutoController', 'sobre', false],
    'produto/detalhes'              => ['ProdutoController', 'detalhes', false],
    
    // --- LOGIN E CADASTRO ---
    'login'                         => ['LoginController', 'index', false],
    'autenticar'                    => ['LoginController', 'entrar', false],
    'logout'                        => ['LoginController', 'logout', false],
    'login/google_callback'         => ['LoginController', 'google_callback', false],
    
    // --- ÁREA DO CLIENTE (Pública / Sem restrição do Admin) ---
    'cliente/cadastro'              => ['ClienteController', 'cadastro', false],
    'cliente/processar_cadastro'    => ['ClienteController', 'processar_cadastro', false],
    'cliente/esqueci_senha'         => ['ClienteController', 'esqueci_senha', false],
    'cliente/processar_recuperacao' => ['ClienteController', 'processar_recuperacao', false],
    'cliente/login'                 => ['ClienteAreaController', 'login', false],
    'cliente/autenticar'            => ['ClienteAreaController', 'autenticar', false],
    'cliente/google_callback'       => ['ClienteAreaController', 'google_callback', false],
    'cliente/sair'                  => ['ClienteAreaController', 'sair', false],
    'cliente/painel'                => ['ClienteAreaController', 'painel', false],
    'cliente/dados'                 => ['ClienteAreaController', 'meusDados', false],
    'cliente/salvarDados'           => ['ClienteAreaController', 'salvarDados', false],
    'cliente/salvar_endereco'       => ['ClienteAreaController', 'salvar_endereco', false],
    'cliente/excluir_endereco'      => ['ClienteAreaController', 'excluir_endereco', false],
    'cliente/tornar_padrao'         => ['ClienteAreaController', 'tornar_padrao', false],
    'cliente/atualizar_senha'       => ['ClienteAreaController', 'atualizar_senha', false],
    'cliente/processar_pagamento'   => ['ClienteAreaController', 'processarPagamento', false],
    'cliente/pedido_confirmado'     => ['ClienteAreaController', 'telaPedidoConfirmado', false],
    
    // --- CHECKOUT E CARRINHO ---
    'cliente/checkout'              => ['ClienteAreaController', 'checkout', false],
    'cliente/checkout_carrinho'     => ['ClienteAreaController', 'telaCheckoutCarrinho', false],
    'carrinho/salvar'               => ['ClienteAreaController', 'salvarSessaoCarrinho', false],
    'carrinho/processar_pagamento'  => ['ClienteAreaController', 'processarPedidoCompleto', false],
    
    // --- APIS PÚBLICAS ---
    'api/chatbot'                   => ['ApiController', 'chatIa', false],
    'api/enderecos'                 => ['VendaController', 'apiEnderecos', false],
    'api/enderecos_cliente'         => ['ApiController', 'enderecos_cliente', false],
    'api/itens_venda'               => ['ApiController', 'itensVenda', false],

    // ==================================================
    // ÁREA RESTRITA (Requer Login de Administrador)
    // ==================================================
    'home'                          => ['HomeController', 'index', true],
    'admin'                         => ['LoginController', 'painelAdmin', true],
    'master'                        => ['LoginController', 'painelMaster', true],
    'admin/chaves'                  => ['ChaveController', 'index', true],
    'admin/chaves/salvar'           => ['ChaveController', 'salvar', true],
    'admin/imprimir_pedido'         => ['VendaController', 'imprimir_pedido', true],
    'admin/confirmar_entrega'       => ['ApiController', 'confirmarEntrega', true],
    'entregas'                      => ['ApiController', 'entregas', true],
    
    // Produtos
    'produtos'                      => ['ProdutoController', 'index', true],
    'cadastro'                      => ['ProdutoController', 'index', true], // Alias
    'produto/novo'                  => ['ProdutoController', 'criar', true],
    'produto/salvar'                => ['ProdutoController', 'salvar', true],
    'salvar_produto'                => ['ProdutoController', 'salvar', true], // Alias
    'produto/editar'                => ['ProdutoController', 'editar', true],
    'produto/excluir'               => ['ProdutoController', 'excluir', true],
    
    // Clientes Administrativo
    'clientes'                      => ['ClienteController', 'index', true],
    'cliente/novo'                  => ['ClienteController', 'criar', true],
    'cliente/editar'                => ['ClienteController', 'editar', true],
    'cliente/salvar'                => ['ClienteController', 'salvar', true],
    'cliente/excluir'               => ['ClienteController', 'excluir', true],

    // Categorias
    'categorias'                    => ['CategoriaController', 'index', true],
    'categoria/nova'                => ['CategoriaController', 'criar', true],
    'categoria/editar'              => ['CategoriaController', 'editar', true],
    'categoria/salvar'              => ['CategoriaController', 'salvar', true],
    'categoria/excluir'             => ['CategoriaController', 'excluir', true],
    
    // Vendas e Relatórios
    'vendas'                        => ['VendaController', 'index', true],
    'configuracao'                  => ['ConfigController', 'index', true],
    'configuracao/salvar'           => ['ConfigController', 'salvar', true],
    
    // View Direta de Relatórios
    'relatorios'                    => ['VIEW', 'views/relatorios/index.php', true],
    
    // APIs Administrativas
    'api/buscar'                    => ['ApiController', 'buscarProduto', true],
    'api/finalizar'                 => ['ApiController', 'finalizarVenda', true],
    'api/pesquisar'                 => ['ApiController', 'pesquisarProdutos', true],
    'api/pesquisar_cliente'         => ['ApiController', 'pesquisarCliente', true],
    'api/detalhes_admin'            => ['ApiController', 'detalhesVendaAdmin', true],
    'api/relatorio_dados'           => ['ApiController', 'dadosRelatorio', true],
    'api/usuarios'                  => ['ApiController', 'index', true],
    'usuarios/editar'               => ['ApiController', 'editar', true],
    'api/salvar_edicao'             => ['ApiController', 'salvar_edicao', true],
    'api/excluir'                   => ['ApiController', 'excluir', true],
    'usuarios/novo'                 => ['ApiController', 'novo', true],
    'api/salvar'                    => ['ApiController', 'salvar', true],
];

// ==================================================
// 4. MOTOR DE EXECUÇÃO (Faz a mágica acontecer)
// ==================================================

if (array_key_exists($rota, $mapaRotas)) {
    
    // Extrai as configurações da rota escolhida
    list($controllerName, $metodo, $requerAuthAdmin) = $mapaRotas[$rota];

    // Verifica Segurança: Se for rota restrita, o admin deve estar logado
    if ($requerAuthAdmin && !isset($_SESSION['usuario_id'])) {
        header('Location: ' . BASE_URL . 'vitrine');
        exit;
    }

    // Caso Especial: Se a rota for apenas um arquivo View (ex: relatórios)
    if ($controllerName === 'VIEW') {
        require $metodo;
        exit;
    }

    // Puxa o arquivo do Controller dinamicamente
    $arquivoController = "controllers/{$controllerName}.php";
    
    if (file_exists($arquivoController)) {
        require_once $arquivoController;
        $instancia = new $controllerName();
        
        // Executa a função mapeada
        if (method_exists($instancia, $metodo)) {
            $instancia->$metodo();
            exit;
        } else {
            die("<b>Erro de Rota:</b> A função <code>{$metodo}()</code> não existe dentro de <code>{$controllerName}</code>.");
        }
    } else {
        die("<b>Erro de Rota:</b> O arquivo <code>{$arquivoController}</code> não foi encontrado.");
    }

} else {
    // Tela padrão de erro 404 se a URL não existir na lista
    http_response_code(404);
    echo "<div style='font-family:sans-serif; text-align:center; padding: 50px;'>";
    echo "<h1>404 - Página não encontrada</h1>";
    echo "<p>A rota <b>'{$rota}'</b> não existe no sistema.</p>";
    echo "<a href='" . BASE_URL . "vitrine'>Voltar para a Loja</a>";
    echo "</div>";
    exit;
}