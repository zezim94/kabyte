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

                foreach ($links as $link) {
                    $ativo = (strpos($uri, $link['rota']) !== false) ? 'active' : '';
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
                    <a href="<?= BASE_URL ?>index.php?rota=login/logout" class="btn-logout" title="Sair">
                        Sair <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                    </a>
                </div>
            </nav>

            <!-- Área do usuário (desktop) -->
            <div class="user-area-desktop">
                <span class="user-greeting">Olá, <strong><?= htmlspecialchars($primeiroNome, ENT_QUOTES, 'UTF-8') ?></strong></span>
                <a href="<?= BASE_URL ?>index.php?rota=login/logout" class="btn-logout" title="Sair" aria-label="Sair do sistema">
                    <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </header>

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