<?php
// models/Auth.php
class Auth
{
    public static function iniciarSessao()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function logar($usuario)
    {
        self::iniciarSessao();
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['nivel'] = $usuario['nivel'];

        // Se tiver foto do Google usa, senão usa padrão
        $_SESSION['foto'] = !empty($usuario['foto']) ? $usuario['foto'] : 'public/img/avatar_default.png';

        self::log('Login', "Usuário {$usuario['nome']} entrou.");
    }

    public static function verificar($niveisPermitidos = [])
    {
        self::iniciarSessao();

        // Se não tá logado, tchau
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?rota=login');
            exit;
        }

        // Se a rota exige níveis específicos e o usuário não tem
        if (!empty($niveisPermitidos) && !in_array($_SESSION['nivel'], $niveisPermitidos)) {
            die("Acesso Negado. Seu nível ({$_SESSION['nivel']}) não permite acessar esta página.");
        }
    }

    public static function log($acao, $detalhe)
    {
        // Salva na tabela de logs (Exclusivo do Master depois)
        $pdo = Database::connect();
        $stmt = $pdo->prepare("INSERT INTO sistema_logs (usuario_id, acao) VALUES (?, ?)");
        $uid = $_SESSION['usuario_id'] ?? null;
        $msg = "$acao: $detalhe";
        $stmt->execute([$uid, $msg]);
    }

    public static function logout()
    {
        self::iniciarSessao();

        // Limpa todas as variáveis da sessão
        $_SESSION = [];

        // Remove o cookie da sessão
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Destrói a sessão
        session_destroy();

        // Evita cache (IMPRESCINDÍVEL)
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        // Redireciona corretamente
        header("Location: " . BASE_URL . "vitrine");
        exit;
    }

}