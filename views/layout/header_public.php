<?php
// Se a configuração não foi carregada no controller, carrega aqui
if (!isset($config)) {
    require_once __DIR__ . '/../../models/Configuracao.php';
    $config = Configuracao::get();
}

// LÓGICA: Verifica se estamos na página da VITRINE para exibir o carrinho
$rotaAtual = $_GET['rota'] ?? '';
$ehPaginaVitrine = ($rotaAtual == 'vitrine');
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo - <?= htmlspecialchars($config['nome_loja'] ?? 'Loja') ?></title>

    <link rel="icon" href="<?= BASE_URL ?>public/uploads/logo-removebg-preview.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/header_public.css">
    <meta name="google-site-verification" content="cewLTqSJ-DR4m_I07HHno2zznAe6nHe1H74SZIBWouQ" />

    <style>
        /* =========================================
           RESETS E TRAVAS GERAIS
           ========================================= */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            background-color:
                <?= $config['cor_fundo'] ?? '#f4f7f6' ?>;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        /* =========================================
           ESTRUTURA DO HEADER (100% RESPONSIVO)
           ========================================= */
        .vitrine-header {
            background-color:
                <?= $config['cor_header'] ?? '#2c3e50' ?>;
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            gap: 15px;
            z-index: 1000 !important;
        }

        .header-left {
            flex: 0 0 auto;
            min-width: 0;
            display: flex;
            align-items: center;
        }

        .vitrine-logo {
            max-width: 100%;
            display: flex;
            align-items: center;
        }

        .vitrine-logo img {
            max-height: 70px;
            max-width: 100%;
            object-fit: contain;
            transition: max-height 0.3s ease;
        }

        .header-center {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .header-right {
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 15px;
        }

        /* =========================================
           BARRA DE PESQUISA ANIMADA
           ========================================= */
        .header-search-form {
            display: flex;
            align-items: center;
            background-color: transparent;
            border-radius: 25px;
            transition: background-color 0.4s ease;
        }

        .header-search-form:hover,
        .header-search-form:focus-within {
            background-color: rgba(255, 255, 255, 0.15);
        }

        .header-search-input {
            width: 0;
            padding: 0;
            border: none;
            background: transparent;
            color: #fff;
            outline: none;
            font-size: 0.95rem;
            transition: width 0.4s ease, padding 0.4s ease;
        }

        .header-search-input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .header-search-form:hover .header-search-input,
        .header-search-form:focus-within .header-search-input {
            width: 280px;
            padding: 8px 15px;
        }

        .header-search-btn {
            background: transparent;
            border: none;
            color: #fff;
            font-size: 1.1rem;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 50%;
            transition: transform 0.3s;
        }

        .header-search-form:hover .header-search-btn {
            transform: scale(1.1);
        }

        /* =========================================
           CORREÇÃO DE CAMADAS
           ========================================= */
        .nav-overlay {
            z-index: 990 !important;
        }

        .header-nav {
            z-index: 1005 !important;
        }

        .hamburger {
            z-index: 1006 !important;
        }

        /* =========================================
           MODAL DO CARRINHO (ESTILO PREMIUM)
           ========================================= */
        .cart-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(3px);
            /* Desfoque de fundo elegante */
            z-index: 2000;
            display: none;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .cart-modal-overlay.active {
            display: flex;
            opacity: 1;
        }

        .cart-modal-box {
            background: #f8f9fa;
            /* Fundo cinza bem clarinho */
            width: 90%;
            max-width: 450px;
            border-radius: 16px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            max-height: 85vh;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
            transform: translateY(20px);
            transition: transform 0.3s ease;
        }

        .cart-modal-overlay.active .cart-modal-box {
            transform: translateY(0);
        }

        .cart-modal-header {
            padding: 18px 20px;
            background: <?= $config['cor_header'] ?? '#2c3e50' ?>;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cart-modal-header h3 {
            margin: 0;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
        }

        .btn-close-modal {
            background: transparent;
            border: none;
            color: white;
            font-size: 1.8rem;
            cursor: pointer;
            transition: transform 0.2s;
            line-height: 1;
        }

        .btn-close-modal:hover {
            transform: scale(1.1);
        }

        .cart-modal-body {
            padding: 20px;
            overflow-y: auto;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        /* Card individual do produto dentro do carrinho */
        .cart-modal-item {
            display: flex;
            align-items: center;
            gap: 15px;
            background: white;
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            border: 1px solid #f0f0f0;
            transition: transform 0.2s ease;
        }

        .cart-modal-item:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .cart-modal-item img {
            width: 65px;
            height: 65px;
            object-fit: contain;
            border-radius: 8px;
        }

        .cart-modal-item-info {
            flex: 1;
        }

        .cart-modal-item-info h4 {
            margin: 0 0 4px 0;
            font-size: 0.95rem;
            color: #2c3e50;
            line-height: 1.3;
        }

        .cart-modal-item-price {
            font-size: 0.85rem;
            color: #7f8c8d;
        }

        .cart-modal-item-price strong {
            color: #27ae60;
            font-size: 1rem;
            display: block;
            margin-top: 3px;
        }

        .cart-modal-item-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 4px;
            border: 1px solid #eee;
        }

        .cart-modal-btn-qty {
            background: white;
            border: 1px solid #ddd;
            color: #333;
            width: 28px;
            height: 28px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
        }

        .cart-modal-btn-qty:hover {
            background: #3498db;
            color: white;
            border-color: #3498db;
        }

        .btn-trash {
            background: transparent;
            color: #e74c3c;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 6px;
            cursor: pointer;
            margin-left: 5px;
            transition: 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-trash:hover {
            background: #ffeaea;
        }

        .cart-modal-footer {
            padding: 20px;
            border-top: 1px solid #eee;
            background: white;
        }

        .cart-modal-total {
            font-size: 1.3rem;
            font-weight: 800;
            color: #2c3e50;
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .cart-modal-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-clear-cart {
            flex: 1;
            padding: 12px;
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            color: #e74c3c;
            transition: 0.2s;
        }

        .btn-clear-cart:hover:not(:disabled) {
            background: #fff0f0;
            border-color: #e74c3c;
        }

        .btn-checkout-modal {
            flex: 2;
            padding: 12px;
            background: #27ae60;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            color: white;
            font-size: 1.05rem;
            transition: 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 10px rgba(39, 174, 96, 0.3);
        }

        .btn-checkout-modal:hover:not(:disabled) {
            background: #219150;
            transform: translateY(-2px);
        }

        .btn-checkout-modal:disabled,
        .btn-clear-cart:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* =========================================
           MODAL DE CONFIRMAÇÃO (CUSTOMIZADO)
           ========================================= */
        .confirm-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 3000;
            /* Fica ACIMA do modal do carrinho */
            display: none;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .confirm-modal-overlay.active {
            display: flex;
            opacity: 1;
        }

        .confirm-modal-box {
            background: white;
            padding: 30px;
            border-radius: 16px;
            text-align: center;
            width: 90%;
            max-width: 350px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transform: scale(0.9);
            transition: transform 0.2s ease;
        }

        .confirm-modal-overlay.active .confirm-modal-box {
            transform: scale(1);
        }

        .confirm-icon {
            font-size: 3rem;
            color: #e74c3c;
            margin-bottom: 15px;
        }

        .confirm-modal-box h3 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 1.3rem;
        }

        .confirm-modal-box p {
            color: #7f8c8d;
            margin: 0 0 25px 0;
            font-size: 0.95rem;
            line-height: 1.4;
        }

        .confirm-buttons {
            display: flex;
            gap: 10px;
        }

        .confirm-btn-cancel {
            flex: 1;
            padding: 10px;
            background: #ecf0f1;
            color: #34495e;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
        }

        .confirm-btn-cancel:hover {
            background: #bdc3c7;
        }

        .confirm-btn-danger {
            flex: 1;
            padding: 10px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
        }

        .confirm-btn-danger:hover {
            background: #c0392b;
        }

        /* =========================================
           MEDIA QUERIES
           ========================================= */
        @media (max-width: 850px) {
            .vitrine-header {
                padding: 10px 15px;
                gap: 10px;
            }

            .header-right {
                gap: 10px;
            }

            .vitrine-logo img {
                max-height: 50px;
            }

            .btn-header-cart {
                padding: 8px 10px;
            }

            .header-search-form:hover .header-search-input,
            .header-search-form:focus-within .header-search-input {
                width: 140px;
            }

            .header-nav {
                background-color: <?= $config['cor_header'] ?? '#2c3e50' ?>;
            }
        }

        @media (max-width: 450px) {
            .vitrine-header {
                padding: 10px 5px;
                gap: 5px;
            }

            .vitrine-logo img {
                max-height: 35px;
            }

            .vitrine-logo span {
                font-size: 1rem !important;
            }

            .header-search-form:hover .header-search-input,
            .header-search-form:focus-within .header-search-input {
                width: 110px;
                padding: 8px 10px;
            }

            .header-right {
                gap: 2px;
            }

            .cart-modal-buttons {
                flex-direction: column;
            }

            /* --- O SEGREDO DO CARRINHO NO CELULAR --- */
            .cart-modal-item {
                flex-wrap: wrap;
                /* Joga os botões para a linha de baixo se a tela for estreita */
                padding: 12px;
            }

            .cart-modal-item-info {
                min-width: 150px;
                /* Protege o texto de ser esmagado */
            }

            .cart-modal-item-actions {
                width: 100%;
                /* Força os botões a ocuparem uma linha inteira embaixo */
                justify-content: space-between;
                /* Joga o +/- para a esquerda e a lixeira para a direita */
                background: transparent;
                border: none;
                padding: 5px 0 0 0;
            }

            .cart-modal-btn-qty {
                width: 32px;
                height: 32px;
            }

            .btn-trash {
                width: 35px;
                height: 35px;
                margin-left: 0;
            }
        }

        /* =========================================
           AJUSTE EXTREMO PARA TELAS DE 320px (iPhone SE, Fold)
           ========================================= */
        @media (max-width: 340px) {

            .header-search-form:hover .header-search-input,
            .header-search-form:focus-within .header-search-input {
                width: 70px;
            }

            .vitrine-logo span {
                display: none !important;
            }

            /* Compacta ainda mais o carrinho em 320px */
            .cart-modal-item-actions {
                justify-content: flex-start;
                /* Agrupa todos os botões juntos */
                gap: 15px;
            }

            .cart-modal-total {
                font-size: 1.1rem;
            }
        }
    </style>
</head>

<body>

    <div class="nav-overlay" onclick="toggleMenu()"></div>

    <header class="vitrine-header">

        <div class="header-left">
            <div class="vitrine-logo">
                <?php
                $caminhoBanco = $config['logo_loja'] ?? '';
                $caminhoLimpo = explode('?', $caminhoBanco)[0];
                $caminhoFisico = __DIR__ . '/../../' . $caminhoLimpo;
                ?>

                <?php if (!empty($caminhoLimpo) && file_exists($caminhoFisico)): ?>
                    <img src="<?= BASE_URL . $caminhoBanco ?>" alt="<?= htmlspecialchars($config['nome_loja']) ?>">
                <?php else: ?>
                    <i class="fas fa-bicycle"></i> <span
                        style="margin-left:8px; font-size:1.2rem; color:white;"><?= htmlspecialchars($config['nome_loja']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="header-center">
            <form action="index.php" method="GET" class="header-search-form">
                <input type="hidden" name="rota" value="vitrine">
                <input type="text" name="busca" class="header-search-input" placeholder="O que procura?"
                    value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>">
                <button type="submit" class="header-search-btn" title="Buscar">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        <div class="header-right">
            <?php if ($ehPaginaVitrine): ?>
                <button onclick="abrirModalCarrinho()" class="btn-header-cart" title="Ver Carrinho">
                    <i class="fas fa-shopping-basket"></i>
                    <span id="headerCartCount" style="margin-left: 4px;"></span>
                </button>
            <?php endif; ?>

            <button class="hamburger" onclick="toggleMenu()">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>

            <nav class="header-nav">
                <?php if (isset($_SESSION['cliente_id'])): ?>
                    <a href="<?= BASE_URL ?>cliente/painel" class="user-link">
                        <i class="fas fa-user"></i>
                        <span>Olá, <?= htmlspecialchars(explode(' ', $_SESSION['cliente_nome'])[0]) ?></span>
                    </a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>cliente/login" class="user-link"><i class="fas fa-user-circle"></i> Login</a>
                <?php endif; ?>

                <a href="<?= BASE_URL ?>vitrine" class="btn-nav"><i class="fas fa-store"></i> Loja</a>
                <a href="<?= BASE_URL ?>sobre" class="btn-nav"><i class="fas fa-info-circle"></i> Sobre Nós</a>

                <?php if (isset($_SESSION['cliente_id'])): ?>
                    <a href="<?= BASE_URL ?>cliente/dados" class="btn-nav"><i class="fas fa-id-card"></i> Dados</a>
                    <a href="<?= BASE_URL ?>cliente/sair" class="btn-nav btn-logout"><i class="fas fa-sign-out-alt"></i> Sair</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div id="modalCarrinho" class="cart-modal-overlay" onclick="fecharModalCarrinho(event)">
        <div class="cart-modal-box">
            <div class="cart-modal-header">
                <h3><i class="fas fa-shopping-basket"></i> Seu Pedido</h3>
                <button class="btn-close-modal" onclick="fecharModalCarrinho(event)">&times;</button>
            </div>

            <div class="cart-modal-body" id="cartModalBody">
            </div>

            <div class="cart-modal-footer">
                <div class="cart-modal-total"><span>Total:</span> <span>R$ <span id="cartModalTotal">0,00</span></span></div>
                <div class="cart-modal-buttons">
                    <button class="btn-clear-cart" onclick="verificarLimparCarrinho()"><i class="fas fa-trash-alt"></i> Esvaziar</button>
                    <button class="btn-checkout-modal" onclick="irParaCheckout()">Avançar <i class="fas fa-arrow-right"></i></button>
                </div>
            </div>
        </div>
    </div>

    <div id="confirmModal" class="confirm-modal-overlay" onclick="fecharConfirmModal(event)">
        <div class="confirm-modal-box">
            <div class="confirm-icon"><i class="fas fa-exclamation-circle"></i></div>
            <h3 id="confirmTitle">Atenção</h3>
            <p id="confirmMessage">Deseja realmente excluir?</p>
            <div class="confirm-buttons">
                <button class="confirm-btn-cancel" onclick="fecharConfirmModal()">Cancelar</button>
                <button class="confirm-btn-danger" id="btnConfirmAction" onclick="executarAcaoConfirmada()">Sim, excluir</button>
            </div>
        </div>
    </div>

    <script>
        // Menu Mobile
        function toggleMenu() {
            const hamburger = document.querySelector(".hamburger");
            const navMenu = document.querySelector(".header-nav");
            const overlay = document.querySelector(".nav-overlay");
            hamburger.classList.toggle("active");
            navMenu.classList.toggle("active");
            overlay.classList.toggle("active");
        }

        function abrirModalCarrinho() {
            renderizarModalCarrinho();
            document.getElementById('modalCarrinho').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function fecharModalCarrinho(e) {
            if (!e || e.target.classList.contains('cart-modal-overlay') || e.target.classList.contains('btn-close-modal')) {
                document.getElementById('modalCarrinho').classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        // ==========================================
        // SISTEMA DO MODAL DE CONFIRMAÇÃO
        // ==========================================
        let acaoPendente = null;

        function abrirConfirmModal(titulo, mensagem, acao) {
            document.getElementById('confirmTitle').innerText = titulo;
            document.getElementById('confirmMessage').innerText = mensagem;
            acaoPendente = acao;

            document.getElementById('confirmModal').classList.add('active');
        }

        function fecharConfirmModal(e) {
            if (!e || e.target.classList.contains('confirm-modal-overlay') || e.target.classList.contains('confirm-btn-cancel')) {
                document.getElementById('confirmModal').classList.remove('active');
                acaoPendente = null;
            }
        }

        function executarAcaoConfirmada() {
            if (acaoPendente) {
                acaoPendente(); // Executa a função salva (excluir um item ou esvaziar tudo)
            }
            fecharConfirmModal();
        }


        // ==========================================
        // RENDERIZAÇÃO DO CARRINHO
        // ==========================================
        function renderizarModalCarrinho() {
            const body = document.getElementById('cartModalBody');
            let total = 0;
            let html = '';
            let qtdItens = 0;

            let carrinhoMetaLocal = JSON.parse(localStorage.getItem('carrinho_meta')) || {};

            if (typeof carrinho !== 'undefined') {
                for (let id in carrinho) {
                    let qtd = carrinho[id];
                    if (qtd > 0 && carrinhoMetaLocal[id]) {
                        qtdItens++;
                        const meta = carrinhoMetaLocal[id];
                        const subtotal = meta.preco * qtd;
                        total += subtotal;

                        html += `
                            <div class="cart-modal-item">
                                ${meta.imagem ? `<img src="${meta.imagem}" alt="${meta.nome}">` : '<div style="width:65px;height:65px;background:#eee;border-radius:8px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-image fa-2x" style="color:#ccc;"></i></div>'}
                                
                                <div class="cart-modal-item-info">
                                    <h4>${meta.nome}</h4>
                                    <div class="cart-modal-item-price">
                                        ${qtd}x R$ ${meta.preco.toLocaleString('pt-BR', { minimumFractionDigits: 2 })} 
                                        <strong>R$ ${subtotal.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}</strong>
                                    </div>
                                </div>

                                <div class="cart-modal-item-actions">
                                    <button class="cart-modal-btn-qty" onclick="alterarQtdModal(${id}, -1)">-</button>
                                    <span style="font-weight:bold; width:20px; text-align:center; font-size: 0.95rem;">${qtd}</span>
                                    <button class="cart-modal-btn-qty" onclick="alterarQtdModal(${id}, 1)">+</button>
                                    
                                    <button class="btn-trash" onclick="verificarRemoverItem(${id})" title="Remover Produto"><i class="fas fa-trash-alt"></i></button>
                                </div>
                            </div>
                        `;
                    }
                }
            }

            const btnCheckout = document.querySelector('.btn-checkout-modal');
            const btnClear = document.querySelector('.btn-clear-cart');

            if (qtdItens === 0) {
                html = `
                    <div style="text-align:center; padding: 40px 20px; color:#999; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%;">
                        <div style="width: 80px; height: 80px; background: #eee; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                            <i class="fas fa-shopping-basket fa-3x" style="color:#ccc;"></i>
                        </div>
                        <h3 style="color: #555; margin-bottom: 5px;">Seu carrinho está vazio</h3>
                        <p style="font-size: 0.9rem;">Explore a loja e adicione produtos.</p>
                    </div>`;
                btnCheckout.disabled = true;
                btnClear.disabled = true;
            } else {
                btnCheckout.disabled = false;
                btnClear.disabled = false;
            }

            body.innerHTML = html;
            document.getElementById('cartModalTotal').innerText = total.toLocaleString('pt-BR', {
                minimumFractionDigits: 2
            });
        }

        function alterarQtdModal(id, delta) {
            if (typeof alterarQtd === 'function') {
                alterarQtd(id, delta);
                renderizarModalCarrinho();
            }
        }

        // NOVO: Chama o Modal Customizado para 1 item
        function verificarRemoverItem(id) {
            abrirConfirmModal('Remover Produto', 'Deseja retirar este item do seu pedido?', function() {
                removerItemCarrinho(id);
            });
        }

        function removerItemCarrinho(id) {
            if (typeof carrinho !== 'undefined') {
                delete carrinho[id];

                const elVitrine = document.getElementById('qtd-' + id);
                if (elVitrine) elVitrine.innerText = '0';
                const elCar = document.getElementById('qtd-car-' + id);
                if (elCar) elCar.innerText = '0';

                let carrinhoMetaLocal = JSON.parse(localStorage.getItem('carrinho_meta')) || {};
                delete carrinhoMetaLocal[id];

                localStorage.setItem('meu_carrinho_pdv', JSON.stringify(carrinho));
                localStorage.setItem('carrinho_meta', JSON.stringify(carrinhoMetaLocal));

                if (typeof atualizarBarraInferior === 'function') atualizarBarraInferior();
                renderizarModalCarrinho();
            }
        }

        // NOVO: Chama o Modal Customizado para limpar tudo
        function verificarLimparCarrinho() {
            abrirConfirmModal('Esvaziar Carrinho', 'Tem certeza que deseja remover todos os itens?', function() {
                limparCarrinhoModal();
            });
        }

        function limparCarrinhoModal() {
            if (typeof carrinho !== 'undefined') {
                for (let prop in carrinho) {
                    delete carrinho[prop];
                }
                localStorage.removeItem('meu_carrinho_pdv');
                localStorage.removeItem('carrinho_meta');

                document.querySelectorAll('.qty-display').forEach(el => el.innerText = '0');
                document.querySelectorAll('.detalhe-qty-display').forEach(el => el.innerText = '0');

                if (typeof atualizarBarraInferior === 'function') atualizarBarraInferior();
                fecharModalCarrinho(); // Fecha o carrinho depois de limpar tudo
            }
        }
    </script>