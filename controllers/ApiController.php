<?php
require_once __DIR__ . '/../models/Produto.php';
require_once __DIR__ . '/../models/Venda.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../config/Database.php';

class ApiController
{

    public function index()
    {
        // Apenas admin ou master podem ver a lista de usuários
        Auth::verificar(['admin', 'master']);

        require_once __DIR__ . '/../models/Usuario.php';

        // Busca a lista
        $usuarios = Usuario::listarParaAdmin();

        // Carrega a tela
        require __DIR__ . '/../views/adm/admin_usuarios.php';
    }
    public function buscarProduto()
    {
        header('Content-Type: application/json');
        $codigo = $_GET['codigo'] ?? '';

        $produto = Produto::buscarPorCodigo($codigo);

        if ($produto) {
            echo json_encode(['sucesso' => true, 'produto' => $produto]);
        } else {
            echo json_encode(['sucesso' => false, 'msg' => 'Produto não encontrado']);
        }
    }

    public function finalizarVenda()
    {
        header('Content-Type: application/json');

        // 1. Garante sessão e Login
        if (session_status() == PHP_SESSION_NONE)
            session_start();

        $idUsuario = $_SESSION['usuario_id'] ?? null;
        if (!$idUsuario) {
            echo json_encode(['sucesso' => false, 'msg' => 'Sessão expirada. Faça login.']);
            return;
        }

        // 2. Recebe JSON
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || empty($input['itens'])) {
            echo json_encode(['sucesso' => false, 'msg' => 'Carrinho vazio ou dados inválidos.']);
            return;
        }

        // 3. PREPARAÇÃO DOS DADOS (Aqui está o ajuste principal)
        // Agrupamos tudo num array para passar ao Model novo

        $tipoEntrega = $input['tipo_entrega'] ?? 'retirada';
        $enderecoTexto = $input['endereco_entrega_texto'] ?? '';

        // Define uma observação automática baseada na entrega
        $obs = ($tipoEntrega === 'entrega') ? "Entrega: $enderecoTexto" : "Retirada na Loja";

        $dadosVenda = [
            // Dados Básicos
            'usuario_id' => $idUsuario,
            'cliente_id' => $input['cliente_id'] ?? null,
            'total' => $input['total'],
            'forma_pagamento' => $input['forma_pagamento'],
            'valor_recebido' => $input['valor_recebido'] ?? 0,

            // Dados Novos (Correção para salvar endereço)
            'tipo_entrega' => $tipoEntrega,
            'endereco_entrega' => $enderecoTexto,
            'observacoes' => $obs,
            'data_entrega' => $input['data_entrega'] ?? null, // Padrão NULL, será preenchido quando sair para entrega

            // Opcional: Se quiser forçar o status aqui, pode. 
            // Se não passar, o Model calcula (como fizemos na lógica anterior).
        ];

        // 4. CHAMADA AO MODEL ATUALIZADO
        // Agora passamos o ARRAY de dados e o ARRAY de itens
        $resultado = Venda::registrar($dadosVenda, $input['itens']);

