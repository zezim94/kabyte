<?php
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Venda.php';
require_once __DIR__ . '/../models/Produto.php';
require_once __DIR__ . '/../services/MercadoPagoService.php';
require_once __DIR__ . '/../models/Seguranca.php';

class ClienteAreaController
{
    // TODAS AS ROTAS DO CLIENTE LIBERADAS DO BLOQUEIO DE ADMINISTRADOR
    public $livre = [
        'login',
        'autenticar',
        'google_callback',
        'sair',
        'painel',
        'meusDados',
        'salvarDados',
        'salvar_endereco',
        'excluir_endereco',
        'tornar_padrao',
        'atualizar_senha',
        'pegarTokenGoogle',

    ];
    // Tela de Login
    public function login()
    {
        if (isset($_SESSION['cliente_id'])) {
            // REDIRECIONAMENTO CORRIGIDO PARA O NOVO PADRÃO
            header('Location: ' . BASE_URL . 'cliente_area/painel');
            exit;
        }

        // 1. Requer o Model (caso ainda não esteja no topo do ficheiro)
        require_once __DIR__ . '/../models/Chave.php';

        // 2. Puxa a chave diretamente do banco de dados
        $googleClientId = Chave::get('google_client_id');

        // URL DE CALLBACK CORRIGIDA PARA O NOVO PADRÃO
        $callbackUrl = BASE_URL . 'cliente_area/google_callback';

        $params = [
            'client_id' => $googleClientId,
            'redirect_uri' => $callbackUrl,
            'response_type' => 'code',
            'scope' => 'email profile',
            'access_type' => 'online'
        ];

        // Se a chave não estiver configurada no banco, podemos evitar montar uma URL quebrada
        if (empty($googleClientId)) {
            $authUrl = '#'; // Ou você pode criar uma mensagem de erro na view
        } else {
            $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
        }

        require __DIR__ . '/../views/clientes/login.php';
    }

