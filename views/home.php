<?php
require __DIR__ . '/layout/header.php';
?>

<style>
    /* --- ESTILOS DO PDV EXISTENTES --- */
    .container {
        height: calc(100vh - 60px);
        display: flex;
        gap: 20px;
        padding: 20px;
        background: #f4f6f9;
    }

    .painel-esquerdo {
        flex: 2;
        display: flex;
        flex-direction: column;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        padding: 15px;
    }

    .painel-direito {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .lista-produtos-container {
        flex: 1;
        overflow-y: auto;
        border: 1px solid #eee;
        margin-bottom: 15px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        background: #3498db;
        color: white;
        padding: 10px;
        position: sticky;
        top: 0;
    }

    td {
        padding: 8px;
        border-bottom: 1px solid #eee;
    }

    .input-area {
        display: flex;
        gap: 10px;
    }

    .input-area input {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 1rem;
    }

    #codigo {
        flex: 1;
    }

    .box-total {
        background: #2c3e50;
        color: white;
        padding: 20px;
        border-radius: 8px;
        text-align: right;
    }

    #valor-total {
        font-size: 2.5rem;
        font-weight: bold;
    }

    .status-caixa {
        background: white;
        padding: 15px;
        border-radius: 8px;
        flex: 1;
    }

    #ultimo-item span {
        display: block;
        font-size: 1.2rem;
        font-weight: bold;
        color: #27ae60;
        margin-top: 5px;
    }

    #btn-finalizar {
        background: #27ae60;
        color: white;
        border: none;
        padding: 20px;
        font-size: 1.5rem;
        border-radius: 8px;
        cursor: pointer;
        transition: 0.2s;
    }

    #btn-finalizar:hover {
        background: #219150;
    }

    /* Modais */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        display: none;
        justify-content: center;
        align-items: center;
    }

    .modal-box {
        background: white;
        width: 90%;
        max-width: 600px;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .item-endereco {
        padding: 10px;
        border: 1px solid #eee;
        margin-bottom: 5px;
        cursor: pointer;
        border-radius: 4px;
    }

    .item-endereco:hover {
        background: #f9f9f9;
        border-color: #ccc;
    }

    .item-endereco input {
        margin-right: 10px;
    }

    /* --- NOVO: ESTILO DOS TOASTS (NOTIFICAÇÕES) --- */
    #toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 99999;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .toast {
        min-width: 250px;
        background: #333;
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        font-family: 'Segoe UI', sans-serif;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 12px;
        opacity: 0;
        transform: translateX(100%);
        animation: slideIn 0.3s forwards;
    }

    .toast.hide {
        animation: slideOut 0.3s forwards;
    }

    /* Cores por tipo */
    .toast.error {
        background: #e74c3c;
        border-left: 5px solid #c0392b;
    }

    .toast.success {
        background: #27ae60;
        border-left: 5px solid #2ecc71;
    }

    .toast.warning {
        background: #f39c12;
        border-left: 5px solid #d35400;
    }

    @keyframes slideIn {
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slideOut {
        to {
            opacity: 0;
            transform: translateX(100%);
        }
    }

    /* --- IMPRESSÃO DO CUPOM --- */
    @media print {

        /* Esconde tudo do sistema normal */
        body * {
            visibility: hidden;
        }

        /* Torna o cupom visível */
        #area-impressao,
        #area-impressao * {
            visibility: visible;
        }

        /* O SEGREDO ESTÁ AQUI: Força o display block para a impressora ler o tamanho */
        #area-impressao {
            display: block !important;
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        /* Opcional: Tira margens do navegador para impressora térmica de 58mm ou 80mm */
        @page {
            margin: 0;
        }
    }

    /* Esconde o cupom na tela normal do sistema */
    #area-impressao {
        display: none;
    }
</style>

<div id="toast-container"></div>

<div class="container">
    <div class="painel-esquerdo">
        <div class="lista-produtos-container">
            <table id="tabela-produtos">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th>Produto</th>
                        <th width="10%">Qtd</th>
                        <th width="15%">Unit.</th>
                        <th width="15%">Total</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <div class="input-area">
            <button id="btn-buscar" onclick="abrirModalPesquisa()" title="Pesquisar Produto (F4)"
                style="padding: 0 15px; cursor: pointer;">
                <i class="fas fa-search"></i>
            </button>

            <input type="number" id="qtd-input" value="1" min="1" placeholder="Qtd" title="Quantidade"
                style="width: 80px; text-align: center;">

            <input type="text" id="codigo" placeholder="Código de barras... (Ex: 123 ou 5*123)" autofocus
                autocomplete="off">

            <button id="btn-adicionar"
                style="padding: 0 20px; background: #3498db; color: white; border: none; cursor: pointer;">
                <i class="fas fa-plus"></i>
            </button>
        </div>
        <p class="instrucao" style="margin-top: 5px; font-size: 0.8rem; color: #666;">
            <i class="fas fa-keyboard"></i> F2: Finalizar | F4: Pesquisar | Dica: Digite <b>5*Código</b> para
            multiplicar
        </p>
    </div>

    <div class="painel-direito">
        <div class="box-total">
            <small>Total a Pagar</small>
            <div id="valor-total">R$ 0,00</div>
        </div>

        <div class="status-caixa">
            <div id="ultimo-item">
                <small>Último Item Adicionado</small>
                <span>...</span>
            </div>
        </div>

        <button id="btn-finalizar" onclick="abrirModalPagamento()">
            <i class="fas fa-check-circle"></i> Finalizar (F2)
        </button>
    </div>
