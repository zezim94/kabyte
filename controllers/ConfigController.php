<?php
require_once __DIR__ . '/../models/Configuracao.php';
require_once __DIR__ . '/../models/Auth.php';

class ConfigController
{
    public $livre = ['salvar'];

    public function salvar()
    {
        Auth::verificar(['admin', 'master']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            try {
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
                            // Substituímos o 'die()' por uma Exception para gerar o Toast de erro
                            throw new Exception("Não foi possível criar a pasta de uploads.");
                        }
                    }

                    $caminhoCompleto = $pastaDestino . $novoNome;

                    if (move_uploaded_file($_FILES['logo']['tmp_name'], $caminhoCompleto)) {
                        $caminhoLogo = 'public/uploads/' . $novoNome . '?v=' . time();
                    } else {
                        throw new Exception("Verifique as permissões da pasta 'public/uploads'.");
                    }
                }

                // ====================================================
                // 2. LÓGICA DOS BANNERS DINÂMICOS
                // ====================================================
                $configAtual = Configuracao::get();
                $bannersAtuais = json_decode($configAtual['banners'] ?? '[]', true);
                if (!is_array($bannersAtuais)) {
                    $bannersAtuais = [];
                }

                // LIMPEZA EXTREMA (Remove espaços e aspas "fantasmas")
                if (isset($_POST['remover_banners']) && is_array($_POST['remover_banners'])) {
                    $bannersParaManter = [];

                    $listaParaRemover = array_map(function ($item) {
                        return trim($item, " \t\n\r\0\x0B\"'");
                    }, $_POST['remover_banners']);

                    foreach ($bannersAtuais as $bannerSalvo) {
                        $bannerBancoLimpo = trim($bannerSalvo, " \t\n\r\0\x0B\"'");

                        if (!in_array($bannerBancoLimpo, $listaParaRemover)) {
                            $bannersParaManter[] = $bannerSalvo;
                        } else {
                            $caminhoImgBanner = __DIR__ . '/../public/uploads/' . $bannerBancoLimpo;
                            if (file_exists($caminhoImgBanner)) {
                                @unlink($caminhoImgBanner);
                            }
                        }
                    }

                    $bannersAtuais = $bannersParaManter;
                }

                // Fazer upload dos NOVOS banners
                if (isset($_FILES['novos_banners']) && !empty($_FILES['novos_banners']['name'][0])) {
                    $totalArquivos = count($_FILES['novos_banners']['name']);
                    $pastaDestino = __DIR__ . '/../public/uploads/';

                    for ($i = 0; $i < $totalArquivos; $i++) {
                        if ($_FILES['novos_banners']['error'][$i] === UPLOAD_ERR_OK) {
                            $extensao = pathinfo($_FILES['novos_banners']['name'][$i], PATHINFO_EXTENSION);
                            $nomeOriginal = pathinfo($_FILES['novos_banners']['name'][$i], PATHINFO_FILENAME);
                            $nomeOriginalLimpo = preg_replace('/[^a-z0-9]/', '_', strtolower($nomeOriginal));

                            $nomeBanner = "banner_" . date("YmdHis") . "_" . uniqid() . "_" . $nomeOriginalLimpo . "." . $extensao;
                            $destinoBanner = $pastaDestino . $nomeBanner;

                            if (move_uploaded_file($_FILES['novos_banners']['tmp_name'][$i], $destinoBanner)) {
                                $bannersAtuais[] = $nomeBanner;
                            }
                        }
                    }
                }

                // ====================================================
                // 3. SALVAR TUDO NO BANCO DE DADOS
                // ====================================================
                $dadosParaSalvar = [
                    'nome' => $_POST['nome'] ?? '',
                    'whatsapp' => $_POST['whatsapp'] ?? '',
                    'cor_header' => $_POST['cor_header'] ?? '#000000',
                    'cor_fundo' => $_POST['cor_fundo'] ?? '#ffffff',
                    'logo' => $caminhoLogo,
                    'banners' => json_encode(array_values($bannersAtuais), JSON_UNESCAPED_SLASHES)
                ];

                $sucesso = Configuracao::salvar($dadosParaSalvar);

                if (!$sucesso) {
                    throw new Exception("O servidor não conseguiu gravar as alterações na base de dados.");
                }

                // REDIRECIONAMENTO COM TOAST DE SUCESSO
                $msg = urlencode("Configurações salvas com sucesso!");
                header("Location: " . BASE_URL . "admin/config?toast=success&msg=" . $msg);
                exit;
            } catch (Exception $e) {
                // REDIRECIONAMENTO COM TOAST DE ERRO
                $msgErro = urlencode("Erro: " . $e->getMessage());
                header("Location: " . BASE_URL . "admin/config?toast=error&msg=" . $msgErro);
                exit;
            }
        }
    }
}
