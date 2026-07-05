<?php require __DIR__ . '/../layout/header_admin.php'; ?>

<style>
    .chaves-container {
        max-width: 900px;
        margin: 30px auto;
        background: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    .chave-item {
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }
    .chave-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    .chave-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    .chave-titulo {
        font-weight: bold;
        font-size: 1.1rem;
        color: #2c3e50;
    }
    .chave-nome {
        font-family: monospace;
        background: #f1f2f6;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 0.85rem;
        color: #e74c3c;
    }
    .form-control {
        width: 100%;
        padding: 12px;
        border: 1px solid #ced4da;
        border-radius: 6px;
        font-family: monospace; /* Fonte monoespaçada é melhor para visualizar chaves/tokens */
        font-size: 0.95rem;
    }
    .form-control:focus {
        border-color: #3498db;
        outline: none;
    }
    .btn-salvar {
        background: #27ae60;
        color: #fff;
        padding: 12px 25px;
        border: none;
        border-radius: 6px;
        font-weight: bold;
        cursor: pointer;
        font-size: 1rem;
        transition: 0.3s;
        width: 100%;
        margin-top: 20px;
    }
    .btn-salvar:hover {
        background: #219150;
    }
</style>

<div class="chaves-container">
    <h2 style="margin-top: 0; color: #333;"><i class="fas fa-key"></i> Gerenciador de Chaves (API)</h2>
    <p style="color: #666; margin-bottom: 30px;">Atualize os tokens de acesso e chaves de API do sistema. Tenha cuidado ao alterar estes valores.</p>

    <?php if (isset($_GET['msg'])): ?>
        <div style="padding: 15px; border-radius: 6px; margin-bottom: 20px; text-align: center;
            <?= ($_GET['sucesso'] == 1) ? 'background:#d4edda; color:#155724;' : 'background:#f8d7da; color:#721c24;' ?>">
            <?= htmlspecialchars($_GET['msg']) ?>
        </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>index.php?rota=admin/chaves/salvar" method="POST">
        
        <?php foreach ($chaves as $chave): ?>
            <div class="chave-item">
                <div class="chave-header">
                    <span class="chave-titulo"><?= htmlspecialchars($chave['descricao']) ?></span>
                    <span class="chave-nome"><?= htmlspecialchars($chave['nome']) ?></span>
                </div>
                
                <input type="text" name="chaves[<?= $chave['id'] ?>]" class="form-control" 
                       value="<?= htmlspecialchars($chave['valor']) ?>" 
                       placeholder="Insira a chave/token aqui">
            </div>
        <?php endforeach; ?>

        <button type="submit" class="btn-salvar">
            <i class="fas fa-save"></i> Salvar Todas as Chaves
        </button>
    </form>
</div>
