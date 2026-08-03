<?php
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Configuracao.php';
require_once __DIR__ . '/../models/Produto.php';
require_once __DIR__ . '/../models/Auth.php';

class AdminController
{
    public function index()
    {
        // Proteção: Só entra se for Admin ou Master
        Auth::verificar(['admin', 'master']);

        $faturamento = Usuario::getFaturamentoHoje();
        $ultimasVendas = Usuario::getUltimasVendas();
        $porPagamento = Usuario::getFaturamentoPorPagamento();
        $topProdutosGeral = Produto::listarMaisVistosGeral(10);

        require __DIR__ . '/../views/adm/admin_painel.php';
    }

    public function master()
    {
        // Proteção: Só entra se for Master
        Auth::verificar(['master']);

        $logs = Usuario::getLogs();

        require __DIR__ . '/../views/master/master_logs.php';
    }

    public function usuarios()
    {
        // Apenas admin ou master podem ver a lista de usuários
        Auth::verificar(['admin', 'master']);

        require_once __DIR__ . '/../models/Usuario.php';

        // Busca a lista
        $usuarios = Usuario::listarParaAdmin();

        // Carrega a tela
        require __DIR__ . '/../views/adm/admin_usuarios.php';
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

            echo "<script>alert('Usuário cadastrado com sucesso!'); window.location='" . BASE_URL . "admin/usuarios';</script>";
        }
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

            echo "<script>alert('Usuário atualizado com sucesso!'); window.location='" . BASE_URL . "admin/usuarios';</script>";
        }
    }
    public function novo()
    {
        Auth::verificar(['admin', 'master']);
        require __DIR__ . '/../views/adm/admin_usuario_novo.php';
    }

    public function excluir()
    {
        Auth::verificar(['admin', 'master']);
        $id = $_GET['id'] ?? null;

        if ($id && $id != $_SESSION['usuario_id']) { // Trava de segurança extra
            require_once __DIR__ . '/../models/Usuario.php';
            Usuario::excluir($id);
        }

        header('Location: ' . BASE_URL . 'admin/usuarios');
        exit;
    }

    public function config()
    {
        Auth::verificar(['admin', 'master']);
        $config = Configuracao::get();
        require __DIR__ . '/../views/adm/configuracao.php';
    }
}
