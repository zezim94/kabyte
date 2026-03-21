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
                <?= $config['cor_fundo'] ?? '#f4f7f6' ?>
            ;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
            /* Evita barra de rolagem horizontal no site todo */
        }

        /* =========================================
           ESTRUTURA DO HEADER (100% RESPONSIVO)
           ========================================= */
        .vitrine-header {
            background-color:
                <?= $config['cor_header'] ?? '#2c3e50' ?>
            ;
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            gap: 15px;
            /* Espaço seguro entre os blocos */

        }

        /* Área 1: Logo (Esquerda) */
        .header-left {
            flex: 0 0 auto;
            /* Permite crescer e encolher */
            min-width: 0;
            /* MUITO IMPORTANTE: Impede que a logo force o layout a quebrar a tela */
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

        /* Área 2: Busca (Centro) */
        .header-center {
            flex: 1;
            /* Ocupa apenas o tamanho do conteúdo */
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Área 3: Botões e Menu (Direita) */
        .header-right {
            flex: 0 0 auto;
            /* Ocupa apenas o tamanho do conteúdo */
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
            /* Tamanho no Desktop */
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
           CORREÇÃO DE CAMADAS (MENU MOBILE CLICÁVEL)
           ========================================= */
        .vitrine-header {
            /* Mantém o cabeçalho SEMPRE acima da película */
            z-index: 1000 !important;
        }

        .nav-overlay {
            /* Película escura fica abaixo do cabeçalho, mas acima dos produtos */
            z-index: 990 !important;
        }

        .header-nav {
            /* O Menu em si fica no topo de tudo */
            z-index: 1005 !important;
        }

        .hamburger {
            /* O botão "X" fica acima do menu para sempre ser clicável */
            z-index: 1006 !important;
        }


        /* =========================================
           MEDIA QUERIES (TABLETS E CELULARES)
           ========================================= */
        @media (max-width: 850px) {
            .vitrine-header {
                padding: 10px 15px;
                gap: 10px;
            }

            .header-right {
                gap: 10px;
                /* Reduz espaço entre carrinho e menu */
            }

            .vitrine-logo img {
                max-height: 50px;
                /* Diminui logo no tablet/celular */
            }

            /* Força sobrescrever margens do CSS externo no mobile */
            .btn-header-cart {
                margin-right: 0 !important;
                padding: 8px 10px;
            }

            .header-search-form:hover .header-search-input,
            .header-search-form:focus-within .header-search-input {
                width: 140px;
                /* Barra de pesquisa menor para caber na tela */
            }

            .header-nav {
                background-color:
                    <?= $config['cor_header'] ?? '#2c3e50' ?>
                ;
            }
        }

        /* Celulares muito pequenos (iPhone SE, etc) */
        @media (max-width: 400px) {
            .vitrine-logo img {
                max-height: 40px;
            }

            .header-search-form:hover .header-search-input,
            .header-search-form:focus-within .header-search-input {
                width: 110px;
                /* Barra super compacta */
            }

            .header-right {
                gap: 5px;
                /* Deixa ícones mais juntos */
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
                        style="margin-left:8px; font-size:1.2rem;"><?= htmlspecialchars($config['nome_loja']) ?></span>
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
                <button onclick="irParaCheckout()" class="btn-header-cart" title="Ir para Pagamento">
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
                    <a href="<?= BASE_URL ?>cliente/sair" class="btn-nav btn-logout"><i class="fas fa-sign-out-alt"></i>
                        Sair</a>
                <?php endif; ?>
            </nav>
        </div>

    </header>

    <script>
        function toggleMenu() {
            const hamburger = document.querySelector(".hamburger");
            const navMenu = document.querySelector(".header-nav");
            const overlay = document.querySelector(".nav-overlay");
            hamburger.classList.toggle("active");
            navMenu.classList.toggle("active");
            overlay.classList.toggle("active");
        }
    </script>
</body>

</html>