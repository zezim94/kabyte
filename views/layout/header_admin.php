<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$nomeCompleto = $_SESSION['nome'] ?? 'Admin';
$primeiroNome = explode(' ', trim($nomeCompleto))[0];

// Identifica a rota atual para destacar o link ativo
$rotaAtual = $_GET['rota'] ?? '';
$uri = $_SERVER['REQUEST_URI'];
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🚲</text></svg>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/adm_header.css">
    <script>
        const BASE_URL = "<?= BASE_URL ?>";
    </script>
    <style>
        /* =========================================
   ESTILOS MODERNOS DO TOAST (NOTIFICAÇÕES)
   ========================================= */
        #toast-container {
            position: fixed;
            top: 25px;
            right: 25px;
            z-index: 999999;
            display: flex;
            flex-direction: column;
            gap: 15px;
            pointer-events: none;
            /* Para não bloquear cliques atrás do container */
        }

        .toast {
            min-width: 320px;
            background-color: #ffffff;
            color: #333333;
            padding: 18px 24px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08), 0 2px 10px rgba(0, 0, 0, 0.04);
            font-family: 'Segoe UI', system-ui, sans-serif;
            font-size: 1rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 16px;
            opacity: 0;
            transform: translateX(120%);
            /* Animação elástica (bounce) super moderna */
            animation: slideInToast 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
            border-left: 6px solid;
            pointer-events: auto;
            /* Permite interagir/clicar no toast */
        }

        .toast.hide {
            animation: slideOutToast 0.4s ease forwards;
        }

        /* Cores e Ícones por tipo */
        .toast.success {
            border-left-color: #2ecc71;
        }

        .toast.success i {
            color: #2ecc71;
            font-size: 1.5rem;
        }

        .toast.error {
            border-left-color: #e74c3c;
        }

        .toast.error i {
            color: #e74c3c;
            font-size: 1.5rem;
        }

        .toast.warning {
            border-left-color: #f1c40f;
        }

        .toast.warning i {
            color: #f1c40f;
            font-size: 1.5rem;
        }

        /* Animações Desktop (Desliza da Direita) */
        @keyframes slideInToast {
            0% {
                opacity: 0;
                transform: translateX(120%);
            }

            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideOutToast {
            0% {
                opacity: 1;
                transform: translateX(0);
            }

            100% {
                opacity: 0;
                transform: translateX(120%);
            }
        }

        /* Responsividade para Ecrãs Pequenos (Desliza de Baixo) */
        @media (max-width: 600px) {
            #toast-container {
                top: auto;
                bottom: 20px;
                right: 15px;
                left: 15px;
                align-items: center;
            }

            .toast {
                min-width: 100%;
                box-sizing: border-box;
                transform: translateY(120%);
                animation: slideInToastMobile 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
            }

            .toast.hide {
                animation: slideOutToastMobile 0.4s ease forwards;
            }

            @keyframes slideInToastMobile {
                0% {
                    opacity: 0;
                    transform: translateY(120%);
                }

                100% {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes slideOutToastMobile {
                0% {
                    opacity: 1;
                    transform: translateY(0);
                }

                100% {
                    opacity: 0;
                    transform: translateY(120%);
                }
            }
        }
    </style>
</head>

<body>

    <header class="admin-header" role="banner">
        <div class="header-container">

            <!-- Botão mobile -->
            <button class="menu-toggle" id="mobile-menu" aria-label="Abrir menu" aria-expanded="false" aria-controls="nav-list">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>

            <!-- Navegação principal -->
            <nav class="menu-area" id="nav-list" role="navigation" aria-label="Menu principal">
                <?php
                $links = [
                    ['rota' => 'admin', 'url' => BASE_URL . 'admin', 'icone' => 'fa-tachometer-alt', 'texto' => 'Dashboard'],
                    ['rota' => 'api/entregas', 'url' => BASE_URL . 'api/entregas', 'icone' => 'fa-motorcycle', 'texto' => 'Entregas'],
                    ['rota' => 'admin/usuarios', 'url' => BASE_URL . 'admin/usuarios', 'icone' => 'fa-users', 'texto' => 'Usuários'],
                    ['rota' => 'cliente', 'url' => BASE_URL . 'cliente', 'icone' => 'fa-address-book', 'texto' => 'Clientes'],
                    ['rota' => 'venda', 'url' => BASE_URL . 'venda', 'icone' => 'fa-receipt', 'texto' => 'Vendas'],
                    ['rota' => 'relatorio', 'url' => BASE_URL . 'relatorio', 'icone' => 'fa-chart-line', 'texto' => 'Relatórios'],
                    ['rota' => 'produto', 'url' => BASE_URL . 'produto', 'icone' => 'fa-boxes', 'texto' => 'Estoque'],
                    ['rota' => 'categoria', 'url' => BASE_URL . 'categoria', 'icone' => 'fa-tags', 'texto' => 'Categorias'],
                    ['rota' => 'admin/config', 'url' => BASE_URL . 'admin/config', 'icone' => 'fa-cog', 'texto' => 'Configurar'],
                    ['rota' => 'chave', 'url' => BASE_URL . 'chave', 'icone' => 'fa-key', 'texto' => 'Chaves'],
                ];

                // Limpa a URL para remover variáveis (ex: ?id=1) e focar só no caminho
                $urlLimpa = parse_url($uri, PHP_URL_PATH);

                foreach ($links as $link) {
                    $ativo = '';

                    if ($link['rota'] === 'admin') {
                        // Se for o Dashboard, aplica uma regra rigorosa: a URL tem de terminar EXATAMENTE em /admin ou /admin/
                        // Ou a variável da rota deve ser exatamente 'admin' ou vazia (página inicial do painel)
                        if (preg_match('/\/admin\/?$/', $urlLimpa) || $rotaAtual === 'admin' || $rotaAtual === '') {
                            $ativo = 'active';
                        }
                    } else {
                        // Para os outros botões, mantemos a sua lógica excelente que deteta sub-páginas
                        if (strpos($uri, $link['rota']) !== false) {
                            $ativo = 'active';
                        }
                    }

                    echo "<a href=\"{$link['url']}\" class=\"menu-item $ativo\">";
                    echo "<i class=\"fas {$link['icone']}\" aria-hidden=\"true\"></i> ";
                    echo "<span>{$link['texto']}</span>";
                    echo "</a>";
                }
                ?>

                <a href="<?= BASE_URL ?>home" class="menu-item btn-pdv">
                    <i class="fas fa-cash-register" aria-hidden="true"></i> Caixa
                </a>

                <!-- Área do usuário (versão mobile) -->
                <div class="user-area-mobile">
                    <span class="user-greeting">Olá, <?= htmlspecialchars($primeiroNome, ENT_QUOTES, 'UTF-8') ?></span>
                    <a href="<?= BASE_URL ?>login/logout" class="btn-logout" title="Sair">
                        Sair <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                    </a>
                </div>
            </nav>

            <!-- Área do usuário (desktop) -->
            <div class="user-area-desktop">
                <span class="user-greeting">Olá, <strong><?= htmlspecialchars($primeiroNome, ENT_QUOTES, 'UTF-8') ?></strong></span>
                <a href="<?= BASE_URL ?>login/logout" class="btn-logout" title="Sair" aria-label="Sair do sistema">
                    <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </header>
    <div id="toast-container"></div>
    <script>
        // Se a função showToast ainda não estiver incluída neste ficheiro, aqui está ela:
        function showToast(mensagem, tipo = "success") {
            const container = document.getElementById("toast-container");
            if (!container) return;

            let icon = "";
            if (tipo === "success") icon = '<i class="fas fa-check-circle"></i>';
            if (tipo === "error") icon = '<i class="fas fa-times-circle"></i>';
            if (tipo === "warning") icon = '<i class="fas fa-exclamation-triangle"></i>';

            const toast = document.createElement("div");
            toast.className = `toast ${tipo}`;
            toast.innerHTML = `${icon} <span>${mensagem}</span>`;

            container.appendChild(toast);

            // Remove após 3.5 segundos
            setTimeout(() => {
                toast.classList.add("hide");
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        }

        // --- LEITOR DE URL PARA DISPARAR O TOAST ---
        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);

            // Verifica se a URL tem "toast" e "msg" (ex: ?toast=success&msg=Usuário+salvo)
            if (urlParams.has('toast') && urlParams.has('msg')) {
                const tipo = urlParams.get('toast');
                const msg = urlParams.get('msg');

                showToast(msg, tipo);

                // Magia: Limpa a URL silenciosamente para que o Toast não repita se o utilizador apertar F5
                window.history.replaceState(null, null, window.location.pathname);
            }
        });
    </script>

    <script>
        (function() {
            const toggleBtn = document.getElementById('mobile-menu');
            const navList = document.getElementById('nav-list');
            const body = document.body;

            if (toggleBtn && navList) {
                toggleBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isOpen = navList.classList.toggle('active');
                    toggleBtn.setAttribute('aria-expanded', isOpen);
                    // Trava o scroll da página quando menu aberto
                    body.style.overflow = isOpen ? 'hidden' : '';
                });

                // Fecha ao clicar fora do menu
                document.addEventListener('click', function(event) {
                    if (!navList.contains(event.target) && !toggleBtn.contains(event.target)) {
                        if (navList.classList.contains('active')) {
                            navList.classList.remove('active');
                            toggleBtn.setAttribute('aria-expanded', 'false');
                            body.style.overflow = '';
                        }
                    }
                });

                // Fecha ao pressionar ESC
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && navList.classList.contains('active')) {
                        navList.classList.remove('active');
                        toggleBtn.setAttribute('aria-expanded', 'false');
                        toggleBtn.focus(); // retorna foco ao botão
                        body.style.overflow = '';
                    }
                });

                // Fecha ao redimensionar para desktop (evita estado preso)
                window.addEventListener('resize', function() {
                    if (window.innerWidth > 1024 && navList.classList.contains('active')) {
                        navList.classList.remove('active');
                        toggleBtn.setAttribute('aria-expanded', 'false');
                        body.style.overflow = '';
                    }
                });
            }
        })();
    </script>