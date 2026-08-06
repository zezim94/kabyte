<?php
require_once __DIR__ . '/../models/Produto.php';
require_once __DIR__ . '/../models/Venda.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Seguranca.php';

class ApiController
{
    public $livre = ['chatIa', 'itensVenda'];

    public function finalizarVenda()
    {
        // 1. Escudo anti-HTML para garantir JSON puro
        if (ob_get_length()) {
            ob_clean();
        }
        header('Content-Type: application/json');

        // 2. Garante sessão e Login
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $idUsuario = $_SESSION['usuario_id'] ?? null;
        if (!$idUsuario) {
            echo json_encode(['sucesso' => false, 'msg' => 'Sessão expirada. Faça login.']);
            return;
        }

        // 3. Recebe JSON
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || empty($input['itens'])) {
            echo json_encode(['sucesso' => false, 'msg' => 'Carrinho vazio ou dados inválidos.']);
            return;
        }

        // === A MÁGICA DA SEGURANÇA: RECALCULAR O TOTAL NO SERVIDOR ===
        // Varre os itens do carrinho e soma (Preço * Quantidade)
        $totalCalculado = 0;
        foreach ($input['itens'] as $item) {
            $totalCalculado += ((float)$item['preco'] * (int)$item['qtd']);
        }

        // 4. PREPARAÇÃO DOS DADOS
        $tipoEntrega = $input['tipo_entrega'] ?? 'retirada';
        $enderecoTexto = $input['endereco_entrega_texto'] ?? '';
        $obs = ($tipoEntrega === 'entrega') ? "Entrega: $enderecoTexto" : "Retirada na Loja";

        $dadosVenda = [
            'usuario_id' => $idUsuario,
            'cliente_id' => $input['cliente_id'] ?? null,
            'total' => $totalCalculado, // <--- TOTAL SEGURO INJETADO AQUI
            'forma_pagamento' => $input['forma_pagamento'] ?? 'Dinheiro',
            'valor_recebido' => $input['valor_recebido'] ?? $totalCalculado,
            'tipo_entrega' => $tipoEntrega,
            'endereco_entrega' => $enderecoTexto,
            'observacoes' => $obs,
            'data_entrega' => $input['data_entrega'] ?? null,
        ];

