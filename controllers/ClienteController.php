<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Auth.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../models/Seguranca.php';

class ClienteController
{
    public $livre = [
        'esqueci_senha',
        'processar_recuperacao',
        'cadastro',
        'processar_cadastro'
    ];

    public function index()
    {
        Auth::verificar(['admin', 'master']);
        $clientes = Cliente::listar();
        require __DIR__ . '/../views/clientes/listar.php';
    }

    public function criar()
    {
        Auth::verificar(['admin', 'master']);
        require __DIR__ . '/../views/clientes/formulario.php';
    }

    public function editar()
    {
        Auth::verificar(['admin', 'master']);
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $msg = urlencode('ID do cliente não informado.');
            header('Location: ' . BASE_URL . 'cliente?toast=warning&msg=' . $msg);
            exit;
        }

        $cliente = Cliente::buscarPorId($id);

        // NOVO: Busca o endereço padrão do cliente para preencher a Etapa 2
        $endereco = Cliente::buscarEnderecoPadrao($id);

        if (!$cliente) {
            // Em vez de 'die()', devolvemos o utilizador para a lista com um Toast de erro
            $msg = urlencode('Cliente não encontrado na base de dados.');
            header('Location: ' . BASE_URL . 'cliente?toast=error&msg=' . $msg);
            exit;
        }

