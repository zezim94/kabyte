<?php
require_once __DIR__ . '/../config/Database.php';

class Venda
{
    public static function registrar($dados, $itens)
    {
        $pdo = Database::connect();

        try {
            $pdo->beginTransaction();

            // 1. PREPARAÇÃO DOS DADOS
            // Extrai variáveis para facilitar a leitura ou usa padrões se não vierem
            $total = $dados['total'];
            $forma_pagamento = $dados['forma_pagamento'];
            $valor_recebido = $dados['valor_recebido'] ?? 0;

            // Lógica de Status (Recalcula por segurança ou usa o que veio do Controller)
            // Se o controller já mandou 'status_pagamento', usamos ele. Senão, calculamos.
            if (isset($dados['status_pagamento'])) {
                $status = $dados['status_pagamento'];
                $dataPagamento = $dados['data_pagamento']; // Já vem formatada ou NULL
            } else {
                // Cálculo de segurança (Fallback)
                $valor_pago_real = ($forma_pagamento == 'Dinheiro') ? $valor_recebido : $total;
                if ($valor_pago_real >= $total) {
                    $status = 'pago';
                    $dataPagamento = date('Y-m-d H:i:s');
                } else {
                    $status = ($valor_pago_real > 0) ? 'parcial' : 'pendente';
                    $dataPagamento = null;
                }
            }

            // Garante que valor pago não seja maior que o total no banco (questão de relatório)
            $valor_registrado = ($forma_pagamento == 'Dinheiro' && $valor_recebido > $total) ? $total : $valor_recebido;
            if ($forma_pagamento != 'Dinheiro')
                $valor_registrado = $total;


            // 2. INSERIR A VENDA (Com todas as colunas novas)
            $sql = "INSERT INTO vendas (
                        usuario_id, 
                        cliente_id, 
                        total, 
                        valor_pago, 
                        forma_pagamento, 
                        status_pagamento, 
                        data_venda, 
                        data_pagamento,
                        tipo_entrega,
                        endereco_entrega,
                        data_entrega,
                        observacoes
                    ) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $dados['usuario_id'],
                $dados['cliente_id'] ?? null,
                $total,
                $valor_registrado,
                $forma_pagamento,
                $status,
                $dataPagamento,           // Coluna Nova
                $dados['tipo_entrega'],   // Coluna Nova
                $dados['endereco_entrega'], // Coluna Nova
                $dados['data_entrega'] ?? null, // Coluna Nova (Geralmente NULL na criação)
                $dados['observacoes'] ?? ''     // Coluna Nova
            ]);

            $venda_id = $pdo->lastInsertId();

            // 3. SALVAR ITENS E BAIXAR ESTOQUE
            $stmtItem = $pdo->prepare("INSERT INTO itens_venda (venda_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");
            $stmtEstoque = $pdo->prepare("UPDATE produtos SET estoque = estoque - ? WHERE id = ?");

            foreach ($itens as $item) {
                $stmtItem->execute([
                    $venda_id,
                    $item['id'],
                    $item['qtd'],
                    $item['preco']
                ]);

                $stmtEstoque->execute([$item['qtd'], $item['id']]);
            }

            $pdo->commit();

            return [
                'sucesso' => true,
                'venda_id' => $venda_id,
                'status' => $status
            ];
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            // Logar erro em arquivo para debug se necessário
            // error_log($e->getMessage());
            return ['sucesso' => false, 'msg' => 'Erro ao registrar venda: ' . $e->getMessage()];
        }
    }

    public static function listarTodas($filtros = [])
    {
        $pdo = Database::connect();

        // Query Base
        $sql = "SELECT 
                    v.id, 
                    v.data_venda, 
                    v.total, 
                    v.valor_pago,
                    v.status_pagamento,
                    v.tipo_entrega,
                    u.nome as vendedor, 
                    c.nome as cliente 
                FROM vendas v
                LEFT JOIN usuarios u ON v.usuario_id = u.id
                LEFT JOIN clientes c ON v.cliente_id = c.id
                WHERE 1=1"; // Truque para facilitar a adição de 'AND'

        $params = [];


        // 1. Filtro de Data
        if (!empty($filtros['data_inicio']) && !empty($filtros['data_fim'])) {
            $sql .= " AND v.data_venda BETWEEN ? AND ?";
            // Adiciona horas para pegar o dia inteiro (00:00 até 23:59)
            $params[] = $filtros['data_inicio'] . ' 00:00:00';
            $params[] = $filtros['data_fim'] . ' 23:59:59';
        }

        // 2. Filtro de Cliente (Busca parcial com LIKE)
        if (!empty($filtros['cliente'])) {
            $sql .= " AND c.nome LIKE ?";
            $params[] = '%' . $filtros['cliente'] . '%';
        }

        // 3. Filtro de Status
        if (!empty($filtros['status'])) {
            $sql .= " AND v.status_pagamento = ?";
            $params[] = $filtros['status'];
        }

        // Ordenação
        $sql .= " ORDER BY v.data_venda DESC";

        // Execução Segura
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Busca todos os dados de uma única venda/pedido pelo ID
    public static function buscarPorId($id)
    {
        $pdo = Database::connect();

        $sql = "SELECT 
                    v.*, 
                    c.nome as cliente_nome, 
                    c.telefone as cliente_telefone 
                FROM vendas v
                LEFT JOIN clientes c ON v.cliente_id = c.id
                WHERE v.id = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function listarItens($venda_id)
    {
        $pdo = Database::connect();

        $sql = "SELECT 
                    i.quantidade, 
                    i.preco_unitario, 
                    p.nome as produto_nome 
                FROM itens_venda i
                JOIN produtos p ON i.produto_id = p.id
                WHERE i.venda_id = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$venda_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listarEntregas($filtros = [])
    {
        $pdo = Database::connect();

        $data = $filtros['data'] ?? date('Y-m-d'); // Padrão: Hoje
        $status = $filtros['status'] ?? 'todos';   // Padrão: Todos

        // Montagem da Query
        $sql = "SELECT v.*, c.nome as cliente_nome, c.telefone as cliente_telefone 
                FROM vendas v 
                JOIN clientes c ON v.cliente_id = c.id 
                WHERE v.tipo_entrega = 'entrega'";

        $params = [];

        // 1. Filtro de Data (Ignora hora, compara só o dia)
        if (!empty($data)) {
            $sql .= " AND DATE(v.data_entrega) = ?";
            $params[] = $data;
        }

        // 2. Filtro de Status Pagamento
        if ($status !== 'todos') {
            $sql .= " AND v.status_pagamento = ?";
            $params[] = $status;
        }

        // Ordenar: Pendentes primeiro, depois por hora de entrega
        $sql .= " ORDER BY v.status_pagamento ASC, v.data_entrega ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function confirmarEntrega($id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("UPDATE vendas SET status_entrega = 'entregue' WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Lista todas as compras de um cliente específico (Histórico)
     */
    public static function listarPorCliente($clienteId)
    {
        $pdo = Database::connect();
        $sql = 'SELECT * FROM vendas WHERE cliente_id = ? ORDER BY data_venda DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$clienteId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca os 10 produtos mais comprados por um cliente específico
     */
    public static function topProdutosPorCliente($clienteId)
    {
        $pdo = Database::connect();
        $sql = "SELECT p.nome, SUM(iv.quantidade) as total_qtd
                FROM itens_venda iv
                JOIN vendas v ON iv.venda_id = v.id
                JOIN produtos p ON iv.produto_id = p.id
                WHERE v.cliente_id = ?
                GROUP BY p.id
                ORDER BY total_qtd DESC
                LIMIT 10";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$clienteId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Registra uma Venda, os Itens, atualiza Estoque e salva o Pagamento na mesma transação.
     */
    public static function registrarPedidoOnline($dadosVenda, $itens, $dadosPagamento = null)
    {
        $pdo = Database::connect();

        try {
            // Inicia a transação no Model
            $pdo->beginTransaction();

            // 1. INSERIR A VENDA
            $sqlVenda = "INSERT INTO vendas (
                            cliente_id, data_venda, total, valor_pago, status_pagamento, 
                            data_pagamento, forma_pagamento, tipo_entrega, endereco_entrega, 
                            data_entrega, observacoes
                        ) VALUES (?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmtVenda = $pdo->prepare($sqlVenda);
            $stmtVenda->execute([
                $dadosVenda['cliente_id'],
                $dadosVenda['total'],
                $dadosVenda['valor_pago'],
                $dadosVenda['status_pagamento'],
                $dadosVenda['data_pagamento'],
                $dadosVenda['forma_pagamento'],
                $dadosVenda['tipo_entrega'],
                $dadosVenda['endereco_entrega'],
                $dadosVenda['data_entrega'],
                $dadosVenda['observacoes']
            ]);

            $vendaId = $pdo->lastInsertId();

            // 2. INSERIR ITENS E BAIXAR ESTOQUE
            $sqlItem = "INSERT INTO itens_venda (venda_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)";
            $stmtItem = $pdo->prepare($sqlItem);

            $sqlEstoque = "UPDATE produtos SET estoque = estoque - ? WHERE id = ?";
            $stmtEstoque = $pdo->prepare($sqlEstoque);

            foreach ($itens as $item) {
                $stmtItem->execute([$vendaId, $item['id'], $item['quantidade'], $item['preco_unitario']]);
                $stmtEstoque->execute([$item['quantidade'], $item['id']]);
            }

            // 3. SALVAR DETALHES DO PAGAMENTO (Usando o Model Pagamento)
            if ($dadosPagamento) {
                // Injeta o ID da venda recém-criada
                $dadosPagamento['venda_id'] = $vendaId;

                // Passa o $pdo para o Model Pagamento usar a MESMA transação
                Pagamento::salvar($pdo, $dadosPagamento);
            }

            // Confirma tudo no banco de dados
            $pdo->commit();

            return ['sucesso' => true, 'venda_id' => $vendaId];
        } catch (Throwable $e) {
            // Se algo falhar, reverte TUDO e atira o erro para o Controlador tratar
            $pdo->rollBack();
            throw new Exception("Falha na transação do banco de dados: " . $e->getMessage());
        }
    }

    /**
     * Busca todos os detalhes de uma venda para exibição no painel Admin, 
     * incluindo dados do cliente e do vendedor que a registrou.
     */
    public static function buscarDetalhesAdmin($vendaId)
    {
        $pdo = Database::connect();
        $sql = "SELECT 
                    v.*, 
                    c.nome as cliente_nome, 
                    c.email as cliente_email, 
                    c.data_cadastro as cliente_desde,
                    u.nome as vendedor_nome
                FROM vendas v
                LEFT JOIN clientes c ON v.cliente_id = c.id
                LEFT JOIN usuarios u ON v.usuario_id = u.id
                WHERE v.id = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$vendaId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Busca os itens de uma venda específica para o painel Admin,
     * incluindo a imagem e o nome do produto.
     */
    public static function listarItensAdmin($vendaId)
    {
        $pdo = Database::connect();
        $sql = "SELECT 
                    iv.quantidade, 
                    iv.preco_unitario, 
                    p.nome as produto_nome, 
                    p.imagem
                FROM itens_venda iv
                JOIN produtos p ON iv.produto_id = p.id
                WHERE iv.venda_id = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$vendaId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
