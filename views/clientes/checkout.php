<?php
// 1. PREPARAÇÃO DOS DADOS (Blindagem)
// A variável que vem do Controller é $total, não $pendente.

// Valor: Força ser float com ponto (Ex: 150.50) para o JavaScript
$valorFinal = number_format((float) $total, 2, '.', '');

// Nome: Garante que tenha Primeiro e Último nome para o Mercado Pago
$nomeCompleto = trim($_SESSION['cliente_nome'] ?? 'Cliente PatyBike');
$partes = explode(' ', $nomeCompleto);
$primeiroNome = $partes[0];
$sobrenome = isset($partes[1]) ? end($partes) : 'Sobrenome';

// Email: Se não tiver na sessão, usa um de teste (o MP exige formato de email válido)
$emailCliente = $_SESSION['cliente_email'] ?? 'cliente@email.com';
if (!filter_var($emailCliente, FILTER_VALIDATE_EMAIL)) {
    $emailCliente = 'cliente_sem_email@patybike.com';
}

require __DIR__ . '/../layout/header_public.php';
?>

<div
    style="max-width:600px;margin:30px auto;padding:20px;background:#fff;border-radius:8px;box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
    <h3 style="color: #333; margin-bottom: 20px;">Finalizar Pagamento</h3>
    <p style="font-size: 1.1rem; color: #666;">Pedido #<?= $vendaId ?></p>

    <div
        style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #27ae60;">
        <p style="margin:0; font-size: 0.9rem; color: #555;">Valor Pendente:</p>
        <strong style="color:#27ae60;font-size:1.8rem">
            R$ <?= number_format($total, 2, ',', '.') ?>
        </strong>
    </div>

    <div id="paymentBrick_container"></div>
</div>

<script src="https://sdk.mercadopago.com/js/v2"></script>

<script>
    const mp = new MercadoPago("<?= MP_PUBLIC_KEY ?>", {
        locale: "pt-BR",
        advancedFraudPrevention: false
    });

    // Passando as variáveis do PHP para o JS
    const totalPedido = <?= json_encode((float) $total) ?>;
    const vendaId = <?= json_encode((int) $vendaId) ?>;
    const userEmail = "<?= $emailCliente ?>";

    const bricksBuilder = mp.bricks();

    const renderPaymentBrick = async (bricksBuilder) => {
        const settings = {
            initialization: {
                amount: totalPedido,
                payer: {
                    email: userEmail,
                    // Garante que é Pessoa Física para evitar warnings no console
                    entityType: "individual",
                },
            },
            customization: {
                visual: {
                    style: {
                        theme: "bootstrap", // bootstrap, dark, default, flat
                    },
                },
                paymentMethods: {
                    creditCard: "all",
                    debitCard: "all",
                    ticket: "all",
                    bankTransfer: "all",
                },
            },
            callbacks: {
                onReady: () => {
                    console.log("✅ Brick pronto");
                },
                onSubmit: async (formData) => {
                    // callback chamado ao clicar no botão de compra
                    console.log("📦 Enviando para o PHP...", formData);

                    return new Promise((resolve, reject) => {
                        fetch("<?= BASE_URL ?>cliente/processar_pagamento", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                            },
                            body: JSON.stringify({
                                venda_id: vendaId,
                                formData: formData
                            })
                        })
                            .then((response) => response.text()) // Pega como texto primeiro para evitar erro de sintaxe se vier HTML
                            .then((text) => {
                                try {
                                    return JSON.parse(text); // Tenta converter
                                } catch (e) {
                                    console.error("Resposta inválida do servidor:", text);
                                    throw new Error("Erro no servidor. Verifique o console.");
                                }
                            })
                            .then((response) => {
                                console.log("📩 Resposta PHP:", response);

                                if (response.sucesso) {
                                    // Sucesso! Redireciona
                                    window.location.href = "<?= BASE_URL ?>cliente/painel?msg=pagamento_sucesso";
                                    resolve();
                                } else {
                                    // Erro (ex: Cartão recusado, CPF inválido no MP)
                                    alert("Atenção: " + (response.msg || "Erro desconhecido"));
                                    reject();
                                }
                            })
                            .catch((error) => {
                                console.error("Erro de rede/lógica:", error);
                                alert("Ocorreu um erro ao processar. Tente novamente.");
                                reject();
                            });
                    });
                },
                onError: (error) => {
                    console.error("❌ Erro no Brick:", error);
                },
            },
        };

        window.paymentBrickController = await bricksBuilder.create(
            "payment",
            "paymentBrick_container",
            settings
        );
    };

    renderPaymentBrick(bricksBuilder);
</script>

<?php require __DIR__ . '/../layout/footer_public.php'; ?>