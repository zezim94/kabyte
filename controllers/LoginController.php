<?php
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Auth.php';

class LoginController
{

    public function index()
    {
        $params = [
            'client_id' => GOOGLE_CLIENT_ID,
            'redirect_uri' => GOOGLE_REDIRECT_URL,
            'response_type' => 'code',
            'scope' => 'email profile',
            'access_type' => 'online'
        ];
        $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
        require __DIR__ . '/../views/login.php';
    }

    public function entrar()
    {
        $user = $_POST['usuario'];
        $pass = $_POST['senha'];

        $usuarioBanco = Usuario::buscarPorLogin($user);

        // Verifica se usuário existe E se a senha bate com o Hash
        if ($usuarioBanco && password_verify($pass, $usuarioBanco['senha'])) {
            Auth::logar($usuarioBanco);
            header('Location: index.php?rota=home');
        } else {
            $erro = "Usuário ou senha inválidos!";
            require __DIR__ . '/../views/login.php';
        }
    }

    public function logout()
    {
        Auth::logout();
    }

    // Área do Admin
    public function painelAdmin()
    {
        Auth::verificar(['admin', 'master']);
        $faturamento = Usuario::getFaturamentoHoje();
        $ultimasVendas = Usuario::getUltimasVendas();
        $porPagamento = Usuario::getFaturamentoPorPagamento();
        require __DIR__ . '/../views/adm/admin_painel.php';
    }

    // Área do Master
    public function painelMaster()
    {
        Auth::verificar(['master']); // Só Master entra
        $logs = Usuario::getLogs();
        require __DIR__ . '/../views/master/master_logs.php';
    }

    public function google_callback()
    {
        if (isset($_GET['code'])) {
            $code = $_GET['code'];

            // 1. Troca o "code" pelo "access_token" via cURL
            $tokenData = $this->pegarTokenGoogle($code);

            if (isset($tokenData['access_token'])) {
                // 2. Usa o token para pegar os dados do usuário via cURL
                $googleUser = $this->pegarDadosUsuarioGoogle($tokenData['access_token']);

                if (isset($googleUser['email'])) {
                    // 3. Salva no banco e loga
                    $usuario = Usuario::salvarGoogle($googleUser);

                    // Adicionei a foto na sessão
                    Auth::logar($usuario);
                    header('Location: index.php?rota=home');
                    exit;
                }
            }
        }

        // Se deu erro
        echo "Erro ao logar com Google.";
        exit;
    }

    // --- FUNÇÕES AUXILIARES (SUBSTITUEM A BIBLIOTECA) ---

    private function pegarTokenGoogle($code)
    {
        $url = 'https://oauth2.googleapis.com/token';
        $postData = [
            'code' => $code,
            'client_id' => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri' => GOOGLE_REDIRECT_URL,
            'grant_type' => 'authorization_code'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    private function pegarDadosUsuarioGoogle($accessToken)
    {
        $url = 'https://www.googleapis.com/oauth2/v1/userinfo?access_token=' . $accessToken;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Importante: Header de Autorização
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}