        try {
            // 5. CHAMADA AO MODEL
            $resultado = Venda::registrar($dadosVenda, $input['itens']);
            echo json_encode($resultado);
        } catch (Throwable $e) {
            echo json_encode(['sucesso' => false, 'msg' => 'Erro interno: ' . $e->getMessage()]);
        }
    }

    public function pesquisarProdutos()
    {
        // Escudo anti-HTML
        if (ob_get_length()) {
            ob_clean();
        }
        header('Content-Type: application/json');

        $termo = $_GET['termo'] ?? '';
        $codigo = $_GET['codigo'] ?? '';

        try {
            // 1. Se a requisição for para ADICIONAR (via Código de Barras)
            if (!empty($codigo)) {
                $produto = Produto::buscarPorCodigo($codigo);

                if ($produto) {
                    echo json_encode(['sucesso' => true, 'produto' => $produto]);
                } else {
                    echo json_encode(['sucesso' => false, 'msg' => 'Produto não encontrado.']);
                }
                return; // Para a execução aqui
            }

            // 2. Se a requisição for de PESQUISA NO MODAL (via Termo)
            if (empty($termo)) {
                $produtos = Produto::listar();
            } else {
                $produtos = Produto::buscarPorTermo($termo);
            }

            echo json_encode(['sucesso' => true, 'produtos' => $produtos]);
        } catch (Throwable $e) {
            echo json_encode(['sucesso' => false, 'msg' => 'Erro interno: ' . $e->getMessage()]);
        }
    }

    public function pesquisarCliente()
    {
        if (ob_get_length()) {
            ob_clean();
        }
        header('Content-Type: application/json');

        $termo = $_GET['termo'] ?? '';

        try {

            // 1. O Model faz a busca pesada (criptografada ou por nome)
            $clientes = Cliente::pesquisarPorTermo($termo);

            // 2. Antes de enviar para o PDV, destrancamos e filtramos o CPF
            foreach ($clientes as $chave => $cliente) {
                if (!empty($cliente['cpf'])) {
                    $cpfAberto = Seguranca::descriptografar($cliente['cpf']);

                    // Aplica a máscara de segurança (ex: ***.456.789-**)
                    if (strlen($cpfAberto) === 11) {
                        $clientes[$chave]['cpf'] = "***." . substr($cpfAberto, 3, 3) . "." . substr($cpfAberto, 6, 3) . "-**";
                    } else {
                        $clientes[$chave]['cpf'] = $cpfAberto;
                    }
                }
            }

            echo json_encode(['sucesso' => true, 'clientes' => $clientes]);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'msg' => 'Erro na busca: ' . $e->getMessage()]);
        }
    }

    public function itensVenda()
    {
        header('Content-Type: application/json');

        $vendaId = (int) ($_GET['id'] ?? 0);

        if ($vendaId <= 0) {
            echo json_encode(['sucesso' => false, 'msg' => 'ID inválido']);
            return;
        }

        $itens = Venda::listarItens($vendaId);

        echo json_encode(['sucesso' => true, 'itens' => $itens]);
    }

    public function entregas()
    {
        // 1. Prepara os filtros baseados na URL
        $filtros = [
            'data'   => $_GET['data'] ?? date('Y-m-d'),
            'status' => $_GET['status'] ?? 'todos'
        ];

        // 2. Pede ao Model para fazer o trabalho pesado
        $entregas = Venda::listarEntregas($filtros);

        // 3. Carrega a View
        require __DIR__ . '/../views/adm/entregas.php';
    }

    public function confirmarEntrega()
    {
        // Escudo anti-HTML para garantir que o JavaScript receba um JSON limpo
        if (ob_get_length()) {
            ob_clean();
        }
        header('Content-Type: application/json');

        // Força a conversão para inteiro por segurança
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            echo json_encode(['sucesso' => false, 'msg' => 'ID da venda inválido.']);
            exit;
        }

        try {
            // Chama a nova função do Model
            Venda::confirmarEntrega($id);

            // Adicionamos a 'msg' aqui para o Toast do JavaScript poder ler!
            echo json_encode(['sucesso' => true, 'msg' => 'Entrega confirmada com sucesso!']);
            exit;
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'msg' => 'Erro ao confirmar entrega: ' . $e->getMessage()]);
            exit;
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
        $enderecos = Cliente::buscarEnderecos($idCliente);

        echo json_encode([
            'sucesso' => true,
            'enderecos' => $enderecos
        ]);
    }

    public function chatIa()
    {
        header('Content-Type: application/json');
        require_once __DIR__ . '/../models/Chave.php';

        $dados = json_decode(file_get_contents('php://input'), true);
        $mensagemUsuario = $dados['mensagem'] ?? '';

        if (empty($mensagemUsuario)) {
            echo json_encode(['sucesso' => false, 'msg' => 'Mensagem vazia.']);
            return;
        }

        try {

            $produtosLoja = Produto::listarIa();

            // 2. FORMATA A LISTA (Agora incluindo o Link de Detalhes com URL LIMPA)
            $listaTexto = "";
            foreach ($produtosLoja as $p) {
                $preco = ($p['promocao'] == 1) ? $p['preco_promocao'] : $p['preco'];
                $descricaoLimpa = preg_replace('/\s+/', ' ', strip_tags($p['descricao'] ?? ''));

                // Monta o caminho da imagem
                $urlImg = !empty($p['imagem']) ? BASE_URL . "public/uploads/" . $p['imagem'] : "";

                // URL LIMPA PARA OS DETALHES DO PRODUTO
                $urlDetalhes = BASE_URL . "produto/detalhes?id=" . $p['id'];

                $listaTexto .= "- Produto: {$p['nome']} | LinkDetalhes: {$urlDetalhes} | Imagem: {$urlImg} | Categoria: {$p['categoria']} | Valor: R$ " . number_format($preco, 2, ',', '.') . " | Detalhes: {$descricaoLimpa}\n";
            }

            // 3. SELEÇÃO DA CHAVE DA API
            $apiKeys = Chave::getChavesGemini();

            if (empty($apiKeys)) {
                echo json_encode(['sucesso' => false, 'msg' => 'Nenhuma chave de IA configurada no banco de dados.']);
                return;
            }

            $chaveEscolhida = $apiKeys[array_rand($apiKeys)];
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=" . $chaveEscolhida;

            // 4. INSTRUÇÕES AVANÇADAS REFINADAS (Exemplo de URL atualizado)
            $instrucoes = "Você é o especialista de vendas da KaByte, uma loja de periféricos e hardware.
            Sua missão é ajudar o cliente a encontrar itens na nossa lista de estoque abaixo.

            REGRAS CRÍTICAS DE FORMATAÇÃO E RESPOSTA:
            1. Analise os 'Detalhes' de cada produto para entender a necessidade do cliente.
            2. SEMPRE que recomendar ou citar um produto, você DEVE mostrar a foto dele logo acima do nome, usando o formato de imagem do Markdown: ![foto](URL_DA_IMAGEM).
            3. O nome do produto deve ser obrigatoriamente um link clicável que aponte para a URL do campo 'LinkDetalhes' usando a formatação Markdown [Nome do Produto](URL_DO_LINK).
            4. Use **negrito** para destacar o link do produto e o preço. 
                Exemplo completo de exibição:
                ![foto](http://localhost/.../imagem.jpg)
                **[Mouse Gamer Razer](" . BASE_URL . "produto/detalhes?id=5)** - **R$ 250,00**
            5. Seja simpático, prestativo e use emojis (🖱️, 🖥️, ⌨️, 🎧).
            6. Se o item não estiver na lista abaixo, diga: 'No momento não temos esse item específico em estoque, mas fique de olho no site que sempre chegam novidades!'.

            ESTOQUE REAL DA KABYTE AGORA:
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

            // curl_close($ch) foi removido daqui!

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
