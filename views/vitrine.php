<?php require __DIR__ . '/layout/header_public.php'; ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<link rel="stylesheet" href="<?= BASE_URL ?>public/css/vitrine.css">

<style>
    /* =========================================
       NOVO: CARROSSEL DE BANNERS PRINCIPAL
       ========================================= */
    .banner-wrapper {
        width: 100%;
        max-width: 1200px;
        margin: 20px auto 40px auto;
        padding: 0 15px;
    }

    .bannerSwiper {
        width: 100%;
        border-radius: 12px;
        /* Deixa os cantos do banner arredondados */
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .bannerSwiper .swiper-slide img {
        width: 100%;
        max-height: 400px;
        /* Limita a altura para não ficar gigante no PC */
        object-fit: cover;
        /* Faz a imagem preencher o espaço bonito */
        display: block;
        cursor: pointer;
    }

    /* Setas e bolinhas exclusivas do banner (brancas) */
    .banner-next,
    .banner-prev {
        color: white !important;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
    }

    .bannerSwiper .swiper-pagination-bullet {
        background: white;
        opacity: 0.7;
    }

    .bannerSwiper .swiper-pagination-bullet-active {
        background: var(--cor-primaria) !important;
        opacity: 1;
    }

    /* =========================================
       SUBMENU DE CATEGORIAS (PILLS)
       ========================================= */
    .menu-categorias-wrapper {
        background-color: var(--cor-branco);
        border-bottom: 1px solid var(--cor-borda);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
        margin-bottom: 20px;
        margin: 0 auto;
    }

    .menu-categorias {
        max-width: 1360px;
        margin: 0 auto;
        padding: 12px 15px;
        display: flex;
        gap: 10px;
        align-items: center;

        /* overflow-x: auto; */
        /* Permite rolar no celular */
        /* scrollbar-width: none; */
        /* Esconde scrollbar no Firefox */
    }

    .menu-categorias::-webkit-scrollbar {
        display: none;
        /* Esconde scrollbar no Chrome/Safari */
    }

    .cat-pill {
        display: inline-block;
        padding: 8px 18px;
        background-color: var(--cor-fundo-qty);
        color: var(--cor-texto-secundario);
        border-radius: 25px;
        font-size: 0.9rem;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
        /* Não quebra linha */
        transition: all 0.3s ease;
        border: 1px solid var(--cor-borda);
    }

    .cat-pill:hover {
        background-color: var(--cor-primaria-light);
        color: var(--cor-primaria);
        border-color: var(--cor-primaria);
    }

    .cat-pill.active {
        background-color: var(--cor-primaria);
        color: var(--cor-branco);
        border-color: var(--cor-primaria);
    }

    /* =========================================
       RESTANTE DOS ESTILOS LOCAIS
       ========================================= */
    .card-produto {
        position: relative;
        overflow: hidden;
        background-color: var(--cor-fundo-card);
    }

    .badge-oferta {
        position: absolute;
        top: 10px;
        right: 10px;
        background: var(--cor-oferta);
        color: var(--cor-branco);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        z-index: 5;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        pointer-events: none;
    }

    .preco-antigo {
        text-decoration: line-through;
        color: var(--cor-texto-muted);
        font-size: 0.85rem;
        margin-right: 6px;
    }

    .preco-novo {
        color: var(--cor-oferta);
        font-weight: bold;
        font-size: 1.1rem;
    }

    .link-detalhes {
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .btn-limpar-busca {
        display: inline-block;
        margin-top: 10px;
        background: var(--cor-cinza-botao);
        color: var(--cor-branco);
        padding: 6px 18px;
        border-radius: 20px;
        text-decoration: none;
        font-size: 0.9rem;
        transition: background 0.3s;
    }

    .btn-limpar-busca:hover {
        background: var(--cor-cinza-hover);
    }

    .sem-produtos {
        text-align: center;
        grid-column: 1 / -1;
        padding: 60px 20px;
        color: var(--cor-texto-muted);
    }

    .sem-produtos i {
        margin-bottom: 20px;
        color: #bdc3c7;
    }

    /* CARROSSEL OFERTAS */
    .secao-ofertas-full {
        width: 100%;
        background: linear-gradient(to right, var(--cor-oferta-bg), var(--cor-branco));
        border-bottom: 1px solid var(--cor-borda);
        padding: 30px 0;
        margin-bottom: 30px;
        position: relative;
    }

    .titulo-ofertas {
        text-align: center;
        margin-bottom: 25px;
        color: var(--cor-oferta-texto);
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 800;
    }

    .swiper-ofertas {
        width: 100%;
        padding-bottom: 45px !important;
        padding-left: 50px;
        padding-right: 50px;
    }

    .swiper-ofertas .swiper-slide {
        height: auto;
        display: flex;
        justify-content: center;
    }

    .swiper-ofertas .swiper-slide .card-produto {
        width: 100%;
        max-width: 280px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        margin: 0 auto;
    }

    .ofertas-next,
    .ofertas-prev {
        color: var(--cor-oferta-texto) !important;
        background-color: var(--cor-branco);
        width: 45px;
        height: 45px;
        border-radius: 50%;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 20;
        top: 55%;
    }

    .ofertas-next:after,
    .ofertas-prev:after {
        font-size: 1.2rem;
        font-weight: bold;
    }

    .ofertas-prev {
        left: 20px !important;
    }

    .ofertas-next {
        right: 20px !important;
    }

    .swiper-ofertas .swiper-pagination-bullet-active {
        background: var(--cor-oferta) !important;
    }

    @media (max-width: 768px) {
        .swiper-ofertas {
            padding-left: 20px;
            padding-right: 20px;
        }

        .ofertas-next,
        .ofertas-prev {
            display: none;
        }

        .bannerSwiper .swiper-slide img {
            max-height: 200px;
        }

        /* Banner menor no celular */
    }
</style>

<div class="menu-categorias-wrapper">
    <div class="menu-categorias">
        <a href="<?= BASE_URL ?>vitrine"
            class="cat-pill <?= empty($_GET['categoria']) && empty($_GET['busca']) ? 'active' : '' ?>">
            <i class="fas fa-th-large"></i> Todos
        </a>

        <?php if (!empty($categorias)): ?>
            <?php foreach ($categorias as $cat):
                // Verifica se esta é a categoria que está selecionada agora
                $isActive = (isset($_GET['categoria']) && $_GET['categoria'] == $cat['id']) ? 'active' : '';
            ?>
                <a href="<?= BASE_URL ?>vitrine&categoria=<?= $cat['id'] ?>" class="cat-pill <?= $isActive ?>">
                    <?= htmlspecialchars($cat['nome']) ?>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>


<?php
// Lê os banners do banco e transforma de volta em uma lista (array)
$listaBanners = json_decode($config['banners'] ?? '[]', true);
?>

<?php if (empty($_GET['busca']) && !empty($listaBanners)): // Só exibe se NÃO for busca e TIVER banners cadastrados 
?>
    <div class="banner-wrapper">
        <div class="swiper bannerSwiper">
            <div class="swiper-wrapper">

                <?php foreach ($listaBanners as $bannerImagem): ?>
                    <div class="swiper-slide">
                        <img src="<?= BASE_URL ?>public/uploads/<?= htmlspecialchars($bannerImagem) ?>" alt="Banner Promocional KaByte">
                    </div>
                <?php endforeach; ?>

            </div>

            <div class="swiper-pagination banner-pagination"></div>
            <div class="swiper-button-next banner-next"></div>
            <div class="swiper-button-prev banner-prev"></div>
        </div>
    </div>
<?php endif; ?>

<?php
// 1. LÓGICA: FILTRAR PRODUTOS EM OFERTA
$ofertas = [];
if (!empty($produtos)) {
    foreach ($produtos as $p) {
        if ($p['promocao'] == 1 && $p['preco_promocao'] > 0) {
            $ofertas[] = $p;
        }
    }
}
?>

<?php if (!empty($ofertas) && empty($_GET['busca'])): ?>
    <div class="secao-ofertas-full">
        <h2 class="titulo-ofertas"><i class="fas fa-fire"></i> Ofertas Imperdíveis</h2>

        <div class="swiper swiper-ofertas">
            <div class="swiper-wrapper">
                <?php foreach ($ofertas as $p): ?>
                    <div class="swiper-slide">
                        <div class="card-produto" data-id="<?= $p['id'] ?>" data-preco="<?= $p['preco_promocao'] ?>"
                            data-nome="<?= htmlspecialchars($p['nome']) ?>">

                            <div class="badge-oferta">OFERTA</div>

                            <a href="<?= BASE_URL ?>produto/detalhes&id=<?= $p['id'] ?>" class="link-detalhes">
                                <div class="card-img-area">
                                    <?php if (!empty($p['imagem']) && file_exists(__DIR__ . '/../public/uploads/' . $p['imagem'])): ?>
                                        <img src="<?= BASE_URL ?>public/uploads/<?= htmlspecialchars($p['imagem']) ?>"
                                            alt="<?= htmlspecialchars($p['nome']) ?>">
                                    <?php else: ?>
                                        <i class="fas fa-bicycle fa-3x" style="color: #ddd;"></i>
                                    <?php endif; ?>
                                </div>
                            </a>

                            <div class="card-info">
                                <div>
                                    <a href="<?= BASE_URL ?>produto/detalhes&id=<?= $p['id'] ?>" class="link-detalhes">
                                        <div class="card-nome"><?= htmlspecialchars($p['nome']) ?></div>
                                    </a>
                                    <div class="card-preco">
                                        <span class="preco-antigo">R$ <?= number_format($p['preco'], 2, ',', '.') ?></span>
                                        <span class="preco-novo">R$
                                            <?= number_format($p['preco_promocao'], 2, ',', '.') ?></span>
                                    </div>
                                </div>
                                <div class="btn-wrapper">
                                    <?php if ($p['estoque'] > 0): ?>
                                        <div class="qty-control">
                                            <button class="btn-qty minus" onclick="alterarQtd(<?= $p['id'] ?>, -1)">-</button>
                                            <span class="qty-display" id="qtd-car-<?= $p['id'] ?>">0</span>
                                            <button class="btn-qty plus" onclick="alterarQtd(<?= $p['id'] ?>, 1)">+</button>
                                        </div>
                                    <?php else: ?>
                                        <span class="btn-esgotado">Indisponível</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="swiper-pagination ofertas-pagination"></div>
            <div class="swiper-button-next ofertas-next"></div>
            <div class="swiper-button-prev ofertas-prev"></div>
        </div>
    </div>
<?php endif; ?>


<div class="vitrine-container">

    <div class="vitrine-intro">
        <?php if (!empty($_GET['busca'])): ?>
            <h1><i class="fas fa-search"></i> Resultados da busca</h1>
            <p>
                Exibindo produtos para: <strong>"<?= htmlspecialchars($_GET['busca']) ?>"</strong><br>
                <a href="<?= BASE_URL ?>vitrine" class="btn-limpar-busca">
                    <i class="fas fa-times"></i> Limpar Filtro
                </a>
            </p>
        <?php else: ?>
            <h1>Nossa Vitrine</h1>
            <p>Faça suas compras online e receba em casa.</p>
        <?php endif; ?>
    </div>

    <div class="produtos-grid">
        <?php if (empty($produtos)): ?>
            <div class="sem-produtos">
                <i class="fas fa-search fa-4x"></i>
                <h3>Ops! Nenhum produto encontrado.</h3>
                <p>Não encontramos nada com o termo pesquisado.</p>
                <a href="<?= BASE_URL ?>vitrine" class="btn-limpar-busca" style="background: var(--cor-info);">
                    Ver todos os produtos
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($produtos as $p):
                $emOferta = ($p['promocao'] == 1 && $p['preco_promocao'] > 0);
                $precoFinal = $emOferta ? $p['preco_promocao'] : $p['preco'];
            ?>
                <div class="card-produto" data-id="<?= $p['id'] ?>" data-preco="<?= $precoFinal ?>"
                    data-nome="<?= htmlspecialchars($p['nome']) ?>">

                    <?php if ($emOferta): ?>
                        <div class="badge-oferta">OFERTA</div>
                    <?php endif; ?>

                    <a href="<?= BASE_URL ?>produto/detalhes&id=<?= $p['id'] ?>" class="link-detalhes">
                        <div class="card-img-area">
                            <?php if (!empty($p['imagem']) && file_exists(__DIR__ . '/../public/uploads/' . $p['imagem'])): ?>
                                <img src="<?= BASE_URL ?>public/uploads/<?= htmlspecialchars($p['imagem']) ?>"
                                    alt="<?= htmlspecialchars($p['nome']) ?>">
                            <?php else: ?>
                                <i class="fas fa-bicycle fa-3x" style="color: #ddd;"></i>
                            <?php endif; ?>
                        </div>
                    </a>

                    <div class="card-info">
                        <div>
                            <a href="<?= BASE_URL ?>produto/detalhes&id=<?= $p['id'] ?>" class="link-detalhes">
                                <div class="card-nome"><?= htmlspecialchars($p['nome']) ?></div>
                            </a>

                            <div class="card-preco">
                                <?php if ($emOferta): ?>
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
        <?php endif; ?>
    </div>
</div>

<div id="cartBar" class="cart-bar">
    <div>
        <div class="cart-info"><span id="totalItens">0</span> itens</div>
        <div class="cart-total">Total: R$ <span id="totalValor">0,00</span></div>
    </div>
    <button class="btn-checkout" onclick="irParaCheckout()" id="btnFinalizar">
        Ir para Pagamento <i class="fas fa-arrow-right"></i>
    </button>
</div>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
    // --- 1. CARROSSEL DE BANNERS (TOPO) ---
    var bannerSwiper = new Swiper(".bannerSwiper", {
        loop: true,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false
        }, // Passa a cada 4 seg
        pagination: {
            el: ".banner-pagination",
            clickable: true
        },
        navigation: {
            nextEl: ".banner-next",
            prevEl: ".banner-prev"
        },
    });

    // --- 2. CARROSSEL DE OFERTAS ---
    var ofertasSwiper = new Swiper(".swiper-ofertas", {
        slidesPerView: 1,
        spaceBetween: 10,
        loop: true,
        autoplay: {
            delay: 3500,
            disableOnInteraction: false
        },
        pagination: {
            el: ".ofertas-pagination",
            clickable: true
        },
        navigation: {
            nextEl: ".ofertas-next",
            prevEl: ".ofertas-prev"
        },
        breakpoints: {
            640: {
                slidesPerView: 2,
                spaceBetween: 20
            },
            768: {
                slidesPerView: 3,
                spaceBetween: 30
            },
            1024: {
                slidesPerView: 4,
                spaceBetween: 30
            },
        },
    });

    // --- 3. LÓGICA DO CARRINHO ---
    let carrinho = {};

    document.addEventListener("DOMContentLoaded", () => {
        const salvo = localStorage.getItem('meu_carrinho_pdv');
        if (salvo) {
            try {
                carrinho = JSON.parse(salvo);
                atualizarInterfaceCompleta();
            } catch (e) {
                console.error(e);
            }
        }
    });

    function atualizarInterfaceCompleta() {
        for (const [id, qtd] of Object.entries(carrinho)) {
            const elGrid = document.getElementById('qtd-' + id);
            if (elGrid) elGrid.innerText = qtd;

            const elCar = document.getElementById('qtd-car-' + id);
            if (elCar) elCar.innerText = qtd;
        }
        atualizarBarraInferior();
    }

    function alterarQtd(id, delta) {
        if (!carrinho[id]) carrinho[id] = 0;

        carrinho[id] += delta;
        if (carrinho[id] < 0) carrinho[id] = 0;

        const elGrid = document.getElementById('qtd-' + id);
        if (elGrid) elGrid.innerText = carrinho[id];

        const elCar = document.getElementById('qtd-car-' + id);
        if (elCar) elCar.innerText = carrinho[id];

        if (carrinho[id] === 0) delete carrinho[id];

        localStorage.setItem('meu_carrinho_pdv', JSON.stringify(carrinho));
        atualizarBarraInferior();
    }

    function atualizarBarraInferior() {
        let totalItens = 0;
        let totalValor = 0.0;

        const mapPrecos = {};
        document.querySelectorAll('.card-produto').forEach(card => {
            let id = parseInt(card.getAttribute('data-id'));
            let preco = parseFloat(card.getAttribute('data-preco'));
            mapPrecos[id] = preco;
        });

        for (const [id, qtd] of Object.entries(carrinho)) {
            if (mapPrecos[id]) {
                totalItens += qtd;
                totalValor += (qtd * mapPrecos[id]);
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
        const btn = document.getElementById('btnFinalizar');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ...';

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
                btn.disabled = false;
                btn.innerHTML = 'Ir para Pagamento';
            }
        } catch (e) {
            alert('Erro de conexão.');
            btn.disabled = false;
        }
    }
</script>

<?php require __DIR__ . '/layout/footer_public.php'; ?>