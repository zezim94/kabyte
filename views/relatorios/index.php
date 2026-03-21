<?php require __DIR__ . '/../layout/header_admin.php';

// Prepara lista de vendedores para o select
$pdo = Database::connect();
$vendedores = $pdo->query("SELECT id, nome FROM usuarios")->fetchAll(PDO::FETCH_ASSOC);
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .filter-bar {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        align-items: end;
        margin-bottom: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-size: 0.85rem;
        color: #666;
        margin-bottom: 5px;
        font-weight: bold;
    }

    .form-control {
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        min-width: 150px;
    }

    .btn-filtrar {
        background: #3498db;
        color: white;
        border: none;
        padding: 9px 20px;
        border-radius: 4px;
        cursor: pointer;
        height: 38px;
    }

    .btn-filtrar:hover {
        background: #2980b9;
    }

    /* Cards de Resumo */
    .cards-wrapper {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .card-kpi {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        border-left: 5px solid #3498db;
    }

    .kpi-title {
        font-size: 0.9rem;
        color: #777;
    }

    .kpi-value {
        font-size: 1.8rem;
        font-weight: bold;
        color: #2c3e50;
        margin-top: 5px;
    }

    /* Área dos Gráficos */
    .charts-wrapper {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .chart-box {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    /* Tabela */
    .table-responsive {
        overflow-x: auto;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        background: #f8f9fa;
        text-align: left;
        padding: 12px;
        border-bottom: 2px solid #eee;
    }

    td {
        padding: 12px;
        border-bottom: 1px solid #eee;
    }

    .status-badge {
        padding: 3px 8px;
        border-radius: 10px;
        font-size: 0.8rem;
        font-weight: bold;
    }

    .st-pago {
        background: #d4edda;
        color: #155724;
    }

    .st-pendente {
        background: #f8d7da;
        color: #721c24;
    }

    .st-parcial {
        background: #fff3cd;
        color: #856404;
    }
</style>

<div class="admin-container" style="padding: 20px;">

    <h2 style="color: #2c3e50; margin-bottom: 20px;"><i class="fas fa-chart-line"></i> Relatório de Vendas</h2>

    <div class="filter-bar">
        <div class="form-group">
            <label>Data Início</label>
            <input type="date" id="data_inicio" class="form-control" value="<?= date('Y-01-01') ?>">
        </div>
        <div class="form-group">
            <label>Data Fim</label>
            <input type="date" id="data_fim" class="form-control" value="<?= date('Y-m-t') ?>">
        </div>
        <div class="form-group">
            <label>Vendedor</label>
            <select id="vendedor_id" class="form-control">
                <option value="">Todos</option>
                <?php foreach ($vendedores as $vend): ?>
                    <option value="<?= $vend['id'] ?>"><?= $vend['nome'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select id="status" class="form-control">
                <option value="">Todos</option>
                <option value="pago">Pago</option>
                <option value="pendente">Pendente</option>
                <option value="parcial">Parcial</option>
            </select>
        </div>
        <div class="form-group">
            <label>Forma Pagto.</label>
            <select id="forma_pagamento" class="form-control">
                <option value="">Todas</option>
                <option value="Dinheiro">Dinheiro</option>
                <option value="Pix">Pix</option>
                <option value="Cartão">Cartão</option>
                <option value="Boleto">Boleto</option>
            </select>
        </div>
        <div class="form-group">
            <label>Produto (Nome)</label>
            <input type="text" id="produto_nome" class="form-control" placeholder="Ex: Bicicleta...">
        </div>
        <button onclick="carregarRelatorio()" class="btn-filtrar"><i class="fas fa-filter"></i> Filtrar</button>
    </div>

    <div class="cards-wrapper">
        <div class="card-kpi" style="border-left-color: #27ae60;">
            <span class="kpi-title">Total Vendido</span>
            <div class="kpi-value" id="kpiTotal">R$ 0,00</div>
        </div>
        <div class="card-kpi" style="border-left-color: #f39c12;">
            <span class="kpi-title">Total Recebido</span>
            <div class="kpi-value" id="kpiRecebido">R$ 0,00</div>
        </div>
        <div class="card-kpi" style="border-left-color: #8e44ad;">
            <span class="kpi-title">Qtd. Vendas</span>
            <div class="kpi-value" id="kpiQtd">0</div>
        </div>
        <div class="card-kpi" style="border-left-color: #2980b9;">
            <span class="kpi-title">Ticket Médio</span>
            <div class="kpi-value" id="kpiTicket">R$ 0,00</div>
        </div>
    </div>

    <div class="charts-wrapper">
        <div class="chart-box">
            <h4>Evolução Diária</h4>
            <canvas id="chartTimeline"></canvas>
        </div>
        <div class="chart-box">
            <h4>Vendas por Vendedor</h4>
            <canvas id="chartVendedor"></canvas>
        </div>
        <div class="chart-box">
            <h4>Meios de Pagamento</h4>
            <canvas id="chartPagamento"></canvas>
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Data</th>
                    <th>Cliente</th>
                    <th>Vendedor</th>
                    <th>Pagamento</th>
                    <th>Status</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody id="tabelaDados">
            </tbody>
        </table>
    </div>

</div>

<script>
    // Variáveis globais para os gráficos (para poder destruir e recriar)
    let chartTimelineInst = null;
    let chartVendedorInst = null;
    let chartPagamentoInst = null;

    document.addEventListener("DOMContentLoaded", () => {
        carregarRelatorio(); // Carrega ao abrir a página
    });

    async function carregarRelatorio() {
        const params = new URLSearchParams({
            data_inicio: document.getElementById('data_inicio').value,
            data_fim: document.getElementById('data_fim').value,
            vendedor_id: document.getElementById('vendedor_id').value,
            status: document.getElementById('status').value,
            forma_pagamento: document.getElementById('forma_pagamento').value,
            produto_nome: document.getElementById('produto_nome').value
        });

        // Feedback de carregamento
        document.getElementById('tabelaDados').innerHTML = '<tr><td colspan="7" style="text-align:center">Carregando dados...</td></tr>';

        try {
            const res = await fetch('<?= BASE_URL ?>api/relatorio_dados?' + params.toString());
            const dados = await res.json();

            if (dados.sucesso) {
                atualizarKPIs(dados.resumo);
                atualizarTabela(dados.lista);
                atualizarGraficos(dados.graficos);
            } else {
                alert('Erro ao carregar dados: ' + dados.msg);
            }
        } catch (error) {
            console.error(error);
            alert('Erro de conexão.');
        }
    }

    function atualizarKPIs(resumo) {
        document.getElementById('kpiTotal').innerText = formatMoney(resumo.total_vendido);
        document.getElementById('kpiRecebido').innerText = formatMoney(resumo.total_recebido);
        document.getElementById('kpiQtd').innerText = resumo.qtd_vendas;
        document.getElementById('kpiTicket').innerText = formatMoney(resumo.ticket_medio);
    }

    function atualizarTabela(lista) {
        const tbody = document.getElementById('tabelaDados');
        tbody.innerHTML = '';

        if (lista.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center; padding: 20px;">Nenhum registro encontrado.</td></tr>';
            return;
        }

        lista.forEach(v => {
            let statusClass = 'st-' + v.status_pagamento;
            let dataFormatada = new Date(v.data_venda).toLocaleDateString('pt-BR') + ' ' + new Date(v.data_venda).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });

            let row = `
                <tr>
                    <td>#${v.id}</td>
                    <td>${dataFormatada}</td>
                    <td>${v.cliente || '<i style="color:#ccc">Balcão</i>'}</td>
                    <td>${v.vendedor || '-'}</td>
                    <td>${v.forma_pagamento}</td>
                    <td><span class="status-badge ${statusClass}">${v.status_pagamento.toUpperCase()}</span></td>
                    <td style="font-weight:bold; color:#2c3e50;">${formatMoney(v.total)}</td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    }

    function atualizarGraficos(graficos) {
        // Cores padrão
        const colors = ['#3498db', '#e74c3c', '#2ecc71', '#f1c40f', '#9b59b6', '#34495e'];

        // 1. Gráfico Timeline (Linha)
        if (chartTimelineInst) chartTimelineInst.destroy();
        chartTimelineInst = new Chart(document.getElementById('chartTimeline'), {
            type: 'line',
            data: {
                labels: Object.keys(graficos.timeline),
                datasets: [{
                    label: 'Vendas (R$)',
                    data: Object.values(graficos.timeline),
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.2)',
                    fill: true,
                    tension: 0.3
                }]
            }
        });

        // 2. Gráfico Vendedor (Barra)
        if (chartVendedorInst) chartVendedorInst.destroy();
        chartVendedorInst = new Chart(document.getElementById('chartVendedor'), {
            type: 'bar',
            data: {
                labels: Object.keys(graficos.vendedor),
                datasets: [{
                    label: 'Total Vendido',
                    data: Object.values(graficos.vendedor),
                    backgroundColor: colors
                }]
            }
        });

        // 3. Gráfico Pagamento (Pizza/Doughnut)
        if (chartPagamentoInst) chartPagamentoInst.destroy();
        chartPagamentoInst = new Chart(document.getElementById('chartPagamento'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(graficos.pagamento),
                datasets: [{
                    data: Object.values(graficos.pagamento),
                    backgroundColor: ['#27ae60', '#e67e22', '#2980b9', '#8e44ad', '#c0392b']
                }]
            }
        });
    }

    function formatMoney(value) {
        return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }
</script>