</div>

<div id="modal-pesquisa" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header" style="display:flex; justify-content:space-between; margin-bottom:10px;">
            <h3>Pesquisar Produto</h3>
            <button class="btn-fechar" onclick="fecharModais()"
                style="cursor:pointer; border:none; background:none; font-size:1.5rem;">&times;</button>
        </div>
        <input type="text" id="input-pesquisa" placeholder="Digite o nome do produto..."
            style="width: 100%; padding: 10px; box-sizing: border-box; font-size:1rem;">
        <div id="lista-pesquisa" style="margin-top:10px; max-height:300px; overflow-y:auto;"></div>
    </div>
</div>

<div id="modal-pagamento" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header" style="display:flex; justify-content:space-between; margin-bottom:15px;">
            <h3><i class="fas fa-cash-register"></i> Finalizar Venda</h3>
            <button class="btn-fechar" onclick="fecharModais()"
                style="border:none; background:none; font-size:1.5rem; cursor:pointer;">&times;</button>
        </div>

        <div class="form-pagamento">

            <label>Cliente:</label>
            <div style="position: relative; margin-bottom: 15px;">
                <input type="text" id="input-busca-cliente"
                    placeholder="Digite nome ou CPF (Deixe vazio para Consumidor)"
                    style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">

                <input type="hidden" id="id-cliente-selecionado">

                <div id="lista-clientes-resultado"
                    style="position: absolute; width: 100%; background: white; border: 1px solid #ddd; max-height: 150px; overflow-y: auto; display: none; z-index: 100;">
                </div>
            </div>

            <div
                style="background: #f8f9fa; padding: 10px; border-radius: 5px; margin-bottom: 15px; border: 1px dashed #ccc;">

                <div style="display:flex; justify-content:space-around; margin-bottom:10px;">
                    <label style="cursor: pointer;">
                        <input type="radio" name="tipo_entrega_main" value="retirada" checked
                            onchange="mudarTipoEntrega('retirada')">
                        🏢 Retirada
                    </label>
                    <label style="cursor: pointer;">
                        <input type="radio" name="tipo_entrega_main" value="entrega"
                            onchange="mudarTipoEntrega('entrega')">
                        🛵 Entrega
                    </label>
                </div>

                <div id="resumo-endereco" style="display:none; border-top: 1px solid #ddd; padding-top: 10px;">

                    <div style="text-align:center; margin-bottom:10px;">
                        <small style="color:#777;">Endereço:</small><br>
                        <strong><span id="texto-endereco-final">Nenhum selecionado</span></strong>
                        <br>
                        <button onclick="abrirModalEndereco()"
                            style="background:none; border:none; color:blue; text-decoration:underline; cursor:pointer; font-size:0.8rem;">
                            Alterar
                        </button>
                    </div>

                    <div style="display:flex; gap:10px;">
                        <div style="flex:1;">
                            <label style="font-size:0.85rem;">Data Entrega:</label>
                            <input type="date" id="data-entrega" class="form-control"
                                style="width:100%; padding:5px; font-size:0.9rem;">
                        </div>
                        <div style="flex:1;">
                            <label style="font-size:0.85rem;">Hora (Opcional):</label>
                            <input type="time" id="hora-entrega" class="form-control"
                                style="width:100%; padding:5px; font-size:0.9rem;">
                        </div>
                    </div>
                </div>
            </div>

            <hr style="margin: 15px 0; border: 0; border-top: 1px solid #eee;">

            <div style="display:flex; gap: 10px;">
                <div style="flex:1;">
                    <label>Forma Pagamento:</label>
                    <select id="forma-pagamento"
                        style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                        <option value="Dinheiro">Dinheiro</option>
                        <option value="Pix">Pix</option>
                        <option value="Cartão de Crédito">Cartão de Crédito</option>
                        <option value="Cartão de Débito">Cartão de Débito</option>
                    </select>
                </div>
                <div style="flex:1; text-align:right;">
                    <label>Total:</label>
                    <div id="pagamento-total" style="font-size: 1.4rem; font-weight: bold; color: #27ae60;">R$ 0,00
                    </div>
                </div>
            </div>

            <div id="area-dinheiro" style="margin-top: 15px; background: #fff8e1; padding: 10px; border-radius: 5px;">
                <div style="display:flex; gap: 10px; align-items:center;">
                    <div style="flex:1;">
                        <label>Recebido (R$):</label>
                        <input type="number" id="valor-recebido" placeholder="0,00" step="0.01"
                            style="width: 100%; padding: 8px;">
                    </div>
                    <div style="flex:1;">
                        <label>Troco:</label>
                        <div id="valor-troco" style="font-weight:bold; font-size:1.1rem; color:#d35400;">R$ 0,00</div>
                    </div>
                </div>
            </div>

            <button onclick="confirmarVenda()"
                style="width: 100%; margin-top: 20px; padding: 15px; background: #27ae60; color: white; border: none; border-radius: 5px; font-size: 1.1rem; cursor: pointer; font-weight:bold;">
                <i class="fas fa-check"></i> Finalizar Venda
            </button>
        </div>
    </div>
