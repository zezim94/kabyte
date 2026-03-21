<?php
require_once __DIR__ . '/../models/Configuracao.php';
require_once __DIR__ . '/../models/Auth.php';

class ConfigController
{

    public function index()
    {
        Auth::verificar(['admin', 'master']);
        $config = Configuracao::get();
        require __DIR__ . '/../views/adm/configuracao.php';
    }

    public function salvar()
    {
        Auth::verificar(['admin', 'master']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // ====================================================
            // 1. LÓGICA DA LOGO DA LOJA
            // ====================================================
            $caminhoLogo = $_POST['logo_atual'] ?? '';

            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {

                $extensao = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                $novoNome = "logo_loja." . $extensao;
                $pastaDestino = __DIR__ . '/../public/uploads/';

                if (!is_dir($pastaDestino)) {
                    if (!mkdir($pastaDestino, 0777, true)) {
                        die("ERRO: Não foi possível criar a pasta: " . $pastaDestino);
                    }
                }

                $caminhoCompleto = $pastaDestino . $novoNome;

                if (move_uploaded_file($_FILES['logo']['tmp_name'], $caminhoCompleto)) {
                    $caminhoLogo = 'public/uploads/' . $novoNome . '?v=' . time();
                } else {
                    die("ERRO AO MOVER: Verifique se a pasta 'public/uploads' existe.");
                }
            }

            // ====================================================
            // 2. LÓGICA DOS BANNERS DINÂMICOS
            // ====================================================

            // Pega os banners que já existem no banco
            $configAtual = Configuracao::get();
            $bannersAtuais = json_decode($configAtual['banners'] ?? '[]', true);
            if (!is_array($bannersAtuais)) {
                $bannersAtuais = [];
            }

            // Verifica se o admin marcou algum banner antigo para excluir
            if (!empty($_POST['remover_banners'])) {
                foreach ($_POST['remover_banners'] as $remover) {
                    $index = array_search($remover, $bannersAtuais);
                    if ($index !== false) {
                        unset($bannersAtuais[$index]); // Remove da lista do array

                        $caminhoImgBanner = __DIR__ . '/../public/uploads/' . $remover;
                        if (file_exists($caminhoImgBanner)) {
                            unlink($caminhoImgBanner); // Apaga a imagem fisicamente do servidor
                        }
                    }
                }
            }

            // Fazer upload dos NOVOS banners selecionados (se houver)
            if (!empty($_FILES['novos_banners']['name'][0])) {
                $totalArquivos = count($_FILES['novos_banners']['name']);
                $pastaDestino = __DIR__ . '/../public/uploads/';

                for ($i = 0; $i < $totalArquivos; $i++) {
                    if ($_FILES['novos_banners']['error'][$i] === UPLOAD_ERR_OK) {
                        $extensao = pathinfo($_FILES['novos_banners']['name'][$i], PATHINFO_EXTENSION);

                        // Gera um nome único para o novo banner para não sobrescrever nenhum
                        $nomeBanner = "banner_" . date("YmdHis") . "_" . uniqid() . "." . $extensao;
                        $destinoBanner = $pastaDestino . $nomeBanner;

                        if (move_uploaded_file($_FILES['novos_banners']['tmp_name'][$i], $destinoBanner)) {
                            $bannersAtuais[] = $nomeBanner; // Adiciona o nome do novo banner na lista do banco
                        }
                    }
                }
            }

            // ====================================================
            // 3. SALVAR TUDO NO BANCO DE DADOS
            // ====================================================
            $dadosParaSalvar = [
                'nome' => $_POST['nome'],
                'whatsapp' => $_POST['whatsapp'] ?? '',
                'cor_header' => $_POST['cor_header'] ?? '#000000',
                'cor_fundo' => $_POST['cor_fundo'] ?? '#ffffff',
                'logo' => $caminhoLogo,

                // Salva o array de banners atualizado em formato JSON no banco
                // array_values() garante que os índices fiquem corretos caso tenhamos deletado algum
                'banners' => json_encode(array_values($bannersAtuais))
            ];

            Configuracao::salvar($dadosParaSalvar);

            // Recarrega a página com sucesso
            echo "<script>alert('Configurações salvas com sucesso!'); window.location='index.php?rota=configuracao';</script>";
        }
    }

}