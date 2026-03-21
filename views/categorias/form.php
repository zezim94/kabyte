<?php require __DIR__ . '/../layout/header_admin.php'; ?>

<link rel="stylesheet" href="<?= BASE_URL ?>public/css/cat_form.css">

<div class="form-container">
    <div class="card">
        
        <div class="card-header">
            <div class="header-icon">
                <i class="fas fa-tags"></i>
            </div>
            <h2><?= empty($categoria['id']) ? 'Nova Categoria' : 'Editar Categoria' ?></h2>
        </div>

        <div class="card-body">

            <?php if(isset($erro)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= htmlspecialchars($erro) ?>
                </div>
            <?php endif; ?>

            <form action="index.php?rota=categoria/salvar" method="POST">
                
                <input type="hidden" name="id" value="<?= $categoria['id'] ?>">

                <div class="form-group">
                    <label for="nome" class="form-label">Nome da Categoria</label>
                    <div class="input-group">
                        <i class="fas fa-pen input-icon"></i>
                        <input type="text" 
                               name="nome" 
                               id="nome" 
                               class="form-control" 
                               placeholder="Ex: Bebidas, Limpeza, Padaria..." 
                               required 
                               autofocus
                               value="<?= htmlspecialchars($categoria['nome']) ?>">
                    </div>
                </div>

                <div class="form-actions">
                    <a href="index.php?rota=categorias" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Cancelar
                    </a>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