</div>

<div id="modalEntrega" class="modal-overlay" style="z-index: 1100;">
    <div class="modal-box" style="max-width: 500px;">
        <div class="modal-header">
            <h3><i class="fas fa-map-marker-alt"></i> Selecionar Endereço</h3>
        </div>
        <div class="modal-body">

            <div id="listaEnderecosSalvos" style="max-height: 200px; overflow-y: auto; margin-bottom: 15px;">
                <p style="padding:10px; color:#777;">Carregando endereços...</p>
            </div>

            <label style="display:block; margin-top:10px; color: #2980b9; cursor:pointer; font-weight:bold;">
                <input type="radio" name="endereco_selecionado" value="novo" id="radioNovoEnd"
                    onchange="mostrarFormNovo(true)">
                + Cadastrar Novo Endereço
            </label>

            <div id="formNovoEndereco"
                style="display:none; background: #f0f4f8; padding: 15px; border-radius: 5px; margin-top: 10px;">
                <input type="text" id="endRua" placeholder="Logradouro (Rua, Av...)"
                    style="width:100%; margin-bottom:10px; padding:8px; border:1px solid #ccc;">
                <div style="display:flex; gap:10px; margin-bottom:10px;">
                    <input type="text" id="endNum" placeholder="Número"
                        style="width:30%; padding:8px; border:1px solid #ccc;">
                    <input type="text" id="endBairro" placeholder="Bairro"
                        style="width:70%; padding:8px; border:1px solid #ccc;">
                </div>
                <input type="text" id="endCidade" placeholder="Cidade"
                    style="width:100%; padding:8px; border:1px solid #ccc;">
            </div>

        </div>
        <div class="modal-footer"
            style="margin-top: 20px; text-align: right; display:flex; justify-content:space-between;">
            <button onclick="fecharModalEndereco()"
                style="padding: 10px 20px; border: 1px solid #ccc; background: white; cursor: pointer; border-radius: 4px;">Voltar</button>
            <button onclick="confirmarEndereco()"
                style="padding: 10px 20px; border: none; background: #3498db; color: white; cursor: pointer; border-radius: 4px; font-weight: bold;">Confirmar</button>
        </div>
    </div>
</div>

<div id="area-impressao">
    <div style="text-align:center; font-family: monospace;">
        <h3>Loja Kabyte</h3>
        <p>Rua das Flores, 123 - Centro<br>CNPJ: 00.000.000/0001-99</p>
        <hr>
        <p>Data: <span id="cupom-data"></span><br>Cliente: <span id="cupom-cliente">Consumidor</span></p>
        <hr>
        <table style="width:100%; font-size:0.9rem;">
            <tbody id="cupom-itens"></tbody>
        </table>
        <hr>
        <div style="text-align:right;">
            <h3>TOTAL: <span id="cupom-total"></span></h3>
            <p>Pgto: <span id="cupom-recebido"></span></p>
            <p style="display:none;"><span>Troco:</span> <span id="cupom-troco"></span></p>
        </div>
        <hr>
        <p>Obrigado pela preferência!</p>
    </div>
</div>

<script src="<?= BASE_URL ?>public/js/script.js"></script>