        require __DIR__ . '/../views/clientes/formulario.php';
    }

    public function salvar()
    {
        Auth::verificar(['admin', 'master']);
        $dados = $_POST; // Pega tudo do POST

        try {
            if (isset($_GET['id'])) {
                Cliente::atualizar($_GET['id'], $dados);
                $msg = urlencode('Cliente atualizado com sucesso!');
            } else {
                Cliente::salvar($dados);
                $msg = urlencode('Cliente cadastrado com sucesso!');
            }

            // Redireciona com Toast de Sucesso
            header('Location: ' . BASE_URL . 'cliente?toast=success&msg=' . $msg);
            exit;
        } catch (Exception $e) {
            // Redireciona com Toast de Erro caso falhe (ex: CPF duplicado)
            $msg = urlencode('Erro ao salvar cliente: ' . $e->getMessage());
            header('Location: ' . BASE_URL . 'cliente?toast=error&msg=' . $msg);
            exit;
        }
    }

    public function excluir()
    {
        Auth::verificar(['admin', 'master']);
        $id = $_GET['id'] ?? null;

        if ($id) {
            try {
                Cliente::excluir($id);

                $msg = urlencode('Cliente excluído com sucesso!');
                header('Location: ' . BASE_URL . 'cliente?toast=success&msg=' . $msg);
                exit;
            } catch (Exception $e) {
                // Caso haja erro (ex: cliente tem vendas vinculadas e não pode ser apagado)
                $msg = urlencode('Erro ao excluir: ' . $e->getMessage());
                header('Location: ' . BASE_URL . 'cliente?toast=error&msg=' . $msg);
                exit;
            }
        } else {
            // Se tentarem aceder à rota de excluir sem passar o ID
            $msg = urlencode('Nenhum cliente selecionado para exclusão.');
            header('Location: ' . BASE_URL . 'cliente?toast=warning&msg=' . $msg);
            exit;
        }
    }

    // 1. Abre a tela de Esqueci a Senha
    public function esqueci_senha()
    {
        require __DIR__ . '/../views/esqueci_senha.php';
    }

    // 2. Processa o e-mail digitado
    public function processar_recuperacao()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);

            $cliente = Cliente::buscarPorEmail($email);

            if ($cliente) {
                // 1. Gera e salva a nova senha
                $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $novaSenha = substr(str_shuffle($caracteres), 0, 8);
                $senhaCriptografada = password_hash($novaSenha, PASSWORD_DEFAULT);
                Cliente::atualizarSenha($cliente['id'], $senhaCriptografada);


                // 3. Configura o PHPMailer
                $mail = new PHPMailer(true);

                try {
                    // Configurações do Servidor (SMTP Gmail)
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'andelsonascimento@gmail.com'; // e-mail do Gmail aqui
                    $mail->Password = 'uera thtq fsuh qbdq';    // Senha de App de 16 letras aqui (sem espaços)
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;
                    $mail->CharSet = 'UTF-8'; // Para não dar erro em acentos (ç, ã, etc)

                    // Remetente e Destinatário
                    $mail->setFrom('andelsonascimento@gmail.com', 'KaByte Informática');
                    $mail->addAddress($email, $cliente['nome']);

                    // Conteúdo do E-mail
                    $mail->isHTML(true); // Habilita formato HTML
                    $mail->Subject = 'Sua nova senha de acesso - KaByte';

                    // Corpo do e-mail estilizado
                    $primeiroNome = explode(' ', $cliente['nome'])[0];
                    $mail->Body = "
                        <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto; border: 1px solid #eee; padding: 20px; border-radius: 8px;'>
                            <h2 style='color: #1e12c9;'>Olá, {$primeiroNome}!</h2>
                            <p>Recebemos um pedido de recuperação de senha para a sua conta na <strong>KaByte</strong>.</p>
                            <p>Sua nova senha temporária foi gerada com sucesso:</p>
                            
                            <div style='background: #f5f5f5; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 2px; border-radius: 8px; margin: 20px 0;'>
                                {$novaSenha}
                            </div>
                            
                            <p>Recomendamos que você acesse o sistema com esta senha e <strong>altere-a imediatamente</strong> no seu painel para uma de sua preferência.</p>
                            <br>
                            <p>Atenciosamente,<br><strong>Equipe KaByte</strong></p>
                        </div>
                    ";

                    // Envia o e-mail
                    $mail->send();

                    // Redireciona com sucesso
                    header('Location: ' . BASE_URL . 'cliente/esqueci_senha&sucesso=1');
                    exit;
                } catch (Exception $e) {
                    // Se falhar a conexão com o Google (senha errada, sem internet, etc)
                    $erro = "Não foi possível enviar o e-mail no momento. Erro técnico: {$mail->ErrorInfo}";
                    require __DIR__ . '/../views/esqueci_senha.php';
                }
            } else {
                $erro = "Não encontramos nenhuma conta cadastrada com este e-mail.";
                require __DIR__ . '/../views/esqueci_senha.php';
            }
        }
    }

    // Exibe a tela de cadastro
    public function cadastro()
    {
        require __DIR__ . '/../views/cliente_cadastro.php';
    }

    // Processa os dados de cadastro vindos do formulário
    public function processar_cadastro()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $nome = trim($_POST['nome'] ?? '');
            $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
            $telefone = trim($_POST['telefone'] ?? '');
            $senha = $_POST['senha'] ?? '';

            // --- NOVO: Validação básica para evitar cadastros "fantasmas" em branco ---
            if (empty($nome) || empty($email) || empty($senha)) {
                $erro = "Por favor, preencha todos os campos obrigatórios (Nome, E-mail e Senha).";
                require __DIR__ . '/../views/cliente_cadastro.php';
                return;
            }

            // Remove pontuações para salvar o CPF limpo
            $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf'] ?? '');
            $cpfCriptografado = Seguranca::encriptar($cpf);

            // 1. Validação de E-mail
            $clienteExistente = Cliente::buscarPorEmail($email);
            if ($clienteExistente) {
                $erro = "Este e-mail já está cadastrado. Por favor, faça login.";
                require __DIR__ . '/../views/cliente_cadastro.php';
                return;
            }

            // 2. Validação de CPF
            if (!empty($cpf)) {
                $cpfExistente = Cliente::buscarPorCpf($cpfCriptografado);
                if ($cpfExistente) {
                    $erro = "Este CPF já está cadastrado em nossa base de dados.";
                    require __DIR__ . '/../views/cliente_cadastro.php';
                    return;
                }
            }

            // 3. Criptografa a senha por segurança
            $senhaCriptografada = password_hash($senha, PASSWORD_DEFAULT);

            // 4. Monta o array de dados para salvar
            $dados = [
                'nome' => $nome,
                'email' => $email,
                'telefone' => $telefone,
                'cpf' => $cpfCriptografado,
                'senha' => $senhaCriptografada
            ];

            // 5. Salva no banco de dados
            $idNovoCliente = Cliente::registrarNovoCliente($dados);

            if ($idNovoCliente) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                // Efetua o login automático criando a sessão
                $_SESSION['cliente_id'] = $idNovoCliente;
                $_SESSION['cliente_nome'] = $nome;

                // Redireciona direto para o painel do cliente
                header('Location: ' . BASE_URL . 'clientearea/painel');
                exit;
            } else {
                $erro = "Ocorreu um erro ao criar sua conta. Tente novamente mais tarde.";
                require __DIR__ . '/../views/cliente_cadastro.php';
            }
        }
    }
}
