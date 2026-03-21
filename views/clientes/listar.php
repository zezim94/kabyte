<?php
// Garante sessão
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inicializa variável
$clientes = $clientes ?? [];
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Clientes - KaByte</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/adm_cliente_listar.css">

    <script>
        const BASE_URL = "<?= BASE_URL ?>";
    </script>
</head>

<body class="bg-admin">

    <?php require_once __DIR__ . '/../layout/header_admin.php'; ?>

    <div class="admin-container">

        <div class="page-header">
            <div class="header-content">
                <h2><i class="fas fa-users"></i> Carteira de Clientes</h2>
                <p>Gerencie, edite e acompanhe seus clientes cadastrados.</p>
            </div>

            <div class="header-actions">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="filtroCliente" onkeyup="filtrarTabela()"
                        placeholder="Buscar por nome, email ou telefone...">
                </div>

                <a href="<?= BASE_URL ?>cliente/novo" class="btn-novo">
                    <i class="fas fa-plus"></i>
                    <span>Novo Cliente</span>
                </a>
            </div>
        </div>

        <div class="painel-tabela">
            <table class="tabela-clientes" id="tabelaClientes">
                <thead>
                    <tr>
                        <th class="col-id">#</th>
                        <th>Cliente</th>
                        <th>Contato</th>
                        <th>Documento (CPF)</th>
                        <th class="col-actions">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($clientes)):
                        $i = 0 ?>
                        <?php foreach ($clientes as $c):
                            $i++ ?>
                            <tr class="linha-cliente">
                                <td data-label="ID" class="col-id">
                                    <span class="badge-id">#<?= str_pad($c['id'], 4, '0', STR_PAD_LEFT) ?></span>
                                </td>

                                <td data-label="Cliente">
                                    <div class="user-profile">
                                        <div class="avatar-circle">
                                            <?= strtoupper(substr($c['nome'], 0, 1)) ?>
                                        </div>
                                        <div class="user-details">
                                            <span class="user-name"><?= htmlspecialchars($c['nome']) ?></span>
                                            <span class="user-since">Cadastrado em:
                                                <?= date('d/m/Y', strtotime($c['data_cadastro'] ?? 'now')) ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td data-label="Contato">
                                    <div class="contact-info">
                                        <?php if (!empty($c['email'])): ?>
                                            <div class="contact-item">
                                                <i class="far fa-envelope"></i> <?= htmlspecialchars($c['email']) ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($c['telefone'])): ?>
                                            <div class="contact-item">
                                                <i class="fas fa-phone"></i> <?= htmlspecialchars($c['telefone']) ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="contact-item" style="color:#999;">
                                                <i class="fas fa-phone-slash"></i> Sem telefone
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <td data-label="Documento">
                                    <span class="location-text">
                                        <i class="fas fa-id-card"></i>
                                        <?php if (!empty($c['cpf'])): ?>
                                            <?= htmlspecialchars($c['cpf']) ?>
                                        <?php else: ?>
                                            <span style="color:#999;">Não informado</span>
                                        <?php endif; ?>
                                    </span>
                                </td>

                                <td data-label="Ações" class="col-actions">
                                    <div class="action-group">
                                        <a href="<?= BASE_URL ?>cliente/editar?id=<?= $c['id'] ?>" class="btn-icon btn-edit"
                                            title="Editar">
                                            <i class="fas fa-pen"></i>
                                        </a>

                                        <?php if (!empty($c['telefone'])): ?>
                                            <a href="https://wa.me/55<?= preg_replace('/\D/', '', $c['telefone']) ?>"
                                                target="_blank" class="btn-icon btn-whatsapp" title="Conversar no WhatsApp">
                                                <i class="fab fa-whatsapp"></i>
                                            </a>
                                        <?php endif; ?>

                                        <button type="button" class="btn-icon btn-delete" title="Excluir"
                                            onclick="abrirModalExclusao(<?= $c['id'] ?>, '<?= addslashes($c['nome']) ?>')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="empty-state">
                                <i class="fas fa-user-slash"
                                    style="font-size: 3rem; color: #ccc; margin-bottom: 10px; display:block;"></i>
                                <p>Nenhum cliente cadastrado no momento.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div id="msgSemResultados" class="no-results"
                style="display:none; text-align:center; padding: 40px; color:#999;">
                <i class="fas fa-search" style="font-size: 2rem; margin-bottom: 10px;"></i>
                <p>Nenhum cliente encontrado para sua busca.</p>
            </div>
        </div>

    </div>

    <div id="modalExcluir" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <i class="fas fa-exclamation-circle"></i>
                <h3>Excluir Cliente?</h3>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja remover o cliente:</p>
                <strong id="nomeClienteModal" class="highlight-name"></strong>
                <p class="warning-text">O histórico de compras deste cliente pode ser afetado.</p>
            </div>
            <div class="modal-footer">
                <button onclick="fecharModal()" class="btn-cancelar">Cancelar</button>
                <a id="btnConfirmarExclusao" href="#" class="btn-confirmar">Sim, Excluir</a>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('modalExcluir');
        const nomeSpan = document.getElementById('nomeClienteModal');
        const btnConfirmar = document.getElementById('btnConfirmarExclusao');

        function abrirModalExclusao(id, nome) {
            nomeSpan.textContent = nome;
            btnConfirmar.href = `<?= BASE_URL ?>cliente/excluir?id=${id}`;
            modal.classList.add('active');
        }

        function fecharModal() {
            modal.classList.remove('active');
        }

        modal.addEventListener('click', (e) => {
            if (e.target === modal) fecharModal();
        });

        function filtrarTabela() {
            let input = document.getElementById('filtroCliente');
            let filter = input.value.toLowerCase();
            let table = document.getElementById('tabelaClientes');
            let tr = table.getElementsByClassName('linha-cliente');
            let encontrou = false;

            for (let i = 0; i < tr.length; i++) {
                let textoLinha = tr[i].textContent || tr[i].innerText;

                if (textoLinha.toLowerCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                    encontrou = true;
                } else {
                    tr[i].style.display = "none";
                }
            }

            let msg = document.getElementById('msgSemResultados');
            let thead = table.querySelector('thead');

            if (encontrou || tr.length === 0) {
                msg.style.display = "none";
                thead.style.display = "";
            } else {
                msg.style.display = "block";
                thead.style.display = "none";
            }
        }
    </script>
</body>

</html>