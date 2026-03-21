<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Venda.php';
require_once __DIR__ . '/../services/MercadoPagoService.php';

class ClienteAreaController
{
    // Tela de Login
    public function login()
    {
        if (isset($_SESSION['cliente_id'])) {
            header('Location: ' . BASE_URL . 'clientes/painel');
            exit;
        }
        $callbackUrl = BASE_URL . 'index.php?rota=cliente/google_callback';

        $params = [
            'client_id' => GOOGLE_CLIENT_ID,
            'redirect_uri' => $callbackUrl, // <--- FORÇA O CALLBACK DO CLIENTE
            'response_type' => 'code',
            'scope' => 'email profile',
            'access_type' => 'online'
        ];
        $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);

        require __DIR__ . '/../views/clientes/login.php';
    }

    // 2. AUTENTICAÇÃO VIA SENHA (Blindado na tabela Clientes)
    public function autenticar()
    {
        if (session_status() == PHP_SESSION_NONE)
            session_start();

        $login = $_POST['login'] ?? '';
        $senha = $_POST['senha'] ?? '';

        $pdo = Database::connect();

        // --- SEGURANÇA ---
        // Consulta EXCLUSIVA na tabela 'clientes'.
        // Isso impede que um admin (tabela 'usuarios') logue por aqui.
        $sql = 'SELECT * FROM clientes WHERE (cpf = ? OR email = ? OR telefone = ?) LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$login, $login, $login]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica se o cliente existe E se a senha confere
        if ($cliente && password_verify($senha, $cliente['senha'])) {
            $this->criarSessaoCliente($cliente);
            header('Location: ' . BASE_URL . 'clientes/painel');
            exit;
        } else {
            // Define a mensagem de erro que aparecerá na View
            $erro = 'Login ou senha incorretos!';

            // Recarrega o método login (que gera a URL do Google e carrega a view)
            $this->login();
        }
    }

    // 3. RETORNO DO GOOGLE
    public function google_callback()
    {
        if (isset($_GET['code'])) {
            $code = $_GET['code'];

            // Passamos a URL correta para validação
            $tokenData = $this->pegarTokenGoogle($code);

            if (isset($tokenData['access_token'])) {
                $googleUser = $this->pegarDadosUsuarioGoogle($tokenData['access_token']);

                if (isset($googleUser['email'])) {
                    // Salva na tabela CLIENTES
                    $cliente = Cliente::salvarGoogle($googleUser);

                    if ($cliente) {
                        $this->criarSessaoCliente($cliente);
                        header('Location: ' . BASE_URL . 'cliente/painel');
                        exit;
                    }
                }
            }
        }

        header('Location: ' . BASE_URL . 'cliente/login?erro=google_fail');
        exit;
    }

    // --- MÉTODOS DE SESSÃO ---

    private function criarSessaoCliente($cliente)
    {
        if (session_status() == PHP_SESSION_NONE)
            session_start();

        // --- SEGURANÇA CRÍTICA ---
        // Se houver uma sessão de administrador ('usuario_id') aberta, nós a destruímos.
        // Isso evita que o sistema confunda um Admin com um Cliente.
        if (isset($_SESSION['usuario_id'])) {
            unset($_SESSION['usuario_id']);
            unset($_SESSION['nivel']);
        }

        // Define as sessões do cliente
        $_SESSION['cliente_id'] = $cliente['id'];
        $_SESSION['cliente_nome'] = $cliente['nome'];
        $_SESSION['cliente_email'] = $cliente['email'];

        // Tratamento da Foto:
        // Se vier do Google (começa com http), usa direto. 
        // Se estiver vazia, usa o avatar padrão.
        $foto = $cliente['foto'];
        if (empty($foto)) {
            $foto = BASE_URL . 'public/img/avatar_default.png';
        }
        $_SESSION['cliente_foto'] = $foto;
    }

    // --- HELPERS cURL (Para funcionar sem Composer) ---

    private function pegarTokenGoogle($code)
    {
        // A URL DE CALLBACK TEM QUE SER IDÊNTICA A QUE GEROU O LINK
        $callbackUrl = BASE_URL . 'index.php?rota=cliente/google_callback';

        $url = 'https://oauth2.googleapis.com/token';
        $postData = [
            'code' => $code,
            'client_id' => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri' => $callbackUrl, // <--- IMPORTANTE: MUDAR AQUI TAMBÉM
            'grant_type' => 'authorization_code'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    private function pegarDadosUsuarioGoogle($accessToken)
    {
        $url = 'https://www.googleapis.com/oauth2/v1/userinfo?access_token=' . $accessToken;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);

        // Descomente a linha abaixo se estiver usando XAMPP e der erro de SSL
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    // Painel Principal
    public function painel()
    {
        $this->verificarLogin();
        $id = $_SESSION['cliente_id'];

        $pdo = Database::connect();
        $sql = 'SELECT * FROM vendas WHERE cliente_id = ? ORDER BY data_venda DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $minhasCompras = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2. NOVO: Buscar Top 10 produtos mais comprados pelo cliente
        // (Soma a quantidade de vezes que o cliente comprou cada produto)
        $sqlTop = "SELECT p.nome, SUM(iv.quantidade) as total_qtd
               FROM itens_venda iv
               JOIN vendas v ON iv.venda_id = v.id
               JOIN produtos p ON iv.produto_id = p.id
               WHERE v.cliente_id = ?
               GROUP BY p.id
               ORDER BY total_qtd DESC
               LIMIT 10";
        $stmtTop = $pdo->prepare($sqlTop);
        $stmtTop->execute([$id]);
        $topProdutos = $stmtTop->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../views/clientes/painel.php';
    }

    // Exibe a tela com o Brick
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
    // --- AQUI COMEÇA O PROCESSAMENTO DO PAGAMENTO ---
    public function processarPagamento()
    {
        header('Content-Type: application/json');

        // 1. Validar Sessão
        if (!isset($_SESSION['cliente_id']) || empty($_SESSION['carrinho_temp'])) {
            echo json_encode(['sucesso' => false, 'msg' => 'Sessão inválida ou carrinho vazio.']);
            exit;
        }

        try {
            // 2. Receber Dados do Frontend
            $input = json_decode(file_get_contents('php://input'), true);
            $brickData = $input['formData'] ?? [];
            $dadosExtras = $input['dadosExtras'] ?? [];

            // 3. Preparar Dados para Mercado Pago
            $totalVenda = $_SESSION['carrinho_temp']['total'];

            $payment_data = [
                'transaction_amount' => (float) $totalVenda,
                'token' => $brickData['token'] ?? null,
                'description' => 'Pedido Loja - Cliente ' . $_SESSION['cliente_nome'],
                'installments' => (int) ($brickData['installments'] ?? 1),
                'payment_method_id' => $brickData['payment_method_id'],
                'payer' => [
                    'email' => $brickData['payer']['email'] ?? 'test_user_123@test.com',
                    'identification' => [
                        'type' => $brickData['payer']['identification']['type'] ?? 'CPF',
                        'number' => $brickData['payer']['identification']['number'] ?? ''
                    ]
                ]
            ];

            if (isset($brickData['issuer_id'])) {
                $payment_data['issuer_id'] = (int) $brickData['issuer_id'];
            }

            // 4. CHAMAR API MERCADO PAGO
            // (Assumindo que você tem essa classe configurada)
            $mp = new MercadoPagoService();
            $resultadoMP = $mp->processarPagamentoBrick($payment_data);

            // 5. Verificar Resultado
            if (isset($resultadoMP['status']) && $resultadoMP['status'] == 'approved') {
                $statusVenda = 'pago';
                $dataPagamento = date('Y-m-d H:i:s');
            } else {
                $statusVenda = 'pendente'; // Pix/Boleto ou em análise
                $dataPagamento = null;
            }

            // 6. SALVAR NO BANCO (USANDO O MODEL ROBUSTO)
            $dadosVenda = [
                'usuario_id' => $_SESSION['cliente_id'],
                'cliente_id' => $_SESSION['cliente_id'],
                'total' => $totalVenda,
                'valor_recebido' => ($statusVenda == 'pago') ? $totalVenda : 0,
                'forma_pagamento' => $brickData['payment_method_id'],
                'status_pagamento' => $statusVenda,
                'data_pagamento' => $dataPagamento,

                // Dados de Entrega (Vindo do JS)
                'tipo_entrega' => $dadosExtras['tipo_entrega'] ?? 'retirada',
                'endereco_entrega' => $dadosExtras['endereco_entrega_texto'] ?? 'Retirada',
                'data_entrega' => $dadosExtras['data_entrega'] ?? null,
                'observacoes' => 'Venda Online - MP ID: ' . ($resultadoMP['id'] ?? 'N/A')
            ];

            // Salva Venda + Itens + Baixa Estoque
            $resultadoBD = Venda::registrar($dadosVenda, $_SESSION['carrinho_temp']['itens']);

            if ($resultadoBD['sucesso']) {
                // Sucesso Total
                unset($_SESSION['carrinho_temp']);
                echo json_encode(['sucesso' => true, 'venda_id' => $resultadoBD['venda_id']]);
            } else {
                // Erro ao salvar no banco (mas cobrou no MP? Ideal seria estornar, mas aqui só avisamos)
                echo json_encode(['sucesso' => false, 'msg' => 'Erro ao salvar pedido: ' . $resultadoBD['msg']]);
            }

        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'msg' => 'Erro no servidor: ' . $e->getMessage()]);
        }
    }

    public function sair()
    {
        unset($_SESSION['cliente_id'], $_SESSION['cliente_nome'], $_SESSION['cliente_email']);
        header('Location: ' . BASE_URL . 'cliente/login');
    }

    private function verificarLogin()
    {
        if (!isset($_SESSION['cliente_id'])) {
            header('Location: ' . BASE_URL . 'cliente/login');
            exit;
        }
    }

    public function meusDados()
    {
        if (session_status() == PHP_SESSION_NONE)
            session_start();

        if (!isset($_SESSION['cliente_id'])) {
            header('Location: ' . BASE_URL . 'cliente/login');
            exit;
        }

        require_once __DIR__ . '/../models/Cliente.php';

        // 1. Busca os dados pessoais (Nome, CPF, Telefone, Email)
        $cliente = Cliente::buscarPorId($_SESSION['cliente_id']);

        // 2. Busca todos os endereços vinculados a este cliente
        $enderecos = Cliente::buscarEnderecos($_SESSION['cliente_id']);

        // 3. Carrega a View passando as duas variáveis
        require __DIR__ . '/../views/clientes/dados.php';
    }

    public function salvarDados()
    {
        if (session_status() == PHP_SESSION_NONE)
            session_start();
        if (!isset($_SESSION['cliente_id'])) {
            header('Location: ' . BASE_URL . 'cliente/login');
            exit;
        }

        require_once __DIR__ . '/../models/Cliente.php';
        $pdo = Database::connect();

        // 1. Pega APENAS os dados pessoais
        $dadosPessoais = [
            'nome' => $_POST['nome'] ?? '',
            'cpf' => $_POST['cpf'] ?? '',
            'telefone' => $_POST['telefone'] ?? '',
            'email' => $_POST['email'] ?? '',
            'senha' => $_POST['nova_senha'] ?? ''
        ];

        // Atualiza a tabela Clientes
        $resultado = Cliente::atualizar($_SESSION['cliente_id'], $dadosPessoais);

        if ($resultado['sucesso']) {
            $_SESSION['cliente_nome'] = $dadosPessoais['nome']; // Atualiza sessão

            // 2. Pega os dados de Endereço
            $cep = $_POST['cep'] ?? '';
            $rua = $_POST['rua'] ?? '';
            $numero = $_POST['numero'] ?? '';
            $bairro = $_POST['bairro'] ?? '';
            $cidade = $_POST['cidade'] ?? '';
            $estado = $_POST['estado'] ?? '';

            // Só salva se o cliente preencheu pelo menos a rua e o cep
            if (!empty($cep) && !empty($rua)) {

                // Verifica se o cliente já tem um endereço principal (is_padrao = 1)
                $stmt = $pdo->prepare("SELECT id FROM enderecos WHERE cliente_id = ? AND is_padrao = 1");
                $stmt->execute([$_SESSION['cliente_id']]);
                $enderecoExiste = $stmt->fetch();

                if ($enderecoExiste) {
                    // Atualiza o endereço existente
                    $sqlEnd = "UPDATE enderecos SET cep=?, rua=?, numero=?, bairro=?, cidade=?, estado=? WHERE id=?";
                    $pdo->prepare($sqlEnd)->execute([$cep, $rua, $numero, $bairro, $cidade, $estado, $enderecoExiste['id']]);
                } else {
                    // Insere um novo endereço e marca como padrão (1)
                    $sqlEnd = "INSERT INTO enderecos (cliente_id, cep, rua, numero, bairro, cidade, estado, is_padrao) VALUES (?, ?, ?, ?, ?, ?, ?, 1)";
                    $pdo->prepare($sqlEnd)->execute([$_SESSION['cliente_id'], $cep, $rua, $numero, $bairro, $cidade, $estado]);
                }
            }
        }

        header('Location: ' . BASE_URL . 'cliente/meusDados?msg=' . urlencode($resultado['msg']) . '&sucesso=' . ($resultado['sucesso'] ? 1 : 0));
        exit;
    }

    public function salvarSessaoCarrinho()
    {
        if (session_status() == PHP_SESSION_NONE)
            session_start();
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        $itensJS = $input['itens'] ?? [];

        if (empty($itensJS)) {
            echo json_encode(['sucesso' => false, 'msg' => 'Carrinho vazio']);
            exit;
        }

        $pdo = Database::connect();
        $carrinhoFinal = [];
        $totalGeral = 0;

        foreach ($itensJS as $prodId => $qtd) {
            if ($qtd > 0) {
                // --- MUDANÇA 1: BUSCAR CAMPOS DE PROMOÇÃO ---
                $stmt = $pdo->prepare("SELECT id, nome, preco, preco_promocao, promocao, imagem FROM produtos WHERE id = ?");
                $stmt->execute([$prodId]);
                $prod = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($prod) {
                    // --- MUDANÇA 2: LÓGICA DE PREÇO NO BACKEND ---
                    // Isso garante que o valor cobrado será o promocional, se houver
                    $precoUnitario = $prod['preco'];

                    if ($prod['promocao'] == 1 && $prod['preco_promocao'] > 0) {
                        $precoUnitario = $prod['preco_promocao'];
                    }

                    $totalItem = $precoUnitario * $qtd;
                    $totalGeral += $totalItem;

                    $carrinhoFinal[] = [
                        'id' => $prod['id'],
                        'nome' => $prod['nome'],
                        'preco_unitario' => $precoUnitario, // Salva o preço já com desconto
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
        $this->verificarLogin(); // Garante que está logado

        if (!isset($_SESSION['carrinho_temp']) || empty($_SESSION['carrinho_temp']['itens'])) {
            header('Location: ' . BASE_URL . 'vitrine');
            exit;
        }

        // Busca dados atualizados do cliente (endereço)
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
        $stmt->execute([$_SESSION['cliente_id']]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        $carrinho = $_SESSION['carrinho_temp'];

        require __DIR__ . '/../views/clientes/checkout_carrinho.php';
    }

    public function processarPedidoCompleto()
    {
        // Limpa buffer e define cabeçalho JSON
        if (ob_get_length())
            ob_clean();
        header('Content-Type: application/json');

        if (session_status() == PHP_SESSION_NONE)
            session_start();

        // 1. VERIFICAÇÕES BÁSICAS
        if (!isset($_SESSION['cliente_id']) || empty($_SESSION['carrinho_temp'])) {
            echo json_encode(['sucesso' => false, 'msg' => 'Sessão expirada ou carrinho vazio.']);
            exit;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $brickData = $input['formData'] ?? [];
            $dadosExtras = $input['dadosExtras'] ?? []; // Aqui vêm os dados da entrega

            $carrinho = $_SESSION['carrinho_temp'];
            $clienteId = $_SESSION['cliente_id'];
            $totalCompra = (float) $carrinho['total'];

            // 2. CAPTURAR DADOS DE ENTREGA (CORRIGIDO)
            // O Javascript já manda o texto pronto em 'endereco_entrega_texto'
            $tipoEntrega = $dadosExtras['tipo_entrega'] ?? 'retirada';
            $enderecoTexto = $dadosExtras['endereco_entrega_texto'] ?? 'Retirada na Loja';

            // Tratamento da Data de Entrega (se vier vazia ou nula, salva NULL no banco)
            $dataEntrega = !empty($dadosExtras['data_entrega']) ? $dadosExtras['data_entrega'] : null;

            // 3. PROCESSAR PAGAMENTO NO MERCADO PAGO
            $payment_data = [
                'transaction_amount' => (float) $totalCompra,
                'description' => 'Pedido Web - Cliente #' . $clienteId,
                'payment_method_id' => $brickData['payment_method_id'],
                'payer' => [
                    'email' => $_SESSION['cliente_email'] ?? 'cliente@email.com',
                    'first_name' => explode(' ', $_SESSION['cliente_nome'] ?? 'Cliente')[0],
                    'identification' => [
                        'type' => $brickData['payer']['identification']['type'] ?? 'CPF',
                        'number' => $brickData['payer']['identification']['number'] ?? ''
                    ]
                ]
            ];

            if (!empty($brickData['token'])) {
                $payment_data['token'] = $brickData['token'];
                $payment_data['installments'] = (int) ($brickData['installments'] ?? 1);
                $payment_data['issuer_id'] = (int) $brickData['issuer_id'];
            }

            $mp = new MercadoPagoService();
            $resultado = $mp->processarPagamentoBrick($payment_data);

            // 4. VERIFICAR SE FOI CRIADO
            $statusAceitos = ['approved', 'in_process', 'pending'];
            $statusMP = $resultado['status'] ?? 'erro';

            if (in_array($statusMP, $statusAceitos)) {

                $pdo = Database::connect();
                $pdo->beginTransaction();

                try {
                    // A) DEFINIR STATUS
                    if ($statusMP === 'approved') {
                        $statusVenda = 'pago';
                        $valorPagoVenda = $totalCompra;
                        $dataPagamento = date('Y-m-d H:i:s');
                    } else {
                        $statusVenda = 'pendente';
                        $valorPagoVenda = 0.00;
                        $dataPagamento = null;
                    }

                    // B) CRIAR VENDA (CORRIGIDO: INCLUÍDAS AS COLUNAS DE ENTREGA)
                    $sqlVenda = "INSERT INTO vendas (
                        cliente_id, 
                        data_venda, 
                        total, 
                        valor_pago, 
                        status_pagamento, 
                        data_pagamento,
                        forma_pagamento,
                        tipo_entrega, 
                        endereco_entrega, 
                        data_entrega, 
                        observacoes
                    ) VALUES (?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                    $obs = "Venda Online (MP ID: " . ($resultado['id'] ?? 'N/A') . ")";

                    $stmtVenda = $pdo->prepare($sqlVenda);
                    $stmtVenda->execute([
                        $clienteId,
                        $totalCompra,
                        $valorPagoVenda,
                        $statusVenda,
                        $dataPagamento,
                        $brickData['payment_method_id'],
                        $tipoEntrega,     // Salva se é 'entrega' ou 'retirada'
                        $enderecoTexto,   // Salva o endereço completo
                        $dataEntrega,     // Salva a data agendada (ou null)
                        $obs
                    ]);

                    $vendaId = $pdo->lastInsertId();

                    // C) INSERIR ITENS E BAIXAR ESTOQUE
                    foreach ($carrinho['itens'] as $item) {
                        // Subtotal calculado
                        $subtotal = $item['quantidade'] * $item['preco_unitario'];

                        $sqlItem = "INSERT INTO itens_venda (venda_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)";
                        $pdo->prepare($sqlItem)->execute([$vendaId, $item['id'], $item['quantidade'], $item['preco_unitario']]);

                        $sqlEstoque = "UPDATE produtos SET estoque = estoque - ? WHERE id = ?";
                        $pdo->prepare($sqlEstoque)->execute([$item['quantidade'], $item['id']]);
                    }

                    // D) SALVAR LOG DETALHADO DO PAGAMENTO
                    $transactionDetails = json_encode($resultado['transaction_details'] ?? []);
                    $paymentType = $resultado['payment_type_id'] ?? 'unknown';
                    $installments = $resultado['installments'] ?? 1;
                    $installmentValue = $resultado['transaction_details']['installment_amount'] ?? $totalCompra;
                    $totalPaid = $resultado['transaction_details']['total_paid_amount'] ?? 0;

                    $sqlPag = "INSERT INTO pagamentos (
                        venda_id, cliente_id, mp_id, status, descricao, 
                        transaction_details, valor, data_criacao, 
                        payment_type, payment_method, installments, 
                        installment_value, total_paid
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?)";

                    $pdo->prepare($sqlPag)->execute([
                        $vendaId,
                        $clienteId,
                        $resultado['id'],
                        $statusMP,
                        $resultado['description'] ?? 'Venda Site',
                        $transactionDetails,
                        $totalCompra,
                        $paymentType,
                        $resultado['payment_method_id'],
                        $installments,
                        $installmentValue,
                        $totalPaid
                    ]);

                    $pdo->commit();

                    // F) RESPOSTA FINAL
                    unset($_SESSION['carrinho_temp']);

                    $retorno = ['sucesso' => true, 'venda_id' => $vendaId, 'status' => $statusMP];

                    // Pix
                    if ($brickData['payment_method_id'] === 'pix') {
                        $retorno['pix_copia_cola'] = $resultado['point_of_interaction']['transaction_data']['qr_code'] ?? '';
                        $retorno['pix_base64'] = $resultado['point_of_interaction']['transaction_data']['qr_code_base64'] ?? '';
                    }
                    // Boleto
                    if (isset($resultado['transaction_details']['external_resource_url'])) {
                        $retorno['ticket_url'] = $resultado['transaction_details']['external_resource_url'];
                    }

                    echo json_encode($retorno);

                } catch (Exception $eDb) {
                    $pdo->rollBack();
                    throw new Exception("Erro ao salvar pedido: " . $eDb->getMessage());
                }

            } else {
                $msg = $resultado['status_detail'] ?? 'Pagamento recusado';
                echo json_encode(['sucesso' => false, 'msg' => "Erro: $msg ($statusMP)"]);
            }

        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'msg' => 'Erro interno: ' . $e->getMessage()]);
        }
        exit;
    }

    public function telaPedidoConfirmado()
    {
        $this->verificarLogin();

        $vendaId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$vendaId) {
            header('Location: ' . BASE_URL . 'cliente/painel');
            exit;
        }

        $pdo = Database::connect();

        // 1. Busca a Venda e garante que é deste cliente
        $stmt = $pdo->prepare("SELECT * FROM vendas WHERE id = ? AND cliente_id = ?");
        $stmt->execute([$vendaId, $_SESSION['cliente_id']]);
        $venda = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$venda)
            die("Venda não encontrada.");

        // 2. Busca o Pagamento associado
        $stmtPag = $pdo->prepare("SELECT * FROM pagamentos WHERE venda_id = ? ORDER BY id DESC LIMIT 1");
        $stmtPag->execute([$vendaId]);
        $pagamentoDB = $stmtPag->fetch(PDO::FETCH_ASSOC);

        // 3. SE NÃO ESTIVER APROVADO, BUSCAMOS DADOS NA API (VIA CURL)
        $dadosMP = null;

        if ($pagamentoDB && $pagamentoDB['status'] !== 'approved') {

            // --- CÓDIGO MANUAL SEM SDK ---
            $mpId = $pagamentoDB['mp_id'];
            $url = "https://api.mercadopago.com/v1/payments/$mpId";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . MP_ACCESS_TOKEN, // Seu token do config.php
                'Content-Type: application/json'
            ]);

            // Descomente abaixo se der erro de SSL no Localhost
            // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);

            if (!curl_errno($ch)) {
                // Decodifica como OBJETO (sem o true no segundo parametro)
                // Isso é importante porque sua View acessa como $dadosMP->propriedade
                $dadosMP = json_decode($response);
            }

            curl_close($ch);
            // -----------------------------
        }

        require __DIR__ . '/../views/clientes/pedido_confirmado.php';
    }

    // Processa o formulário de NOVO ENDEREÇO
    public function salvar_endereco()
    {
        if (session_status() == PHP_SESSION_NONE)
            session_start();
        $this->verificarLogin();

        require_once __DIR__ . '/../models/Cliente.php';

        $dados = [
            'cliente_id' => $_SESSION['cliente_id'],
            'cep' => $_POST['cep'] ?? '',
            'rua' => $_POST['rua'] ?? '',
            'numero' => $_POST['numero'] ?? '',
            'bairro' => $_POST['bairro'] ?? '',
            'cidade' => $_POST['cidade'] ?? '',
            'estado' => $_POST['estado'] ?? '',
            'complemento' => $_POST['complemento'] ?? ''
        ];

        if (Cliente::salvarEndereco($dados)) {
            header('Location: ' . BASE_URL . 'cliente/meusDados?msg=' . urlencode('Endereço cadastrado com sucesso!') . '&sucesso=1');
        } else {
            header('Location: ' . BASE_URL . 'cliente/meusDados?msg=' . urlencode('Erro ao cadastrar endereço.') . '&sucesso=0');
        }
        exit;
    }

    // Processa a exclusão do endereço
    public function excluir_endereco()
    {
        if (session_status() == PHP_SESSION_NONE)
            session_start();
        $this->verificarLogin();

        $id = $_GET['id'] ?? 0;
        require_once __DIR__ . '/../models/Cliente.php';

        if (Cliente::excluirEndereco($id, $_SESSION['cliente_id'])) {
            header('Location: ' . BASE_URL . 'cliente/meusDados?msg=' . urlencode('Endereço removido com sucesso!') . '&sucesso=1');
        } else {
            header('Location: ' . BASE_URL . 'cliente/meusDados?msg=' . urlencode('Erro ao remover endereço.') . '&sucesso=0');
        }
        exit;
    }
}