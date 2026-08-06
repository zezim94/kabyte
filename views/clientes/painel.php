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

    <!-- === SEÇÃO DE PRODUTOS MAIS VISTOS PELO CLIENTE === -->
    <?php if (!empty($meusMaisVistos)): ?>
        <div class="section-tools" style="margin-bottom: 30px; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <h3 style="margin:0 0 15px 0; color: #2c3e50; border-bottom: 2px solid #f1f2f6; padding-bottom: 10px;">
                <i class="fas fa-eye" style="color: #3498db;"></i> Vistos Recentemente por Mim
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 15px; margin-top: 15px;">
                <?php foreach ($meusMaisVistos as $prod): ?>
                    <?php
                    // Tratamento de imagem (se não tiver foto, exibe a default)
                    $imgSrc = !empty($prod['imagem']) ? BASE_URL . 'public/uploads/' . $prod['imagem'] : BASE_URL . 'public/images/default.png';
                    ?>
                    <div style="border: 1px solid #eee; border-radius: 8px; padding: 10px; text-align: center; transition: 0.3s; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($prod['nome']) ?>" style="width: 100%; height: 120px; object-fit: contain; margin-bottom: 10px;">

                        <h4 style="font-size: 0.9rem; color: #333; margin: 0 0 5px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($prod['nome']) ?>">
                            <?= htmlspecialchars($prod['nome']) ?>
                        </h4>

                        <p style="color: #27ae60; font-weight: bold; margin: 0 0 10px 0;">
                            R$ <?= number_format($prod['preco'], 2, ',', '.') ?>
                        </p>

                        <a href="<?= BASE_URL ?>index.php?rota=produto/detalhes&id=<?= $prod['id'] ?>" style="display: block; background: #3498db; color: #fff; padding: 6px; border-radius: 4px; text-decoration: none; font-size: 0.85rem; font-weight: 500;">
                            <i class="fas fa-search"></i> Ver Novamente
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
    <!-- ========================================================== -->

    <?php if (!empty($topProdutos)): ?>
        <div class="section-tools">
            <div class="chart-container">
                <h3 style="color:#666; margin: 0 0 15px 0;">Top 10 Produtos Mais Comprados</h3>
                <canvas id="chartTopProdutos"></canvas>
            </div>
        </div>
    <?php endif; ?>

    <!-- === FILTROS AJUSTADOS === -->
    <div class="filter-container" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
        <h3 style="margin:0; color: #2c3e50; margin-right: auto;">Histórico de Pedidos</h3>
        
        <div>
            <label for="filtroStatus" style="font-weight:600; font-size: 0.9rem; margin-right:5px; color:#555;">Pagamento:</label>
            <select id="filtroStatus" class="filter-select" onchange="filtrarTabela()">
                <option value="todos">Todos</option>
                <option value="pago">Pago</option>
                <option value="pendente">Pendente</option>
                <option value="parcial">Parcial</option>
            </select>
        </div>

        <div>
            <label for="filtroEntrega" style="font-weight:600; font-size: 0.9rem; margin-right:5px; color:#555;">Modo/Entrega:</label>
            <select id="filtroEntrega" class="filter-select" onchange="filtrarTabela()">
                <option value="todos">Todos</option>
                <option value="entrega">Apenas Entregas</option>
                <option value="retirada">Pegar na Loja</option>
            </select>
        </div>
    </div>
    <!-- ========================= -->

    <div class="tabela-container">
        <table id="tabelaCompras">
            <thead>
                <tr>
                    <th width="8%">#ID</th>
                    <th width="15%">Data</th>
                    <th width="15%">Total</th>
                    <th width="12%">Status Pag.</th>
                    <th width="20%">Logística</th>
                    <th width="30%">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($minhasCompras as $c): ?>
                    <?php
                    $total = (float) $c['total'];
                    $pago = (float) $c['valor_pago'];
                    $pendente = round($total - $pago, 2);
                    $statusReal = strtolower($c['status_pagamento']);

                    // Classes CSS para Pagamento
                    $statusClass = 'st-pago';
                    $textoStatus = 'PAGO';

                    if ($statusReal == 'pendente') {
                        $statusClass = 'st-pendente';
                        $textoStatus = 'PENDENTE';
                    } elseif ($statusReal == 'parcial') {
                        $statusClass = 'st-parcial';
                        $textoStatus = 'PARCIAL';
                    } elseif ($pendente <= 0) {
                        $statusReal = 'pago'; 
                    }

                    // Logística / Entrega
                    $tipoEntrega = strtolower($c['tipo_entrega'] ?? 'retirada'); // 'entrega' ou 'retirada'
                    $statusEntrega = strtolower($c['status_entrega'] ?? 'pendente'); // 'pendente', 'enviado', 'entregue'
                    
                    // Tratamento visual para logística
                    $corEntrega = ($statusEntrega == 'entregue') ? '#27ae60' : '#f39c12';
                    $textoEntrega = ($statusEntrega == 'entregue') ? 'Entregue' : 'Pendente';
                    if($tipoEntrega == 'retirada' && $statusEntrega == 'entregue'){
                        $textoEntrega = 'Retirado';
                    }
                    ?>

                    <!-- Adicionado data-entrega para o filtro funcionar -->
                    <tr class="linha-compra" data-status="<?= $statusReal ?>" data-entrega="<?= $tipoEntrega ?>">
                        <td data-label="ID">#<?= $c['id'] ?></td>
                        <td data-label="Data"><?= date('d/m/Y H:i', strtotime($c['data_venda'])) ?></td>
                        <td data-label="Total">R$ <?= number_format($total, 2, ',', '.') ?></td>
                        
                        <td data-label="Pagamento">
                            <span class="status-badge <?= $statusClass ?>">
                                <?= $textoStatus ?>
                            </span>
                        </td>

                        <td data-label="Logística">
                            <?php if($tipoEntrega === 'entrega'): ?>
                                <div style="font-weight: 500;"><i class="fas fa-truck" style="color:#3498db;"></i> Entrega</div>
                            <?php else: ?>
                                <div style="font-weight: 500;"><i class="fas fa-store" style="color:#8e44ad;"></i> Retirada</div>
                            <?php endif; ?>
                            <small style="color: <?= $corEntrega ?>; font-weight: bold;"><?= $textoEntrega ?></small>
                        </td>

                        <td data-label="Ações">
                            <!-- Botão modificado para enviar dados de entrega para o modal -->
                            <button onclick="abrirModalDetalhes(<?= $c['id'] ?>, '<?= $tipoEntrega ?>', '<?= $statusEntrega ?>')" class="btn-acao btn-detalhes">
                                <i class="fas fa-eye"></i> Detalhes
                            </button>

                            <?php if ($pendente > 0 && $statusReal != 'pendente_analise'): ?>
                                <a href="<?= BASE_URL ?>pagamento/checkout&id=<?= $c['id'] ?>" class="btn-acao btn-pagar">
                                    <i class="fas fa-wallet"></i> Pagar R$ <?= number_format($pendente, 2, ',', '.') ?>
                                </a>
                            <?php elseif ($pendente <= 0): ?>
                                <span style="color: #27ae60; font-size: 1.2rem; margin-left: 5px;" title="Pago integralmente">
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

