<?php require __DIR__ . '/../layout/header_admin.php'; ?>

<style>
    .admin-container {
        max-width: 800px;
        margin: 30px auto;
        padding: 0 20px;
        font-family: 'Segoe UI', sans-serif;
    }

    .page-header {
        margin-bottom: 25px;
        border-bottom: 1px solid #eee;
        padding-bottom: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .page-header h2 { margin: 0; color: #2c3e50; }

    .btn-voltar {
        background: #95a5a6; color: white; padding: 8px 15px; border-radius: 8px; text-decoration: none; font-weight: bold; transition: background 0.3s;
    }
    .btn-voltar:hover { background: #7f8c8d; }

    .card-form {
        background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-weight: 600; margin-bottom: 8px; color: #34495e; }
    
    .form-control {
        width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem; transition: border-color 0.3s;
    }
    .form-control:focus { border-color: #27ae60; outline: none; }

    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

    .btn-salvar {
        width: 100%; padding: 15px; background: #27ae60; color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: bold; cursor: pointer; transition: background 0.3s; margin-top: 10px;
    }
    .btn-salvar:hover { background: #219150; }
</style>

<div class="admin-container">

    <div class="page-header">
        <h2><i class="fas fa-user-plus"></i> Cadastrar Novo Usuário</h2>
        <a href="<?= BASE_URL ?>api/usuarios" class="btn-voltar"><i class="fas fa-arrow-left"></i> Voltar</a>
    </div>

    <form action="<?= BASE_URL ?>usuarios/salvar" method="POST" class="card-form">
        
        <div class="form-group">
            <label>Nome Completo</label>
            <input type="text" name="nome" class="form-control" placeholder="Ex: João da Silva" required autofocus>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>E-mail</label>
                <input type="email" name="email" class="form-control" placeholder="joao@kabyte.com.br" required>
            </div>
            
            <div class="form-group">
                <label>Login (Nome de Usuário)</label>
                <input type="text" name="usuario" class="form-control" placeholder="joaosilva" required>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Nível de Acesso</label>
                <select name="nivel" class="form-control" required>
                    <option value="vendedor">Vendedor (Acesso Restrito)</option>
                    <option value="admin">Administrador (Acesso Total)</option>
                </select>
            </div>

            <div class="form-group">
                <label>Senha de Acesso</label>
                <input type="password" name="senha" class="form-control" placeholder="Crie uma senha segura" required>
            </div>
        </div>

        <button type="submit" class="btn-salvar">
            <i class="fas fa-check-circle"></i> Cadastrar Usuário
        </button>

    </form>
</div>

</body>
</html>