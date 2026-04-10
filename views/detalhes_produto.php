<?php require __DIR__ . '/layout/header_public.php'; ?>

<link rel="stylesheet" href="<?= BASE_URL ?>public/css/vitrine.css">

<style>
    /* --- ETIQUETA DE OFERTA DOS CARDS RELACIONADOS --- */
    .badge-oferta {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: #e74c3c;
        color: #ffffff;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        z-index: 5;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        pointer-events: none;
    }

    /* --- GALERIA DE IMAGENS --- */
    .galeria-wrapper {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .prod-thumbnails {
        display: flex;
        gap: 12px;
        overflow-x: auto;
        padding-bottom: 5px;
    }

    /* Esconde barra de rolagem chata nos thumbnails */
    .prod-thumbnails::-webkit-scrollbar {
        height: 6px;
    }

    .prod-thumbnails::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 10px;
    }

    .thumb-item {
        width: 80px;
        height: 80px;
        border: 2px solid var(--cor-borda);
        border-radius: 10px;
        padding: 5px;
        background: var(--cor-branco);
        cursor: pointer;
        object-fit: contain;
        transition: all 0.3s;
    }

    .thumb-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .thumb-item.active {
        border-color: var(--cor-primaria);
        box-shadow: 0 0 0 1px var(--cor-primaria);
    }

    /* =========================================
       LAYOUT DETALHES DO PRODUTO
       ========================================= */
    .detalhe-wrapper {
        background-color: var(--cor-fundo-area-img);
        padding-top: 30px;
        padding-bottom: 60px;
    }

    .detalhe-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    /* Botão Voltar */
    .btn-voltar {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--cor-texto-secundario);
        text-decoration: none;
        font-weight: 600;
        margin-bottom: 20px;
        transition: color 0.3s;
    }

    .btn-voltar:hover {
        color: var(--cor-primaria);
    }

    /* Card Principal do Produto */
    .produto-main {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 50px;
        background: var(--cor-branco);
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06);
    }

    /* Área da Imagem */
    .prod-img-big {
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--cor-branco);
        border: 1px solid var(--cor-borda);
        border-radius: 16px;
        height: 500px;
        position: relative;
        padding: 20px;
    }

    .prod-img-big img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        transition: transform 0.3s ease;
    }

    .prod-img-big:hover img {
        transform: scale(1.05);
    }

    /* Área de Informações */
    .prod-info {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .prod-info h1 {
        font-size: 2.2rem;
        color: var(--cor-texto-principal);
        margin-bottom: 5px;
        line-height: 1.2;
        font-weight: 800;
    }

    .prod-cod {
        color: var(--cor-texto-muted);
        font-size: 0.95rem;
        margin-bottom: 25px;
        font-family: monospace;
        background: var(--cor-fundo-qty);
        padding: 4px 10px;
        border-radius: 6px;
        display: inline-block;
        width: fit-content;
    }

    .prod-price-area {
        margin-bottom: 30px;
        background: var(--cor-fundo-area-img);
        padding: 20px;
        border-radius: 12px;
        border-left: 5px solid var(--cor-primaria);
    }

    .prod-price-area.promo {
        border-left-color: var(--cor-oferta);
    }

    .big-old {
        text-decoration: line-through;
        color: var(--cor-texto-muted);
        font-size: 1.2rem;
        display: block;
        margin-bottom: 5px;
    }

    .big-price {
        font-size: 2.8rem;
        font-weight: 900;
        color: var(--cor-primaria);
        line-height: 1;
    }

    .big-promo {
        color: var(--cor-oferta);
    }

    .prod-desc {
        color: var(--cor-texto-secundario);
        line-height: 1.8;
        margin-bottom: 40px;
        font-size: 1.05rem;
    }

    .prod-desc h3 {
        color: var(--cor-texto-principal);
        margin-bottom: 10px;
        font-size: 1.2rem;
    }

    .actions-area {
        margin-top: auto;
    }

    .estoque-aviso {
        color: var(--cor-primaria);
        font-weight: 700;
        font-size: 0.95rem;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Oculta os estilos do card-produto base para não quebrar o layout da página de detalhes */
    .detalhe-card-invisivel {
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
        padding: 0 !important;
        display: block !important;
        width: auto !important;
    }

    .detalhe-card-invisivel:hover {
        transform: none !important;
        box-shadow: none !important;
    }

    .badge-detalhe {
        position: absolute;
        top: 20px;
        right: 20px;
        background-color: #e74c3c;
        color: #ffffff;
        padding: 8px 20px;
        border-radius: 30px;
        font-size: 1.1rem;
        font-weight: 900;
        text-transform: uppercase;
        z-index: 10;
        box-shadow: 0 4px 12px rgba(231, 76, 60, 0.4);
        letter-spacing: 1px;
    }

    .detalhe-qty-control {
        display: inline-flex;
        align-items: center;
        background: var(--cor-fundo-qty);
        border: 1px solid var(--cor-borda);
        border-radius: 50px;
        padding: 5px;
    }

    .detalhe-btn-qty {
        background: var(--cor-branco);
        color: var(--cor-primaria);
        border: none;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        font-size: 1.5rem;
        font-weight: bold;
        cursor: pointer;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .detalhe-btn-qty:hover {
        background: var(--cor-primaria);
        color: var(--cor-branco);
    }

    .detalhe-qty-display {
        min-width: 60px;
        text-align: center;
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--cor-texto-principal);
    }

    .btn-esgotado-big {
        display: inline-block;
        background: var(--cor-borda);
        color: var(--cor-texto-muted);
        padding: 15px 30px;
        border-radius: 30px;
        font-size: 1.2rem;
        font-weight: bold;
        text-transform: uppercase;
    }

    .section-title {
        margin: 60px 0 30px 0;
        font-size: 1.8rem;
        color: var(--cor-texto-principal);
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .section-title::after {
        content: "";
        flex: 1;
        height: 2px;
        background: var(--cor-borda);
    }

    @media (max-width: 900px) {
        .produto-main {
            grid-template-columns: 1fr;
            gap: 30px;
            padding: 25px;
        }

        .prod-img-big {
            height: 350px;
            border: none;
            padding: 0;
        }

        .prod-info h1 {
            font-size: 1.8rem;
        }

        .big-price {
            font-size: 2.2rem;
        }
    }
</style>

<div class="detalhe-wrapper">
    <div class="detalhe-container">

        <a href="javascript:history.back()" class="btn-voltar">
            <i class="fas fa-arrow-left"></i> Voltar para a loja
        </a>

        <div class="produto-main">

            <div class="galeria-wrapper">
                <div class="prod-img-big">
                    <?php
                    $emOferta = ($produto['promocao'] == 1 && $produto['preco_promocao'] > 0);
                    $precoFinal = $emOferta ? $produto['preco_promocao'] : $produto['preco'];
                    $caminhoPrincipal = (!empty($produto['imagem']) && file_exists(__DIR__ . '/../public/uploads/' . $produto['imagem']))
                        ? BASE_URL . "public/uploads/" . htmlspecialchars($produto['imagem'])
                        : null;
                    ?>

                    <?php if ($emOferta): ?>
                        <div class="badge-detalhe">OFERTA</div>
                    <?php endif; ?>

                    <?php if ($caminhoPrincipal): ?>
                        <img id="imgPrincipal" src="<?= $caminhoPrincipal ?>" alt="<?= htmlspecialchars($produto['nome']) ?>">
                    <?php else: ?>
                        <i id="imgPlaceholder" class="fas fa-image fa-7x" style="color: var(--cor-borda);"></i>
                    <?php endif; ?>
                </div>

                <?php if ($caminhoPrincipal || !empty($imagensAdicionais)): ?>
                    <div class="prod-thumbnails">
                        <?php if ($caminhoPrincipal): ?>
                            <img src="<?= $caminhoPrincipal ?>" class="thumb-item active" onclick="trocarImagem(this.src, this)">
                        <?php endif; ?>

                        <?php if (!empty($imagensAdicionais)): ?>
                            <?php foreach ($imagensAdicionais as $imgExtra): ?>
                                <img src="<?= BASE_URL ?>public/uploads/<?= htmlspecialchars($imgExtra['imagem']) ?>"
                                    class="thumb-item" onclick="trocarImagem(this.src, this)">
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="prod-info">
                <h1><?= htmlspecialchars($produto['nome']) ?></h1>
                <div class="prod-cod">REF: <?= htmlspecialchars($produto['codigo_barras']) ?></div>

                <div class="prod-price-area <?= $emOferta ? 'promo' : '' ?>">
                    <?php if ($emOferta): ?>
                        <span class="big-old">De: R$ <?= number_format($produto['preco'], 2, ',', '.') ?></span>
                        <span class="big-price big-promo">R$
                            <?= number_format($produto['preco_promocao'], 2, ',', '.') ?></span>
                    <?php else: ?>
                        <span class="big-price">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></span>
                    <?php endif; ?>
                </div>

                <div class="prod-desc">
                    <h3>Detalhes do Produto</h3>
                    <p><?= nl2br(htmlspecialchars($produto['descricao'] ?? 'Nenhuma descrição detalhada disponível para este item.')) ?></p>
                </div>

                <div class="actions-area">
                    <?php if ($produto['estoque'] > 0): ?>
                        <div class="estoque-aviso">
                            <i class="fas fa-check-circle"></i> Em estoque (<?= $produto['estoque'] ?> disponíveis)
                        </div>

                        <div class="card-produto detalhe-card-invisivel" data-id="<?= $produto['id'] ?>"
                            data-preco="<?= $precoFinal ?>" data-nome="<?= htmlspecialchars($produto['nome']) ?>">

                            <?php if ($caminhoPrincipal): ?>
                                <img src="<?= $caminhoPrincipal ?>" style="display:none;">
                            <?php endif; ?>

                            <div class="detalhe-qty-control">
                                <button class="detalhe-btn-qty minus" onclick="alterarQtd(<?= $produto['id'] ?>, -1)">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span class="detalhe-qty-display" id="qtd-<?= $produto['id'] ?>">0</span>
                                <button class="detalhe-btn-qty plus" onclick="alterarQtd(<?= $produto['id'] ?>, 1)">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    <?php else: ?>
                        <span class="btn-esgotado-big">Esgotado</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (!empty($relacionados)): ?>
            <h3 class="section-title">Aproveite e veja também</h3>
            <div class="produtos-grid">
                <?php foreach ($relacionados as $p):
                    $ofertaRel = ($p['promocao'] == 1 && $p['preco_promocao'] > 0);
                    $precoRel = $ofertaRel ? $p['preco_promocao'] : $p['preco'];
                ?>
                    <div class="card-produto" data-id="<?= $p['id'] ?>" data-preco="<?= $precoRel ?>"
                        data-nome="<?= htmlspecialchars($p['nome']) ?>">

                        <?php if ($ofertaRel): ?>
                            <div class="badge-oferta">OFERTA</div>
                        <?php endif; ?>

                        <a href="<?= BASE_URL ?>produto/detalhes&id=<?= $p['id'] ?>" class="link-detalhes">
                            <div class="card-img-area">
                                <?php if (!empty($p['imagem']) && file_exists(__DIR__ . '/../public/uploads/' . $p['imagem'])): ?>
                                    <img src="<?= BASE_URL ?>public/uploads/<?= htmlspecialchars($p['imagem']) ?>"
                                        alt="<?= htmlspecialchars($p['nome']) ?>">
                                <?php else: ?>
                                    <i class="fas fa-bicycle fa-3x" style="color: var(--cor-borda);"></i>
                                <?php endif; ?>
                            </div>
                        </a>

                        <div class="card-info">
                            <div>
                                <a href="<?= BASE_URL ?>produto/detalhes&id=<?= $p['id'] ?>" class="link-detalhes">
                                    <div class="card-nome"><?= htmlspecialchars($p['nome']) ?></div>
                                </a>
                                <div class="card-preco">
                                    <?php if ($ofertaRel): ?>
                                        <span class="preco-antigo">R$ <?= number_format($p['preco'], 2, ',', '.') ?></span>
                                        <span class="preco-novo">R$ <?= number_format($p['preco_promocao'], 2, ',', '.') ?></span>
                                    <?php else: ?>
                                        R$ <?= number_format($p['preco'], 2, ',', '.') ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="btn-wrapper">
                                <?php if ($p['estoque'] > 0): ?>
                                    <div class="qty-control">
                                        <button class="btn-qty minus" onclick="alterarQtd(<?= $p['id'] ?>, -1)">-</button>
                                        <span class="qty-display" id="qtd-<?= $p['id'] ?>">0</span>
                                        <button class="btn-qty plus" onclick="alterarQtd(<?= $p['id'] ?>, 1)">+</button>
                                    </div>
                                <?php else: ?>
                                    <span class="btn-esgotado">Indisponível</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<div id="cartBar" class="cart-bar">
    <div>
        <div class="cart-info"><span id="totalItens">0</span> itens</div>
        <div class="cart-total">Total: R$ <span id="totalValor">0,00</span></div>
    </div>
    <button class="btn-checkout" onclick="abrirModalCarrinho()" id="btnFinalizar">
        Ver Carrinho <i class="fas fa-shopping-basket"></i>
    </button>
</div>

<script>
    // --- LÓGICA DO CARRINHO (ATUALIZADA) ---
    let carrinho = {};
    let carrinhoMeta = {}; // NOVO: Guarda nome, foto e preco na memoria!

    document.addEventListener("DOMContentLoaded", () => {
        const salvo = localStorage.getItem('meu_carrinho_pdv');
        const salvoMeta = localStorage.getItem('carrinho_meta');
        if (salvo) {
            try {
                carrinho = JSON.parse(salvo);
                if (salvoMeta) carrinhoMeta = JSON.parse(salvoMeta);

                for (const [id, qtd] of Object.entries(carrinho)) {
                    const elGrid = document.getElementById('qtd-' + id);
                    if (elGrid) elGrid.innerText = qtd;

                    const elCar = document.getElementById('qtd-car-' + id);
                    if (elCar) elCar.innerText = qtd;
                }
                atualizarBarraInferior();
            } catch (e) {
                console.error(e);
            }
        }
    });

    function alterarQtd(id, delta) {
        if (!carrinho[id]) carrinho[id] = 0;

        // NOVO: Se for adição, salva os metadados do produto para usar depois
        if (!carrinhoMeta[id]) {
            const card = document.querySelector(`.card-produto[data-id="${id}"]`);
            if (card) {
                const imgEl = card.querySelector('img');
                carrinhoMeta[id] = {
                    nome: card.getAttribute('data-nome'),
                    preco: parseFloat(card.getAttribute('data-preco')),
                    imagem: imgEl ? imgEl.src : ''
                };
            }
        }

        carrinho[id] += delta;
        if (carrinho[id] < 0) carrinho[id] = 0;

        const elGrid = document.getElementById('qtd-' + id);
        if (elGrid) elGrid.innerText = carrinho[id];

        const elCar = document.getElementById('qtd-car-' + id);
        if (elCar) elCar.innerText = carrinho[id];

        // Limpa o item se chegar a zero
        if (carrinho[id] === 0) {
            delete carrinho[id];
            delete carrinhoMeta[id];
        }

        localStorage.setItem('meu_carrinho_pdv', JSON.stringify(carrinho));
        localStorage.setItem('carrinho_meta', JSON.stringify(carrinhoMeta)); // Salva metadados
        atualizarBarraInferior();
    }

    // NOVO: Agora calcula usando a memória, independente se está na vitrine ou detalhes
    function atualizarBarraInferior() {
        let totalItens = 0;
        let totalValor = 0.0;

        for (const [id, qtd] of Object.entries(carrinho)) {
            if (carrinhoMeta[id]) {
                totalItens += qtd;
                totalValor += (qtd * carrinhoMeta[id].preco);
            }
        }

        document.getElementById('totalItens').innerText = totalItens;
        document.getElementById('totalValor').innerText = totalValor.toLocaleString('pt-BR', {
            minimumFractionDigits: 2
        });

        const headerCount = document.getElementById('headerCartCount');
        if (headerCount) headerCount.innerText = totalItens > 0 ? totalItens : '';

        const bar = document.getElementById('cartBar');
        if (totalItens > 0) bar.classList.add('visible');
        else bar.classList.remove('visible');
    }

    async function irParaCheckout() {
        const btn = document.querySelector('.btn-checkout-modal');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Aguarde...';
        }

        const payload = {
            itens: carrinho
        };

        try {
            const res = await fetch('<?= BASE_URL ?>carrinho/salvar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            const dados = await res.json();

            if (dados.sucesso) {
                window.location.href = '<?= BASE_URL ?>cliente/checkout_carrinho';
            } else {
                alert('Erro: ' + dados.msg);
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-check"></i> Ir para Pagamento';
                }
            }
        } catch (e) {
            alert('Erro de conexão. Verifique sua internet.');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check"></i> Ir para Pagamento';
            }
        }
    }

    // LÓGICA DA GALERIA DE IMAGENS (Exclusiva da detalhes.php, mas pode deixar nos dois)
    function trocarImagem(novaUrl, elementoClicado) {
        const imgGrande = document.getElementById('imgPrincipal');
        if (imgGrande) {
            imgGrande.src = novaUrl;
        }
        const thumbnails = document.querySelectorAll('.thumb-item');
        thumbnails.forEach(thumb => thumb.classList.remove('active'));
        if (elementoClicado) elementoClicado.classList.add('active');
    }
</script>

<?php require __DIR__ . '/layout/footer_public.php'; ?>