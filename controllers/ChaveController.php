<?php
require_once __DIR__ . '/../models/Auth.php';
require_once __DIR__ . '/../models/Chave.php';

class ChaveController
{
    // Exibe a página com a lista de chaves
    public function index()
    {
        Auth::verificar(['admin', 'master']);
        $chaves = Chave::buscarTodasAdmin();
        require __DIR__ . '/../views/adm/chaves.php';
    }

    // Processa a atualização caso ainda use um formulário tradicional com POST array
    public function salvar()
    {
        Auth::verificar(['admin', 'master']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['chaves'])) {
            try {
                // Percorre o array enviado pelo formulário (ID => Novo Valor)
                foreach ($_POST['chaves'] as $id => $valor) {
                    $valorLimpo = trim($valor);
                    Chave::atualizar($id, $valorLimpo);
                }

                $msg = urlencode('Chaves atualizadas com sucesso!');
                header('Location: ' . BASE_URL . 'chave?toast=success&msg=' . $msg);
                exit;
            } catch (Exception $e) {
                $msg = urlencode('Erro ao atualizar chaves: ' . $e->getMessage());
                header('Location: ' . BASE_URL . 'chave?toast=error&msg=' . $msg);
                exit;
            }
        }
    }

    // Processa a requisição AJAX enviada pelo Modal da View
    public function salvarIndividual()
    {
        Auth::verificar(['admin', 'master']);
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $valor = $_POST['valor'] ?? '';

            if (!$id || empty(trim($valor))) {
                echo json_encode(['sucesso' => false, 'msg' => 'ID ou valor não podem estar vazios.']);
                exit;
            }

            try {
                $valorLimpo = trim($valor);
                $sucesso = Chave::atualizar($id, $valorLimpo);

                if ($sucesso) {
                    echo json_encode(['sucesso' => true, 'msg' => 'Chave atualizada com sucesso!']);
                } else {
                    echo json_encode(['sucesso' => false, 'msg' => 'Nenhuma alteração foi feita.']);
                }
            } catch (Exception $e) {
                echo json_encode(['sucesso' => false, 'msg' => 'Erro interno: ' . $e->getMessage()]);
            }
            exit;
        }
    }
}
