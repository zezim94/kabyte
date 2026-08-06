<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários - KaByte</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<?php require __DIR__ . '/../layout/header_admin.php'; ?>

<style>
    :root {
        --bg-body: #f1f5f9;
        --card-bg: #ffffff;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --border-color: #e2e8f0;
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.07), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        --shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
        --radius: 12px;
        --transition: 0.2s ease;
        --green: #10b981;
        --green-hover: #059669;
        --blue: #3b82f6;
        --blue-hover: #2563eb;
        --red: #ef4444;
        --red-hover: #dc2626;
        --orange: #f59e0b;
        --gray: #94a3b8;
    }

    body {
        background-color: var(--bg-body);
        margin: 0;
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        color: var(--text-primary);
    }

    .admin-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1.5rem;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border-color);
    }

    .page-header h2 {
        margin: 0;
        color: var(--text-primary);
        font-size: 1.6rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-novo {
        background: var(--green);
        color: white;
        padding: 0.6rem 1.4rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: background var(--transition), transform var(--transition);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        white-space: nowrap;
    }

    .btn-novo:hover {
        background: var(--green-hover);
        transform: translateY(-1px);
    }

    /* Container da tabela com scroll responsivo */
    .table-container {
        background: var(--card-bg);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border: 1px solid var(--border-color);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        min-width: 700px;
        /* garante scroll suave no mobile */
        font-size: 0.95rem;
    }

    thead {
        background-color: #f8fafc;
    }

    th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.05em;
        color: var(--text-secondary);
        padding: 1rem 1.2rem;
        border-bottom: 1px solid var(--border-color);
    }

    td {
        padding: 0.9rem 1.2rem;
        border-bottom: 1px solid #f1f5f9;
        color: var(--text-primary);
    }

    tbody tr:last-child td {
        border-bottom: none;
    }

    tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Badges de nível */
    .badge-nivel {
        padding: 0.25rem 0.8rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .badge-admin {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-vendedor {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-padrao {
        background: #f1f5f9;
        color: #475569;
    }

    /* Botões de ação */
    .action-btn {
        padding: 0.4rem 0.7rem;
        border-radius: 6px;
        text-decoration: none;
        color: white;
        font-size: 0.9rem;
        margin-right: 4px;
        cursor: pointer;
        border: none;
        transition: background var(--transition), transform var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }

    .btn-edit {
        background: var(--blue);
    }

    .btn-edit:hover {
        background: var(--blue-hover);
        transform: scale(1.05);
    }

    .btn-delete {
        background: var(--red);
    }

    .btn-delete:hover {
        background: var(--red-hover);
        transform: scale(1.05);
    }

    /* Modal */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 10000;
        display: none;
        justify-content: center;
        align-items: center;
        padding: 1rem;
        backdrop-filter: blur(3px);
    }

    .modal-box {
        background: white;
        width: 100%;
        max-width: 400px;
        max-height: 90vh;
        overflow-y: auto;
        padding: 2rem;
        border-radius: var(--radius);
        text-align: center;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        animation: popIn 0.3s ease;
    }

    @keyframes popIn {
        from {
            transform: scale(0.9);
            opacity: 0;
        }

        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .modal-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn-modal {
        padding: 0.6rem 1.5rem;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: background var(--transition);
    }

    .btn-cancel {
        background: #f1f5f9;
        color: #475569;
    }

    .btn-cancel:hover {
        background: #e2e8f0;
    }

    /* Responsivo */
    @media (max-width: 768px) {
        .admin-container {
            padding: 0 1rem;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .btn-novo {
            width: 100%;
            justify-content: center;
        }

        table {
            min-width: 600px;
            /* reduz um pouco o mínimo */
        }

        th,
        td {
            padding: 0.75rem 0.8rem;
            font-size: 0.9rem;
        }
    }
</style>

<div id="modalExclusao" class="modal-overlay">
    <div class="modal-box">
        <div style="font-size: 3rem; color: #e74c3c; margin-bottom: 10px;">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3 style="color: #2c3e50; margin: 0;">Excluir Usuário?</h3>
        <p style="color: #666; margin-top: 10px;">Esta ação não poderá ser desfeita. Tem certeza que deseja remover este
            acesso?</p>

        <div class="modal-actions">
            <button onclick="fecharModalExclusao()" class="btn-modal btn-cancel">Cancelar</button>
            <a id="btnConfirmarExclusao" href="#" class="btn-modal btn-delete">Sim, Excluir</a>
        </div>
    </div>
</div>

<div class="admin-container">

    <div class="page-header">
        <h2><i class="fas fa-users-cog"></i> Gerenciar Usuários do Sistema</h2>
        <a href="<?= BASE_URL ?>admin/novo" class="btn-novo">
            <i class="fas fa-plus"></i> Novo Usuário
        </a>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Login (Usuário)</th>
                    <th>E-mail</th>
                    <th>Nível de Acesso</th>
                    <th style="text-align: center;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($usuarios)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #777; padding: 30px;">
                            Nenhum usuário encontrado.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($usuarios as $u): ?>
                        <?php
                        $classeBadge = 'badge-padrao';
                        if ($u['nivel'] == 'admin')
                            $classeBadge = 'badge-admin';
                        if ($u['nivel'] == 'vendedor')
                            $classeBadge = 'badge-vendedor';
                        ?>
                        <tr>
                            <td style="color: #7f8c8d; font-weight: bold;">#<?= $u['id'] ?></td>
                            <td style="font-weight: 500; color: #2c3e50;"><?= htmlspecialchars($u['nome']) ?></td>
                            <td><?= htmlspecialchars($u['usuario']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td>
                                <span class="badge-nivel <?= $classeBadge ?>">
                                    <?= htmlspecialchars($u['nivel']) ?>
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <a href="<?= BASE_URL ?>admin/editar?id=<?= $u['id'] ?>" class="action-btn btn-edit"
                                    title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <?php if ($_SESSION['usuario_id'] != $u['id']): ?>
                                    <button onclick="abrirModalExclusao('<?= BASE_URL ?>usuarios/excluir?id=<?= $u['id'] ?>')"
                                        class="action-btn btn-delete" title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<script>
    // Lógica para o Modal de Exclusão
    function abrirModalExclusao(urlExclusao) {
        // Pega o botão "Sim, Excluir" do modal e injeta o link correto nele
        document.getElementById('btnConfirmarExclusao').href = urlExclusao;
        // Mostra o modal na tela
        document.getElementById('modalExclusao').style.display = 'flex';
    }

    function fecharModalExclusao() {
        // Esconde o modal
        document.getElementById('modalExclusao').style.display = 'none';
    }
</script>
<!-- Container obrigatório para o Toast (se já não estiver no header/footer) -->

</body>

</html>