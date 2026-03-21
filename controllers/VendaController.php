<?php
require_once __DIR__ . '/../models/Venda.php';
require_once __DIR__ . '/../models/Auth.php';
require_once __DIR__ . '/../models/Endereco.php';

class VendaController
{
    public function index()
    {
        Auth::verificar(['admin', 'master']);

        // 1. Captura os filtros ou define padrões (ex: Mês atual)
        $filtros = [
            'data_inicio' => $_GET['data_inicio'] ?? date('Y-m-01'), // Primeiro dia do mês
            'data_fim' => $_GET['data_fim'] ?? date('Y-m-t'),  // Último dia do mês
            'cliente' => $_GET['cliente'] ?? '',
            'status' => $_GET['status'] ?? ''
        ];

        // 2. Passa os filtros para o Model
        $vendas = Venda::listarTodas($filtros);


        require __DIR__ . '/../views/vendas/listar.php';
    }

    public function apiEnderecos()
    {
        // Define o tipo de conteúdo como JSON
        header('Content-Type: application/json');

        $clienteId = $_GET['id'] ?? 0;

        if (!$clienteId) {
            echo json_encode([]);
            exit;
        }

        $pdo = Database::connect();
        $listaFinal = [];

        // 1. BUSCAR O ENDEREÇO PRINCIPAL (Tabela Clientes)
        // Ajuste os nomes das colunas (rua, numero, bairro) conforme seu banco
        $sqlCliente = "SELECT rua, numero, bairro, cidade, cep 
                       FROM clientes WHERE id = ?";
        $stmt = $pdo->prepare($sqlCliente);
        $stmt->execute([$clienteId]);
        $dadosCliente = $stmt->fetch(PDO::FETCH_ASSOC);

        // Se o cliente tem endereço cadastrado, adiciona na lista
        if ($dadosCliente && !empty($dadosCliente['rua'])) {
            $listaFinal[] = [
                'id' => 'principal', // ID simbólico para saber que é o principal
                'logradouro' => $dadosCliente['rua'], // Mapeia 'rua' para 'logradouro'
                'numero' => $dadosCliente['numero'],
                'bairro' => $dadosCliente['bairro'],
                'cidade' => $dadosCliente['cidade'] ?? '',
                'tipo_desc' => '⭐ Endereço Principal' // Para exibir bonito
            ];
        }

        // 2. BUSCAR ENDEREÇOS EXTRAS (Tabela Enderecos)
        $sqlExtras = "SELECT id, logradouro, numero, bairro, cidade, cep 
                      FROM enderecos WHERE cliente_id = ?";
        $stmt2 = $pdo->prepare($sqlExtras);
        $stmt2->execute([$clienteId]);
        $enderecosExtras = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        // Adiciona os extras na lista
        foreach ($enderecosExtras as $extra) {
            $listaFinal[] = [
                'id' => $extra['id'],
                'logradouro' => $extra['logradouro'],
                'numero' => $extra['numero'],
                'bairro' => $extra['bairro'],
                'cidade' => $extra['cidade'],
                'tipo_desc' => '📍 Endereço Adicional'
            ];
        }

        // Retorna tudo junto para o Javascript
        echo json_encode($listaFinal);
        exit;
    }

    public function imprimir_pedido()
    {
        Auth::verificar(['admin', 'master']);
        $id = $_GET['id'] ?? null;

        if (!$id) {
            die("Pedido não encontrado.");
        }

        // 1. Busca os dados gerais da venda/pedido
        require_once __DIR__ . '/../models/Venda.php';
        $venda = Venda::buscarPorId($id);

        if (!$venda) {
            die("Pedido inválido.");
        }

        // 2. Busca os itens (produtos) daquele pedido específico
        $itens = Venda::listarItens($id);

        // 3. Busca o nome da loja para o topo do cupom
        require_once __DIR__ . '/../models/Configuracao.php';
        $config = Configuracao::get();

        // 4. Carrega a tela de impressão
        require __DIR__ . '/../views/admin_imprimir_pedido.php';
    }
}