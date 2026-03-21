<?php
// Simulação de dados (caso a controller não envie)
$produtos = $produtos ?? [];
$categorias = $categorias ?? [];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estoque de Produtos - KaByte</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/produto_adm.css">

    <script>const BASE_URL = "<?= BASE_URL ?>";</script>

    <style>
        /* Estilo extra para o filtro de categoria */
        .search-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .select-filtro {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 20px;
            outline: none;
            color: #555;
            cursor: pointer;
            background: white;
        }

        /* Estilos Novos para Preços e Promoção */
        .price-container {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .old-price {
            text-decoration: line-through;
            color: #999;
            font-size: 0.85em;
        }

        .promo-price {
            color: #e74c3c;
            font-weight: bold;
            font-size: 1.1em;
        }

        .std-price {
            color: #2c3e50;
            font-weight: bold;
        }

        .custo-price {
            font-size: 0.75em;
            color: #aaa;
            margin-top: 2px;
        }

        .badge-promo {
            background-color: #e74c3c;
            color: white;
            font-size: 0.7em;
            padding: 2px 6px;
            border-radius: 4px;
            vertical-align: middle;
            margin-left: 5px;
        }

        /* --- ESTILOS DA MINI-GALERIA NA TABELA --- */
        .mini-galeria {
            display: flex;
            gap: 4px;
            margin-top: 8px;
            flex-wrap: wrap;
            justify-content: center;
            max-width: 80px;
            /* Mantém na mesma largura da imagem principal */
        }

        .mini-thumb {
            width: 22px;
            height: 22px;
            border-radius: 4px;
            object-fit: cover;
            border: 1px solid #ccc;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .mini-thumb:hover {
            transform: scale(1.5);
            z-index: 10;
        }
    </style>
</head>

<body class="bg-admin">
    <?php require_once __DIR__ . '/../layout/header_admin.php'; ?>

    <div class="admin-container">

        <div class="page-header">
            <div class="header-info">
                <h2><i class="fas fa-boxes"></i> Estoque de Produtos</h2>
                <p>Gerencie seu catálogo, preços e quantidade em estoque.</p>
            </div>

            <div class="header-actions">
                <div class="search-group">
                    <select id="filtroCategoria" class="select-filtro" onchange="filtrarTabela()">
                        <option value="">Todas as Categorias</option>
                        <?php foreach ($categorias as $c): ?>
                            <option value="<?= htmlspecialchars($c['nome']) ?>">
                                <?= htmlspecialchars($c['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="filtroProduto" onkeyup="filtrarTabela()" placeholder="Buscar produto...">
                    </div>
                </div>

                <a href="<?= BASE_URL ?>produto/novo" class="btn-novo">
                    <i class="fas fa-plus"></i> <span>Novo Produto</span>
                </a>
            </div>
        </div>

        <div class="painel-tabela">
            <table class="tabela-produtos" id="tabelaProdutos">
                <thead>
                    <tr>
                        <th class="col-img text-center">Imagens</th>
                        <th>Produto</th>
                        <th>Categoria</th>
                        <th>Cód. Barras</th>
                        <th class="text-right">Preço Venda</th>
                        <th class="text-center">Estoque</th>
                        <th class="col-actions">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($produtos)): ?>
                        <tr>
                            <td colspan="7" class="empty-state">
                                <div class="empty-content">
                                    <i class="fas fa-box-open"></i>
                                    <p>Nenhum produto cadastrado.</p>
                                    <a href="<?= BASE_URL ?>produto/novo">Cadastrar o primeiro</a>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($produtos as $p): ?>
                            <tr class="linha-produto">
                                <td data-label="Imagens" class="col-img">

                                    <div class="img-wrapper" style="margin: 0 auto;">
                                        <?php if (!empty($p['imagem']) && file_exists(__DIR__ . '/../../public/uploads/' . $p['imagem'])): ?>
                                            <img src="<?= BASE_URL ?>public/uploads/<?= $p['imagem'] ?>"
                                                alt="<?= htmlspecialchars($p['nome']) ?>">
                                        <?php else: ?>
                                            <div class="no-image"><i class="fas fa-camera"></i></div>
                                        <?php endif; ?>
                                    </div>

                                    <?php
                                    // Busca as imagens extras deste produto específico
                                    $imagensExtras = Produto::buscarImagensAdicionais($p['id']);
                                    if (!empty($imagensExtras)):
                                        ?>
                                        <div class="mini-galeria" title="<?= count($imagensExtras) ?> foto(s) extra(s)">
                                            <?php foreach ($imagensExtras as $imgExt): ?>
                                                <img src="<?= BASE_URL ?>public/uploads/<?= htmlspecialchars($imgExt['imagem']) ?>"
                                                    class="mini-thumb">
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                </td>

                                <td data-label="Produto">
                                    <strong class="product-name"><?= htmlspecialchars($p['nome']) ?></strong>
                                    <?php if ($p['promocao'] == 1): ?>
                                        <span class="badge-promo">OFERTA</span>
                                    <?php endif; ?>
                                </td>

                                <td data-label="Categoria" class="col-categoria">
                                    <?php if (!empty($p['categoria_nome'])): ?>
                                        <span class="badge badge-info"
                                            style="background:#e3f2fd; color:#0d47a1; padding:4px 8px; border-radius:4px; font-size:0.85em;">
                                            <?= htmlspecialchars($p['categoria_nome']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color:#999;">—</span>
                                    <?php endif; ?>
                                </td>

                                <td data-label="Cód. Barras" class="text-muted">
                                    <i class="fas fa-barcode"></i> <?= htmlspecialchars($p['codigo_barras']) ?>
                                </td>

                                <td data-label="Preço" class="text-right">
                                    <div class="price-container">
                                        <?php if ($p['promocao'] == 1 && $p['preco_promocao'] > 0): ?>
                                            <span class="old-price">R$ <?= number_format($p['preco'], 2, ',', '.') ?></span>
                                            <span class="promo-price">R$
                                                <?= number_format($p['preco_promocao'], 2, ',', '.') ?></span>
                                        <?php else: ?>
                                            <span class="std-price">R$ <?= number_format($p['preco'], 2, ',', '.') ?></span>
                                        <?php endif; ?>

                                        <span class="custo-price" title="Preço de Custo">
                                            (Custo: <?= number_format($p['preco_custo'], 2, ',', '.') ?>)
                                        </span>
                                    </div>
                                </td>

                                <td data-label="Estoque" class="text-center">
                                    <?php if ($p['estoque'] <= 5): ?>
                                        <span class="badge badge-low">
                                            <?= $p['estoque'] ?> un (Baixo)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-ok">
                                            <?= $p['estoque'] ?> un
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td data-label="Ações" class="col-actions">
                                    <div class="action-buttons">
                                        <a href="<?= BASE_URL ?>produto/editar/<?= $p['id'] ?>" class="btn-icon btn-edit"
                                            title="Editar">
                                            <i class="fas fa-pen"></i>
                                        </a>

                                        <button type="button" class="btn-icon btn-delete" title="Excluir"
                                            onclick="abrirModalExclusao(<?= $p['id'] ?>, '<?= addslashes($p['nome']) ?>')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <div id="noResults" class="no-results" style="display: none;">
                <i class="fas fa-search"></i>
                <p>Nenhum produto encontrado com estes filtros.</p>
            </div>
        </div>
    </div>

    <div id="modalExcluir" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>Confirmar Exclusão</h3>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o produto:</p>
                <strong id="nomeProdutoModal" class="highlight-name"></strong>
                <p class="warning-text">Essa ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button onclick="fecharModal()" class="btn-cancelar">Cancelar</button>
                <a id="btnConfirmarExclusao" href="#" class="btn-confirmar">Sim, Excluir</a>
            </div>
        </div>
    </div>

    <script>
        // --- Lógica do Modal ---
        const modal = document.getElementById('modalExcluir');
        const nomeProdutoSpan = document.getElementById('nomeProdutoModal');
        const btnConfirmar = document.getElementById('btnConfirmarExclusao');

        function abrirModalExclusao(id, nome) {
            nomeProdutoSpan.textContent = nome;
            btnConfirmar.href = `<?= BASE_URL ?>produto/excluir/${id}`;
            modal.classList.add('active');
        }

        function fecharModal() {
            modal.classList.remove('active');
        }

        modal.addEventListener('click', (e) => {
            if (e.target === modal) fecharModal();
        });

        // --- Lógica de Filtro Avançado ---
        function filtrarTabela() {
            const inputTexto = document.getElementById('filtroProduto');
            const selectCat = document.getElementById('filtroCategoria');
            const textoBusca = inputTexto.value.toLowerCase();
            const catSelecionada = selectCat.value.toLowerCase();
            const rows = document.querySelectorAll('.linha-produto');
            let hasResult = false;

            rows.forEach(row => {
                const rowText = row.innerText.toLowerCase();
                const catText = row.querySelector('.col-categoria').innerText.toLowerCase();
                const matchTexto = rowText.includes(textoBusca);
                const matchCat = catSelecionada === "" || catText.includes(catSelecionada);

                if (matchTexto && matchCat) {
                    row.style.display = "";
                    hasResult = true;
                } else {
                    row.style.display = "none";
                }
            });

            const noResults = document.getElementById('noResults');
            const thead = document.querySelector('thead');

            if (hasResult) {
                noResults.style.display = "none";
                thead.style.display = "";
            } else {
                noResults.style.display = "block";
                thead.style.display = "none";
            }
        }
    </script>

</body>

</html>