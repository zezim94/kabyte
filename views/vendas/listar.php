<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Vendas - KaByte</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<?php require __DIR__ . '/../layout/header_admin.php'; ?>

<style>
    :root {
        --bg-body: #f1f5f9;
        --card-bg: #ffffff;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --border-color: #e2e8f0;
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.07), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        --shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
        --radius: 12px;
        --transition: 0.2s ease;

        --green: #10b981;
        --green-light: #d1fae5;
        --blue: #3b82f6;
        --blue-hover: #2563eb;
        --red: #ef4444;
        --red-light: #fee2e2;
        --orange: #f59e0b;
        --orange-light: #fef3c7;
        --dark: #1e293b;
        --dark-header: #34495e;
    }

    body {
        background-color: var(--bg-body);
        margin: 0;
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        color: var(--text-primary);
    }

    * {
        box-sizing: border-box;
    }

    /* Container principal */
    .admin-container {
        max-width: 1400px;
        margin: 2rem auto;
        padding: 0 1.5rem;
    }

    /* Cabeçalho */
    .page-header {
        margin-bottom: 1.5rem;
    }

    .page-header h2 {
        color: var(--text-primary);
        font-size: 1.8rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Filtro */
    .filter-form {
        background: var(--card-bg);
        padding: 1.5rem;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        margin-bottom: 1.5rem;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        align-items: end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }

    .filter-group label {
        font-weight: 600;
        font-size: 0.85rem;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .filter-control {
        padding: 0.6rem 0.8rem;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 0.95rem;
        background: white;
        transition: border-color var(--transition);
    }

    .filter-control:focus {
        outline: none;
        border-color: var(--blue);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .btn-filtrar {
        background: var(--blue);
        color: white;
        border: none;
        padding: 0.6rem 1.2rem;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        transition: background var(--transition);
        height: auto;
        align-self: end;
    }

    .btn-filtrar:hover {
        background: var(--blue-hover);
    }

    /* Tabela */
    .painel-tabela {
        background: var(--card-bg);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow: hidden;
        border: 1px solid var(--border-color);
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 750px;
    }

    .data-table thead {
        background: var(--dark-header);
        color: white;
    }

    .data-table th {
        padding: 0.9rem 1.2rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        border-bottom: 2px solid rgba(255, 255, 255, 0.1);
    }

    .data-table td {
        padding: 0.8rem 1.2rem;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-primary);
        vertical-align: middle;
    }

    .data-table tbody tr:last-child td {
        border-bottom: none;
    }

    .data-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .badge-id {
        color: #64748b;
        font-family: monospace;
        font-weight: 600;
    }

    .cliente-nome {
        color: var(--blue);
        font-weight: 500;
    }

    .consumidor-final {
        color: #94a3b8;
        font-style: italic;
    }

    /* Badges de status */
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .status-pago {
        background: var(--green-light);
        color: #065f46;
    }

    .status-pendente {
        background: var(--red-light);
        color: #991b1b;
    }

    .status-parcial {
        background: var(--orange-light);
        color: #92400e;
    }

    .btn-detalhes {
        border: 1px solid var(--blue);
        color: var(--blue);
        background: white;
        padding: 0.35rem 0.8rem;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all var(--transition);
    }

    .btn-detalhes:hover {
        background: var(--blue);
        color: white;
    }

    /* =========================================
       MODAL REDESENHADO (VERTICAL E LARGO)
       ========================================= */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
        align-items: center;
        justify-content: center;
        padding: 1rem;
        backdrop-filter: blur(3px);
    }

    .modal-content {
        background: white;
        width: 100%;
        max-width: 950px; /* Aumentado para utilizar mais a largura da página */
        max-height: 95vh;
        border-radius: var(--radius);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        animation: modalPop 0.3s ease;
    }

    @keyframes modalPop {
        from { transform: scale(0.95); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    .modal-header {
        background: var(--dark-header);
        color: white;
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
    }

    .modal-header h3 {
        margin: 0;
        font-size: 1.25rem;
    }

    .close-modal {
        font-size: 1.8rem;
        cursor: pointer;
        line-height: 1;
        transition: color 0.2s;
    }

    .close-modal:hover {
        color: #ccc;
    }

    .modal-body-scroll {
        overflow-y: auto;
        padding: 1.5rem;
        flex: 1;
    }

    /* Layout Vertical Proporcional */
    .info-grid {
        display: flex;
        flex-direction: column; /* Empilha os blocos verticalmente */
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .info-box {
        background: #f8fafc;
        padding: 1.5rem;
        border-radius: 8px;
        border-left: 4px solid var(--blue);
        width: 100%; /* Ocupa toda a largura do modal */
    }

    .info-box h4 {
        margin: 0 0 1rem;
        font-size: 1.1rem;
        color: var(--text-primary);
        border-bottom: 2px solid var(--border-color);
        padding-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Grelha interna para os dados ficarem bem distribuídos */
    .info-box-content {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.25rem;
    }

    .info-row {
        display: flex;
        flex-direction: column; /* Coloca o título em cima e o dado em baixo */
        font-size: 0.95rem;
    }

    .info-label {
        font-weight: 700;
        color: var(--text-secondary);
        font-size: 0.8rem;
        text-transform: uppercase;
        margin-bottom: 0.3rem;
        letter-spacing: 0.03em;
    }

    .info-value {
        color: var(--text-primary);
        font-size: 1.05rem;
        font-weight: 500;
    }

    /* Tabela de itens no modal */
    .table-itens {
        width: 100%;
        border-collapse: collapse;
        margin: 1rem 0;
        font-size: 0.95rem;
    }

    .table-itens th {
        background: #f1f5f9;
        text-align: left;
        padding: 0.8rem 1rem;
        font-weight: 600;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-secondary);
        text-transform: uppercase;
        font-size: 0.8rem;
    }

    .table-itens td {
        padding: 0.8rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .produto-img {
        width: 50px;
        height: 50px;
        object-fit: contain;
        border-radius: 6px;
        border: 1px solid var(--border-color);
        background: white;
    }

    .total-venda {
        text-align: right;
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--green);
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 2px solid var(--border-color);
    }

    .spinner {
        padding: 3rem;
        text-align: center;
        color: var(--text-secondary);
    }

    /* Responsivo geral */
    @media (max-width: 768px) {
        .admin-container {
            padding: 0 1rem;
        }

        .data-table {
            min-width: 650px;
        }

        .btn-filtrar {
            grid-column: 1 / -1;
        }
    }
</style>

<div class="admin-container">
    <div class="page-header">
        <h2><i class="fas fa-file-invoice-dollar"></i> Histórico de Vendas</h2>
    </div>

    <!-- FORMULÁRIO DE FILTRO -->
    <form method="GET" action="" class="filter-form">
        <div class="filter-group">
            <label>De</label>
            <input type="date" name="data_inicio" class="filter-control"
                value="<?= $_GET['data_inicio'] ?? date('Y-m-01') ?>">
        </div>

        <div class="filter-group">
            <label>Até</label>
            <input type="date" name="data_fim" class="filter-control"
                value="<?= $_GET['data_fim'] ?? date('Y-m-t') ?>">
        </div>

        <div class="filter-group">
            <label>Cliente</label>
            <input type="text" name="cliente" class="filter-control" placeholder="Nome..."
                value="<?= htmlspecialchars($_GET['cliente'] ?? '') ?>">
        </div>

        <div class="filter-group">
            <label>Status</label>
            <select name="status" class="filter-control">
                <option value="">Todos</option>
                <option value="pago" <?= ($_GET['status'] ?? '') == 'pago' ? 'selected' : '' ?>>Pago</option>
                <option value="pendente" <?= ($_GET['status'] ?? '') == 'pendente' ? 'selected' : '' ?>>Pendente</option>
                <option value="parcial" <?= ($_GET['status'] ?? '') == 'parcial' ? 'selected' : '' ?>>Parcial</option>
            </select>
        </div>

        <button type="submit" class="btn-filtrar">
            <i class="fas fa-search"></i> Filtrar
        </button>
    </form>

    <!-- TABELA DE VENDAS -->
    <div class="painel-tabela">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Data/Hora</th>
                        <th>Cliente</th>
                        <th>Vendedor</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th style="text-align: center;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vendas as $v): ?>
                        <tr>
                            <td class="badge-id">#<?= str_pad($v['id'], 4, '0', STR_PAD_LEFT) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($v['data_venda'])) ?></td>
                            <td>
                                <?php if ($v['cliente']): ?>
                                    <span class="cliente-nome"><i class="fas fa-user"></i> <?= $v['cliente'] ?></span>
                                <?php else: ?>
                                    <span class="consumidor-final">Consumidor Final</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $v['vendedor'] ?? 'Online' ?></td>
                            <td>
                                <span class="status-badge status-<?= $v['status_pagamento'] ?>">
                                    <?= $v['status_pagamento'] ?>
                                </span>
                            </td>
                            <td style="font-weight: 600; color: var(--green);">
                                R$ <?= number_format($v['total'], 2, ',', '.') ?>
                            </td>
                            <td style="text-align: center;">
                                <button onclick="abrirModalAdmin(<?= $v['id'] ?>)" class="btn-detalhes">
                                    <i class="fas fa-eye"></i> Detalhes
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modalVendaAdmin" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Detalhes da Venda #<span id="mdlId"></span></h3>
            <span class="close-modal" onclick="fecharModalAdmin()">&times;</span>
        </div>

        <div id="modalLoading" class="spinner">
            <i class="fas fa-spinner fa-spin fa-2x"></i><br><br>Carregando as informações...
        </div>

        <!-- Container com rolagem -->
        <div id="modalBodyContent" class="modal-body-scroll" style="display: none;"></div>
    </div>
</div>

<script>
    const modalAdmin = document.getElementById('modalVendaAdmin');
    const loading = document.getElementById('modalLoading');
    const bodyContent = document.getElementById('modalBodyContent');
    const scrollContainer = bodyContent.querySelector('.modal-body-scroll');

    function fecharModalAdmin() {
        modalAdmin.style.display = 'none';
    }

    window.onclick = function(e) {
        if (e.target == modalAdmin) fecharModalAdmin();
    }

    async function abrirModalAdmin(id) {
        modalAdmin.style.display = 'flex';
        loading.style.display = 'block';
        bodyContent.style.display = 'none';
        document.getElementById('mdlId').innerText = id;

        try {
            const res = await fetch('<?= BASE_URL ?>venda/detalhesVendaAdmin?id=' + id);
            const dados = await res.json();

            if (dados.sucesso) {
                preencherModal(dados.venda, dados.itens);
                loading.style.display = 'none';
                bodyContent.style.display = 'block';
            } else {
                alert('Erro: ' + dados.msg);
                fecharModalAdmin();
            }
        } catch (error) {
            console.error(error);
            alert('Erro de conexão.');
            fecharModalAdmin();
        }
    }

    function preencherModal(venda, itens) {
        let tipoEntrega = venda.tipo_entrega === 'entrega' ? 'Entrega em Domicílio' : 'Retirada na Loja';
        let corTipo = venda.tipo_entrega === 'entrega' ? 'var(--orange)' : 'var(--green)';
        let iconeLogistica = venda.tipo_entrega === 'entrega' ? 'fa-truck' : 'fa-store';

        // Geração do HTML reestruturado para ser Vertical e Largo
        let html = `
        <div class="info-grid">
            
            <div class="info-box">
                <h4><i class="fas fa-receipt"></i> Dados da Venda</h4>
                <div class="info-box-content">
                    <div class="info-row">
                        <span class="info-label">Vendedor</span>
                        <span class="info-value">${venda.vendedor_nome || 'Sistema (Online)'}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Data e Hora</span>
                        <span class="info-value">${new Date(venda.data_venda).toLocaleString('pt-BR')}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Forma de Pagamento</span>
                        <span class="info-value">${venda.forma_pagamento || '-'}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="info-value">
                            <span class="status-badge status-${venda.status_pagamento}">${venda.status_pagamento.toUpperCase()}</span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="info-box">
                <h4><i class="fas fa-user-circle"></i> Dados do Cliente</h4>
                <div class="info-box-content">
                    <div class="info-row">
                        <span class="info-label">Nome do Cliente</span>
                        <span class="info-value">${venda.cliente_nome || 'Consumidor Final'}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">E-mail de Contato</span>
                        <span class="info-value">${venda.cliente_email || 'Não informado'}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Cliente Desde</span>
                        <span class="info-value">${venda.cliente_desde ? new Date(venda.cliente_desde).toLocaleDateString('pt-BR') : '-'}</span>
                    </div>
                </div>
            </div>

            <div class="info-box">
                <h4><i class="fas ${iconeLogistica}"></i> Logística e Entrega</h4>
                <div class="info-box-content">
                    <div class="info-row">
                        <span class="info-label">Tipo de Logística</span>
                        <span class="info-value" style="color:${corTipo}; font-weight:700;">${tipoEntrega}</span>
                    </div>
                    ${venda.tipo_entrega === 'entrega' ? `
                        <div class="info-row">
                            <span class="info-label">Data Agendada</span>
                            <span class="info-value">${venda.data_entrega ? new Date(venda.data_entrega).toLocaleString('pt-BR') : 'Sem data definida'}</span>
                        </div>
                        <div class="info-row" style="grid-column: 1 / -1;">
                            <span class="info-label">Endereço de Entrega</span>
                            <span class="info-value">${venda.endereco_entrega || 'Endereço não preenchido'}</span>
                        </div>
                    ` : `
                        <div class="info-row">
                            <span class="info-label">Status da Retirada</span>
                            <span class="info-value">Aguardando na Loja</span>
                        </div>
                    `}
                </div>
            </div>
            
        </div>

        <h4 style="color: var(--text-primary); margin: 0 0 1rem 0;"><i class="fas fa-box-open"></i> Itens do Pedido</h4>
        <div style="overflow-x: auto;">
            <table class="table-itens">
                <thead>
                    <tr>
                        <th style="width:60px;">Foto</th>
                        <th>Produto</th>
                        <th style="text-align: center;">Qtd</th>
                        <th style="text-align: right;">Preço Unit.</th>
                        <th style="text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
    `;

        itens.forEach(item => {
            let subtotal = item.quantidade * item.preco_unitario;
            let imgUrl = item.imagem ? '<?= BASE_URL ?>public/uploads/' + item.imagem : 'https://via.placeholder.com/50?text=S/F';
            html += `
            <tr>
                <td><img src="${imgUrl}" class="produto-img"></td>
                <td style="font-weight: 500;">${item.produto_nome}</td>
                <td style="text-align: center;">${item.quantidade}</td>
                <td style="text-align: right;">R$ ${parseFloat(item.preco_unitario).toFixed(2).replace('.', ',')}</td>
                <td style="text-align: right; font-weight: 700;">R$ ${subtotal.toFixed(2).replace('.', ',')}</td>
            </tr>
        `;
        });

        html += `</tbody></table></div>`;
        html += `<div class="total-venda">Total Geral: R$ ${parseFloat(venda.total).toFixed(2).replace('.', ',')}</div>`;

        document.getElementById('modalBodyContent').innerHTML = html;
    }
</script>