<?php
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Auth.php';
require_once __DIR__ . '/../models/Chave.php';

class LoginController
{
    // AVISA O INDEX.PHP QUE ESSAS ROTAS SÃO PÚBLICAS (NÃO PRECISAM DE LOGIN)
    public $livre = ['index', 'entrar', 'logout', 'google_callback', 'pegarTokenGoogle'];

    public function index()
    {
        $googleClientId = Chave::get('google_client_id');

        $callbackUrl = BASE_URL . 'login/google_callback';

        $params = [
            'client_id' => $googleClientId,
            'redirect_uri' => $callbackUrl,
            'response_type' => 'code',
            'scope' => 'email profile',
            'access_type' => 'online'
        ];

        if (empty($googleClientId)) {
            $authUrl = '#';
        } else {
            $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
        }

        require __DIR__ . '/../views/login.php';
    }

    public function entrar()
    {
        $user = $_POST['usuario'];
        $pass = $_POST['senha'];

        $usuarioBanco = Usuario::buscarPorLogin($user);

        if ($usuarioBanco && password_verify($pass, $usuarioBanco['senha'])) {
            Auth::logar($usuarioBanco);

            // REDIRECIONA PARA O NOVO CONTROLLER DO ADMIN (URL LIMPA)
            header('Location: ' . BASE_URL . 'admin');
            exit;
        } else {
            $erro = "Usuário ou senha inválidos!";
            require __DIR__ . '/../views/login.php';
        }
    }

    public function logout()
    {
        Auth::logout();
    }

    public function google_callback()
    {
        if (isset($_GET['code'])) {
            $code = $_GET['code'];
            $tokenData = $this->pegarTokenGoogle($code);

            if (isset($tokenData['access_token'])) {
                $googleUser = $this->pegarDadosUsuarioGoogle($tokenData['access_token']);

                if (isset($googleUser['email'])) {
                    $usuario = Usuario::salvarGoogle($googleUser);
                    Auth::logar($usuario);

                    // REDIRECIONA PARA O NOVO CONTROLLER DO ADMIN (URL LIMPA)
                    header('Location: ' . BASE_URL . 'admin');
                    exit;
                }
            }
        }

        echo "Erro ao logar com Google.";
        exit;
    }

    private function pegarTokenGoogle($code)
    {
        $googleClientId = Chave::get('google_client_id');
        $googleClientSecret = Chave::get('google_client_secret');

        $callbackUrl = BASE_URL . 'login/google_callback';

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

        return json_decode($response, true);
    }

    private function pegarDadosUsuarioGoogle($accessToken)
    {
        $url = 'https://www.googleapis.com/oauth2/v1/userinfo?access_token=' . $accessToken;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
        $response = curl_exec($ch);

        return json_decode($response, true);
    }
}
