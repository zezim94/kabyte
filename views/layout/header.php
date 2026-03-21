<?php
// Garante que a sessão está ativa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$nivel = $_SESSION['nivel'] ?? 'vendedor';
$nome = $_SESSION['nome'] ?? 'Visitante';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDV - Paty Bike</title>

    <link rel="icon"
        href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🚲</text></svg>">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/style.css">

    <script>
        const BASE_URL = "<?= BASE_URL ?>";
    </script>
</head>

<body>

    <nav class="main-header">
        <div class="logo-area">
            <span>Kabyte</span>
        </div>

        <div class="menu-area">
            <a href="<?= BASE_URL ?>home" class="menu-item" title="Frente de Caixa">
                <i class="fas fa-cash-register"></i> <span class="hide-mobile">Caixa</span>
            </a>

            <?php if ($nivel == 'admin' || $nivel == 'master'): ?>
                <a href="<?= BASE_URL ?>admin" class="menu-item" title="Faturamento e Fechamento">
                    <i class="fas fa-chart-line"></i> <span class="hide-mobile">Gestão</span>
                </a>
            <?php endif; ?>

            <?php if ($nivel == 'master'): ?>
                <a href="<?= BASE_URL ?>master" class="menu-item destaque-master" title="Logs de Segurança">
                    <i class="fas fa-user-shield"></i> <span class="hide-mobile">Master</span>
                </a>
            <?php endif; ?>
        </div>

        <div class="user-area">
            <span class="user-name">
                <i class="fas fa-user-circle"></i> <?= explode(' ', $nome)[0] ?>
            </span>
            <a href="<?= BASE_URL ?>logout" class="btn-logout" title="Sair do Sistema">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </nav>

    <div class="conteudo-principal">