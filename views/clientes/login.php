<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Área do Cliente - Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon"
        href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22></text></svg>">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/cliente_login.css">
</head>

<body>

    <main class="login-wrapper">
        <div class="login-card">

            <div class="login-header" style="text-align: center;">
                <div class="icon-circle">
                    <img src="<?= BASE_URL ?>public/uploads/login.jpeg" alt="Login KaByte">
                </div>
                <h2>Bem-vindo de volta!</h2>
                <p>Acesse sua área exclusiva para acompanhar pedidos</p>
            </div>

            <?php if (isset($erro)): ?>
                <div class="alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span><?= htmlspecialchars($erro) ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>cliente/autenticar" method="POST">

                <div class="form-group">
                    <label for="login">Login</label>
                    <div class="input-group">
                        <i class="fa-solid fa-user input-icon left"></i>
                        <input type="text" id="login" name="login" placeholder="CPF, Email ou Telefone" required
                            autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="senha">Senha</label>
                    <div class="input-group">
                        <i class="fa-solid fa-lock input-icon left"></i>
                        <input type="password" id="senha" name="senha" placeholder="Sua senha secreta" required>
                        <i class="fa-solid fa-eye input-icon right" id="btnToggleSenha"></i>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    Entrar <i class="fa-solid fa-arrow-right"></i>
                </button>

                <?php if (isset($authUrl)): ?>
                    <div class="separator">
                        <span>ou continue com</span>
                    </div>

                    <a href="<?= $authUrl ?>" class="btn-google">
                        <i class="fab fa-google"></i> Google
                    </a>
                <?php endif; ?>

                <div class="login-footer" style="margin-top: 20px;">
                    <p style="margin-bottom: 15px; color: #555;">
                        Ainda não tem uma conta? <br>
                        <a href="<?= BASE_URL ?>cliente/cadastro"
                            style="font-weight: bold; color: var(--cor-primaria); font-size: 1.05rem;">Cadastre-se
                            grátis</a>
                    </p>
                    <a href="<?= BASE_URL ?>cliente/esqueci_senha">Esqueci minha senha</a>
                    <br><br>
                    <a href="<?= BASE_URL ?>vitrine" style="color: #777; text-decoration:none;">&larr; Voltar para
                        Loja</a>
                </div>
            </form>
        </div>
    </main>

    <script>
        const togglePassword = document.querySelector('#btnToggleSenha');
        const password = document.querySelector('#senha');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>

</html>