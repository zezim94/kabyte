<?php
// Lógica PHP para recuperar o nome da sessão
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$nome = $_SESSION['nome'] ?? 'Admin';
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="icon"
    href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🚲</text></svg>">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- adm -->
<link rel="stylesheet" href="<?= BASE_URL ?>public/css/adm_header.css">

<script>
    const BASE_URL = "<?= BASE_URL ?>";
</script>

<nav class="admin-header">
    <div class="header-container">
        <div class="logo-area">
            <i class="fas fa-cogs"></i>
            <span>Gestão <strong>Kabyte</strong></span>
        </div>

        <div class="menu-toggle" id="mobile-menu">
            <i class="fas fa-bars"></i>
        </div>

        <div class="menu-area" id="nav-list">
            <a href="<?= BASE_URL ?>admin" class="menu-item">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="<?= BASE_URL ?>api/entregas" class="menu-item">
                <i class="fas fa-motorcycle"></i> Entregas
            </a>
            <a href="<?= BASE_URL ?>admin/usuarios" class="menu-item">
                <i class="fas fa-users"></i> Usuários
            </a>
            <a href="<?= BASE_URL ?>cliente" class="menu-item">
                <i class="fas fa-users"></i> Clientes
            </a>
            <a href="<?= BASE_URL ?>venda" class="menu-item">
                <i class="fas fa-receipt"></i> Vendas
            </a>
            <a href="<?= BASE_URL ?>relatorio" class="menu-item">
                <i class="fas fa-chart-line"></i> Relatórios
            </a>
            <a href="<?= BASE_URL ?>produto" class="menu-item">
                <i class="fas fa-boxes"></i> Estoque
            </a>
            <a href="<?= BASE_URL ?>categoria" class="menu-item">
                <i class="fas fa-boxes"></i> Categorias
            </a>
            <a href="<?= BASE_URL ?>admin/config" class="menu-item">
                <i class="fas fa-cog"></i> Configurar
            </a>

            <a href="<?= BASE_URL ?>chave" class="menu-item">
                <i class="fas fa-key"></i> Chaves
            </a>

            <a href="<?= BASE_URL ?>home" class="menu-item btn-pdv">
                <i class="fas fa-cash-register"></i> Caixa
            </a>


            <div class="user-area-mobile">
                <span>Olá, <?= explode(' ', $nome)[0] ?></span>
                <!-- LINK CORRIGIDO -->
                <a href="<?= BASE_URL ?>index.php?rota=login/logout" class="btn-logout" title="Sair">Sair <i class="fas fa-sign-out-alt"></i></a>
            </div>
        </div>

        <div class="user-area-desktop">
            <span>Olá, <strong><?= explode(' ', $nome)[0] ?></strong></span>
            <!-- LINK CORRIGIDO -->
            <a href="<?= BASE_URL ?>index.php?rota=login/logout" class="btn-logout" title="Sair"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </div>
    </div>
</nav>

<script>
    document.getElementById('mobile-menu').addEventListener('click', function() {
        document.getElementById('nav-list').classList.toggle('active');
    });
</script>