<?php require __DIR__ . '/../layout/header_admin.php'; ?>

<style>
    /* =========================================
       LAYOUT GERAL
       ========================================= */
    .admin-container {
        max-width: 1200px;
        margin: 30px auto;
        padding: 0 20px;
        font-family: 'Segoe UI', sans-serif;
    }

    .page-header {
        margin-bottom: 30px;
        border-bottom: 1px solid #eee;
        padding-bottom: 20px;
    }

    .page-header h2 {
        margin: 0;
        color: #2c3e50;
        font-size: 1.8rem;
    }

    .page-header p {
        margin: 5px 0 0;
        color: #7f8c8d;
    }

    /* GRID LAYOUT: Lado a Lado */
    .config-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        align-items: start;
    }

    /* =========================================
       COLUNA 1: FORMULÁRIO
       ========================================= */
    .card-form {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label.main-label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #34495e;
    }

    .form-control {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 1rem;
        transition: border-color 0.3s;
    }

    .form-control:focus {
        border-color: #3498db;
        outline: none;
    }

    /* =========================================
       NOVO: CAIXA DE UPLOAD DE LOGO
       ========================================= */
    .upload-box {
        display: block;
        width: 100%;
        border: 2px dashed #bdc3c7;
        border-radius: 12px;
        padding: 30px 20px;
        text-align: center;
        background-color: #f8f9fa;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }

    .upload-box:hover {
        border-color: #3498db;
        background-color: #eaf2f8;
    }

    /* Esconde o input original feio */
    .upload-box input[type="file"] {
        display: none;
    }

    .upload-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }

    .upload-content i {
        font-size: 2.5rem;
        color: #3498db;
        margin-bottom: 5px;
    }

    .upload-content span {
        font-weight: 600;
        font-size: 1rem;
        color: #2c3e50;
    }

    .upload-content small {
        font-size: 0.85rem;
        color: #7f8c8d;
    }

    /* O preview da imagem dentro da caixa */
    .upload-box img {
        max-height: 100px;
        max-width: 100%;
        object-fit: contain;
        margin: 0 auto;
        border-radius: 8px;
    }

    /* Input de Cor Customizado */
    .color-picker-wrapper {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background: #f9f9f9;
        cursor: pointer;
    }

    input[type="color"] {
        -webkit-appearance: none;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
        cursor: pointer;
        padding: 0;
        background: none;
    }

    input[type="color"]::-webkit-color-swatch-wrapper {
        padding: 0;
    }

    input[type="color"]::-webkit-color-swatch {
        border: none;
        border-radius: 50%;
        border: 2px solid #ddd;
    }

    .btn-salvar {
        width: 100%;
        padding: 15px;
        background: #27ae60;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.3s;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
    }

    .btn-salvar:hover {
        background: #219150;
        transform: translateY(-2px);
    }

    /* =========================================
       COLUNA 2: PREVIEW (MOCKUP)
       ========================================= */
    .preview-container {
        position: sticky;
        top: 20px;
    }

    .preview-title {
        text-align: center;
        color: #95a5a6;
        font-size: 0.9rem;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .mock-browser {
        border: 1px solid #ddd;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        background: #fff;
    }

    .mock-toolbar {
        background: #e0e0e0;
        padding: 10px 15px;
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #ccc;
    }

    .dot.red {
        background: #ff5f56;
    }

    .dot.yellow {
        background: #ffbd2e;
    }

    .dot.green {
        background: #27c93f;
    }

    .fake-url {
        flex: 1;
        background: white;
        height: 20px;
        border-radius: 4px;
        margin-left: 10px;
        font-size: 0.7rem;
        color: #aaa;
        display: flex;
        align-items: center;
        padding-left: 10px;
    }

    .preview-site {
        min-height: 400px;
        transition: background-color 0.3s;
        display: flex;
        flex-direction: column;
    }

    .preview-header {
        padding: 15px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background-color 0.3s;
    }

    .preview-logo {
        font-weight: bold;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .preview-nav span {
        display: inline-block;
        width: 40px;
        height: 8px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 4px;
        margin-left: 5px;
    }

    .preview-content {
        padding: 20px;
    }

    /* --- ESTILOS DO CARROSSEL (NOVO) --- */
    .preview-carousel {
        width: 100%;
        margin: 0 auto 20px auto;
        overflow: hidden;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        background: #e0e0e0;
        position: relative;
    }

    .carousel-track {
        display: flex;
        width: 100%;
        transition: transform 0.5s ease-in-out;
    }

    .carousel-slide {
        min-width: 100%;
        height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .carousel-slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .carousel-placeholder {
        width: 100%;
        height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #7f8c8d;
        font-size: 0.9rem;
        background: #ecf0f1;
    }

    .preview-hero {
        text-align: center;
        margin-bottom: 20px;
    }

    .preview-hero h3 {
        color: #333;
        margin: 0 0 5px 0;
    }

    .preview-hero p {
        color: #777;
        font-size: 0.8rem;
        margin: 0;
    }

    .preview-card {
        background: white;
        border-radius: 8px;
        padding: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        width: 140px;
        margin: 0 auto;
        text-align: center;
    }

    .p-img {
        width: 100%;
        height: 80px;
        background: #eee;
        border-radius: 4px;
        margin-bottom: 8px;
    }

    .p-line {
        height: 6px;
        background: #eee;
        border-radius: 3px;
        margin: 4px auto;
        width: 80%;
    }

    .p-btn {
        margin-top: 10px;
        background: #25d366;
        color: white;
        font-size: 0.7rem;
        padding: 5px;
        border-radius: 4px;
        display: block;
    }

    /* RESPONSIVIDADE */
    @media (max-width: 900px) {
        .config-grid {
            grid-template-columns: 1fr;
        }

        .preview-container {
            display: none;
        }
    }
</style>

<div class="admin-container">

    <div class="page-header">
        <h2><i class="fas fa-paint-brush"></i> Personalizar Vitrine</h2>
        <p>Altere as cores e informações da loja e veja o resultado em tempo real.</p>
    </div>

    <form action="index.php?rota=configuracao/salvar" method="POST" class="config-grid" enctype="multipart/form-data">
        <div class="card-form">

            <div class="form-group">
                <label class="main-label">Nome da Loja (Texto)</label>
                <input type="text" name="nome" id="inputNome" class="form-control" value="<?= $config['nome_loja'] ?>"
                    required oninput="atualizarPreview()">
            </div>

            <div class="form-group">
                <label class="main-label">Logo da Loja (Imagem)</label>

                <input type="hidden" name="logo_atual" value="<?= $config['logo_loja'] ?? '' ?>">

                <label class="upload-box" for="inputLogo">
                    <input type="file" name="logo" id="inputLogo" accept="image/*" onchange="previewImagem(this)">

                    <div class="upload-content" id="uploadContent"
                        style="<?= !empty($config['logo_loja']) ? 'display: none;' : '' ?>">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>Clique para selecionar a logo</span>
                        <small>Formatos: PNG transparente recomendado</small>
                    </div>

                    <img id="imgPreview" src="<?= $config['logo_loja'] ?? '' ?>"
                        style="<?= empty($config['logo_loja']) ? 'display: none;' : 'display: block;' ?>">
                </label>
            </div>

            <div class="form-group">
                <label class="main-label">WhatsApp (Somente números)</label>
                <input type="text" name="whatsapp" class="form-control" value="<?= $config['whatsapp'] ?>"
                    placeholder="5511999999999" required>
                <small style="color: #999;">Ex: 5513999999999. O cliente será redirecionado para este número.</small>
            </div>

            <div class="form-group">
                <label class="main-label">Cor do Cabeçalho (Header)</label>
                <label class="color-picker-wrapper">
                    <input type="color" name="cor_header" id="inputCorHeader" value="<?= $config['cor_header'] ?>"
                        oninput="atualizarPreview()">
                    <span style="flex:1; color: #555;">Clique para escolher</span>
                    <i class="fas fa-eye-dropper" style="color:#aaa"></i>
                </label>
            </div>

            <div class="form-group">
                <label class="main-label">Cor de Fundo da Página</label>
                <label class="color-picker-wrapper">
                    <input type="color" name="cor_fundo" id="inputCorFundo" value="<?= $config['cor_fundo'] ?>"
                        oninput="atualizarPreview()">
                    <span style="flex:1; color: #555;">Clique para escolher</span>
                    <i class="fas fa-eye-dropper" style="color:#aaa"></i>
                </label>
            </div>

            <hr style="border: 1px solid #eee; margin: 30px 0;">
            <h3 style="color: #2c3e50; margin-bottom: 15px;"><i class="fas fa-images"></i> Banners da Vitrine</h3>

            <div class="form-group">
                <label class="main-label upload-box">Adicionar Novos Banners
                    <input type="file" name="novos_banners[]" class="form-control" accept="image/*" multiple>
                    <small style="color: #999;">Dica: Você pode selecionar várias imagens de uma vez segurando a tecla
                        CTRL
                        no computador.</small>
                </label>
            </div>

            <?php
            // Decodifica a lista de banners do banco de dados
            $bannersSalvos = json_decode($config['banners'] ?? '[]', true);
            if (!empty($bannersSalvos)):
                ?>
                <div class="form-group">
                    <label class="main-label">Banners Atuais <small style="color: #e74c3c;">(Marque a caixinha para
                            excluir)</small></label>
                    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                        <?php foreach ($bannersSalvos as $b): ?>
                            <label
                                style="border: 2px solid #ddd; padding: 5px; border-radius: 8px; text-align: center; cursor: pointer; transition: 0.3s;"
                                onchange="this.style.borderColor = this.querySelector('input').checked ? '#e74c3c' : '#ddd'">
                                <img src="<?= BASE_URL ?>public/uploads/<?= htmlspecialchars($b) ?>"
                                    style="height: 60px; max-width: 120px; object-fit: cover; display: block; margin-bottom: 8px; border-radius: 4px;">
                                <input type="checkbox" name="remover_banners[]" value="<?= htmlspecialchars($b) ?>"> Excluir
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            <hr style="border: 1px solid #eee; margin: 30px 0 20px 0;">

            <button type="submit" class="btn-salvar">
                <i class="fas fa-check-circle"></i> Salvar Alterações
            </button>
        </div>

        <div class="preview-container">
            <div class="preview-title"><i class="fas fa-eye"></i> Pré-visualização ao vivo</div>

            <div class="mock-browser">
                <div class="mock-toolbar">
                    <div class="dot red"></div>
                    <div class="dot yellow"></div>
                    <div class="dot green"></div>
                    <div class="fake-url">seu-site.com.br/vitrine</div>
                </div>

                <div id="previewSite" class="preview-site" style="background-color: <?= $config['cor_fundo'] ?>;">

                    <div id="previewHeader" class="preview-header"
                        style="background-color: <?= $config['cor_header'] ?>;">

                        <div class="preview-logo" id="areaLogoPreview">
                            <?php if (!empty($config['logo_loja'])): ?>
                                <img src="<?= $config['logo_loja'] ?>" style="max-height: 30px; margin-right:5px;">

                            <?php else: ?>
                                <i class="fas fa-bicycle" style="margin-right:5px;"></i>
                                <span id="txtNomeLoja"><?= $config['nome_loja'] ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="preview-nav">
                            <span></span><span></span><span></span>
                        </div>
                    </div>

                    <div class="preview-content">

                        <div class="preview-carousel">
                            <?php if (!empty($bannersSalvos)): ?>
                                <div class="carousel-track" id="carouselTrack">
                                    <?php foreach ($bannersSalvos as $b): ?>
                                        <div class="carousel-slide">
                                            <img src="<?= BASE_URL ?>public/uploads/<?= htmlspecialchars($b) ?>">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="carousel-placeholder">
                                    <i class="fas fa-image" style="margin-right: 8px;"></i> Nenhum banner
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="preview-hero">
                            <h3>Nossos Produtos</h3>
                            <p>Confira as ofertas disponíveis</p>
                        </div>

                        <div class="preview-card">
                            <div class="p-img"></div>
                            <div class="p-line" style="width: 90%; background: #333; height: 8px;"></div>
                            <div class="p-line" style="width: 50%;"></div>
                            <div class="p-line" style="width: 40%; background: #27ae60; height: 8px;"></div>
                            <span class="p-btn"><i class="fab fa-whatsapp"></i> Tenho Interesse</span>
                        </div>
                    </div>

                </div>
            </div>

            <p style="text-align: center; font-size: 0.8rem; color: #aaa; margin-top: 10px;">
                * Imagem ilustrativa. O layout real pode variar conforme o dispositivo.
            </p>
        </div>

    </form>
</div>

<script>
    // 1. Atualiza Cores e Texto
    function atualizarPreview() {
        const nome = document.getElementById('inputNome').value;
        const corHeader = document.getElementById('inputCorHeader').value;
        const corFundo = document.getElementById('inputCorFundo').value;

        const txtNome = document.getElementById('txtNomeLoja');
        if (txtNome) txtNome.innerText = nome;

        document.getElementById('previewHeader').style.backgroundColor = corHeader;
        document.getElementById('previewSite').style.backgroundColor = corFundo;
    }

    // 2. Atualiza a Imagem (Upload Box e Mockup)
    function previewImagem(input) {
        var previewPequeno = document.getElementById('imgPreview');
        var uploadContent = document.getElementById('uploadContent');
        var areaLogoSite = document.getElementById('areaLogoPreview');

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                // Atualiza a caixa de upload principal
                previewPequeno.src = e.target.result;
                previewPequeno.style.display = 'block';
                uploadContent.style.display = 'none';

                // Atualiza o Mockup do Site do lado direito
                areaLogoSite.innerHTML = '<img src="' + e.target.result + '" style="max-height: 30px;">';
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    // 3. Lógica do Carrossel Animado
    function iniciarCarrossel() {
        const track = document.getElementById('carouselTrack');
        if (!track) return; // Se não tiver track (ex: sem banners), para aqui

        const slides = track.querySelectorAll('.carousel-slide');
        const totalSlides = slides.length;

        if (totalSlides <= 1) return; // Se tiver só 1, não precisa girar

        let currentIndex = 0;

        setInterval(() => {
            currentIndex++;
            if (currentIndex >= totalSlides) {
                currentIndex = 0; // Volta pro começo
            }
            // Move a 'esteira' para a esquerda de acordo com o index
            track.style.transform = `translateX(-${currentIndex * 100}%)`;
        }, 3000); // Gira a cada 3 segundos
    }

    // Inicia funções ao carregar a página
    document.addEventListener("DOMContentLoaded", () => {
        atualizarPreview();
        iniciarCarrossel();
    });
</script>