<script>
    // Variáveis Globais de Entrega
    let tipoEntregaAtual = 'retirada';
    let enderecoTextoAtual = '';

    // Função chamada pelos Radios principais
    function mudarTipoEntrega(tipo) {
        tipoEntregaAtual = tipo;
        const resumo = document.getElementById('resumo-endereco');

        if (tipo === 'entrega') {
            resumo.style.display = 'block';

            // Se ainda não tem endereço, abre o modal pra escolher
            if (enderecoTextoAtual === '') {
                abrirModalEndereco();
            }
        } else {
            resumo.style.display = 'none';
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        const hoje = new Date().toISOString().split('T')[0];
        const campoData = document.getElementById('data-entrega');
        if (campoData) campoData.value = hoje;
    });

    // Abre o modal de endereços
    function abrirModalEndereco() {
        const idCliente = document.getElementById('id-cliente-selecionado').value;

        if (!idCliente) {
            showToast("Selecione um cliente primeiro para definir a entrega.", "warning");
            document.querySelector('input[value="retirada"]').checked = true;
            mudarTipoEntrega('retirada');
            return;
        }

        document.getElementById('modalEntrega').style.display = 'flex';
        carregarEnderecosCliente(idCliente);
    }

    function fecharModalEndereco() {
        document.getElementById('modalEntrega').style.display = 'none';

        // Se fechou sem escolher e estava como entrega, volta pra retirada
        if (enderecoTextoAtual === '' && tipoEntregaAtual === 'entrega') {
            document.querySelector('input[value="retirada"]').checked = true;
            mudarTipoEntrega('retirada');
        }
    }

    // --- LÓGICA DE BUSCAR OS ENDEREÇOS DO CLIENTE NO BANCO ---
    async function carregarEnderecosCliente(idCliente) {
        const listaDiv = document.getElementById('listaEnderecosSalvos');
        listaDiv.innerHTML = '<p style="padding:10px; color:#777;"><i class="fas fa-spinner fa-spin"></i> Buscando endereços...</p>';

        try {
            const res = await fetch(`${BASE_URL}api/enderecos_cliente?id=${idCliente}`);
            const dados = await res.json();

            if (dados.sucesso && dados.enderecos.length > 0) {
                let html = '';
                dados.enderecos.forEach((end, index) => {
                    // Formata o texto bonito para exibir
                    let textoEndereco = `${end.rua}, ${end.numero} - ${end.bairro} (${end.cidade}/${end.estado})`;
                    if (end.complemento) textoEndereco += ` | Comp: ${end.complemento}`;

                    // Marca o endereço padrão automaticamente
                    let checked = (end.is_padrao == 1 || index === 0) ? 'checked' : '';
                    let badge = (end.is_padrao == 1) ? '<span style="color:#3498db; font-size:0.8rem; font-weight:bold;">(Principal)</span>' : '';

                    html += `
                        <label class="item-endereco" style="display:block; margin-bottom:8px;" onclick="mostrarFormNovo(false)">
                            <input type="radio" name="endereco_selecionado" value="${end.id}" data-texto="${textoEndereco}" ${checked}>
                            ${textoEndereco} ${badge}
                        </label>
                    `;
                });
                listaDiv.innerHTML = html;
            } else {
                listaDiv.innerHTML = '<p style="padding:10px; color:#e74c3c;">Nenhum endereço cadastrado para este cliente.</p>';
                // Se não tem endereço, já abre o form de novo
                document.getElementById('radioNovoEnd').checked = true;
                mostrarFormNovo(true);
            }
        } catch (e) {
            listaDiv.innerHTML = '<p style="padding:10px; color:#e74c3c;">Erro ao buscar endereços. Verifique a conexão.</p>';
        }
    }

    function mostrarFormNovo(mostrar) {
        document.getElementById('formNovoEndereco').style.display = mostrar ? 'block' : 'none';
    }

    // Confirmar a escolha do endereço no modal
    function confirmarEndereco() {
        const opcao = document.querySelector('input[name="endereco_selecionado"]:checked');

        if (!opcao) {
            showToast("Selecione uma opção de entrega.", "warning");
            return;
        }

        // Se o motoboy/caixa digitou um endereço novo ali na hora
        if (opcao.value === 'novo') {
            const rua = document.getElementById('endRua').value;
            const num = document.getElementById('endNum').value;
            const bairro = document.getElementById('endBairro').value;
            const cidade = document.getElementById('endCidade').value;

            if (!rua || !num || !bairro) {
                showToast("Preencha rua, número e bairro.", "error");
                return;
            }
            enderecoTextoAtual = `${rua}, ${num} - ${bairro} (${cidade})`;
        } else {
            // Se ele selecionou um endereço da lista do banco
            enderecoTextoAtual = opcao.getAttribute('data-texto');
        }

        // Atualiza o texto no modal principal
        document.getElementById('texto-endereco-final').innerText = enderecoTextoAtual;

        // Fecha modal
        document.getElementById('modalEntrega').style.display = 'none';
        showToast("Endereço definido!", "success");
    }


</script>