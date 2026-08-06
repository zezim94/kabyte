<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Área do Cliente - Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22></text></svg>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/cliente_login.css">

    <!-- ESTILOS DO TOAST INJETADOS PARA A PÁGINA DE LOGIN -->
    <style>
        #toast-container {
            position: fixed;
            top: 25px;
            right: 25px;
            z-index: 999999;
            display: flex;
            flex-direction: column;
            gap: 15px;
            pointer-events: none;
        }

        .toast {
            min-width: 320px;
            background-color: #ffffff;
            color: #333333;
            padding: 18px 24px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08), 0 2px 10px rgba(0, 0, 0, 0.04);
            font-family: 'Segoe UI', system-ui, sans-serif;
            font-size: 1rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 16px;
            opacity: 0;
            transform: translateX(120%);
            animation: slideInToast 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
            border-left: 6px solid;
            pointer-events: auto;
        }

        .toast.hide {
            animation: slideOutToast 0.4s ease forwards;
        }

        .toast.success { border-left-color: #2ecc71; }
        .toast.success i { color: #2ecc71; font-size: 1.5rem; }

        .toast.error { border-left-color: #e74c3c; }
        .toast.error i { color: #e74c3c; font-size: 1.5rem; }

        .toast.warning { border-left-color: #f1c40f; }
        .toast.warning i { color: #f1c40f; font-size: 1.5rem; }

        @keyframes slideInToast {
            0% { opacity: 0; transform: translateX(120%); }
            100% { opacity: 1; transform: translateX(0); }
        }

        @keyframes slideOutToast {
            0% { opacity: 1; transform: translateX(0); }
            100% { opacity: 0; transform: translateX(120%); }
        }

        @media (max-width: 600px) {
            #toast-container {
                top: 20px;
                right: 15px;
                left: 15px;
                align-items: center;
            }
            .toast {
                min-width: 100%;
                box-sizing: border-box;
                transform: translateY(-120%);
                animation: slideInToastMobile 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
            }
            .toast.hide {
                animation: slideOutToastMobile 0.4s ease forwards;
            }
            @keyframes slideInToastMobile {
                0% { opacity: 0; transform: translateY(-120%); }
                100% { opacity: 1; transform: translateY(0); }
            }
            @keyframes slideOutToastMobile {
                0% { opacity: 1; transform: translateY(0); }
                100% { opacity: 0; transform: translateY(-120%); }
            }
        }
    </style>
</head>

<body>

    <!-- CONTAINER ONDE OS TOASTS VÃO APARECER -->
    <div id="toast-container"></div>

    <main class="login-wrapper">
        <div class="login-card">

            <div class="login-header" style="text-align: center;">
                <div class="icon-circle">
                    <img src="<?= BASE_URL ?>public/uploads/login.jpeg" alt="Login KaByte">
                </div>
                <h2>Bem-vindo de volta!</h2>
                <p>Acesse sua área exclusiva para acompanhar pedidos</p>
            </div>

            <!-- Mantido para erros internos do Controller (login incorreto) -->
            <?php if (isset($erro)): ?>
                <div class="alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span><?= htmlspecialchars($erro) ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>index.php?rota=cliente_area/autenticar" method="POST">

                <div class="form-group">
                    <label for="login">Login</label>
                    <div class="input-group">
                        <i class="fa-solid fa-user input-icon left"></i>
                        <input type="text" id="login" name="login" placeholder="CPF, Email ou Telefone" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="senha">Senha</label>
                    <div class="input-group">
                        <i class="fa-solid fa-lock input-icon left"></i>
                        <input type="password" id="senha" name="senha" placeholder="Sua senha secreta" required>
                        <i class="fa-solid fa-eye input-icon right" id="btnToggleSenha" style="cursor: pointer;"></i>
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
                        <a href="<?= BASE_URL ?>cliente/cadastro" style="font-weight: bold; color: var(--cor-primaria); font-size: 1.05rem;">Cadastre-se grátis</a>
                    </p>
                    
                    <a href="<?= BASE_URL ?>index.php?rota=cliente/esqueci_senha">Esqueci minha senha</a>
                    <br><br>
                    
                    <a href="<?= BASE_URL ?>index.php?rota=produto/vitrine" style="color: #777; text-decoration:none;">
                        &larr; Voltar para Loja
                    </a>
                </div>
            </form>
        </div>
    </main>

    <script>
        // Lógica de Mostrar/Ocultar Senha
        const togglePassword = document.querySelector('#btnToggleSenha');
        const password = document.querySelector('#senha');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        // ==========================================
        // LÓGICA DO TOAST (Disparado pela URL)
        // ==========================================
        function showToast(mensagem, tipo = "success") {
            const container = document.getElementById("toast-container");
            if (!container) return; 

            let icon = "";
            if (tipo === "success") icon = '<i class="fa-solid fa-circle-check"></i>';
            if (tipo === "error") icon = '<i class="fa-solid fa-circle-xmark"></i>';
            if (tipo === "warning") icon = '<i class="fa-solid fa-triangle-exclamation"></i>';

            const toast = document.createElement("div");
            toast.className = `toast ${tipo}`;
            toast.innerHTML = `${icon} <span>${mensagem}</span>`;

            container.appendChild(toast);

            setTimeout(() => {
                toast.classList.add("hide");
                setTimeout(() => toast.remove(), 400);
            }, 4500);
        }

        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            
            // Se encontrar o Toast na URL (Ex: após alterar a senha)
            if (urlParams.has('toast') && urlParams.has('msg')) {
                const tipo = urlParams.get('toast');
                const msg = urlParams.get('msg');
                
                showToast(msg, tipo);
                
                // Limpa APENAS o toast da URL, mantendo rotas como ?rota=cliente_area/login
                urlParams.delete('toast');
                urlParams.delete('msg');
                
                let novaUrl = window.location.pathname;
                if (urlParams.toString()) {
                    novaUrl += '?' + urlParams.toString();
                }
                window.history.replaceState(null, null, novaUrl);
            }
        });
    </script>
</body>

</html>