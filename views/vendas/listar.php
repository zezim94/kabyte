<?php require __DIR__ . '/../layout/header_admin.php'; ?>

<style>
    .admin-container {
        padding: 20px;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.6);
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background-color: #fff;
        margin: 2% auto;
        padding: 0;
        border-radius: 8px;
        width: 90%;
        max-width: 800px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        position: relative;
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            top: -50px;
            opacity: 0;
        }

        to {
            top: 0;
            opacity: 1;
        }
    }

    .modal-header {
        background: #2c3e50;
        color: white;
        padding: 15px 20px;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .close-modal {
        color: white;
        font-size: 28px;
        cursor: pointer;
        font-weight: bold;
    }

    .close-modal:hover {
        color: #ccc;
    }

    .modal-body {
        padding: 20px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .modal-footer {
        padding: 15px;
        background: #f9f9f9;
        border-bottom-left-radius: 8px;
        border-bottom-right-radius: 8px;
    }

    .info-box {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 6px;
        border-left: 4px solid #3498db;
    }

    .info-title {
        font-weight: bold;
        color: #555;
        margin-bottom: 5px;
        display: block;
    }

    .info-value {
        color: #333;
        font-size: 1.1em;
    }

    /* Tabela de Itens no Modal */
    .table-itens {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .table-itens th {
        background: #eee;
        text-align: left;
        padding: 10px;
        border-bottom: 2px solid #ddd;
    }

    .table-itens td {
        padding: 10px;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
    }

    .produto-img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #ddd;
    }

    .status-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.85em;
        font-weight: bold;
        text-transform: uppercase;
    }

    .status-pago {
        background: #d4edda;
        color: #155724;
    }

    .status-pendente {
        background: #f8d7da;
        color: #721c24;
    }

    .status-parcial {
        background: #fff3cd;
        color: #856404;
    }
</style>

<div class="admin-container">
    <div style="margin-bottom: 20px;">
        <h2 style="color: #2c3e50;"><i class="fas fa-file-invoice-dollar"></i> Histórico de Vendas</h2>
    </div>

    <form method="GET" action=""
        style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 20px;">
        <div
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; align-items: end;">

            <div>
                <label style="font-weight:bold; font-size:0.9em;">De:</label>
                <input type="date" name="data_inicio" class="form-control"
                    value="<?= $_GET['data_inicio'] ?? date('Y-m-01') ?>"
                    style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
            </div>

            <div>
                <label style="font-weight:bold; font-size:0.9em;">Até:</label>
                <input type="date" name="data_fim" class="form-control"
                    value="<?= $_GET['data_fim'] ?? date('Y-m-t') ?>"
                    style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
            </div>

            <div>
                <label style="font-weight:bold; font-size:0.9em;">Cliente:</label>
                <input type="text" name="cliente" class="form-control" placeholder="Nome..."
                    value="<?= htmlspecialchars($_GET['cliente'] ?? '') ?>"
                    style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
            </div>

            <div>
                <label style="font-weight:bold; font-size:0.9em;">Status:</label>
                <select name="status" class="form-control"
                    style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
                    <option value="">Todos</option>
                    <option value="pago" <?= ($_GET['status'] ?? '') == 'pago' ? 'selected' : '' ?>>Pago</option>
                    <option value="pendente" <?= ($_GET['status'] ?? '') == 'pendente' ? 'selected' : '' ?>>Pendente
                    </option>
                    <option value="parcial" <?= ($_GET['status'] ?? '') == 'parcial' ? 'selected' : '' ?>>Parcial</option>
                </select>
            </div>

            <div>
                <button type="submit"
                    style="background:#3498db; color:white; border:none; padding:10px 20px; border-radius:4px; cursor:pointer; width:100%; font-weight:bold;">
                    <i class="fas fa-search"></i> Filtrar
                </button>
            </div>
        </div>
    </form>

    <div class="painel-tabela">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #34495e; color: white; text-align: left;">
                    <th style="padding: 12px;">#ID</th>
                    <th style="padding: 12px;">Data/Hora</th>
                    <th style="padding: 12px;">Cliente</th>
                    <th style="padding: 12px;">Vendedor</th>
                    <th style="padding: 12px;">Status</th>
                    <th style="padding: 12px;">Total</th>
                    <th style="padding: 12px; text-align: center;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vendas as $v): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 12px; color: #888;">#<?= str_pad($v['id'], 4, '0', STR_PAD_LEFT) ?></td>
                        <td style="padding: 12px;"><?= date('d/m/Y H:i', strtotime($v['data_venda'])) ?></td>
                        <td style="padding: 12px;">
                            <?php if ($v['cliente']): ?>
                                <span style="color: #2980b9; font-weight: bold;"><i class="fas fa-user"></i>
                                    <?= $v['cliente'] ?></span>
                            <?php else: ?>
                                <span style="color: #95a5a6; font-style: italic;">Consumidor Final</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 12px;"><?= $v['vendedor'] ?? 'Online' ?></td>
                        <td style="padding: 12px;">
                            <span class="status-badge status-<?= $v['status_pagamento'] ?>">
                                <?= $v['status_pagamento'] ?>
                            </span>
                        </td>
                        <td style="padding: 12px; font-weight: bold; color: #27ae60;">
                            R$ <?= number_format($v['total'], 2, ',', '.') ?>
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            <button onclick="abrirModalAdmin(<?= $v['id'] ?>)"
                                style="border: 1px solid #3498db; color: #3498db; background: white; padding: 5px 10px; cursor: pointer; border-radius: 4px; transition: 0.3s;">
                                <i class="fas fa-eye"></i> Detalhes
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalVendaAdmin" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Detalhes da Venda #<span id="mdlId"></span></h3>
            <span class="close-modal" onclick="fecharModalAdmin()">&times;</span>
        </div>

        <div id="modalLoading" style="padding:50px; text-align:center;">
            <i class="fas fa-spinner fa-spin fa-2x"></i><br>Carregando informações...
        </div>

        <div id="modalBodyContent" style="display:none;">
            <div class="modal-body">
                <div class="info-box">
                    <h4 style="margin-top:0; color:#2c3e50; border-bottom:1px solid #ddd; padding-bottom:5px;">Dados da
                        Venda</h4>
                    <span class="info-title">Vendedor:</span> <span class="info-value" id="mdlVendedor"></span><br>
                    <span class="info-title">Data:</span> <span class="info-value" id="mdlData"></span><br>
                    <span class="info-title">Forma Pagto:</span> <span class="info-value" id="mdlForma"></span><br>
                    <span class="info-title">Status:</span> <span id="mdlStatus"></span>
                </div>

                <div class="info-box" style="border-left-color: #e67e22;">
                    <h4 style="margin-top:0; color:#2c3e50; border-bottom:1px solid #ddd; padding-bottom:5px;">Dados do
                        Cliente</h4>
                    <span class="info-title">Nome:</span> <span class="info-value" id="mdlCliNome"></span><br>
                    <span class="info-title">E-mail:</span> <span class="info-value" id="mdlCliEmail"></span><br>
                    <span class="info-title">Cliente Desde:</span> <span class="info-value" id="mdlCliData"></span>
                </div>
            </div>

            <div style="padding: 0 20px 20px 20px;">
                <h4 style="color:#2c3e50;">Itens do Pedido</h4>
                <div id="mdlTabelaItens"></div>

                <div style="text-align: right; margin-top: 15px; font-size: 1.3em;">
                    Total: <strong style="color: #27ae60;" id="mdlTotal"></strong>
                </div>
            </div>

            <div class="info-box" style="border-left-color: #9b59b6; margin-top: 15px; grid-column: span 2;">
                <h4 style="margin-top:0; color:#2c3e50; border-bottom:1px solid #ddd; padding-bottom:5px;">
                    <i class="fas fa-truck"></i> Informações de Entrega
                </h4>
                <span class="info-title">Tipo:</span> <span class="info-value" id="mdlTipoEntrega"></span><br>

                <div id="mdlDetalhesEntrega" style="display:none;">
                    <span class="info-title">Endereço:</span> <span class="info-value" id="mdlEndereco"></span><br>
                    <span class="info-title">Data Agendada:</span> <span class="info-value" id="mdlDataEntrega"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const modalAdmin = document.getElementById('modalVendaAdmin');
    const loading = document.getElementById('modalLoading');
    const content = document.getElementById('modalBodyContent');

    function fecharModalAdmin() {
        modalAdmin.style.display = 'none';
    }

    // Fecha ao clicar fora
    window.onclick = function (e) {
        if (e.target == modalAdmin) fecharModalAdmin();
    }

    async function abrirModalAdmin(id) {
        modalAdmin.style.display = 'flex'; // Usando flex para centralizar
        loading.style.display = 'block';
        content.style.display = 'none';
        document.getElementById('mdlId').innerText = id;

        try {
            const res = await fetch('<?= BASE_URL ?>api/detalhes_admin?id=' + id);
            const dados = await res.json();

            if (dados.sucesso) {
                preencherModal(dados.venda, dados.itens);
                loading.style.display = 'none';
                content.style.display = 'block';
            } else {
                alert('Erro: ' + dados.msg);
                fecharModalAdmin();
            }
        } catch (error) {
            console.error(error);
            alert('Erro de conexão ao buscar detalhes.');
            fecharModalAdmin();
        }
    }

    function preencherModal(venda, itens) {
        // Preenche Venda
        document.getElementById('mdlVendedor').innerText = venda.vendedor_nome || 'Sistema';
        document.getElementById('mdlData').innerText = new Date(venda.data_venda).toLocaleString('pt-BR');
        document.getElementById('mdlForma').innerText = venda.forma_pagamento;
        document.getElementById('mdlTotal').innerText = 'R$ ' + parseFloat(venda.total).toFixed(2).replace('.', ',');

        // Preenche Status com cor
        const spanStatus = document.getElementById('mdlStatus');
        spanStatus.className = 'status-badge status-' + venda.status_pagamento;
        spanStatus.innerText = venda.status_pagamento.toUpperCase();

        const tipoEntrega = venda.tipo_entrega === 'entrega' ? 'Entrega em Domicílio' : 'Retirada na Loja';
        const spanTipo = document.getElementById('mdlTipoEntrega');
        spanTipo.innerText = tipoEntrega;

        // Estilização visual do tipo
        if (venda.tipo_entrega === 'entrega') {
            spanTipo.style.color = '#e67e22'; // Laranja
            document.getElementById('mdlDetalhesEntrega').style.display = 'block';

            document.getElementById('mdlEndereco').innerText = venda.endereco_entrega || 'Endereço não informado';

            // Formata data de entrega se existir
            let dataEntregaTexto = 'Não agendada';
            if (venda.data_entrega) {
                const dataObj = new Date(venda.data_entrega);
                dataEntregaTexto = dataObj.toLocaleDateString('pt-BR') + ' às ' + dataObj.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
            }
            document.getElementById('mdlDataEntrega').innerText = dataEntregaTexto;

        } else {
            spanTipo.style.color = '#27ae60'; // Verde
            document.getElementById('mdlDetalhesEntrega').style.display = 'none';
        }

        // Preenche Cliente
        if (venda.cliente_nome) {
            document.getElementById('mdlCliNome').innerText = venda.cliente_nome;
            document.getElementById('mdlCliEmail').innerText = venda.cliente_email;
            document.getElementById('mdlCliData').innerText = new Date(venda.cliente_desde).toLocaleDateString('pt-BR');
        } else {
            document.getElementById('mdlCliNome').innerText = 'Consumidor Final';
            document.getElementById('mdlCliEmail').innerText = '-';
            document.getElementById('mdlCliData').innerText = '-';
        }

        // Gera Tabela de Itens
        let html = `
            <table class="table-itens">
                <thead>
                    <tr>
                        <th style="width: 70px;">Img</th>
                        <th>Produto</th>
                        <th>Qtd</th>
                        <th>Preço Unit.</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>`;

        itens.forEach(item => {
            let subtotal = item.quantidade * item.preco_unitario;

            // Verifica se tem imagem, se não usa uma padrão cinza
            let imgUrl = item.imagem ? '<?= BASE_URL ?>public/uploads/' + item.imagem : 'https://via.placeholder.com/50?text=S/F';

            html += `
                <tr>
                    <td><img src="${imgUrl}" class="produto-img"></td>
                    <td>${item.produto_nome}</td>
                    <td>${item.quantidade}</td>
                    <td>R$ ${parseFloat(item.preco_unitario).toFixed(2).replace('.', ',')}</td>
                    <td><strong>R$ ${subtotal.toFixed(2).replace('.', ',')}</strong></td>
                </tr>`;
        });

        html += `</tbody></table>`;
        document.getElementById('mdlTabelaItens').innerHTML = html;
    }
</script>

</div>
</body>

</html>