<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Chaves - KaByte</title>
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
        --radius: 12px;
        --transition: 0.2s ease;
        --green: #10b981;
        --green-hover: #059669;
        --blue: #3b82f6;
        --blue-hover: #2563eb;
    }

    body {
        background-color: var(--bg-body);
        margin: 0;
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        color: var(--text-primary);
    }

    * {
        box-sizing: border-box;
    }

    .chaves-container {
        max-width: 900px;
        margin: 2rem auto;
        padding: 0 1.5rem;
    }

    .page-header {
        margin-bottom: 2rem;
    }

    .page-header h2 {
        color: var(--text-primary);
        font-size: 1.8rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0 0 0.5rem;
    }

    .page-header p {
        color: var(--text-secondary);
        margin: 0;
    }

    /* Lista de chaves */
    .chaves-list {
        background: var(--card-bg);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid var(--border-color);
        overflow: hidden;
    }

    .chave-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.2rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        transition: background var(--transition);
    }

    .chave-item:last-child {
        border-bottom: none;
    }

    .chave-item:hover {
        background: #f8fafc;
    }

    .chave-info {
        flex: 1;
        min-width: 0;
    }

    .chave-descricao {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .chave-nome {
        font-family: 'Courier New', monospace;
        background: #f1f5f9;
        padding: 0.15rem 0.5rem;
        border-radius: 4px;
        font-size: 0.8rem;
        color: #64748b;
        display: inline-block;
    }

    .chave-valor {
        color: var(--text-secondary);
        font-family: 'Courier New', monospace;
        font-size: 0.9rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 300px;
        display: inline-block;
        margin-left: 1rem;
    }

    .btn-editar {
        background: var(--blue);
        color: white;
        border: none;
        padding: 0.5rem 1.2rem;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: background var(--transition), transform var(--transition);
        display: flex;
        align-items: center;
        gap: 0.4rem;
        flex-shrink: 0;
    }

    .btn-editar:hover {
        background: var(--blue-hover);
        transform: translateY(-1px);
    }

    /* Modal */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        backdrop-filter: blur(3px);
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s, visibility 0.3s;
    }

    .modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .modal-box {
        background: white;
        width: 100%;
        max-width: 500px;
        border-radius: var(--radius);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        transform: translateY(20px);
        transition: transform 0.3s ease;
    }

    .modal-overlay.active .modal-box {
        transform: translateY(0);
    }

    .modal-header {
        padding: 1.2rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        margin: 0;
        color: var(--text-primary);
        font-size: 1.2rem;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #94a3b8;
        cursor: pointer;
        padding: 0;
        line-height: 1;
    }

    .modal-close:hover {
        color: var(--text-primary);
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-body label {
        font-weight: 600;
        color: var(--text-secondary);
        display: block;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .modal-body input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-family: 'Courier New', monospace;
        font-size: 0.95rem;
        transition: border-color var(--transition);
    }

    .modal-body input:focus {
        outline: none;
        border-color: var(--blue);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border-color);
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }

    .btn-cancelar {
        background: #f1f5f9;
        color: #64748b;
        border: none;
        padding: 0.6rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: background var(--transition);
    }

    .btn-cancelar:hover {
        background: #e2e8f0;
    }

    .btn-salvar-modal {
        background: var(--green);
        color: white;
        border: none;
        padding: 0.6rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: background var(--transition);
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .btn-salvar-modal:hover {
        background: var(--green-hover);
    }

    .btn-salvar-modal:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* Responsivo */
    @media (max-width: 600px) {
        .chave-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.8rem;
        }

        .chave-valor {
            max-width: 100%;
            margin-left: 0;
            display: block;
            margin-top: 0.3rem;
        }

        .btn-editar {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="chaves-container">
    <div class="page-header">
        <h2><i class="fas fa-key"></i> Gerenciador de Chaves (API)</h2>
        <p>Atualize tokens e chaves de API do sistema com segurança. Clique em "Editar" para modificar cada chave individualmente.</p>
    </div>

    <!-- Lista de chaves -->
    <div class="chaves-list">
        <?php foreach ($chaves as $chave): ?>
            <div class="chave-item">
                <div class="chave-info">
                    <div class="chave-descricao"><?= htmlspecialchars($chave['descricao']) ?></div>
                    <span class="chave-nome"><?= htmlspecialchars($chave['nome']) ?></span>
                    <span class="chave-valor" title="<?= htmlspecialchars($chave['valor']) ?>">
                        <?= htmlspecialchars(strlen($chave['valor']) > 40 ? substr($chave['valor'], 0, 40) . '…' : $chave['valor']) ?>
                    </span>
                </div>
                <button class="btn-editar" onclick="abrirModal(<?= $chave['id'] ?>, '<?= addslashes($chave['descricao']) ?>', '<?= addslashes($chave['nome']) ?>', '<?= addslashes($chave['valor']) ?>')">
                    <i class="fas fa-pen"></i> Editar
                </button>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal de edição -->
<div class="modal-overlay" id="modalEdicao">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Editar Chave</h3>
            <button class="modal-close" onclick="fecharModal()">&times;</button>
        </div>
        <div class="modal-body">
            <label id="modalDescricao"></label>
            <input type="text" id="modalValor" placeholder="Insira o novo valor">
        </div>
        <div class="modal-footer">
            <button class="btn-cancelar" onclick="fecharModal()">Cancelar</button>
            <button class="btn-salvar-modal" id="btnSalvar" onclick="salvarChave()">
                <i class="fas fa-save"></i> Salvar
            </button>
        </div>
    </div>
</div>

<script>
    let chaveIdAtual = null;

    function abrirModal(id, descricao, nome, valor) {
        chaveIdAtual = id;
        document.getElementById('modalDescricao').innerHTML = `${descricao} <small style="color:#64748b;">(${nome})</small>`;
        document.getElementById('modalValor').value = valor;
        document.getElementById('modalEdicao').classList.add('active');
        document.getElementById('modalValor').focus();
    }

    function fecharModal() {
        document.getElementById('modalEdicao').classList.remove('active');
        chaveIdAtual = null;
    }

    // Fechar ao clicar fora do modal
    document.getElementById('modalEdicao').addEventListener('click', function(e) {
        if (e.target === this) fecharModal();
    });

    async function salvarChave() {
        const btn = document.getElementById('btnSalvar');
        const valor = document.getElementById('modalValor').value.trim();

        if (!valor) {
            // Utiliza o nosso Toast global
            if (typeof showToast === "function") showToast('O valor não pode estar vazio.', 'warning');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';

        try {
            const formData = new FormData();
            formData.append('id', chaveIdAtual);
            formData.append('valor', valor);

            const response = await fetch('<?= BASE_URL ?>chave/salvarIndividual', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.sucesso) {
                fecharModal();
                // Redireciona com o Toast Global via URL em vez de um reload seco
                window.location.href = '<?= BASE_URL ?>chave?toast=success&msg=' + encodeURIComponent(data.msg);
            } else {
                if (typeof showToast === "function") showToast(data.msg || 'Erro ao salvar.', 'error');
            }
        } catch (error) {
            console.error(error);
            if (typeof showToast === "function") showToast('Erro de conexão.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save"></i> Salvar';
        }
    }
</script>