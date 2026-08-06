<?php
require_once __DIR__ . '/../models/Categoria.php';
require_once __DIR__ . '/../models/Auth.php';

class CategoriaController
{
    public function __construct()
    {
        // Apenas Admin e Master podem mexer em categorias
        Auth::verificar(['admin', 'master']);
    }

    public function index()
    {
        $categorias = Categoria::listar();
        require __DIR__ . '/../views/categorias/index.php';
    }

    public function criar()
    {
        // Carrega formulário vazio
        $categoria = ['id' => '', 'nome' => ''];
        require __DIR__ . '/../views/categorias/form.php';
    }

    public function editar()
    {
        $id = $_GET['id'] ?? 0;
        $categoria = Categoria::buscarPorId($id);

        if (!$categoria) {
            header('Location: categoria');
            exit;
        }

        require __DIR__ . '/../views/categorias/form.php';
    }

    public function salvar()
    {
        // Recomendo manter a segurança caso apenas admins possam criar categorias
        Auth::verificar(['admin', 'master']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;

            $dados = [
                'id' => $id,
                'nome' => trim($_POST['nome'] ?? '')
            ];

            try {
                // O Model faz o trabalho de verificar e salvar
                $resultado = Categoria::salvar($dados);

                if ($resultado['sucesso']) {
                    // Categoria salva com sucesso
                    $msg = urlencode($resultado['msg']);
                    header('Location: ' . BASE_URL . 'categoria?toast=success&msg=' . $msg);
                    exit;
                } else {
                    // Se o Model barrar (ex: categoria já existe), devolve com Toast de Erro
                    $msg = urlencode($resultado['msg']);

                    // Verifica se estava a editar ou a criar para voltar para a tela certa
                    $rotaVolta = $id ? "categoria/editar&id=$id&" : "categoria/criar?";

                    header('Location: ' . BASE_URL . $rotaVolta . 'toast=error&msg=' . $msg);
                    exit;
                }
            } catch (Exception $e) {
                // Proteção contra falhas inesperadas no banco
                $msg = urlencode("Erro ao salvar categoria: " . $e->getMessage());
                $rotaVolta = $id ? "categoria/editar&id=$id&" : "categoria/criar?";
                header('Location: ' . BASE_URL . $rotaVolta . 'toast=error&msg=' . $msg);
                exit;
            }
        }
    }

    public function excluir()
    {
        Auth::verificar(['admin', 'master']);

        $id = $_GET['id'] ?? 0;

        if ($id) {
            try {
                $resultado = Categoria::excluir($id);

                if ($resultado['sucesso']) {
                    $msg = urlencode($resultado['msg']);
                    header('Location: ' . BASE_URL . 'categoria?toast=success&msg=' . $msg);
                } else {
                    $msg = urlencode($resultado['msg']);
                    header('Location: ' . BASE_URL . 'categoria?toast=error&msg=' . $msg);
                }
                exit;
            } catch (Exception $e) {
                // TRATAMENTO EXCELENTE: Evita que o site quebre se tentar apagar 
                // uma categoria que já tem produtos associados a ela!
                $msg = urlencode("Ação bloqueada! Existem produtos vinculados a esta categoria.");
                header('Location: ' . BASE_URL . 'categoria?toast=error&msg=' . $msg);
                exit;
            }
        } else {
            // Caso chegue sem ID na URL
            $msg = urlencode("Nenhuma categoria selecionada.");
            header('Location: ' . BASE_URL . 'categoria?toast=warning&msg=' . $msg);
            exit;
        }
    }
}
