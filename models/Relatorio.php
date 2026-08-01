<?php
require_once __DIR__ . '/../config/Database.php';

class Relatorio
{
    /**
     * Busca as vendas no banco de dados aplicando os filtros informados
     */
    public static function buscarVendasFiltradas($filtros = [])
    {
        $pdo = Database::connect();

        $dataInicio = $filtros['data_inicio'] ?? date('Y-m-01');
        $dataFim    = $filtros['data_fim'] ?? date('Y-m-t');
        $vendedorId = $filtros['vendedor_id'] ?? '';
        $status     = $filtros['status'] ?? '';
        $pagamento  = $filtros['forma_pagamento'] ?? '';
        $produtoNome = $filtros['produto_nome'] ?? '';

        $sql = "SELECT DISTINCT 
                    v.id, 
                    v.data_venda, 
                    u.nome AS vendedor, 
                    c.nome AS cliente, 
                    v.total, 
                    v.valor_pago, 
                    v.forma_pagamento, 
                    v.status_pagamento, 
                    v.tipo_entrega
                FROM vendas v
                LEFT JOIN usuarios u ON v.usuario_id = u.id
                LEFT JOIN clientes c ON v.cliente_id = c.id ";

        if (!empty($produtoNome)) {
            $sql .= " INNER JOIN itens_venda iv ON v.id = iv.venda_id 
                      INNER JOIN produtos p ON iv.produto_id = p.id ";
        }

        $sql .= " WHERE v.data_venda BETWEEN ? AND ? ";
        $params = [$dataInicio . ' 00:00:00', $dataFim . ' 23:59:59'];

        if (!empty($vendedorId)) {
            $sql .= " AND v.usuario_id = ? ";
            $params[] = $vendedorId;
        }
        if (!empty($status)) {
            $sql .= " AND v.status_pagamento = ? ";
            $params[] = $status;
        }
        if (!empty($pagamento)) {
            $sql .= " AND v.forma_pagamento = ? ";
            $params[] = $pagamento;
        }
        if (!empty($produtoNome)) {
            $sql .= " AND p.nome LIKE ? ";
            $params[] = "%$produtoNome%";
        }

        $sql .= " ORDER BY v.data_venda DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Processa a lista de vendas e monta os totais e gráficos
     */
    public static function processarMetricas($vendas)
    {
        $totalVendido  = 0;
        $totalRecebido = 0;
        $porVendedor   = [];
        $porPagamento  = [];
        $porDia        = [];

        foreach ($vendas as $v) {
            $totalVendido  += $v['total'];
            $totalRecebido += $v['valor_pago'];

            // Gráfico por Vendedor
            $vend = !empty($v['vendedor']) ? $v['vendedor'] : 'Site / Online';
            if (!isset($porVendedor[$vend])) {
                $porVendedor[$vend] = 0;
            }
            $porVendedor[$vend] += $v['total'];

            // Gráfico por Forma de Pagamento
            $pg = !empty($v['forma_pagamento']) ? $v['forma_pagamento'] : 'Indefinido';
            if (!isset($porPagamento[$pg])) {
                $porPagamento[$pg] = 0;
            }
            $porPagamento[$pg] += $v['total'];

            // Gráfico de Timeline (Por Dia)
            $dia = date('d/m', strtotime($v['data_venda']));
            if (!isset($porDia[$dia])) {
                $porDia[$dia] = 0;
            }
            $porDia[$dia] += $v['total'];
        }

        $qtdVendas = count($vendas);

        return [
            'lista' => $vendas,
            'resumo' => [
                'total_vendido'  => $totalVendido,
                'total_recebido' => $totalRecebido,
                'ticket_medio'   => $qtdVendas > 0 ? $totalVendido / $qtdVendas : 0,
                'qtd_vendas'     => $qtdVendas
            ],
            'graficos' => [
                'vendedor'  => $porVendedor,
                'pagamento' => $porPagamento,
                'timeline'  => $porDia
            ]
        ];
    }
}