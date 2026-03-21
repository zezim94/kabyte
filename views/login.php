<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title>Login - KaByte</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="public/css/style.css">
    <style>
        /* --- ESTILOS GERAIS --- */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            /* Gradiente moderno escuro */
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-box {
            background: #ffffff;
            padding: 40px;
            border-radius: 12px;
            width: 350px;
            text-align: center;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* --- LOGO E CABEÇALHO --- */
        .logo-container {
            margin-bottom: 25px;
        }

        .logo-img {
            width: 70px;
            /* Tamanho da imagem */
            height: auto;
            margin-bottom: 10px;
            border-radius: 50%;
        }

        .logo-container h2 {
            margin: 0;
            color: #1e293b;
            font-size: 1.8rem;
            letter-spacing: 1px;
            font-weight: 800;
        }

        .logo-container p {
            margin: 5px 0 0 0;
            color: #64748b;
            font-size: 0.85rem;
        }

        /* --- INPUTS COM ÍCONES --- */
        .input-group {
            position: relative;
            margin-bottom: 15px;
        }

        /* Ícone da esquerda (Padrão) */
        .input-group i:not(.toggle-password) {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.1rem;
            pointer-events: none;
            /* Impede que o ícone bloqueie o clique no input */
        }

        /* Novo Ícone da direita (Olho) */
        .input-group .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.1rem;
            cursor: pointer;
            transition: color 0.3s;
        }

        .input-group .toggle-password:hover {
            color: #3498db;
        }

        .input-group input {
            width: 100%;
            padding: 12px 12px 12px 45px;
            /* Espaço extra na esquerda para o ícone */
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 1rem;
            color: #334155;
            background-color: #f8fafc;
            transition: all 0.3s ease;
        }

        /* Dá espaço na direita do input de senha para não encostar no olho */
        #senha {
            padding-right: 45px;
        }

        .input-group input:focus {
            outline: none;
            border-color: #3498db;
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }

        /* --- BOTÕES --- */
        .btn-entrar {
            width: 100%;
            padding: 12px;
            background: #3498db;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 8px;
            font-weight: bold;
            font-size: 1rem;
            transition: background 0.3s ease;
            margin-top: 5px;
        }

        .btn-entrar:hover {
            background: #2980b9;
        }

        /* --- SEPARADOR MODERNO --- */
        .separator {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 20px 0;
            color: #94a3b8;
            font-size: 0.85rem;
        }

        .separator::before,
        .separator::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
        }

        .separator:not(:empty)::before {
            margin-right: 10px;
        }

        .separator:not(:empty)::after {
            margin-left: 10px;
        }

        /* --- BOTÃO GOOGLE --- */
        .btn-google {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 12px;
            background: #ffffff;
            color: #475569;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
            font-size: 0.95rem;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        .btn-google:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
        }

        .btn-google img {
            width: 20px;
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <div class="login-box">

        <div class="logo-container">
            <img src="public/uploads/login.jpg" alt="Logo KaByte" class="logo-img">
            <h2>KABYTE</h2>
            <p>Acesso ao Sistema</p>
        </div>

        <form method="POST" action="index.php?rota=autenticar">

            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="usuario" placeholder="Usuário" required>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="senha" id="senha" placeholder="Senha" required>
                <i class="fas fa-eye toggle-password" id="togglePassword" title="Mostrar/Ocultar Senha"></i>
            </div>

            <button type="submit" class="btn-entrar">Entrar</button>
        </form>

        <div class="separator">ou entre com</div>

        <?php if (isset($authUrl)): ?>
            <a href="<?= $authUrl ?>" class="btn-google">
                <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="Google Logo">
                Login com Google
            </a>
        <?php endif; ?>

    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const inputSenha = document.getElementById('senha');

        togglePassword.addEventListener('click', function () {
            // Alterna o tipo do input entre 'password' e 'text'
            const tipoAtual = inputSenha.getAttribute('type') === 'password' ? 'text' : 'password';
            inputSenha.setAttribute('type', tipoAtual);

            // Alterna o ícone entre o olho aberto e fechado
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>

</html>