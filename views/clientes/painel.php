<?php
require __DIR__ . '/../layout/header_public.php';

// --- PROCESSAMENTO PHP ---
$totalGasto = 0;
$qtdCompras = count($minhasCompras);
$porStatus = ['pago' => 0, 'pendente' => 0, 'parcial' => 0, 'cancelado' => 0];

foreach ($minhasCompras as $c) {
    $totalGasto += $c['valor_pago'];
    $st = strtolower($c['status_pagamento']);
    if (isset($porStatus[$st])) {
        $porStatus[$st]++;
    } else {
        $porStatus['pendente']++;
    }
}
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="<?= BASE_URL ?>public/css/painel_cliente.css">

<div class="dashboard-container">

    <div class="dashboard-header">
        <h2>Meu Painel</h2>
        <div class="user-actions">
            <span>Olá, <b><?= htmlspecialchars($_SESSION['cliente_nome']) ?></b></span>
            <a href="<?= BASE_URL ?>cliente/sair" class="btn-sair">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="card-kpi kpi-total">
            <h3>Total de Compras</h3>
            <div class="valor"><?= $qtdCompras ?></div>
        </div>
        <div class="card-kpi kpi-gasto">
            <h3>Total Pago</h3>
            <div class="valor">R$ <?= number_format($totalGasto, 2, ',', '.') ?></div>
        </div>
        <div class="card-kpi kpi-pago">
            <h3>Concluídos</h3>
            <div class="valor"><?= $porStatus['pago'] ?></div>
        </div>
        <div class="card-kpi kpi-pendente">
            <h3>Pendentes</h3>
            <div class="valor"><?= $porStatus['pendente'] + $porStatus['parcial'] ?></div>
        </div>
    </div>

    <?php if (!empty($topProdutos)): ?>
        <div class="section-tools">
            <div class="chart-container">
                <h3 style="color:#666; margin: 0 0 15px 0;">Top 10 Produtos Mais Comprados</h3>
                <canvas id="chartTopProdutos"></canvas>
            </div>
        </div>
    <?php endif; ?>

    <div class="filter-container">
        <h3 style="margin:0; color: #2c3e50;">Histórico de Pedidos</h3>
        <div>
            <label for="filtroStatus"
                style="font-weight:600; font-size: 0.9rem; margin-right:5px; color:#555;">Status:</label>
            <select id="filtroStatus" class="filter-select" onchange="filtrarTabela()">
                <option value="todos">Todos</option>
                <option value="pago">Pago</option>
                <option value="pendente">Pendente</option>
                <option value="parcial">Parcial</option>
            </select>
        </div>
    </div>

    <div class="tabela-container">
        <table id="tabelaCompras">
            <thead>
                <tr>
                    <th width="10%">#ID</th>
                    <th width="20%">Data</th>
                    <th width="15%">Total</th>
                    <th width="15%">Pago</th>
                    <th width="15%">Status</th>
                    <th width="25%">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($minhasCompras as $c): ?>
                    <?php
                    $total = (float) $c['total'];
                    $pago = (float) $c['valor_pago'];
                    $pendente = round($total - $pago, 2);
                    $statusReal = strtolower($c['status_pagamento']);

                    // Classes CSS para Status
                    $statusClass = 'st-pago';
                    $textoStatus = 'PAGO';

                    if ($statusReal == 'pendente') {
                        $statusClass = 'st-pendente';
                        $textoStatus = 'PENDENTE';
                    } elseif ($statusReal == 'parcial') {
                        $statusClass = 'st-parcial';
                        $textoStatus = 'PARCIAL';
                    } elseif ($pendente <= 0) {
                        $statusReal = 'pago'; // Força 'pago' se valor zerou
                    }
                    ?>

                    <tr class="linha-compra" data-status="<?= $statusReal ?>">
                        <td data-label="ID">#<?= $c['id'] ?></td>
                        <td data-label="Data"><?= date('d/m/Y H:i', strtotime($c['data_venda'])) ?></td>
                        <td data-label="Total">R$ <?= number_format($total, 2, ',', '.') ?></td>
                        <td data-label="Pago">R$ <?= number_format($pago, 2, ',', '.') ?></td>

                        <td data-label="Status">
                            <span class="status-badge <?= $statusClass ?>">
                                <?= $textoStatus ?>
                            </span>
                        </td>

                        <td data-label="Ações">
                            <button onclick="abrirModalDetalhes(<?= $c['id'] ?>)" class="btn-acao btn-detalhes">
                                <i class="fas fa-eye"></i> Detalhes
                            </button>

                            <?php if ($pendente > 0 && $statusReal != 'pendente_analise'): ?>
                                <a href="<?= BASE_URL ?>cliente/checkout/<?= $c['id'] ?>" class="btn-acao btn-pagar">
                                    <i class="fas fa-wallet"></i> Pagar R$ <?= number_format($pendente, 2, ',', '.') ?>
                                </a>
                            <?php elseif ($pendente <= 0): ?>
                                <span style="color: #27ae60; font-size: 1.2rem; margin-left: 5px;">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (empty($minhasCompras)): ?>
            <p style="padding: 20px; text-align: center; color: #777;">Você ainda não realizou nenhuma compra.</p>
        <?php endif; ?>
    </div>
