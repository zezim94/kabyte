<?php require __DIR__ . '/../layout/header_admin.php'; ?>

<style>
    :root {
        --primary: #3498db;
        --success: #27ae60;
        --warning: #f39c12;
        --danger: #e74c3c;
        --dark: #2c3e50;
        --light: #ecf0f1;
        --gray: #95a5a6;
    }

    .container{
        padding: 15px;
    }

    /* --- LAYOUT GERAL --- */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .filter-container {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.04);
        margin-bottom: 30px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        align-items: end;
    }

    .form-group label {
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 8px;
        display: block;
        font-size: 0.9rem;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 1rem;
        transition: border-color 0.3s;
    }

    .form-control:focus {
        border-color: var(--primary);
        outline: none;
    }

    /* --- GRID DE CARDS --- */
    .delivery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 25px;
    }

    .card-entrega {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        position: relative;
        transition: transform 0.2s, box-shadow 0.2s;
        border-top: 5px solid #ccc;
        display: flex;
        flex-direction: column;
    }

    .card-entrega:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    /* Cores da Borda Superior */
    .border-pago {
        border-top-color: var(--success);
    }

    .border-pendente {
        border-top-color: var(--danger);
    }

    .border-parcial {
        border-top-color: var(--warning);
    }

    .card-body {
        padding: 20px;
        flex: 1;
    }

    .card-footer {
        padding: 15px 20px;
        background: #f8f9fa;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .time-badge {
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .status-badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: white;
    }

    .bg-pago {
        background: var(--success);
    }

    .bg-pendente {
        background: var(--danger);
    }

    .bg-entregue {
        background: var(--success);
    }

    .client-name {
        font-size: 1.2rem;
        color: var(--dark);
        margin: 0 0 5px 0;
    }

    .address-text {
        color: #666;
        font-size: 0.95rem;
        line-height: 1.4;
        display: flex;
        gap: 8px;
    }

    .obs-box {
        background: #fff8e1;
        border-left: 3px solid #ffca28;
        padding: 8px;
        font-size: 0.85rem;
        margin-top: 10px;
        border-radius: 4px;
        color: #7f6000;
    }

    .price-info {
        text-align: right;
    }

    .price-total {
        font-weight: 800;
        font-size: 1.1rem;
        color: var(--dark);
    }

    .price-debt {
        color: var(--danger);
        font-size: 0.85rem;
        font-weight: 600;
    }

    /* --- BOTÕES --- */
    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-circle {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-decoration: none;
        font-size: 1rem;
        transition: opacity 0.2s;
        border: none;
        cursor: pointer;
    }

    .btn-circle:hover {
        opacity: 0.8;
    }

    .btn-wpp {
        background: #25D366;
    }

    .btn-maps {
        background: #4285F4;
    }

    .btn-print {
        background: #607d8b;
    }

    .btn-confirm {
        width: 100%;
        padding: 12px;
        background: var(--dark);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 15px;
        transition: background 0.3s;
    }

    .btn-confirm:hover {
        background: #1a252f;
    }

    /* --- CUSTOM TOAST --- */
    #toast-container {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .toast {
        min-width: 300px;
        background: #333;
        color: white;
        padding: 12px 20px;
        border-radius: 50px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        display: flex;
        align-items: center;
        gap: 10px;
        animation: slideUp 0.3s forwards;
    }

    .toast.success {
        background: var(--success);
    }

    .toast.error {
        background: var(--danger);
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* --- CUSTOM MODAL --- */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 10000;
        display: none;
        justify-content: center;
        align-items: center;
        backdrop-filter: blur(2px);
    }

    .modal-box {
        background: white;
        width: 90%;
        max-width: 400px;
        padding: 25px;
        border-radius: 15px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        animation: popIn 0.3s;
    }

    @keyframes popIn {
        from {
            transform: scale(0.8);
            opacity: 0;
        }

        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .modal-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
        justify-content: center;
    }

    .btn-modal {
        padding: 10px 25px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-weight: bold;
    }

    .btn-cancel {
        background: #eee;
        color: #555;
    }

    .btn-ok {
        background: var(--success);
        color: white;
    }

    /* Responsividade Mobile */
    @media (max-width: 768px) {
        .delivery-grid {
            grid-template-columns: 1fr;
        }

        /* 1 coluna no mobile */
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }

    /* ESTILOS DE IMPRESSÃO (Romaneio) */
    @media print {
        body * {
            visibility: hidden;
        }

        .container,
        .container * {
            visibility: visible;
        }

        .container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 0;
        }

        .filter-container,
        .btn-confirm,
        .actions-btn,
        .page-header {
            display: none !important;
        }

        .delivery-grid {
            display: block;
        }

        .card-entrega {
            border: 1px solid #000;
            box-shadow: none;
            break-inside: avoid;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }
    }
