<?php require __DIR__ . '/layout/header_public.php'; ?>

<style>
    /* =========================================
       ESTILOS DA PÁGINA SOBRE
       ========================================= */
    .sobre-hero {
        background: linear-gradient(to right, var(--cor-primaria-hover), var(--cor-primaria));
        color: var(--cor-branco);
        padding: 60px 20px;
        text-align: center;
        border-bottom: 5px solid var(--cor-oferta);
    }

    .sobre-hero h1 {
        font-size: 2.8rem;
        margin-bottom: 15px;
        font-weight: 800;
        letter-spacing: 1px;
    }

    .sobre-hero p {
        font-size: 1.2rem;
        max-width: 700px;
        margin: 0 auto;
        opacity: 0.9;
    }

    .sobre-container {
        max-width: 1000px;
        margin: 50px auto;
        padding: 0 20px;
    }

    /* Layout da História */
    .historia-section {
        display: flex;
        align-items: center;
        gap: 40px;
        margin-bottom: 60px;
        background: var(--cor-fundo-card);
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .historia-texto {
        flex: 1;
    }

    .historia-texto h2 {
        color: var(--cor-primaria);
        font-size: 2rem;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .historia-texto p {
        color: var(--cor-texto-secundario);
        line-height: 1.8;
        font-size: 1.05rem;
        margin-bottom: 15px;
        text-align: justify;
    }

    .historia-imagem {
        flex: 1;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .historia-imagem img {
        width: 100%;
        height: auto;
        display: block;
        transition: transform 0.4s ease;
    }

    .historia-imagem:hover img {
        transform: scale(1.05);
    }

    /* Missão, Visão e Valores */
    .mvv-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-bottom: 60px;
    }

    .mvv-card {
        background: var(--cor-fundo-card);
        padding: 30px;
        border-radius: 16px;
        text-align: center;
        border-top: 4px solid var(--cor-info);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.04);
        transition: transform 0.3s ease;
    }

    .mvv-card:hover {
        transform: translateY(-10px);
    }

    .mvv-card.missao {
        border-top-color: var(--cor-primaria);
    }

    .mvv-card.visao {
        border-top-color: var(--cor-info);
    }

    .mvv-card.valores {
        border-top-color: var(--cor-oferta);
    }

    .mvv-card i {
        font-size: 3rem;
        margin-bottom: 20px;
    }

    .mvv-card.missao i {
        color: var(--cor-primaria);
    }

    .mvv-card.visao i {
        color: var(--cor-info);
    }

    .mvv-card.valores i {
        color: var(--cor-oferta);
    }

    .mvv-card h3 {
        color: var(--cor-texto-principal);
        font-size: 1.4rem;
        margin-bottom: 15px;
    }

    .mvv-card p {
        color: var(--cor-texto-secundario);
        line-height: 1.6;
    }

    /* Responsividade */
    @media (max-width: 768px) {
        .historia-section {
            flex-direction: column;
            padding: 25px;
        }

        .sobre-hero h1 {
            font-size: 2.2rem;
        }
    }
</style>

<div class="sobre-hero">
    <h1>Bem-vindo à KaByte</h1>
    <p>Nós respiramos conectividade. Descubra como a nossa paixão por periféricos e acessórios se transformou na loja ideal para completar o seu universo digital.</p>
</div>

<div class="sobre-container">

    <section class="historia-section">

        <div class="historia-texto">
            <h2><i class="fas fa-keyboard"></i> Nossa História</h2>
            <p>
                Tudo começou de forma muito simples: em uma pequena bancada, cercada por cabos, mouses, fones de ouvido e muita curiosidade. Os fundadores da <strong>KaByte</strong> sempre foram apaixonados por explorar a tecnologia, entendendo rapidamente que o dispositivo principal só entrega o seu melhor se estiver acompanhado do acessório certo.
            </p>
            <p>
                Com o tempo, percebemos que o mercado carecia de algo fundamental: um atendimento que ajudasse o cliente a extrair o máximo dos seus aparelhos. Fosse um gamer em busca daquele teclado mecânico para a jogada perfeita, um profissional precisando de ergonomia para o home office, ou alguém procurando capas, cabos e carregadores confiáveis para o dia a dia.
            </p>
            <p>
                O nome <strong>KaByte</strong> nasceu da nossa filosofia: assim como o <em>Kilobyte</em> é a base da informação digital, acreditamos que as grandes experiências começam nos pequenos detalhes. Hoje, somos especialistas dedicados a entregar uma linha vasta e selecionada de periféricos e acessórios inovadores. Crescemos, mas nunca perdemos a essência daquela primeira bancada: respeito, sinceridade e amor por facilitar a sua vida conectada.
            </p>
        </div>

        <div class="historia-imagem">
            <!-- Dica: Use uma imagem que mostre teclados, mouses, fones, ou uma mesa gamer/office bem equipada -->
            <img src="<?= BASE_URL ?>public/uploads/sobre-loja.jpg"
                onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1593640408182-31c70c8268f5?auto=format&fit=crop&q=80&w=800';"
                alt="Setup com acessórios e periféricos KaByte">
        </div>
    </section>

    <section class="mvv-grid">
        <div class="mvv-card missao">
            <i class="fas fa-plug"></i>
            <h3>Nossa Missão</h3>
            <p>Democratizar o acesso a acessórios e periféricos de alta qualidade, oferecendo produtos com preço justo e um atendimento especializado que ajude cada cliente a potencializar seus equipamentos.</p>
        </div>

        <div class="mvv-card visao">
            <i class="fas fa-eye"></i>
            <h3>Nossa Visão</h3>
            <p>Ser reconhecida como a principal referência nacional no varejo de acessórios tecnológicos, inovando constantemente na forma como as pessoas equipam e personalizam sua rotina digital.</p>
        </div>

        <div class="mvv-card valores">
            <i class="fas fa-heart"></i>
            <h3>Nossos Valores</h3>
            <p>
                <strong>Transparência:</strong> Falamos a verdade sobre qual acessório realmente atende à sua necessidade.<br>
                <strong>Inovação:</strong> Sempre atualizados com as tendências em conectividade e estilo.<br>
                <strong>Foco no Cliente:</strong> Deixar o seu setup ou smartphone perfeitamente equipado é o nosso objetivo.
            </p>
        </div>
    </section>

</div>

<?php require __DIR__ . '/layout/footer_public.php'; ?>