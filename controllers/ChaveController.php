<?php
require_once __DIR__ . '/../models/Auth.php';
require_once __DIR__ . '/../models/Chave.php';

class ChaveController
{
    // Exibe a página com a lista de chaves
    public function index()
    {
        // Garante que apenas Admin ou Master podem aceder a esta tela
        Auth::verificar(['admin', 'master']);
        
        $chaves = Chave::buscarTodasAdmin();
        
        require __DIR__ . '/../views/adm/chaves.php';
    }

    // Processa a atualização quando o formulário é enviado
    public function salvar()
    {
        Auth::verificar(['admin', 'master']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['chaves'])) {
            $sucesso = true;

            // Percorre o array enviado pelo formulário (ID => Novo Valor)
            foreach ($_POST['chaves'] as $id => $valor) {
                // Remove espaços em branco acidentais nas pontas
                $valorLimpo = trim($valor); 
                
                if (!Chave::atualizar($id, $valorLimpo)) {
                    $sucesso = false;
                }
            }

            if ($sucesso) {
                header('Location: ' . BASE_URL . 'index.php?rota=admin/chaves&msg=' . urlencode('Chaves atualizadas com sucesso!') . '&sucesso=1');
            } else {
                header('Location: ' . BASE_URL . 'index.php?rota=admin/chaves&msg=' . urlencode('Ocorreu um erro ao atualizar algumas chaves.') . '&sucesso=0');
            }
            exit;
        }
    }
}