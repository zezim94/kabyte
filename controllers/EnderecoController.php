<?php
require_once __DIR__ . '/../models/Cliente.php';

class EnderecoController
{
    // AVISA O INDEX.PHP QUE ESTAS ROTAS ESTÃO LIVRES DO BLOQUEIO DE ADMIN
    public $livre = ['salvar', 'excluir', 'tornar_padrao'];

    // Função de apoio para garantir que apenas clientes logados acedem
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

    public function salvar()
    {
        $this->verificarLogin();

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
            header('Location: ' . BASE_URL . 'cliente_area/meusDados&msg=' . urlencode('Endereço cadastrado com sucesso!') . '&sucesso=1');
        } else {
            header('Location: ' . BASE_URL . 'cliente_area/meusDados&msg=' . urlencode('Erro ao cadastrar endereço.') . '&sucesso=0');
        }
        exit;
    }

    public function excluir()
    {
        $this->verificarLogin();

        $id = $_GET['id'] ?? 0;

        if (Cliente::excluirEndereco($id, $_SESSION['cliente_id'])) {
            header('Location: ' . BASE_URL . 'cliente_area/meusDados&msg=' . urlencode('Endereço removido com sucesso!') . '&sucesso=1');
        } else {
            header('Location: ' . BASE_URL . 'cliente_area/meusDados&msg=' . urlencode('Erro ao remover endereço.') . '&sucesso=0');
        }
        exit;
    }

    public function tornar_padrao()
    {
        $this->verificarLogin();

        $enderecoId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $clienteId = $_SESSION['cliente_id'];

        if ($enderecoId) {
            if (Cliente::tornarEnderecoPadrao($enderecoId, $clienteId)) {
                header('Location: ' . BASE_URL . 'cliente_area/meusDados&msg=' . urlencode('Endereço principal atualizado com sucesso!') . '&sucesso=1');
                exit;
            }
        }

        header('Location: ' . BASE_URL . 'cliente_area/meusDados&msg=' . urlencode('Erro ao atualizar o endereço principal.') . '&sucesso=0');
        exit;
    }
}