        echo json_encode($resultado);
    }

    public function pesquisarProdutos()
    {
        header('Content-Type: application/json');
        $termo = $_GET['termo'] ?? '';

        $pdo = Database::connect();

        if (empty($termo)) {
            // Traz os 50 primeiros se não digitar nada
            $stmt = $pdo->query("SELECT * FROM produtos ORDER BY nome ASC LIMIT 50");
            $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Filtra pelo nome
            $stmt = $pdo->prepare("SELECT * FROM produtos WHERE nome LIKE ? LIMIT 20");
            $stmt->execute(["%$termo%"]);
            $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        echo json_encode(['sucesso' => true, 'produtos' => $produtos]);
    }

    public function pesquisarCliente()
    {
        header('Content-Type: application/json');
        $termo = $_GET['termo'] ?? '';

        $pdo = Database::connect();

        // Busca por Nome ou CPF
        $stmt = $pdo->prepare("SELECT id, nome, cpf FROM clientes WHERE nome LIKE ? OR cpf LIKE ? LIMIT 10");
        $stmt->execute(["%$termo%", "%$termo%"]);

        echo json_encode(['sucesso' => true, 'clientes' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    }

    public function itensVenda()
    {
        header('Content-Type: application/json');

        // Verifica se usuário está logado para segurança (opcional, mas recomendado)
        // if (!isset($_SESSION['cliente_id']) && !isset($_SESSION['usuario_id'])) {
        //     echo json_encode(['sucesso' => false, 'msg' => 'Acesso negado']);
        //     return;
        // }

        $vendaId = (int) ($_GET['id'] ?? 0);

        if ($vendaId <= 0) {
            echo json_encode(['sucesso' => false, 'msg' => 'ID inválido']);
            return;
        }

        $pdo = Database::connect();

        // Busca os itens e o nome do produto
        // Ajuste 'itens_venda' e 'produtos' conforme os nomes reais das suas tabelas
        $sql = "SELECT 
                    iv.quantidade, 
                    iv.preco_unitario, 
                    p.nome 
                FROM itens_venda iv
                JOIN produtos p ON iv.produto_id = p.id
                WHERE iv.venda_id = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$vendaId]);
        $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['sucesso' => true, 'itens' => $itens]);
    }

    public function detalhesVendaAdmin()
    {
        header('Content-Type: application/json');

        // Segurança básica: Apenas Admins/Vendedores logados podem ver
        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['sucesso' => false, 'msg' => 'Acesso negado']);
            return;
        }

        $vendaId = (int) ($_GET['id'] ?? 0);

        try {
            $pdo = Database::connect();

            // 1. Busca Dados da Venda + Cliente + Vendedor
            $sqlVenda = "SELECT 
                            v.*, 
                            c.nome as cliente_nome, 
                            c.email as cliente_email, 
                            c.data_cadastro as cliente_desde,
                            u.nome as vendedor_nome
                         FROM vendas v
                         LEFT JOIN clientes c ON v.cliente_id = c.id
                         LEFT JOIN usuarios u ON v.usuario_id = u.id
                         WHERE v.id = ?";
            $stmt = $pdo->prepare($sqlVenda);
            $stmt->execute([$vendaId]);
            $venda = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$venda) {
                echo json_encode(['sucesso' => false, 'msg' => 'Venda não encontrada']);
                return;
            }

            // 2. Busca os Itens + Imagem do Produto
            $sqlItens = "SELECT 
                            iv.quantidade, 
                            iv.preco_unitario, 
                            p.nome as produto_nome, 
                            p.imagem
                         FROM itens_venda iv
                         JOIN produtos p ON iv.produto_id = p.id
                         WHERE iv.venda_id = ?";
            $stmt = $pdo->prepare($sqlItens);
            $stmt->execute([$vendaId]);
            $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['sucesso' => true, 'venda' => $venda, 'itens' => $itens]);

        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'msg' => 'Erro SQL: ' . $e->getMessage()]);
        }
    }

  public function dadosRelatorio()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        header('Content-Type: application/json');

        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['sucesso' => false, 'msg' => 'Acesso negado']);
            return;
        }

        try {
            $pdo = Database::connect();

            $dataInicio = $_GET['data_inicio'] ?? date('Y-m-01');
            $dataFim = $_GET['data_fim'] ?? date('Y-m-t');
            $vendedorId = $_GET['vendedor_id'] ?? '';
            $status = $_GET['status'] ?? '';
            $pagamento = $_GET['forma_pagamento'] ?? '';
            $produtoNome = $_GET['produto_nome'] ?? '';

            // AQUI ESTAVA O ERRO DE SQL: Apelidamos as colunas v.id e v.total
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

            // CORRIGIDO: v.id ao invés de v.venda_id
            if (!empty($produtoNome)) {
                $sql .= " INNER JOIN itens_venda iv ON v.id = iv.venda_id 
                          INNER JOIN produtos p ON iv.produto_id = p.id ";
            }

            $sql .= " WHERE v.data_venda BETWEEN ? AND ? ";
            $params = [$dataInicio . ' 00:00:00', $dataFim . ' 23:59:59'];

            // CORRIGIDO: Todos os filtros agora usam v. (vendas) e não vw.
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
            $vendas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $totalVendido = 0;
            $totalRecebido = 0;
            $porVendedor = [];
            $porPagamento = [];
            $porDia = [];

            foreach ($vendas as $v) {
                $totalVendido += $v['total'];
                $totalRecebido += $v['valor_pago'];

                $vend = !empty($v['vendedor']) ? $v['vendedor'] : 'Site / Online';
                if (!isset($porVendedor[$vend])) {
                    $porVendedor[$vend] = 0;
                }
                $porVendedor[$vend] += $v['total'];

                $pg = !empty($v['forma_pagamento']) ? $v['forma_pagamento'] : 'Indefinido';
                if (!isset($porPagamento[$pg])) {
                    $porPagamento[$pg] = 0;
                }
                $porPagamento[$pg] += $v['total'];

                $dia = date('d/m', strtotime($v['data_venda']));
                if (!isset($porDia[$dia])) {
                    $porDia[$dia] = 0;
                }
                $porDia[$dia] += $v['total'];
            }

            echo json_encode([
                'sucesso' => true,
                'lista' => $vendas,
                'resumo' => [
                    'total_vendido' => $totalVendido,
                    'total_recebido' => $totalRecebido,
                    'ticket_medio' => count($vendas) > 0 ? $totalVendido / count($vendas) : 0,
                    'qtd_vendas' => count($vendas)
                ],
                'graficos' => [
                    'vendedor' => $porVendedor,
                    'pagamento' => $porPagamento,
                    'timeline' => $porDia
                ]
            ]);

        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'msg' => $e->getMessage()]);
        }
    }

    public function entregas()
    {
        // Verifica se é admin (use sua lógica de auth aqui)
        // Auth::checkAdmin(); 

        $pdo = Database::connect();

        // --- FILTROS ---
        $filtroData = $_GET['data'] ?? date('Y-m-d'); // Padrão: Hoje
        $filtroStatus = $_GET['status'] ?? 'todos';   // Padrão: Todos

        // Montagem da Query
        $sql = "SELECT v.*, c.nome as cliente_nome, c.telefone as cliente_telefone 
                FROM vendas v 
                JOIN clientes c ON v.cliente_id = c.id 
                WHERE v.tipo_entrega = 'entrega'";

        $params = [];

        // 1. Filtro de Data (Ignora hora, compara só o dia)
        if (!empty($filtroData)) {
            $sql .= " AND DATE(v.data_entrega) = ?";
            $params[] = $filtroData;
        }

        // 2. Filtro de Status Pagamento
        if ($filtroStatus !== 'todos') {
            $sql .= " AND v.status_pagamento = ?";
            $params[] = $filtroStatus;
        }

        // Ordenar: Pendentes primeiro, depois por hora de entrega
        $sql .= " ORDER BY v.status_pagamento ASC, v.data_entrega ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $entregas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Carrega a View
        require __DIR__ . '/../views/adm/entregas.php';
    }

    public function confirmarEntrega()
    {
        // Auth::checkAdmin(); // Se tiver autenticação

        header('Content-Type: application/json');

        $id = $_POST['id'] ?? 0;

        if (!$id) {
            echo json_encode(['sucesso' => false, 'msg' => 'ID inválido']);
            exit;
        }

        try {
            $pdo = Database::connect();

            // Atualiza para 'entregue'
            $stmt = $pdo->prepare("UPDATE vendas SET status_entrega = 'entregue' WHERE id = ?");
            $stmt->execute([$id]);

            echo json_encode(['sucesso' => true]);

        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'msg' => 'Erro ao atualizar: ' . $e->getMessage()]);
        }
    }

    public function editar()
    {
        Auth::verificar(['admin', 'master']);
        $id = $_GET['id'] ?? null;

        if (!$id)
            die('Usuário não informado.');

        require_once __DIR__ . '/../models/Usuario.php';
        $usuario = Usuario::buscarPorId($id);

        if (!$usuario)
            die('Usuário não encontrado.');

        require __DIR__ . '/../views/adm/admin_usuario_editar.php';
    }

    public function salvar_edicao()
    {
        Auth::verificar(['admin', 'master']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../models/Usuario.php';

            $id = $_POST['id'];
            $dados = [
                'nome' => $_POST['nome'],
                'email' => $_POST['email'],
                'usuario' => $_POST['usuario'],
                'nivel' => $_POST['nivel']
            ];

            // Se digitou uma senha nova, criptografa e adiciona aos dados
            if (!empty($_POST['senha'])) {
                $dados['senha'] = password_hash($_POST['senha'], PASSWORD_DEFAULT);
            }

            Usuario::atualizar($id, $dados);

            echo "<script>alert('Usuário atualizado com sucesso!'); window.location='" . BASE_URL . "usuarios';</script>";
        }
    }

    public function excluir()
    {
        Auth::verificar(['admin', 'master']);
        $id = $_GET['id'] ?? null;

        if ($id && $id != $_SESSION['usuario_id']) { // Trava de segurança extra
            require_once __DIR__ . '/../models/Usuario.php';
            Usuario::excluir($id);
        }

        header('Location: ' . BASE_URL . 'usuarios');
        exit;
    }

    // Abre a tela de cadastro vazia
    public function novo()
    {
        Auth::verificar(['admin', 'master']);
        require __DIR__ . '/../views/adm/admin_usuario_novo.php';
    }

    // Processa os dados recebidos pelo formulário
    public function salvar()
    {
        Auth::verificar(['admin', 'master']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../models/Usuario.php';

            // Monta os dados
            $dados = [
                'nome' => $_POST['nome'],
                'email' => $_POST['email'],
                'usuario' => $_POST['usuario'],
                'nivel' => $_POST['nivel'],
                // Criptografa a senha antes de salvar no banco
                'senha' => password_hash($_POST['senha'], PASSWORD_DEFAULT)
            ];

            // Tenta salvar. Se houver um e-mail/usuário duplicado, você pode tratar o erro aqui se quiser.
            Usuario::salvarNovo($dados);

            echo "<script>alert('Usuário cadastrado com sucesso!'); window.location='" . BASE_URL . "usuarios';</script>";
        }
    }

    public function enderecos_cliente()
    {
        header('Content-Type: application/json');

        $idCliente = $_GET['id'] ?? 0;

        if ($idCliente <= 0) {
            echo json_encode(['sucesso' => false, 'msg' => 'ID inválido']);
            return;
        }

        // Usa a classe Cliente e o método que já criamos para puxar da nova tabela
        require_once __DIR__ . '/../models/Cliente.php';
        $enderecos = Cliente::buscarEnderecos($idCliente);

        echo json_encode([
            'sucesso' => true,
            'enderecos' => $enderecos
        ]);
    }
    
   public function chatIa()
    {
        header('Content-Type: application/json');

        $dados = json_decode(file_get_contents('php://input'), true);
        $mensagemUsuario = $dados['mensagem'] ?? '';

        if (empty($mensagemUsuario)) {
            echo json_encode(['sucesso' => false, 'msg' => 'Mensagem vazia.']);
            return;
        }

        try {
            $pdo = Database::connect();

            // 1. BUSCA INCLUINDO A DESCRIÇÃO DO PRODUTO (p.descricao)
            $stmt = $pdo->query("SELECT p.nome, p.preco, p.preco_promocao, p.promocao, p.descricao, p.imagem, c.nome as categoria 
                                 FROM produtos p 
                                 LEFT JOIN categorias c ON p.categoria_id = c.id 
                                 WHERE p.estoque > 0 
                                 ORDER BY c.nome ASC");
            $produtosLoja = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 2. FORMATA A LISTA (Agora a IA vai ler as especificações técnicas)
            $listaTexto = "";
            foreach ($produtosLoja as $p) {
                $preco = ($p['promocao'] == 1) ? $p['preco_promocao'] : $p['preco'];
                $descricaoLimpa = preg_replace('/\s+/', ' ', strip_tags($p['descricao'] ?? ''));

                // Monta o caminho da imagem (Se não tiver, deixa vazio)
                $urlImg = !empty($p['imagem']) ? BASE_URL . "public/uploads/" . $p['imagem'] : "";

                $listaTexto .= "- Produto: {$p['nome']} | Imagem: {$urlImg} | Categoria: {$p['categoria']} | Valor: R$ " . number_format($preco, 2, ',', '.') . " | Detalhes: {$descricaoLimpa}\n";
            }

            // 3. SELEÇÃO DA CHAVE DA API
            $apiKeys = [
                'AIzaSyC6WH7XPo28lBKwO-W51Pg-1qsugOfqPvo',
                'AIzaSyDoa-lpA8YY5_DPf0JPWhv2_Uau3EuoybA',
                'AIzaSyCvYKsZDUr9cX6_maoZUiiJObIjXhee54g',
                'AIzaSyDEH39Nkf3FV6jSLq6Ehai-CED6HOunwm4'
            ];
            $chaveEscolhida = $apiKeys[array_rand($apiKeys)];
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=" . $chaveEscolhida;

            // 4. INSTRUÇÕES AVANÇADAS (Forçando a exibição da imagem com regras estritas)
            $instrucoes = "Você é o especialista de vendas da KaByte, uma loja de periféricos e hardware.
            Sua missão é ajudar o cliente a encontrar itens na nossa lista de estoque abaixo.

            REGRAS CRÍTICAS DE FORMATAÇÃO E RESPOSTA:
            1. Analise os 'Detalhes' de cada produto. Exemplo: se pedirem para 'natação', sugira itens 'à prova d'água'.
            2. SEMPRE que recomendar ou citar um produto, você DEVE mostrar a foto dele logo acima do nome, usando EXATAMENTE o formato de imagem do Markdown: ![foto](URL_AQUI). A URL está no campo 'Imagem' da lista. Se não tiver URL, não coloque a imagem.
            3. Use **negrito** para nomes e preços. Exemplo: ![foto](https://...) **Teclado Gamer** - **R$ 150,00**.
            4. Seja simpático, prestativo e use emojis (🖱️, 🖥️, ⌨️, ⌚).
            5. Se o item não estiver na lista abaixo, diga: 'No momento não temos esse item específico em estoque, mas fique de olho no site!'.

            ESTOQUE REAL DA KABYTE AGORA (Com um resumo das especificações):
            $listaTexto";

            $body = [
                "contents" => [
                    ["role" => "user", "parts" => [["text" => $instrucoes . "\n\nPergunta do cliente: " . $mensagemUsuario]]]
                ]
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $respostaApi = curl_exec($ch);
            curl_close($ch);

            $resultado = json_decode($respostaApi, true);

            if (isset($resultado['candidates'][0]['content']['parts'][0]['text'])) {
                $textoResposta = $resultado['candidates'][0]['content']['parts'][0]['text'];
                echo json_encode(['sucesso' => true, 'resposta' => $textoResposta]);
            } else {
                $erroGoogle = $resultado['error']['message'] ?? 'A IA não conseguiu processar os dados.';
                echo json_encode(['sucesso' => false, 'msg' => $erroGoogle]);
            }

        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'msg' => $e->getMessage()]);
        }
    }
}