<?php
require_once __DIR__ . '/../models/Produto.php';
require_once __DIR__ . '/../models/Auth.php';
require_once __DIR__ . '/../models/Categoria.php';

class ProdutoController
{

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
        Auth::verificar(['admin', 'master']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = !empty($_POST['id']) ? $_POST['id'] : null;
            $codigo = $_POST['codigo_barras'] ?? '';

            // VALIDAÇÃO DE DUPLICIDADE (mantido do seu código)
            $produtoExistente = Produto::buscarPorCodigo($codigo);
            if ($produtoExistente) {
                if (!$id) {
                    $this->redirecionarComErro("Já existe um produto com o código $codigo.", 'produto/novo');
                }
                if ($id && $produtoExistente['id'] != $id) {
                    $this->redirecionarComErro("O código $codigo já pertence a outro produto.", "produto/editar&id=$id");
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
                // Atualiza produto existente e exclui imagem principal velha se enviou uma nova
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
                // Cria novo produto e resgata o ID que foi gerado no banco
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
                                unlink($caminho); // Apaga arquivo fisico
                            Produto::excluirImagemExtra($imgId); // Apaga do banco
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
            header("Location: " . BASE_URL . "produtos?msg=" . urlencode($msg));
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
        // NÃO TEM Auth::verificar() pois é pública

        // 1. Busca as categorias no banco para montar o submenu
        $categorias = Categoria::listar();

        // 2. Verifica se tem busca por texto OU filtro de categoria na URL
        $termo = $_GET['busca'] ?? '';
        $categoriaId = $_GET['categoria'] ?? '';

        if (!empty($termo)) {
            // Se o cliente digitou algo na lupa
            $produtos = Produto::buscarPorTermo($termo);
        } elseif (!empty($categoriaId)) {
            // Se o cliente clicou em uma categoria no submenu
            $produtos = Produto::buscarPorCategoria($categoriaId);
        } else {
            // Padrão: mostra todos os produtos
            $produtos = Produto::listar();
        }

        // Carrega a tela enviando os $produtos e as $categorias
        require __DIR__ . '/../views/vitrine.php';
    }

    // Página Sobre a Empresa (Pública)
    public function sobre()
    {
        // Como é uma página estática simples, só precisamos puxar a view
        require __DIR__ . '/../views/sobre.php';
    }
}