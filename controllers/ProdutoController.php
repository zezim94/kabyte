<?php
require_once __DIR__ . '/../models/Produto.php';
require_once __DIR__ . '/../models/Auth.php';
require_once __DIR__ . '/../models/Categoria.php';

class ProdutoController
{
    public $livre = ['vitrine', 'sobre', 'detalhes'];

    // Lista todos os produtos (Admin)
    public function index()
    {
        Auth::verificar(['admin', 'master']);
        $produtos = Produto::listar();
        $categorias = Categoria::listar();
        require __DIR__ . '/../views/produtos/listar.php';
    }

    // Exibe form de cadastro vazio
    public function criar()
    {
        Auth::verificar(['admin', 'master']);
        $categorias = Categoria::listar();
        $produto = [];
        $imagensAdicionais = []; // NOVO: Garante que a variável exista vazia
        require __DIR__ . '/../views/produtos/formulario.php';
    }

    // Exibe form de edição preenchido
    public function editar()
    {
        Auth::verificar(['admin', 'master']);
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . 'produtos');
            exit;
        }
        $produto = Produto::buscarPorId($id);
        if (!$produto)
            die("Produto não encontrado");
        $categorias = Categoria::listar();

        // NOVO: Busca as imagens da galeria para exibir no form de edição
        $imagensAdicionais = Produto::buscarImagensAdicionais($id);

        require __DIR__ . '/../views/produtos/formulario.php';
    }

    public function detalhes()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . 'vitrine');
            exit;
        }

        $produto = Produto::buscarPorId($id);

        if (!$produto) {
            echo "Produto não encontrado."; // Ou redirecionar
            exit;
        }

        // === NOVO: Busca as fotos extras da galeria ===
        $imagensAdicionais = Produto::buscarImagensAdicionais($id);

        // Busca produtos da mesma categoria
        $relacionados = [];
        if (!empty($produto['categoria_id'])) {
            $relacionados = Produto::listarRelacionados($produto['categoria_id'], $id);
        }

        // === CONTADOR DE VISUALIZAÇÕES COM PROTEÇÃO CONTRA SPAM (F5) ===
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['produtos_visualizados'])) {
            $_SESSION['produtos_visualizados'] = [];
        }

        if (!in_array($id, $_SESSION['produtos_visualizados'])) {
            // Pega o ID do cliente se ele estiver logado na hora de ver o produto
            $clienteLogadoId = $_SESSION['cliente_id'] ?? null;

            // Dispara a função enviando o ID do Produto e o ID do Cliente
            Produto::visu($id, $clienteLogadoId);

            $_SESSION['produtos_visualizados'][] = $id;
        }
        // ==============================================================

        require __DIR__ . '/../views/detalhes_produto.php';
    }

    // Função Auxiliar para Upload
    private function uploadImagem()
    {
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
            // Gera nome único: produto_DATA_RANDOM.jpg
            $novoNome = "prod_" . date("YmdHis") . "_" . uniqid() . "." . $extensao;
            $destino = __DIR__ . '/../public/uploads/' . $novoNome;

            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
                return $novoNome;
            }
        }
        return null;
    }

    public function salvar()
    {
        // Esta segurança extra pode ser mantida sem problemas!
        Auth::verificar(['admin', 'master']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = !empty($_POST['id']) ? $_POST['id'] : null;
            $codigo = $_POST['codigo_barras'] ?? '';

            // VALIDAÇÃO DE DUPLICIDADE
            $produtoExistente = Produto::buscarPorCodigo($codigo);
            if ($produtoExistente) {
                if (!$id) {
                    // ROTA CORRIGIDA PARA O NOVO PADRÃO
                    $this->redirecionarComErro("Já existe um produto com o código $codigo.", 'index.php?rota=produto/novo');
                }
                if ($id && $produtoExistente['id'] != $id) {
                    // ROTA CORRIGIDA PARA O NOVO PADRÃO
                    $this->redirecionarComErro("O código $codigo já pertence a outro produto.", "index.php?rota=produto/editar&id=$id");
                }
            }

            // UPLOAD DA IMAGEM PRINCIPAL
            $nomeImagem = $this->uploadImagem();
            $isPromocao = isset($_POST['promocao']) ? 1 : 0;
            $precoCusto = !empty($_POST['preco_custo']) ? str_replace(',', '.', $_POST['preco_custo']) : 0.00;
            $precoPromo = !empty($_POST['preco_promocao']) ? str_replace(',', '.', $_POST['preco_promocao']) : null;

            $dados = [
                'codigo' => $codigo,
                'nome' => $_POST['nome'],
                'descricao' => $_POST['descricao'] ?? '',
                'preco' => str_replace(',', '.', $_POST['preco']),
                'preco_custo' => $precoCusto,
                'promocao' => $isPromocao,
                'preco_promocao' => $precoPromo,
                'estoque' => $_POST['estoque'],
                'categoria_id' => !empty($_POST['categoria_id']) ? $_POST['categoria_id'] : null,
                'imagem' => $nomeImagem
            ];

            // SALVAR DADOS PRINCIPAIS NO BANCO
            if ($id) {
                if ($nomeImagem) {
                    $produtoAntigo = Produto::buscarPorId($id);
                    if ($produtoAntigo && !empty($produtoAntigo['imagem'])) {
                        $caminhoAntigo = __DIR__ . '/../public/uploads/' . $produtoAntigo['imagem'];
                        if (file_exists($caminhoAntigo))
                            unlink($caminhoAntigo);
                    }
                }
                Produto::atualizar($id, $dados);
                $idProdutoFinal = $id;
            } else {
                $idProdutoFinal = Produto::salvar($dados);
            }

            // === LÓGICA DA GALERIA ADICIONAL ===
            if ($idProdutoFinal) {

                // 1. Excluir fotos marcadas no Checkbox
                if (!empty($_POST['remover_imagens_extras'])) {
                    foreach ($_POST['remover_imagens_extras'] as $imgId) {
                        $imgBanco = Produto::buscarImagemExtraPorId($imgId);
                        if ($imgBanco) {
                            $caminho = __DIR__ . '/../public/uploads/' . $imgBanco['imagem'];
                            if (file_exists($caminho))
                                unlink($caminho);
                            Produto::excluirImagemExtra($imgId);
                        }
                    }
                }

                // 2. Upload de novas fotos da galeria
                if (!empty($_FILES['imagens_extras']['name'][0])) {
                    $totalArquivos = count($_FILES['imagens_extras']['name']);
                    for ($i = 0; $i < $totalArquivos; $i++) {
                        if ($_FILES['imagens_extras']['error'][$i] === UPLOAD_ERR_OK) {
                            $ext = pathinfo($_FILES['imagens_extras']['name'][$i], PATHINFO_EXTENSION);
                            $nomeExtra = "galeria_" . $idProdutoFinal . "_" . time() . "_" . uniqid() . "." . $ext;
                            $destino = __DIR__ . '/../public/uploads/' . $nomeExtra;

                            if (move_uploaded_file($_FILES['imagens_extras']['tmp_name'][$i], $destino)) {
                                Produto::salvarImagemExtra($idProdutoFinal, $nomeExtra);
                            }
                        }
                    }
                }
            }

            $msg = $id ? "Produto atualizado" : "Produto criado";

            // ROTA CORRIGIDA (Redireciona para o index do ProdutoController)
            header("Location: " . BASE_URL . "index.php?rota=produto/index&msg=" . urlencode($msg));
            exit;
        }
    }
    // Função Auxiliar para erro (Adicione no final da classe, antes do último fecha chaves '}')
    private function redirecionarComErro($msg, $rota)
    {
        header('Location: ' . BASE_URL . $rota . '&erro=' . urlencode($msg));
        exit;
    }

    // Exclui o produto
    public function excluir()
    {
        Auth::verificar(['admin', 'master']);
        $id = $_GET['id'] ?? null;

        if ($id) {
            // 1. Busca os dados do produto ANTES de excluí-lo do banco
            $produto = Produto::buscarPorId($id);

            // 2. Verifica se o produto tem uma imagem cadastrada
            if ($produto && !empty($produto['imagem'])) {
                $caminhoImagem = __DIR__ . '/../public/uploads/' . $produto['imagem'];

                // 3. Se o arquivo físico existir na pasta, apaga ele (unlink)
                if (file_exists($caminhoImagem)) {
                    unlink($caminhoImagem);
                }
            }

            // 4. Agora sim, exclui o registro do banco de dados
            Produto::excluir($id);
        }

        // Redireciona usando URL Limpa
        header('Location: ' . BASE_URL . 'produtos');
        exit; // É sempre bom colocar um exit após um redirecionamento (header)
    }

    public function vitrine()
    {
        // 1. Busca as categorias no banco para montar o submenu
        $categorias = Categoria::listar();

        // 2. Captura os filtros que vieram pela URL
        $termo = $_GET['busca'] ?? '';
        $apenasPromocoes = isset($_GET['promocao']) && $_GET['promocao'] == 1;
        $categoria_id = $_GET['categoria'] ?? ''; // <-- O nosso novo filtro dos banners!

        // 3. Lógica de filtragem MVC
        if (!empty($termo)) {
            // Se digitou algo na barra de pesquisa, busca por texto
            $produtos = Produto::buscarPorTermo($termo);
        } elseif ($apenasPromocoes) {
            // Se clicou no botão "Promoções"
            $produtos = Produto::listarPromocoes();
        } elseif (!empty($categoria_id)) {
            // Se clicou no banner inteligente, filtra pela categoria!
            $produtos = Produto::buscarPorCategoria($categoria_id);
        } else {
            // Se entrou na loja normalmente sem nenhum filtro, traz tudo
            $produtos = Produto::listar();
        }

        // 4. Carrega a View da vitrine
        require __DIR__ . '/../views/vitrine.php';
    }
}
