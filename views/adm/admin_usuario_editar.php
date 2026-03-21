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
    .form-control:focus { border-color: #3498db; outline: none; }

    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

    .btn-salvar {
        width: 100%; padding: 15px; background: #3498db; color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: bold; cursor: pointer; transition: background 0.3s; margin-top: 10px;
    }
    .btn-salvar:hover { background: #2980b9; }

    .alert-info {
        background: #e8f4fd; color: #2980b9; padding: 12px; border-radius: 8px; font-size: 0.9rem; margin-bottom: 20px; border-left: 4px solid #3498db;
    }
</style>

<div class="admin-container">

    <div class="page-header">
        <h2><i class="fas fa-user-edit"></i> Editar Usuário</h2>
        <a href="<?= BASE_URL ?>api/usuarios" class="btn-voltar"><i class="fas fa-arrow-left"></i> Voltar</a>
    </div>

    <form action="<?= BASE_URL ?>api/salvar_edicao" method="POST" class="card-form">
        
        <input type="hidden" name="id" value="<?= $usuario['id'] ?>">

        <div class="form-group">
            <label>Nome Completo</label>
            <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>E-mail</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Login (Usuário)</label>
                <input type="text" name="usuario" class="form-control" value="<?= htmlspecialchars($usuario['usuario']) ?>" required>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Nível de Acesso</label>
                <select name="nivel" class="form-control" required>
                    <option value="vendedor" <?= $usuario['nivel'] == 'vendedor' ? 'selected' : '' ?>>Vendedor (Apenas Vendas)</option>
                    <option value="admin" <?= $usuario['nivel'] == 'admin' ? 'selected' : '' ?>>Administrador (Acesso Total)</option>
                </select>
            </div>

            <div class="form-group">
                <label>Nova Senha</label>
                <input type="password" name="senha" class="form-control" placeholder="Deixe em branco para manter a atual">
            </div>
        </div>

        <div class="alert-info">
            <i class="fas fa-info-circle"></i> Se não quiser alterar a senha do usuário, basta deixar o campo "Nova Senha" vazio.
        </div>

        <button type="submit" class="btn-salvar">
            <i class="fas fa-save"></i> Salvar Alterações
        </button>

    </form>
</div>

</body>
</html>