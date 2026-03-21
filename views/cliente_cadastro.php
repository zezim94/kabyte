<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Criar Conta - KaByte</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🚲</text></svg>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/cliente_login.css">
</head>

<body>
    <main class="login-wrapper">
        <div class="login-card">

            <div class="login-header" style="text-align: center;">
                <div class="icon-circle">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <h2>Crie sua conta</h2>
                <p>Preencha os dados abaixo para começar a comprar.</p>
            </div>

            <?php if (isset($erro)): ?>
                <div class="alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span><?= htmlspecialchars($erro) ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>cliente/processar_cadastro" method="POST">

                <div class="form-group">
                    <label for="nome">Nome Completo</label>
                    <div class="input-group">
                        <i class="fa-solid fa-user input-icon left"></i>
                        <input type="text" id="nome" name="nome" placeholder="Seu nome" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">E-mail</label>
                    <div class="input-group">
                        <i class="fa-solid fa-envelope input-icon left"></i>
                        <input type="email" id="email" name="email" placeholder="seu@email.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="telefone">Telefone / WhatsApp</label>
                    <div class="input-group">
                        <i class="fa-solid fa-phone input-icon left"></i>
                        <input type="text" id="telefone" name="telefone" placeholder="(11) 99999-9999" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="senha">Crie uma Senha</label>
                    <div class="input-group">
                        <i class="fa-solid fa-lock input-icon left"></i>
                        <input type="password" id="senha" name="senha" placeholder="Mínimo de 6 caracteres" required minlength="6">
                        <i class="fa-solid fa-eye input-icon right" id="btnToggleSenha"></i>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    Criar Conta <i class="fa-solid fa-check"></i>
                </button>

                <div class="login-footer" style="margin-top: 20px;">
                    <p style="color: #555;">Já tem uma conta? <a href="<?= BASE_URL ?>cliente/login" style="font-weight: bold; color: var(--cor-primaria);">Faça login</a></p>
                </div>
            </form>
        </div>
    </main>

    <script>
        // Lógica para mostrar/esconder a senha
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