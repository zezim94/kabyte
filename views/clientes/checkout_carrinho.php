<?php require __DIR__ . '/../layout/header_public.php'; ?>
<script src="https://sdk.mercadopago.com/js/v2"></script>

<style>
    .checkout-container {
        max-width: 900px;
        margin: 30px auto;
        padding: 20px;
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 30px;
    }

    .box {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
    }

    .box h3 {
        margin-top: 0;
        color: #2c3e50;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }

    /* Lista de Produtos */
    .item-resumo {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px dashed #eee;
    }

    .item-resumo img {
        width: 50px;
        height: 50px;
        object-fit: contain;
        margin-right: 10px;
    }

    /* Toggle Entrega */
    .entrega-options {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
    }

    .entrega-option {
        flex: 1;
        border: 1px solid #ddd;
        padding: 10px;
        border-radius: 6px;
        cursor: pointer;
        text-align: center;
        font-weight: bold;
    }

    .entrega-option.selected {
        border-color: #27ae60;
        background: #e8f8f5;
        color: #27ae60;
    }

    /* Form Endereço */
    .endereco-form {
        display: none;
        margin-top: 15px;
    }

    .form-row {
        display: flex;
        gap: 10px;
        margin-bottom: 10px;
    }

    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    /* Responsivo */
    @media(max-width:768px) {
        .checkout-container {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="checkout-container">

    <div class="col-left">

        <div class="box">
            <h3><i class="fas fa-truck"></i> Entrega ou Retirada?</h3>

            <div class="entrega-options">
                <div class="entrega-option selected" id="optRetirada" onclick="selecionarEntrega('retirada')">
                    <i class="fas fa-store"></i> Retirar na Loja
                </div>
                <div class="entrega-option" id="optEntrega" onclick="selecionarEntrega('entrega')">
                    <i class="fas fa-motorcycle"></i> Receber em Casa
                </div>
            </div>

            <div id="divEndereco" class="endereco-form">
                <p><strong>Endereço de Entrega:</strong></p>

                <label
                    style="display:block; margin-bottom:10px; cursor:pointer; background: #f9f9f9; padding: 10px; border-radius: 5px;">
                    <input type="radio" name="tipo_end" value="salvo" checked onclick="toggleFormEndereco(false)">
                    <b>Usar endereço do cadastro:</b><br>
                    <small style="color:#555; margin-left: 20px; display: block; margin-top: 5px;">
                        <?php
                        // Verifica se tem endereço, senão mostra aviso
                        if (!empty($cliente['rua'])) {
                            echo htmlspecialchars($cliente['rua']) . ', ' .
                                htmlspecialchars($cliente['numero']) . ' - ' .
                                htmlspecialchars($cliente['bairro']) . ' (' .
                                htmlspecialchars($cliente['cidade']) . ')';
                        } else {
                            echo '<span style="color:red">Nenhum endereço cadastrado. Preencha abaixo.</span>';
                        }
                        ?>
                    </small>
                </label>

                <label
                    style="display:block; margin-bottom:10px; cursor:pointer; background: #f9f9f9; padding: 10px; border-radius: 5px;">
                    <input type="radio" name="tipo_end" value="novo" id="radioNovo" onclick="toggleFormEndereco(true)">
                    <b>Entregar em outro endereço</b>
                </label>

                <div id="formNovoEndereco"
                    style="display:none; border:1px solid #eee; padding:15px; border-radius:5px; margin-top:10px; background: #fff;">
                    <div class="form-row">
                        <input type="text" id="cep" class="form-control" placeholder="CEP" style="width: 30%;">
                        <input type="text" id="cidade" class="form-control" placeholder="Cidade" style="width: 70%;">
                    </div>
                    <div class="form-row">
                        <input type="text" id="rua" class="form-control" placeholder="Rua / Avenida" style="flex:2">
                        <input type="text" id="numero" class="form-control" placeholder="Nº" style="flex:1">
                    </div>
                    <input type="text" id="bairro" class="form-control" placeholder="Bairro">
                </div>

                <div style="margin-top: 20px; padding-top: 15px; border-top: 1px dashed #ccc;">
                    <p style="margin-bottom: 5px;"><strong><i class="far fa-calendar-alt"></i> Agendar Entrega:</strong>
                    </p>
                    <div class="form-row">
                        <div style="flex:1">
                            <label style="font-size: 0.85rem; color: #666;">Data:</label>
                            <input type="date" id="data_entrega" class="form-control">
                        </div>
                        <div style="flex:1">
                            <label style="font-size: 0.85rem; color: #666;">Hora Aprox. (Opcional):</label>
                            <input type="time" id="hora_entrega" class="form-control">
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="box">
            <h3><i class="fas fa-credit-card"></i> Pagamento</h3>
            <div id="paymentBrick_container"></div>
        </div>
    </div>

    <div class="col-right">
        <div class="box">
            <h3>Resumo do Pedido</h3>
            <?php if (!empty($carrinho['itens'])): ?>
                <?php foreach ($carrinho['itens'] as $item): ?>
                    <div class="item-resumo">
                        <div style="display:flex; align-items:center;">
                            <span style="font-weight:bold; margin-right:5px;"><?= $item['quantidade'] ?>x</span>
                            <?= htmlspecialchars($item['nome']) ?>
                        </div>
                        <div>R$ <?= number_format($item['total'], 2, ',', '.') ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <hr>
            <div style="display:flex; justify-content:space-between; font-size:1.2rem; font-weight:bold;">
                <span>Total:</span>
                <span>R$ <?= number_format($carrinho['total'] ?? 0, 2, ',', '.') ?></span>
            </div>
        </div>
    </div>
</div>

<script>
    // --- 1. CONFIGURAÇÃO INICIAL ---
    let tipoEntrega = 'retirada';
    let usarEnderecoNovo = false;

    // Se o cliente não tiver endereço salvo, forçamos "Novo Endereço"
    const temEnderecoSalvo = <?= !empty($cliente['rua']) ? 'true' : 'false' ?>;

    function selecionarEntrega(tipo) {
        tipoEntrega = tipo;

        // Visual dos botões
        document.getElementById('optRetirada').className = 'entrega-option ' + (tipo === 'retirada' ? 'selected' : '');
        document.getElementById('optEntrega').className = 'entrega-option ' + (tipo === 'entrega' ? 'selected' : '');

        // Mostrar/Esconder área de endereço
        const divEnd = document.getElementById('divEndereco');
        divEnd.style.display = (tipo === 'entrega' ? 'block' : 'none');

        // Se escolheu entrega e não tem endereço salvo, já abre o form de novo
        if (tipo === 'entrega' && !temEnderecoSalvo) {
            document.getElementById('radioNovo').checked = true;
            toggleFormEndereco(true);
        }
    }

    function toggleFormEndereco(isNovo) {
        usarEnderecoNovo = isNovo;
        document.getElementById('formNovoEndereco').style.display = (isNovo ? 'block' : 'none');
    }

    // Configurar Data Mínima (Hoje)
    document.addEventListener("DOMContentLoaded", function () {
        const hoje = new Date().toISOString().split('T')[0];
        const dataInput = document.getElementById('data_entrega');
        if (dataInput) {
            dataInput.setAttribute('min', hoje);
            dataInput.value = hoje;
        }
    });

    // --- 2. MERCADO PAGO ---
    const mp = new MercadoPago("<?= MP_PUBLIC_KEY ?>", { locale: 'pt-BR' });
    const bricksBuilder = mp.bricks();

    const renderPaymentBrick = async (bricksBuilder) => {
        const settings = {
            initialization: {
                amount: <?= $carrinho['total'] ?? 0 ?>,
                preferenceId: null,
            },
            customization: {
                paymentMethods: {
                    ticket: "all",
                    bankTransfer: "all",
                    creditCard: "all",
                    debitCard: "all",
                },
            },
            callbacks: {
                onReady: () => { },
                onSubmit: ({ selectedPaymentMethod, formData }) => {
                    return new Promise((resolve, reject) => {

                        // 1. CAPTURA E VALIDAÇÃO
                        let enderecoFinal = '';
                        let dataEntregaFinal = null;

                        if (tipoEntrega === 'entrega') {

                            // Validação Endereço
                            if (usarEnderecoNovo) {
                                const rua = document.getElementById('rua').value;
                                const num = document.getElementById('numero').value;
                                const bairro = document.getElementById('bairro').value;
                                const cidade = document.getElementById('cidade').value;

                                if (!rua || !num) {
                                    alert("Preencha o endereço completo!");
                                    reject(); return;
                                }
                                enderecoFinal = `${rua}, ${num} - ${bairro}, ${cidade}`;
                            } else {
                                if (!temEnderecoSalvo) {
                                    alert("Você não tem endereço salvo. Selecione 'Entregar em outro endereço'.");
                                    reject(); return;
                                }
                                // PHP injeta o endereço aqui com segurança
                                enderecoFinal = "<?= !empty($cliente['rua']) ? $cliente['rua'] . ', ' . $cliente['numero'] . ' - ' . $cliente['bairro'] : '' ?>";
                            }

                            // Validação Data
                            const dataInput = document.getElementById('data_entrega').value;
                            const horaInput = document.getElementById('hora_entrega').value;

                            if (!dataInput) {
                                alert("Selecione a Data de Entrega.");
                                reject(); return;
                            }
                            const horaFinal = horaInput ? horaInput + ":00" : "12:00:00";
                            dataEntregaFinal = `${dataInput} ${horaFinal}`;

                        } else {
                            enderecoFinal = 'Retirada na Loja';
                            dataEntregaFinal = null;
                        }

                        // 2. ENVIO
                        const dadosExtras = {
                            tipo_entrega: tipoEntrega,
                            endereco_entrega_texto: enderecoFinal,
                            data_entrega: dataEntregaFinal
                        };

                        fetch("<?= BASE_URL ?>carrinho/processar_pagamento", {
                            method: "POST",
                            headers: { "Content-Type": "application/json" },
                            body: JSON.stringify({ formData, dadosExtras })
                        })
                            .then(res => res.json())
                            .then(res => {
                                if (res.sucesso) {
                                    localStorage.removeItem('meu_carrinho_pdv');
                                    window.location.href = "<?= BASE_URL ?>cliente/pedido_confirmado?id=" + res.venda_id;
                                    resolve();
                                } else {
                                    alert("Erro: " + res.msg);
                                    reject();
                                }
                            })
                            .catch(err => {
                                console.error(err);
                                alert("Erro de comunicação.");
                                reject();
                            });
                    });
                },
                onError: (error) => { console.error(error); },
            },
        };
        window.paymentBrickController = await bricksBuilder.create("payment", "paymentBrick_container", settings);
    };

    renderPaymentBrick(bricksBuilder);
</script>

<?php require __DIR__ . '/../layout/footer_public.php'; ?>