</style>

<div id="toast-container"></div>

<div id="modalConfirmacao" class="modal-overlay">
    <div class="modal-box">
        <div style="font-size: 3rem; color: #27ae60; margin-bottom: 10px;">
            <i class="fas fa-check-circle"></i>
        </div>
        <h3 style="color: #2c3e50; margin: 0;">Confirmar Entrega?</h3>
        <p style="color: #666; margin-top: 10px;">O motoboy voltou e o pedido foi entregue com sucesso?</p>

        <div class="modal-actions">
            <button onclick="fecharModal()" class="btn-modal btn-cancel">Cancelar</button>
            <button onclick="procederBaixa()" class="btn-modal btn-ok">Sim, Confirmar</button>
        </div>
    </div>
</div>

<div class="container">

    <div class="page-header">
        <h2 style="margin:0; color: var(--dark);"><i class="fas fa-motorcycle"></i> Controle de Entregas</h2>
        <button onclick="window.print()" class="btn-modal" style="background:var(--dark); color:white;">
            <i class="fas fa-print"></i> Imprimir Romaneio
        </button>
    </div>

    <form class="filter-container" method="GET" action="">
        <div class="form-group">
            <label>Data da Entrega</label>
            <input type="date" name="data" class="form-control" value="<?= $_GET['data'] ?? date('Y-m-d') ?>"
                onchange="this.form.submit()">
        </div>

        <div class="form-group">
            <label>Status Financeiro</label>
            <select name="status" class="form-control" onchange="this.form.submit()">
                <option value="todos" <?= ($_GET['status'] ?? '') == 'todos' ? 'selected' : '' ?>>Todos</option>
                <option value="pago" <?= ($_GET['status'] ?? '') == 'pago' ? 'selected' : '' ?>>Pago</option>
                <option value="pendente" <?= ($_GET['status'] ?? '') == 'pendente' ? 'selected' : '' ?>>Pendente (A
                    Receber)</option>
            </select>
        </div>
    </form>

    <?php if (empty($entregas)): ?>
        <div style="text-align:center; padding: 60px; color: #95a5a6; background:white; border-radius:12px;">
            <i class="fas fa-box-open" style="font-size:3rem; margin-bottom:15px;"></i>
            <h3>Nenhuma entrega encontrada.</h3>
            <p>Verifique a data ou os filtros selecionados.</p>
        </div>
    <?php else: ?>

        <div class="delivery-grid">
            <?php foreach ($entregas as $item): ?>
                <?php
                $statusClass = 'border-' . $item['status_pagamento'];
                $bgClass = 'bg-' . $item['status_pagamento'];
                $enderecoUrl = urlencode($item['endereco_entrega']);
                $zap = preg_replace('/\D/', '', $item['cliente_telefone']);
                $hora = $item['data_entrega'] ? date('H:i', strtotime($item['data_entrega'])) : '--:--';
                $isEntregue = ($item['status_entrega'] === 'entregue');

                // Se entregue, fica opaco
                $cardStyle = $isEntregue ? 'opacity: 0.6; background: #f8f9fa; border-top-color: #95a5a6;' : '';
                ?>

                <div class="card-entrega <?= $statusClass ?>" id="card-<?= $item['id'] ?>" style="<?= $cardStyle ?>">
                    <div class="card-body">
                        <div class="card-header-flex">
                            <div class="time-badge">
                                <i class="far fa-clock"></i> <?= $hora ?>
                            </div>
                            <div>
                                <?php if ($isEntregue): ?>
                                    <span class="status-badge bg-entregue"><i class="fas fa-check"></i> Entregue</span>
                                <?php else: ?>
                                    <span class="status-badge <?= $bgClass ?>"><?= strtoupper($item['status_pagamento']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <h3 class="client-name"><?= htmlspecialchars($item['cliente_nome']) ?> <small
                                style="font-size:0.8rem; color:#999;">#<?= $item['id'] ?></small></h3>

                        <div class="address-text">
                            <div style="min-width:20px; text-align:center;"><i class="fas fa-map-marker-alt"
                                    style="color:var(--danger)"></i></div>
                            <div><?= htmlspecialchars($item['endereco_entrega']) ?></div>
                        </div>

                        <?php if (!empty($item['observacoes'])): ?>
                            <div class="obs-box">
                                <i class="fas fa-info-circle"></i> <?= htmlspecialchars($item['observacoes']) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!$isEntregue): ?>
                            <button onclick="abrirConfirmacao(<?= $item['id'] ?>)" class="btn-confirm">
                                <i class="fas fa-check-double"></i> Confirmar Baixa
                            </button>
                        <?php endif; ?>
                    </div>

                    <div class="card-footer">
                        <div class="action-buttons">
                            <?php if ($zap): ?>
                                <a href="https://wa.me/55<?= $zap ?>?text=Olá, seu pedido está saindo para entrega!" target="_blank"
                                    class="btn-circle btn-wpp" title="WhatsApp">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                            <?php endif; ?>
                            <a href="https://www.google.com/maps/search/?api=1&query=<?= $enderecoUrl ?>" target="_blank"
                                class="btn-circle btn-maps" title="GPS">
                                <i class="fas fa-map-marked-alt"></i>
                            </a>
                            <a href="<?= BASE_URL ?>admin/imprimir_pedido?id=<?= $item['id'] ?>" target="_blank"
                                class="btn-circle btn-print" title="Imprimir">
                                <i class="fas fa-receipt"></i>
                            </a>
                        </div>

                        <div class="price-info">
                            <div class="price-total">R$ <?= number_format($item['total'], 2, ',', '.') ?></div>
                            <?php if ($item['status_pagamento'] != 'pago'): ?>
                                <div class="price-debt">Cobrar: R$
                                    <?= number_format($item['total'] - $item['valor_pago'], 2, ',', '.') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>
