<?php require __DIR__ . '/../layout/header_public.php'; ?>

<style>
    .status-container {
        max-width: 600px;
        margin: 40px auto;
        background: white;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        text-align: center;
    }

    /* Ícones de Status */
    .icon-status {
        font-size: 4rem;
        margin-bottom: 20px;
    }

    .status-success {
        color: #27ae60;
    }

    .status-pending {
        color: #f39c12;
    }

    .status-error {
        color: #e74c3c;
    }

    /* Área do PIX */
    .pix-area {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-top: 20px;
        border: 1px dashed #ccc;
    }

    .qr-img {
        max-width: 200px;
        margin: 10px auto;
        display: block;
    }

    .copy-paste-box {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 0.85rem;
        height: 80px;
        margin-top: 10px;
        resize: none;
        background: white;
    }

    /* Botões */
    .btn-action {
        display: inline-block;
        padding: 12px 25px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: bold;
        margin-top: 20px;
        transition: 0.2s;
    }

    .btn-primary {
        background: #3498db;
        color: white;
        border: none;
        cursor: pointer;
    }

    .btn-outline {
        border: 2px solid #3498db;
        color: #3498db;
        margin-left: 10px;
    }

    .btn-primary:hover {
        background: #2980b9;
    }

    .lista-resumo {
        text-align: left;
        margin-top: 30px;
        border-top: 1px solid #eee;
        padding-top: 20px;
    }

    .row-resumo {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
        color: #555;
    }

    .total-big {
        font-size: 1.3rem;
        font-weight: bold;
        color: #2c3e50;
        margin-top: 10px;
    }
</style>

<div class="status-container">

    <?php if ($venda['status_pagamento'] === 'pago'): ?>
        <i class="fas fa-check-circle icon-status status-success"></i>
        <h2>Pagamento Aprovado!</h2>
        <p>Seu pedido <strong>#<?= $venda['id'] ?></strong> foi confirmado e já vamos separar seus itens.</p>

    <?php elseif ($venda['status_pagamento'] === 'pendente'): ?>

        <i class="fas fa-clock icon-status status-pending"></i>
        <h2>Aguardando Pagamento</h2>
        <p>Finalize o pagamento para liberarmos seu pedido.</p>

        <?php
        // --- LÓGICA DO PIX (BLINDADA) ---
        // Verifica se é pix, se $dadosMP existe E se é um objeto
        if ($pagamentoDB['payment_method'] === 'pix' && $dadosMP && is_object($dadosMP)):

            // Usamos '?? null' para evitar erro se a propriedade não existir
            $pointOfInteraction = $dadosMP->point_of_interaction ?? null;
            $transData = $pointOfInteraction->transaction_data ?? null;

            if ($transData):
                $qrBase64 = $transData->qr_code_base64 ?? '';
                $qrCode = $transData->qr_code ?? '';
                ?>
                <div class="pix-area">
                    <h4 style="margin:0;"><i class="fab fa-pix"></i> Pague com Pix</h4>
                    <p style="font-size:0.9rem; color:#666;">Abra o app do seu banco e escaneie:</p>

                    <?php if ($qrBase64): ?>
                        <img src="data:image/jpeg;base64,<?= $qrBase64 ?>" class="qr-img" alt="QR Code Pix">
                    <?php endif; ?>

                    <p style="font-size:0.9rem; margin-top:15px;">Ou use o Pix Copia e Cola:</p>
                    <textarea class="copy-paste-box" id="pixCode" readonly><?= $qrCode ?></textarea>

                    <button class="btn-action btn-primary" style="font-size:0.8rem; padding: 8px 15px;" onclick="copiarPix()">
                        <i class="fas fa-copy"></i> Copiar Código
                    </button>
                </div>
            <?php else: ?>
                <div class="pix-area">
                    <p style="color:red">Erro ao carregar QR Code. Tente atualizar a página.</p>
                </div>
            <?php endif; ?>

            <?php
            // --- LÓGICA DO BOLETO (BLINDADA) ---
        elseif (
            isset($pagamentoDB['payment_method']) &&
            in_array($pagamentoDB['payment_method'], ['bolbradesco', 'pec', 'ticket']) &&
            $dadosMP && is_object($dadosMP)
        ):

            $transDetails = $dadosMP->transaction_details ?? null;
            $linkBoleto = $transDetails->external_resource_url ?? '#';
            ?>
            <div class="pix-area">
                <h4><i class="fas fa-barcode"></i> Boleto Bancário</h4>
                <p>Clique abaixo para visualizar e imprimir seu boleto.</p>
                <a href="<?= $linkBoleto ?>" target="_blank" class="btn-action btn-primary">
                    <i class="fas fa-print"></i> Abrir Boleto
                </a>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <i class="fas fa-times-circle icon-status status-error"></i>
        <h2>Problema no Pagamento</h2>
        <p>Houve um erro ao processar. Tente novamente.</p>
    <?php endif; ?>

    <div class="lista-resumo">
        <div class="row-resumo">
            <span>Pedido:</span>
            <strong>#<?= $venda['id'] ?></strong>
        </div>
        <div class="row-resumo">
            <span>Data:</span>
            <span><?= date('d/m/Y H:i', strtotime($venda['data_venda'])) ?></span>
        </div>
        <div class="row-resumo total-big">
            <span>Total:</span>
            <span>R$ <?= number_format($venda['total'], 2, ',', '.') ?></span>
        </div>

        <?php if (!empty($venda['observacoes'])): ?>
            <div style="margin-top: 15px; font-size: 0.9rem; background: #fdfdfd; padding: 10px; border: 1px solid #eee;">
                <strong>Entrega:</strong> <?= htmlspecialchars($venda['observacoes']) ?>
            </div>
        <?php endif; ?>
    </div>

    <div style="margin-top: 30px;">
        <a href="<?= BASE_URL ?>cliente/painel" class="btn-action btn-primary">Meus Pedidos</a>
        <a href="<?= BASE_URL ?>vitrine" class="btn-action btn-outline">Voltar à Loja</a>
    </div>

</div>

<script>
    function copiarPix() {
        var copyText = document.getElementById("pixCode");
        copyText.select();
        copyText.setSelectionRange(0, 99999); /* Para mobile */
        navigator.clipboard.writeText(copyText.value);
        alert("Código Pix copiado!");
    }
</script>

<?php require __DIR__ . '/../layout/footer_public.php'; ?>