<!-- === MODAL DETALHES === -->
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

        const backgroundColors = [
            'rgba(255, 99, 132, 0.7)', 
            'rgba(54, 162, 235, 0.7)', 
            'rgba(255, 206, 86, 0.7)', 
            'rgba(75, 192, 192, 0.7)', 
            'rgba(153, 102, 255, 0.7)', 
            'rgba(255, 159, 64, 0.7)', 
            'rgba(231, 76, 60, 0.7)', 
            'rgba(46, 204, 113, 0.7)', 
            'rgba(52, 152, 219, 0.7)', 
            'rgba(155, 89, 182, 0.7)' 
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
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
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

    // --- 2. FILTRO DE TABELA DUPLO ---
    function filtrarTabela() {
        const filtroPagamento = document.getElementById('filtroStatus').value;
        const filtroLogistica = document.getElementById('filtroEntrega').value;
        const linhas = document.querySelectorAll('.linha-compra');

        linhas.forEach(linha => {
            const statusPagamento = linha.getAttribute('data-status');
            const statusLogistica = linha.getAttribute('data-entrega');

            // Verifica se a linha bate com ambos os filtros
            const matchPagamento = (filtroPagamento === 'todos' || statusPagamento === filtroPagamento);
            const matchLogistica = (filtroLogistica === 'todos' || statusLogistica === filtroLogistica);

            if (matchPagamento && matchLogistica) {
                linha.style.display = ''; 
            } else {
                linha.style.display = 'none';
            }
        });
    }

    // --- 3. MODAL ---
    const modal = document.getElementById("modalDetalhes");
    const modalIdSpan = document.getElementById("modalPedidoId");
    const modalConteudo = document.getElementById("modalConteudo");
    let modalInfoLogistica = ''; // Armazena a caixa de aviso da logística

    function fecharModal() {
        modal.style.display = "none";
    }
    
    window.onclick = function(e) {
        if (e.target == modal) fecharModal();
    }

    async function abrirModalDetalhes(idVenda, tipoEntrega, statusEntrega) {
        modal.style.display = "flex"; 
        modalIdSpan.textContent = idVenda;
        modalConteudo.innerHTML = '<div style="text-align:center; padding:20px; color:#666;"><i class="fas fa-spinner fa-spin"></i> Buscando informações...</div>';

        // Prepara a faixa amarela ou verde com os dados logísticos
        let iconeTipo = tipoEntrega === 'entrega' ? '<i class="fas fa-truck"></i> Receber no Endereço' : '<i class="fas fa-store"></i> Retirar na Loja';
        
        let txtStatus = '';
        if(statusEntrega === 'entregue'){
            txtStatus = tipoEntrega === 'entrega' ? 'Entregue ao cliente' : 'Retirado na loja';
        } else {
            txtStatus = tipoEntrega === 'entrega' ? 'Aguardando envio' : 'Aguardando retirada';
        }
        
        let corStatus = statusEntrega === 'entregue' ? '#27ae60' : '#f39c12';

        modalInfoLogistica = `
            <div style="background: #f8f9fa; border-left: 4px solid ${corStatus}; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <div style="font-size: 0.95rem; color: #333;"><b>Modo:</b> ${iconeTipo}</div>
                    <div style="font-size: 0.95rem; color: ${corStatus}; font-weight: bold;">
                        <i class="fas ${statusEntrega === 'entregue' ? 'fa-check-circle' : 'fa-clock'}"></i> ${txtStatus}
                    </div>
                </div>
            </div>
        `;

        try {
            const response = await fetch('<?= BASE_URL ?>api/itensVenda?id=' + idVenda);
            const dados = await response.json();

            if (dados.sucesso) {
                montarTabelaItens(dados.itens);
            } else {
                modalConteudo.innerHTML = '<p style="color:red; text-align:center;">' + (dados.msg || 'Erro') + '</p>';
            }
        } catch (error) {
            modalConteudo.innerHTML = '<p style="color:red; text-align:center;">Erro de conexão.</p>';
        }
    }


    function montarTabelaItens(itens) {
        if (itens.length === 0) {
            modalConteudo.innerHTML = '<p>Nenhum item encontrado.</p>';
            return;
        }

        // Injeta a caixa de informações de logística ANTES da tabela
        let html = modalInfoLogistica + `
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