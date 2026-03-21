<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impressão Pedido #<?= htmlspecialchars($venda['id']) ?></title>
    <style>
        /* Estilos base para a tela (simulando o papel) */
        body {
            background-color: #f0f0f0;
            font-family: 'Courier New', Courier, monospace; /* Fonte monoespaçada de cupom */
            margin: 0;
            padding: 20px;
            color: #000;
        }

        /* O Cupom em si (Tamanho padrão de bobina 80mm) */
        .cupom {
            background-color: #fff;
            width: 300px;
            margin: 0 auto;
            padding: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .center { text-align: center; }
        .left { text-align: left; }
        .right { text-align: right; }
        .bold { font-weight: bold; }

        h2, h3, p { margin: 5px 0; }
        .divisor { border-top: 1px dashed #000; margin: 10px 0; }

        /* Tabela de Itens */
        table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
        th, td { padding: 4px 0; vertical-align: top; }
        th { border-bottom: 1px dashed #000; text-align: left; }
        
        .td-qtd { width: 15%; }
        .td-desc { width: 55%; }
        .td-preco { width: 30%; text-align: right; }

        .totais-grid {
            display: flex;
            justify-content: space-between;
            font-size: 0.95rem;
            margin: 4px 0;
        }

        .total-destaque {
            font-size: 1.2rem;
            font-weight: bold;
        }

        /* Botão flutuante na tela (não sai na impressão) */
        .no-print {
            display: block;
            width: 300px;
            margin: 20px auto;
            padding: 12px;
            background: #2c3e50;
            color: white;
            text-align: center;
            text-decoration: none;
            font-family: Arial, sans-serif;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            border: none;
        }
        .no-print:hover { background: #1a252f; }

        /* REGRA MÁGICA: O que acontece na hora que a impressora puxa o papel */
        @media print {
            body { background: #fff; padding: 0; margin: 0; }
            .cupom { box-shadow: none; width: 100%; max-width: 100%; margin: 0; padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <button class="no-print" onclick="window.print()">
        🖨️ Imprimir Pedido
    </button>

    <div class="cupom">
        
        <div class="center">
            <h2><?= htmlspecialchars($config['nome_loja'] ?? 'KaByte') ?></h2>
            <p>PEDIDO #<?= htmlspecialchars($venda['id']) ?></p>
            <p>Data: <?= date('d/m/Y H:i', strtotime($venda['data_venda'] ?? $venda['data_entrega'] ?? date('Y-m-d H:i'))) ?></p>
        </div>

        <div class="divisor"></div>

        <div class="left">
            <p class="bold">CLIENTE:</p>
            <p><?= htmlspecialchars($venda['cliente_nome'] ?? 'Não informado') ?></p>
            <p>Tel: <?= htmlspecialchars($venda['cliente_telefone'] ?? '') ?></p>
            <br>
            <p class="bold">ENDEREÇO DE ENTREGA:</p>
            <p><?= htmlspecialchars($venda['endereco_entrega'] ?? 'Retirada no Local') ?></p>
            
            <?php if (!empty($venda['observacoes'])): ?>
                <p class="bold" style="margin-top: 8px;">OBSERVAÇÕES:</p>
                <p><?= htmlspecialchars($venda['observacoes']) ?></p>
            <?php endif; ?>
        </div>

        <div class="divisor"></div>

        <table>
            <thead>
                <tr>
                    <th class="td-qtd">Qtd</th>
                    <th class="td-desc">Item</th>
                    <th class="td-preco">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($itens)): ?>
                    <?php foreach ($itens as $item): ?>
                    <tr>
                        <td class="td-qtd"><?= $item['quantidade'] ?>x</td>
                        <td class="td-desc"><?= htmlspecialchars($item['produto_nome']) ?></td>
                        <td class="td-preco">R$ <?= number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3" class="center">Itens não detalhados</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="divisor"></div>

        <div>
            <div class="totais-grid">
                <span>Subtotal:</span>
                <span>R$ <?= number_format($venda['total'] ?? 0, 2, ',', '.') ?></span>
            </div>
            
            <div class="totais-grid total-destaque">
                <span>TOTAL:</span>
                <span>R$ <?= number_format($venda['total'] ?? 0, 2, ',', '.') ?></span>
            </div>
            <br>
            <div class="totais-grid">
                <span>Situação:</span>
                <span class="bold"><?= strtoupper($venda['status_pagamento'] ?? 'PENDENTE') ?></span>
            </div>
            <div class="totais-grid">
                <span>Forma de Pagto:</span>
                <span><?= htmlspecialchars(strtoupper($venda['forma_pagamento'] ?? 'A COMBINAR')) ?></span>
            </div>
        </div>

        <div class="divisor"></div>
        
        <div class="center">
            <p>Obrigado pela preferência!</p>
            <p>Volte Sempre</p>
        </div>

    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>