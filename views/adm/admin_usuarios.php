<?php require __DIR__ . '/../layout/header_admin.php'; ?>

<style>
    .admin-container {
        max-width: 1200px;
        margin: 30px auto;
        padding: 0 20px;
        font-family: 'Segoe UI', sans-serif;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        border-bottom: 1px solid #eee;
        padding-bottom: 15px;
    }

    .page-header h2 {
        margin: 0;
        color: #2c3e50;
    }

    .btn-novo {
        background: #27ae60;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: bold;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background 0.3s;
    }

    .btn-novo:hover {
        background: #219150;
    }

    /* Tabela Estilizada */
    .table-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    thead {
        background-color: #f8f9fa;
        color: #2c3e50;
    }

    th,
    td {
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
    }

    th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }

    tbody tr:hover {
        background-color: #fcfcfc;
    }

    /* Badges de Nível */
    .badge-nivel {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: bold;
        text-transform: uppercase;
    }

    .badge-admin {
        background: #e8f4fd;
        color: #3498db;
    }

    .badge-vendedor {
        background: #fef5e7;
        color: #f39c12;
    }

    .badge-padrao {
        background: #f4f6f6;
        color: #7f8c8d;
    }

    /* Botões de Ação */
    .action-btn {
        padding: 6px 10px;
        border-radius: 6px;
        text-decoration: none;
        color: white;
        font-size: 0.9rem;
        margin-right: 5px;
        cursor: pointer;
        border: none;
    }

    .btn-edit {
        background: #3498db;
    }

    .btn-delete {
        background: #e74c3c;
    }

    .btn-edit:hover {
        background: #2980b9;
    }

    .btn-delete:hover {
        background: #c0392b;
    }

    /* --- CUSTOM MODAL EXCLUSÃO --- */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 10000;
        display: none;
        /* Escondido por padrão */
        justify-content: center;
        align-items: center;
        backdrop-filter: blur(2px);
    }

    .modal-box {
        background: white;
        width: 90%;
        max-width: 400px;
        padding: 25px;
        border-radius: 15px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        animation: popIn 0.3s;
    }

    @keyframes popIn {
        from {
            transform: scale(0.8);
            opacity: 0;
        }

        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .modal-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
        justify-content: center;
    }

    .btn-modal {
        padding: 10px 25px;
        border-radius: 8px;
        border: none;
        font-weight: bold;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }

    .btn-cancel {
        background: #eee;
        color: #555;
    }

    .btn-cancel:hover {
        background: #ddd;
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
        <a href="<?= BASE_URL ?>usuarios/novo" class="btn-novo">
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
                                <a href="<?= BASE_URL ?>usuarios/editar?id=<?= $u['id'] ?>" class="action-btn btn-edit"
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

</body>

</html>