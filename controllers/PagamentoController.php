<?php
require_once __DIR__ . '/../models/Pagamento.php';
require_once __DIR__ . '/../models/Chave.php';
require_once __DIR__ . '/../services/MercadoPagoService.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Produto.php';
require_once __DIR__ . '/../models/Venda.php';

class PagamentoController
{
    // 1. ROTAS PÚBLICAS: Libera a manipulação do carrinho para visitantes não logados

    public $livre = [
        'processarPagamento',
        'telaPedidoConfirmado',
        'checkout',
        'telaCheckoutCarrinho',
        'salvarSessaoCarrinho',
        'processarPedidoCompleto'
    ];

    // Método de segurança interno
    private function verificarLogin()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['cliente_id'])) {
            header('Location: ' . BASE_URL . 'cliente_area/login');
            exit;
        }
    }

    public function salvarSessaoCarrinho()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        $itensJS = $input['itens'] ?? [];

        if (empty($itensJS)) {
            echo json_encode(['sucesso' => false, 'msg' => 'Carrinho vazio']);
            exit;
        }

        $carrinhoFinal = [];
        $totalGeral = 0;

        foreach ($itensJS as $prodId => $qtd) {
            if ($qtd > 0) {

                $prod = Produto::buscarPorId($prodId);

                if ($prod) {
                    $precoUnitario = $prod['preco'];

                    if ($prod['promocao'] == 1 && $prod['preco_promocao'] > 0) {
                        $precoUnitario = $prod['preco_promocao'];
                    }

                    $totalItem = $precoUnitario * $qtd;
                    $totalGeral += $totalItem;

                    $carrinhoFinal[] = [
                        'id' => $prod['id'],
                        'nome' => $prod['nome'],
                        'preco_unitario' => $precoUnitario,
                        'quantidade' => $qtd,
                        'total' => $totalItem,
                        'imagem' => $prod['imagem']
                    ];
                }
            }
        }

        $_SESSION['carrinho_temp'] = [
            'itens' => $carrinhoFinal,
            'total' => $totalGeral
        ];

        echo json_encode(['sucesso' => true]);
        exit;
    }

    public function telaCheckoutCarrinho()
    {
        $this->verificarLogin();

        if (!isset($_SESSION['carrinho_temp']) || empty($_SESSION['carrinho_temp']['itens'])) {
            // URL LIMPA APLICADA
            header('Location: ' . BASE_URL . 'produto/vitrine');
            exit;
        }

        $cliente = Cliente::buscarPorId($_SESSION['cliente_id']);

        $carrinho = $_SESSION['carrinho_temp'];

        require __DIR__ . '/../views/clientes/checkout_carrinho.php';
    }

    public function telaPedidoConfirmado()
    {
        $this->verificarLogin();

        $vendaId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$vendaId) {
            // URL LIMPA APLICADA
            header('Location: ' . BASE_URL . 'cliente_area/painel');
            exit;
        }

        $pdo = Database::connect();

        $stmt = $pdo->prepare("SELECT * FROM vendas WHERE id = ? AND cliente_id = ?");
        $stmt->execute([$vendaId, $_SESSION['cliente_id']]);
        $venda = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$venda) {
            die("Venda não encontrada.");
        }

        // USANDO O NOVO MODEL PARA BUSCAR O PAGAMENTO
        $pagamentoDB = Pagamento::buscarPorVenda($vendaId);

        $dadosMP = null;

        if ($pagamentoDB && $pagamentoDB['status'] !== 'approved') {

            $token = Chave::get('mp_access_token');

            if (!empty($token)) {
                $mpId = $pagamentoDB['mp_id'];
                $url = "https://api.mercadopago.com/v1/payments/$mpId";

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: Bearer ' . $token,
                    'Content-Type: application/json'
                ]);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                $response = curl_exec($ch);

                if (!curl_errno($ch)) {
                    $dadosMP = json_decode($response);
                }
            }
        }

        require __DIR__ . '/../views/clientes/pedido_confirmado.php';
    }

    public function checkout()
    {
        $this->verificarLogin();

        $vendaId = (int) ($_GET['id'] ?? 0);
        if ($vendaId <= 0)
            die('Venda inválida.');

        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT * FROM vendas WHERE id = ? AND cliente_id = ?');
        $stmt->execute([$vendaId, $_SESSION['cliente_id']]);
        $venda = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$venda)
            die('Venda não encontrada.');
        if ($venda['status_pagamento'] === 'pago')
            die('Esta venda já está paga.');

        $total = round((float) ($venda['total'] - $venda['valor_pago']), 2);
        if ($total <= 0)
            die('Valor inválido para pagamento.');

        require __DIR__ . '/../views/clientes/checkout.php';
    }

    public function processarPedidoCompleto()
    {
        // 1. INICIA O ESCUDO ANTI-HTML DO PHP
        if (ob_get_length()) {
            ob_clean();
        }
        ob_start();

        header('Content-Type: application/json');

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['cliente_id']) || empty($_SESSION['carrinho_temp'])) {
            ob_get_clean();
            echo json_encode(['sucesso' => false, 'msg' => 'Sessão expirada ou carrinho vazio.']);
            exit;
        }

        try {

            $input = json_decode(file_get_contents('php://input'), true);
            $brickData = $input['formData'] ?? [];
            $dadosExtras = $input['dadosExtras'] ?? [];

            $carrinho = $_SESSION['carrinho_temp'];
            $clienteId = $_SESSION['cliente_id'];
            $totalCompra = (float) $carrinho['total'];

            $tipoEntrega = $dadosExtras['tipo_entrega'] ?? 'retirada';
            $enderecoTexto = $dadosExtras['endereco_entrega_texto'] ?? 'Retirada na Loja';
            $dataEntrega = !empty($dadosExtras['data_entrega']) ? $dadosExtras['data_entrega'] : null;

            // --- PREPARAÇÃO DE DADOS PARA BOLETOS ---
            $emailValido = filter_var($_SESSION['cliente_email'] ?? '', FILTER_VALIDATE_EMAIL)
                ? $_SESSION['cliente_email']
                : 'cliente_padrao@kabyte.com.br';

            $cpfLimpo = preg_replace('/\D/', '', $brickData['payer']['identification']['number'] ?? '');
            $nomeCompleto = trim($_SESSION['cliente_nome'] ?? 'Cliente Padrao');
            $partesNome = explode(' ', $nomeCompleto);
            $primeiroNome = $partesNome[0];
            $sobrenome = isset($partesNome[1]) ? end($partesNome) : 'Sobrenome';

            $enderecoDB = Cliente::buscarEnderecoPadrao($clienteId);

            $cep = preg_replace('/\D/', '', $enderecoDB['cep'] ?? '11531000');
            $rua = $enderecoDB['rua'] ?? 'Rua Principal';
            $numero = $enderecoDB['numero'] ?? '1';
            $bairro = $enderecoDB['bairro'] ?? 'Centro';
            $cidade = $enderecoDB['cidade'] ?? 'Cubatão';
            $estado = $enderecoDB['estado'] ?? 'SP';

            $payment_data = [
                'transaction_amount' => (float) $totalCompra,
                'description' => 'Pedido Web - Cliente #' . $clienteId,
                'payment_method_id' => $brickData['payment_method_id'],
                'payer' => [
                    'email' => $emailValido,
                    'first_name' => $primeiroNome,
                    'last_name' => $sobrenome,
                    'identification' => [
                        'type' => $brickData['payer']['identification']['type'] ?? 'CPF',
                        'number' => $cpfLimpo
                    ],
                    'address' => [
                        'zip_code' => $cep,
                        'street_name' => $rua,
                        'street_number' => $numero,
                        'neighborhood' => $bairro,
                        'city' => $cidade,
                        'federal_unit' => $estado
                    ]
                ]
            ];

            if (!empty($brickData['token'])) {
                $payment_data['token'] = $brickData['token'];
                $payment_data['installments'] = (int) ($brickData['installments'] ?? 1);
                $payment_data['issuer_id'] = (int) ($brickData['issuer_id'] ?? 0);
            }

            $mp = new MercadoPagoService();
            $resultado = $mp->processarPagamentoBrick($payment_data);

            $statusAceitos = ['approved', 'in_process', 'pending'];
            $statusMP = $resultado['status'] ?? 'erro';

            if (in_array($statusMP, $statusAceitos)) {

                // 1. Organiza os dados da Venda
                $dadosVenda = [
                    'cliente_id' => $clienteId,
                    'total' => $totalCompra,
                    'valor_pago' => ($statusMP === 'approved') ? $totalCompra : 0.00,
                    'status_pagamento' => ($statusMP === 'approved') ? 'pago' : 'pendente',
                    'data_pagamento' => ($statusMP === 'approved') ? date('Y-m-d H:i:s') : null,
                    'forma_pagamento' => $brickData['payment_method_id'] ?? 'unknown',
                    'tipo_entrega' => $tipoEntrega,
                    'endereco_entrega' => $enderecoTexto,
                    'data_entrega' => $dataEntrega,
                    'observacoes' => "Venda Online (MP ID: " . ($resultado['id'] ?? 'N/A') . ")"
                ];

                // 2. Organiza os dados do Pagamento (O ID da Venda será injetado pelo Model)
                $dadosPagamento = [
                    'cliente_id'          => $clienteId,
                    'mp_id'               => $resultado['id'] ?? null,
                    'status'              => $statusMP,
                    'descricao'           => $resultado['description'] ?? 'Venda Site',
                    'transaction_details' => json_encode($resultado['transaction_details'] ?? []),
                    'valor'               => $totalCompra,
                    'payment_type'        => $resultado['payment_type_id'] ?? 'unknown',
                    'payment_method'      => $resultado['payment_method_id'] ?? 'unknown',
                    'installments'        => $resultado['installments'] ?? 1,
                    'installment_value'   => $resultado['transaction_details']['installment_amount'] ?? $totalCompra,
                    'total_paid'          => $resultado['transaction_details']['total_paid_amount'] ?? 0
                ];

                // 3. Pede ao Model para fazer toda a gravação segura
                $resultadoBD = Venda::registrarPedidoOnline($dadosVenda, $carrinho['itens'], $dadosPagamento);

                // 4. Limpa a sessão e prepara o retorno
                unset($_SESSION['carrinho_temp']);
                $retorno = ['sucesso' => true, 'venda_id' => $resultadoBD['venda_id'], 'status' => $statusMP];

                // Adiciona links de PIX e Boleto se aplicável
                if (($brickData['payment_method_id'] ?? '') === 'pix') {
                    $retorno['pix_copia_cola'] = $resultado['point_of_interaction']['transaction_data']['qr_code'] ?? '';
                    $retorno['pix_base64'] = $resultado['point_of_interaction']['transaction_data']['qr_code_base64'] ?? '';
                }
                if (isset($resultado['transaction_details']['external_resource_url'])) {
                    $retorno['ticket_url'] = $resultado['transaction_details']['external_resource_url'];
                }

                ob_get_clean();
                echo json_encode($retorno);
            } else {
                $erroMP = 'Pagamento recusado';
                if (isset($resultado['message'])) {
                    $erroMP = $resultado['message'];
                    if (isset($resultado['cause'][0]['description'])) {
                        $erroMP .= ' - ' . $resultado['cause'][0]['description'];
                    }
                } elseif (isset($resultado['status_detail'])) {
                    $erroMP = $resultado['status_detail'];
                }
                ob_get_clean();
                echo json_encode(['sucesso' => false, 'msg' => "Erro: $erroMP ($statusMP)"]);
            }
        } catch (Throwable $e) {
            $lixoPHP = ob_get_clean();
            error_log("Erro Crítico no Pagamento: " . $e->getMessage() . " | Detalhes PHP: " . $lixoPHP);
            echo json_encode(['sucesso' => false, 'msg' => 'Erro interno do servidor: ' . $e->getMessage()]);
        }
        exit;
    } // <-- Ponto e vírgula removido!


    public function processarPagamento()
    {
        // Adicionado o escudo anti-HTML aqui também para segurança
        if (ob_get_length()) ob_clean();
        ob_start();

        header('Content-Type: application/json');

        if (!isset($_SESSION['cliente_id']) || empty($_SESSION['carrinho_temp'])) {
            ob_get_clean();
            echo json_encode(['sucesso' => false, 'msg' => 'Sessão inválida ou carrinho vazio.']);
            exit;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $brickData = $input['formData'] ?? [];
            $dadosExtras = $input['dadosExtras'] ?? [];

            $totalVenda = $_SESSION['carrinho_temp']['total'];
            $clienteId = $_SESSION['cliente_id'];

            // --- MESMA PREPARAÇÃO DE DADOS PARA BOLETOS ---
            $emailValido = filter_var($_SESSION['cliente_email'] ?? '', FILTER_VALIDATE_EMAIL)
                ? $_SESSION['cliente_email']
                : 'cliente_padrao@kabyte.com.br';

            $cpfLimpo = preg_replace('/\D/', '', $brickData['payer']['identification']['number'] ?? '');

            $nomeCompleto = trim($_SESSION['cliente_nome'] ?? 'Cliente Padrao');
            $partesNome = explode(' ', $nomeCompleto);
            $primeiroNome = $partesNome[0];
            $sobrenome = isset($partesNome[1]) ? end($partesNome) : 'Sobrenome';

            $enderecoDB = Cliente::buscarEnderecoPadrao($clienteId);

            $cep = preg_replace('/\D/', '', $enderecoDB['cep'] ?? '11531000');
            $rua = $enderecoDB['rua'] ?? 'Rua Principal';
            $numero = $enderecoDB['numero'] ?? '1';
            $bairro = $enderecoDB['bairro'] ?? 'Centro';
            $cidade = $enderecoDB['cidade'] ?? 'Cubatão';
            $estado = $enderecoDB['estado'] ?? 'SP';

            $payment_data = [
                'transaction_amount' => (float) $totalVenda,
                'token' => $brickData['token'] ?? null,
                'description' => 'Pedido Loja - Cliente ' . $primeiroNome,
                'installments' => (int) ($brickData['installments'] ?? 1),
                'payment_method_id' => $brickData['payment_method_id'],
                'payer' => [
                    'email' => $emailValido,
                    'first_name' => $primeiroNome,
                    'last_name' => $sobrenome,
                    'identification' => [
                        'type' => $brickData['payer']['identification']['type'] ?? 'CPF',
                        'number' => $cpfLimpo
                    ],
                    'address' => [
                        'zip_code' => $cep,
                        'street_name' => $rua,
                        'street_number' => $numero,
                        'neighborhood' => $bairro,
                        'city' => $cidade,
                        'federal_unit' => $estado
                    ]
                ]
            ];

            if (isset($brickData['issuer_id']) && !empty($brickData['issuer_id'])) {
                $payment_data['issuer_id'] = (int) $brickData['issuer_id'];
            }

            $mp = new MercadoPagoService();
            $resultadoMP = $mp->processarPagamentoBrick($payment_data);

            if (isset($resultadoMP['status']) && $resultadoMP['status'] == 'approved') {
                $statusVenda = 'pago';
                $dataPagamento = date('Y-m-d H:i:s');
            } else {
                $statusVenda = 'pendente';
                $dataPagamento = null;
            }

            $dadosVenda = [
                'usuario_id' => $clienteId,
                'cliente_id' => $clienteId,
                'total' => $totalVenda,
                'valor_recebido' => ($statusVenda == 'pago') ? $totalVenda : 0,
                'forma_pagamento' => $brickData['payment_method_id'],
                'status_pagamento' => $statusVenda,
                'data_pagamento' => $dataPagamento,
                'tipo_entrega' => $dadosExtras['tipo_entrega'] ?? 'retirada',
                'endereco_entrega' => $dadosExtras['endereco_entrega_texto'] ?? 'Retirada',
                'data_entrega' => $dadosExtras['data_entrega'] ?? null,
                'observacoes' => 'Venda Online - MP ID: ' . ($resultadoMP['id'] ?? 'N/A')
            ];

            $resultadoBD = Venda::registrar($dadosVenda, $_SESSION['carrinho_temp']['itens']);

            ob_get_clean();
            if ($resultadoBD['sucesso']) {
                unset($_SESSION['carrinho_temp']);
                echo json_encode(['sucesso' => true, 'venda_id' => $resultadoBD['venda_id']]);
            } else {
                echo json_encode(['sucesso' => false, 'msg' => 'Erro ao salvar pedido: ' . $resultadoBD['msg']]);
            }
        } catch (Throwable $e) {
            ob_get_clean();
            echo json_encode(['sucesso' => false, 'msg' => 'Erro no servidor: ' . $e->getMessage()]);
        }
        exit;
    }
}
