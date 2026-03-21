<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Recuperar Senha</title>
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
                    <i class="fa-solid fa-unlock-keyhole"></i>
                </div>
                <h2>Esqueceu a senha?</h2>
                <p>Digite seu e-mail cadastrado e enviaremos as instruções para redefini-la.</p>
            </div>

            <?php if (isset($erro)): ?>
                <div class="alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span><?= htmlspecialchars($erro) ?></span>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['sucesso'])): ?>
                <div style="background: #e8f5e9; color: #27ae60; padding: 12px; border-radius: 8px; margin-bottom: 20px; display: flex; gap: 10px; align-items: center; font-size: 0.95rem;">
                    <i class="fa-solid fa-check-circle"></i>
                    <span>Instruções enviadas com sucesso! Verifique sua caixa de entrada (ou Spam).</span>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>cliente/processar_recuperacao" method="POST">

                <div class="form-group">
                    <label for="email">E-mail Cadastrado</label>
                    <div class="input-group">
                        <i class="fa-solid fa-envelope input-icon left"></i>
                        <input type="email" id="email" name="email" placeholder="seu@email.com" required autofocus>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    Enviar Link de Recuperação <i class="fa-solid fa-paper-plane"></i>
                </button>

                <div class="login-footer" style="margin-top: 25px;">
                    <a href="<?= BASE_URL ?>cliente/login" style="color: #777; text-decoration:none;">
                        &larr; Voltar para o Login
                    </a>
                </div>
            </form>

        </div>
    </main>
</body>

</html>