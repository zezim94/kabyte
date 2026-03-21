<?php require __DIR__ . '/../layout/header_admin.php'; ?>

<link rel="stylesheet" href="<?= BASE_URL ?>public/css/categoria.css">

<div class="categoria-container">

    <div class="page-header">
        <div>
            <h2><i class="fas fa-tags"></i> Gerenciar Categorias</h2>
            <p class="header-desc">Organize os produtos da sua loja por seções.</p>
        </div>
        <a href="<?= BASE_URL ?>categoria/nova" class="btn-novo">
            <i class="fas fa-plus"></i> Nova Categoria
        </a>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> <?= htmlspecialchars($_GET['msg']) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <table class="tabela-categorias">
                <thead>
                    <tr>
                        <th class="col-id">#</th>
                        <th>Nome da Categoria</th>
                        <th class="col-acoes">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categorias)): ?>
                        <tr>
                            <td colspan="3" style="text-align:center; padding: 30px; color: #777;">
                                <i class="fas fa-folder-open fa-2x"></i><br>
                                Nenhuma categoria cadastrada.
                            </td>
                        </tr>
                    <?php else: $i=0 ?>
                        <?php foreach ($categorias as $c): $i++ ?>
                            <tr>
                                <td class="col-id"><?= $i ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($c['nome']) ?></strong>
                                </td>
                                <td class="col-acoes">
                                    <a href="<?= BASE_URL ?>categoria/editar&id=<?= $c['id'] ?>" class="btn-icon btn-edit"
                                        title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>

                                    <button type="button" class="btn-icon btn-delete" title="Excluir"
                                        onclick="abrirModalExclusao(<?= $c['id'] ?>, '<?= addslashes($c['nome']) ?>')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<div id="modalExclusao" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-icon">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <h3 class="modal-title">Tem certeza?</h3>
        <p class="modal-text">
            Você está prestes a excluir a categoria:
            <span id="nomeCategoriaModal" class="highlight-name"></span>
            <br>
            <small style="color: #e74c3c;">(Isso pode afetar produtos vinculados)</small>
        </p>

        <div class="modal-actions">
            <button onclick="fecharModal()" class="btn-modal btn-cancel">Cancelar</button>
            <a href="#" id="btnConfirmarExclusao" class="btn-modal btn-confirm">Sim, Excluir</a>
        </div>
    </div>
</div>

<script>
    // --- Lógica do Modal ---
    const modal = document.getElementById('modalExclusao');
    const nomeSpan = document.getElementById('nomeCategoriaModal');
    const btnConfirmar = document.getElementById('btnConfirmarExclusao');

    // Função para abrir o modal e setar os dados
    function abrirModalExclusao(id, nome) {
        nomeSpan.textContent = nome;
        // Atualiza o link do botão "Sim" para o ID correto
        // OBS: Ajuste a rota se necessário (ex: index.php?rota=categoria/excluir&id=)
        btnConfirmar.href = "<?= BASE_URL ?>categoria/excluir&id=" + id;

        modal.classList.add('active');
    }

    // Função para fechar
    function fecharModal() {
        modal.classList.remove('active');
    }

    // Fecha se clicar fora do conteúdo do modal
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            fecharModal();
        }
    });

    // Fecha com a tecla ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === "Escape" && modal.classList.contains('active')) {
            fecharModal();
        }
    });
</script>

