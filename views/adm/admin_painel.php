<?php
// Lógica do Dashboard (Faturamento, etc)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$nome = $_SESSION['nome'] ?? 'Admin';

// Simulação de dados (apenas para não quebrar se não tiver controller)
$faturamento = $faturamento ?? ['total' => 0, 'qtd' => 0];
$porPagamento = $porPagamento ?? [];
$ultimasVendas = $ultimasVendas ?? [];
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Paty Bike</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/adm_painel.css">

    <script>
        const BASE_URL = "<?= BASE_URL ?>";
    </script>
</head>

<body class="bg-admin">

    <?php require_once __DIR__ . '/../layout/header_admin.php'; ?>

    <div class="admin-container">

        <div class="page-header">
            <div class="header-title">
                <h2>Visão Geral</h2>
                <p>Resumo de hoje: <strong><?= date('d/m/Y') ?></strong></p>
            </div>

            <button onclick="window.print()" class="btn-print">
                <i class="fas fa-print"></i> Salvar PDF / Imprimir
            </button>
        </div>

        <div class="kpi-grid">
            <div class="card-kpi card-faturamento">
                <div class="kpi-info">
                    <h3>Faturamento Hoje</h3>
                    <div class="kpi-value">R$ <?= number_format($faturamento['total'], 2, ',', '.') ?></div>
                </div>
                <div class="kpi-icon"><i class="fas fa-dollar-sign"></i></div>
            </div>

            <div class="card-kpi card-vendas">
                <div class="kpi-info">
                    <h3>Vendas Realizadas</h3>
                    <div class="kpi-value"><?= $faturamento['qtd'] ?></div>
                </div>
                <div class="kpi-icon"><i class="fas fa-shopping-bag"></i></div>
            </div>

            <?php $ticketMedio = ($faturamento['qtd'] > 0) ? ($faturamento['total'] / $faturamento['qtd']) : 0; ?>
            <div class="card-kpi card-ticket">
                <div class="kpi-info">
                    <h3>Ticket Médio</h3>
                    <div class="kpi-value">R$ <?= number_format($ticketMedio, 2, ',', '.') ?></div>
                </div>
                <div class="kpi-icon"><i class="fas fa-chart-line"></i></div>
            </div>
        </div>

        <?php if (!empty($porPagamento)): ?>
            <div class="section-title">
                <h3><i class="fas fa-wallet"></i> Detalhamento por Pagamento</h3>
            </div>

            <div class="grid-pagamentos">
                <?php foreach ($porPagamento as $pgt): ?>
                    <div class="card-mini border-<?= str_replace(' ', '', $pgt['forma_pagamento']) ?>">
                        <div class="mini-info">
                            <h4><?= $pgt['forma_pagamento'] ?></h4>
                            <div class="mini-value">R$ <?= number_format($pgt['total'], 2, ',', '.') ?></div>
                        </div>
                        <div class="mini-meta">
                            <span><?= $pgt['qtd'] ?> vendas</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="painel-tabela">
            <div class="section-title">
                <h2><i class="fas fa-history"></i> Últimas 10 Vendas</h2>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Horário</th>
                            <th>Vendedor</th>
                            <th>Pagamento</th>
                            <th>Total</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($ultimasVendas)): 
                            $i = 0
                            ?>
                            
                            <?php foreach ($ultimasVendas as $venda):  $i++ ?>
                                <tr>
                                    <td class="col-id"><?= str_pad($i, 4, '0', STR_PAD_LEFT) ?></td>
                                    <td><?= date('H:i', strtotime($venda['data_venda'])) ?></td>
                                    <td><?= $venda['vendedor'] ?? 'Online' ?></td>
                                    <td><?= $venda['forma_pagamento'] ?></td>
                                    <td class="col-total">R$ <?= number_format($venda['total'], 2, ',', '.') ?></td>
                                    <td class="text-center">
                                        <span class="status-badge status-<?= strtolower($venda['status_pagamento']) ?>">
                                            <?= ucfirst($venda['status_pagamento']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="empty-row">Nenhuma venda realizada hoje.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>

</html>