    public function autenticar()
    {
        if (session_status() == PHP_SESSION_NONE)
            session_start();

        $login = $_POST['login'] ?? '';
        $senha = $_POST['senha'] ?? '';

        $cliente = Cliente::buscarPorLogin($login);

        // Verifica se o cliente existe E se a senha confere
        if ($cliente && password_verify($senha, $cliente['senha'])) {
            $this->criarSessaoCliente($cliente);

            // REDIRECIONAMENTO CORRIGIDO PARA O NOVO PADRÃO
            header('Location: ' . BASE_URL . 'cliente_area/painel');
            exit;
        } else {
            // Define a mensagem de erro que aparecerá na View
            $erro = 'Login ou senha incorretos!';

            // 1. Puxa novamente a chave para o botão do Google não quebrar na tela de erro
            require_once __DIR__ . '/../models/Chave.php';
            $googleClientId = Chave::get('google_client_id');

            // ROTA DO GOOGLE CORRIGIDA PARA O NOVO PADRÃO
            $callbackUrl = BASE_URL . 'cliente_area/google_callback';

            $params = [
                'client_id' => $googleClientId,
                'redirect_uri' => $callbackUrl,
                'response_type' => 'code',
                'scope' => 'email profile',
                'access_type' => 'online'
            ];

            $authUrl = empty($googleClientId) ? '#' : 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);

            // 2. Carrega a view diretamente para que a variável $erro seja impressa no HTML
            require __DIR__ . '/../views/clientes/login.php';
        }
    }

    // 3. RETORNO DO GOOGLE
    public function google_callback()
    {
        if (isset($_GET['code'])) {
            $code = $_GET['code'];

            // Passamos a URL correta para validação
            $tokenData = $this->pegarTokenGoogle($code);

            if (isset($tokenData['access_token'])) {
                $googleUser = $this->pegarDadosUsuarioGoogle($tokenData['access_token']);

                if (isset($googleUser['email'])) {
                    // Salva na tabela CLIENTES
                    $cliente = Cliente::salvarGoogle($googleUser);

                    if ($cliente) {
                        $this->criarSessaoCliente($cliente);

                        // REDIRECIONAMENTO CORRIGIDO: Vai para o painel do cliente
                        header('Location: ' . BASE_URL . 'cliente_area/painel');
                        exit;
                    }
                }
            }
        }

        // REDIRECIONAMENTO CORRIGIDO: Volta para o login com a variável &erro
        header('Location: ' . BASE_URL . 'cliente_area/login&erro=google_fail');
        exit;
    }

    // --- MÉTODOS DE SESSÃO ---
    private function criarSessaoCliente($cliente)
    {
        if (session_status() == PHP_SESSION_NONE)
            session_start();

        // --- SEGURANÇA CRÍTICA ---
        // Se houver uma sessão de administrador ('usuario_id') aberta, nós a destruímos.
        // Isso evita que o sistema confunda um Admin com um Cliente.
        if (isset($_SESSION['usuario_id'])) {
            unset($_SESSION['usuario_id']);
            unset($_SESSION['nivel']);
        }

        // Define as sessões do cliente
        $_SESSION['cliente_id'] = $cliente['id'];
        $_SESSION['cliente_nome'] = $cliente['nome'];
        $_SESSION['cliente_email'] = $cliente['email'];

        // Tratamento da Foto:
        // Se vier do Google (começa com http), usa direto. 
        // Se estiver vazia, usa o avatar padrão.
        $foto = $cliente['foto'];
        if (empty($foto)) {
            $foto = BASE_URL . 'public/img/avatar_default.png';
        }
        $_SESSION['cliente_foto'] = $foto;
    }

    private function pegarTokenGoogle($code)
    {
        // 1. Requer o Model para garantir o acesso ao banco
        require_once __DIR__ . '/../models/Chave.php';

        // 2. Puxa as credenciais da tabela 'chaves'
        $googleClientId = Chave::get('google_client_id');
        $googleClientSecret = Chave::get('google_client_secret');

        // A URL DE CALLBACK AGORA COM ROTA LIMPA
        // Atenção: Esta exata URL precisa estar cadastrada no painel da Google Cloud!
        $callbackUrl = BASE_URL . 'cliente_area/google_callback';

        $url = 'https://oauth2.googleapis.com/token';
        $postData = [
            'code' => $code,
            'client_id' => $googleClientId,
            'client_secret' => $googleClientSecret,
            'redirect_uri' => $callbackUrl,
            'grant_type' => 'authorization_code'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        // A LINHA curl_close($ch); FOI REMOVIDA DAQUI
        // O PHP 8+ fecha a conexão cURL automaticamente!

        return json_decode($response, true);
    }

    private function pegarDadosUsuarioGoogle($accessToken)
    {
        $url = 'https://www.googleapis.com/oauth2/v1/userinfo?access_token=' . $accessToken;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);

        // Descomente a linha abaixo se estiver usando XAMPP e der erro de SSL
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);

        return json_decode($response, true);
    }

    public function painel()
    {
        $this->verificarLogin();

        // Pega o ID da sessão
        $id = $_SESSION['cliente_id'];

        // Usa os Models para buscar os dados de forma limpa
        $minhasCompras = Venda::listarPorCliente($id);
        $topProdutos = Venda::topProdutosPorCliente($id);
        $meusMaisVistos = Produto::listarMaisVistosDoCliente($id);

        require __DIR__ . '/../views/clientes/painel.php';
    }

    public function sair()
    {
        unset($_SESSION['cliente_id'], $_SESSION['cliente_nome'], $_SESSION['cliente_email']);
        header('Location: ' . BASE_URL . 'clientearea/login');
    }

    private function verificarLogin()
    {
        if (!isset($_SESSION['cliente_id'])) {
            header('Location: ' . BASE_URL . 'cliente/login');
            exit;
        }
    }

    public function meusDados()
    {
        if (session_status() == PHP_SESSION_NONE)
            session_start();

        if (!isset($_SESSION['cliente_id'])) {
            header('Location: ' . BASE_URL . 'cliente/login');
            exit;
        }

        require_once __DIR__ . '/../models/Cliente.php';

        // 1. Busca os dados pessoais (Nome, CPF, Telefone, Email)
        $cliente = Cliente::buscarPorId($_SESSION['cliente_id']);

        // 2. Busca todos os endereços vinculados a este cliente
        $enderecos = Cliente::buscarEnderecos($_SESSION['cliente_id']);

        // 3. Carrega a View passando as duas variáveis
        require __DIR__ . '/../views/clientes/dados.php';
    }

    public function salvarDados()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Verifica se está logado
        if (!isset($_SESSION['cliente_id'])) {
            // ROTA LIMPA: Vai para o login correto do cliente
            header('Location: ' . BASE_URL . 'cliente_area/login');
            exit;
        }

        require_once __DIR__ . '/../models/Cliente.php';
        // A classe Seguranca já deve estar incluída no topo, mas garantimos aqui caso o escopo mude
        require_once __DIR__ . '/../models/Seguranca.php';

        // Trata o CPF: remove pontos/traços e criptografa
        $cpfLimpo = preg_replace('/[^0-9]/', '', $_POST['cpf'] ?? '');
        $cpfCriptografado = Seguranca::encriptar($cpfLimpo);

        // 1. Pega APENAS os dados da aba "Meus Dados"
        $dadosPessoais = [
            'nome'     => trim($_POST['nome'] ?? ''),
            'cpf'      => $cpfCriptografado, // <-- Agora envia o CPF criptografado para o banco!
            'telefone' => trim($_POST['telefone'] ?? ''),
            'email'    => filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL)
        ];

        // 2. Envia para o Model atualizar no banco de dados
        $atualizou = Cliente::atualizar($_SESSION['cliente_id'], $dadosPessoais);

        // Como $atualizou é true ou false, fazemos a verificação direta
        if ($atualizou) {
            // Atualiza o nome na sessão para que o topo do site mude na mesma hora
            $_SESSION['cliente_nome'] = $dadosPessoais['nome'];
            $msg = 'Dados atualizados com sucesso!';
            $sucesso = 1;
        } else {
            $msg = 'Nenhuma alteração foi feita ou ocorreu um erro.';
            $sucesso = 0;
        }

        // REDIRECIONAMENTO LIMPO: Usa ? em vez de & para o primeiro parâmetro
        header('Location: ' . BASE_URL . 'cliente_area/meusDados?msg=' . urlencode($msg) . '&sucesso=' . $sucesso);
        exit;
    }

    public function atualizar_senha()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Verifica se está logado
        if (!isset($_SESSION['cliente_id'])) {
            header('Location: ' . BASE_URL . 'cliente/login');
            exit;
        }

        $nova_senha = $_POST['nova_senha'] ?? '';
        $confirma_senha = $_POST['confirma_senha'] ?? '';

        // 1. Validações Básicas
        if (empty($nova_senha) || empty($confirma_senha)) {
            header('Location: ' . BASE_URL . 'cliente/dados?msg=' . urlencode('Preencha os campos de senha.') . '&sucesso=0');
            exit;
        }

        if (strlen($nova_senha) < 6) {
            header('Location: ' . BASE_URL . 'cliente/dados?msg=' . urlencode('A nova senha deve ter no mínimo 6 caracteres.') . '&sucesso=0');
            exit;
        }

        if ($nova_senha !== $confirma_senha) {
            header('Location: ' . BASE_URL . 'cliente/dados?msg=' . urlencode('As senhas não coincidem.') . '&sucesso=0');
            exit;
        }

        require_once __DIR__ . '/../models/Cliente.php';

        // 2. Criptografa a nova senha
        $senhaHash = password_hash($nova_senha, PASSWORD_DEFAULT);

        // 3. Atualiza no banco
        if (Cliente::atualizarSenha($_SESSION['cliente_id'], $senhaHash)) {
            // Desloga o cliente por segurança (como avisado na tela)
            unset($_SESSION['cliente_id'], $_SESSION['cliente_nome'], $_SESSION['cliente_email'], $_SESSION['cliente_foto']);

            // Redireciona para o login com mensagem de sucesso
            header('Location: ' . BASE_URL . 'cliente/login?msg=' . urlencode('Senha atualizada com sucesso! Faça login novamente.') . '&sucesso=1');
            exit;
        } else {
            header('Location: ' . BASE_URL . 'cliente/dados?msg=' . urlencode('Erro ao atualizar a senha no banco de dados.') . '&sucesso=0');
            exit;
        }
    }
}