</div>

<script>
    let idVendaParaBaixar = 0;

    // --- FUNÇÕES DO MODAL ---
    function abrirConfirmacao(id) {
        idVendaParaBaixar = id;
        document.getElementById('modalConfirmacao').style.display = 'flex';
    }

    function fecharModal() {
        document.getElementById('modalConfirmacao').style.display = 'none';
        idVendaParaBaixar = 0;
    }

    // --- FUNÇÕES DO TOAST ---
    function showToast(msg, type = 'success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;

        let icon = type === 'success' ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times"></i>';

        toast.innerHTML = `${icon} <span>${msg}</span>`;
        container.appendChild(toast);

        // Remove após 3s
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // --- LÓGICA DE BAIXA ---
    function procederBaixa() {
        if (!idVendaParaBaixar) return;

        const formData = new FormData();
        formData.append('id', idVendaParaBaixar);

        fetch('<?= BASE_URL ?>admin/confirmar_entrega', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.sucesso) {
                    // Atualiza UI
                    const card = document.getElementById('card-' + idVendaParaBaixar);

                    // Remove botão
                    const btn = card.querySelector('.btn-confirm');
                    if (btn) btn.remove();

                    // Muda estilo
                    card.style.opacity = '0.6';
                    card.style.backgroundColor = '#f8f9fa';
                    card.style.borderTopColor = '#95a5a6';

                    // Adiciona Badge
                    const header = card.querySelector('.card-header-flex div:last-child');
                    header.innerHTML = '<span class="status-badge bg-entregue"><i class="fas fa-check"></i> Entregue</span>';

                    showToast("Entrega confirmada com sucesso!");
                    fecharModal();
                } else {
                    showToast("Erro: " + data.msg, "error");
                    fecharModal();
                }
            })
            .catch(err => {
                console.error(err);
                showToast("Erro de conexão.", "error");
                fecharModal();
            });
    }
</script>