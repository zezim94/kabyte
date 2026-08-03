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
        $dados = [
            'id' => $_POST['id'] ?? null,
            'nome' => $_POST['nome'] ?? ''
        ];

        $resultado = Categoria::salvar($dados);

        if ($resultado['sucesso']) {
            // REDIRECIONAMENTO CORRIGIDO PARA O NOVO PADRÃO (Vai para a função index do CategoriaController)
            header('Location: ' . BASE_URL . 'categoria&msg=' . urlencode($resultado['msg']));
            exit;
        } else {
            // Se der erro, volta pro form com os dados preenchidos
            $categoria = $dados;
            $erro = $resultado['msg'];
            require __DIR__ . '/../views/categorias/form.php';
        }
    }

    public function excluir()
    {
        $id = $_GET['id'] ?? 0;
        $resultado = Categoria::excluir($id);
        header('Location: ' . BASE_URL . 'categoria&msg=' . urlencode($resultado['msg']));
    }
}