</div>

<div id="modalDetalhes" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="fecharModal()">&times;</span>
        <h3 style="margin-top:0; color: #2c3e50;">Detalhes do Pedido #<span id="modalPedidoId"></span></h3>
        <hr style="border:0; border-top:1px solid #eee; margin: 10px 0;">
        <div id="modalConteudo">
            <p class="modal-loading">Carregando itens...</p>
        </div>
    </div>
</div>

<script>
    // --- 1. GRÁFICO COLORIDO (Chart.js) ---
    <?php if (!empty($topProdutos)): ?>
        const ctx = document.getElementById('chartTopProdutos').getContext('2d');
        const labels = <?= json_encode(array_column($topProdutos, 'nome')) ?>;
        const dados = <?= json_encode(array_column($topProdutos, 'total_qtd')) ?>;

        // Paleta de cores vibrantes para cada barra
        const backgroundColors = [
            'rgba(255, 99, 132, 0.7)',  // Vermelho
            'rgba(54, 162, 235, 0.7)',  // Azul
            'rgba(255, 206, 86, 0.7)',  // Amarelo
            'rgba(75, 192, 192, 0.7)',  // Verde Água
            'rgba(153, 102, 255, 0.7)', // Roxo
            'rgba(255, 159, 64, 0.7)',  // Laranja
            'rgba(231, 76, 60, 0.7)',   // Vermelho Escuro
            'rgba(46, 204, 113, 0.7)',  // Verde Esmeralda
            'rgba(52, 152, 219, 0.7)',  // Azul Peter River
            'rgba(155, 89, 182, 0.7)'   // Roxo Ametista
        ];

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Quantidade',
                    data: dados,
                    backgroundColor: backgroundColors,
                    borderColor: backgroundColors.map(c => c.replace('0.7', '1')),
                    borderWidth: 1,
                    borderRadius: 5 // Bordas arredondadas nas barras
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false } // Esconde a legenda para ficar mais limpo
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    <?php endif; ?>

    // --- 2. FILTRO DE TABELA ---
    function filtrarTabela() {
        const filtro = document.getElementById('filtroStatus').value;
        const linhas = document.querySelectorAll('.linha-compra');

        linhas.forEach(linha => {
            const status = linha.getAttribute('data-status');
            if (filtro === 'todos' || status === filtro) {
                linha.style.display = ''; // Mostra (padrão da tabela ou block no mobile)
            } else {
                linha.style.display = 'none';
            }
        });
    }

    // --- 3. MODAL ---
    const modal = document.getElementById("modalDetalhes");
    const modalIdSpan = document.getElementById("modalPedidoId");
    const modalConteudo = document.getElementById("modalConteudo");

    function fecharModal() { modal.style.display = "none"; }
    window.onclick = function (e) { if (e.target == modal) fecharModal(); }

    async function abrirModalDetalhes(idVenda) {
        modal.style.display = "flex"; // Flex para centralizar
        modalIdSpan.textContent = idVenda;
        modalConteudo.innerHTML = '<div style="text-align:center; padding:20px; color:#666;"><i class="fas fa-spinner fa-spin"></i> Buscando informações...</div>';

        try {
            const response = await fetch('<?= BASE_URL ?>api/itens_venda?id=' + idVenda);
            const dados = await response.json();

            if (dados.sucesso) { montarTabelaItens(dados.itens); }
            else { modalConteudo.innerHTML = '<p style="color:red; text-align:center;">' + (dados.msg || 'Erro') + '</p>'; }
        } catch (error) {
            modalConteudo.innerHTML = '<p style="color:red; text-align:center;">Erro de conexão.</p>';
        }
    }

    function montarTabelaItens(itens) {
        if (itens.length === 0) { modalConteudo.innerHTML = '<p>Nenhum item encontrado.</p>'; return; }

        let html = `
            <div style="overflow-x:auto;">
            <table class="table-itens">
                <thead>
                    <tr><th>Produto</th><th>Qtd</th><th>Preço</th><th>Total</th></tr>
                </thead>
                <tbody>`;

        itens.forEach(item => {
            let total = item.quantidade * item.preco_unitario;
            let nome = item.nome || item.produto_nome || '#' + item.produto_id;
            html += `
                <tr>
                    <td>${nome}</td>
                    <td>${item.quantidade}</td>
                    <td>R$ ${parseFloat(item.preco_unitario).toFixed(2).replace('.', ',')}</td>
                    <td><b>R$ ${total.toFixed(2).replace('.', ',')}</b></td>
                </tr>`;
        });

        html += `</tbody></table></div>`;
        modalConteudo.innerHTML = html;
    }
</script>

<?php require __DIR__ . '/../layout/footer_public.php'; ?>