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
    <p>Nós respiramos tecnologia. Descubra como transformamos a paixão por hardware em uma das lojas mais inovadoras do
        mercado.</p>
</div>

<div class="sobre-container">

    <section class="historia-section">

        <div class="historia-texto">
            <h2><i class="fas fa-microchip"></i> Nossa História</h2>
            <p>
                Tudo começou de forma muito simples: em uma pequena bancada, cercada por cabos, smartphones, peças de
                computador e muita curiosidade. Os fundadores da <strong>KaByte</strong> eram apaixonados por explorar o
                universo digital, passando horas testando novidades e descobrindo como a tecnologia poderia facilitar a
                rotina das pessoas.
            </p>
            <p>
                Com o tempo, percebemos que o mercado carecia de algo fundamental: um atendimento que não apenas
                vendesse caixas, mas que entendesse de verdade a necessidade de cada cliente. Fosse um gamer em busca do
                desempenho perfeito, um profissional precisando do celular ideal para o trabalho, ou alguém procurando
                aquele acessório essencial para o dia a dia.
            </p>
            <p>
                O nome <strong>KaByte</strong> nasceu da nossa filosofia: assim como o <em>Kilobyte</em> é a base da
                informação digital, acreditamos que grandes soluções começam nos pequenos detalhes. Hoje, evoluímos para
                um e-commerce completo, entregando desde notebooks e smartphones até uma linha vasta de periféricos e
                acessórios para todo o país. Crescemos, mas nunca perdemos a essência daquela primeira bancada:
                respeito, sinceridade e amor pela tecnologia.
            </p>
        </div>

        <div class="historia-imagem">
            <img src="<?= BASE_URL ?>public/uploads/sobre-loja.jpg"
                onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1587202372634-32705e3bf49c?auto=format&fit=crop&q=80&w=800';"
                alt="Bancada de Informática KaByte">
        </div>
    </section>

    <section class="mvv-grid">
        <div class="mvv-card missao">
            <i class="fas fa-rocket"></i>
            <h3>Nossa Missão</h3>
            <p>Democratizar o acesso à tecnologia de alta performance, oferecendo produtos de excelência com preço justo
                e um atendimento técnico especializado que ajude cada cliente a realizar seus objetivos digitais.</p>
        </div>

        <div class="mvv-card visao">
            <i class="fas fa-eye"></i>
            <h3>Nossa Visão</h3>
            <p>Ser reconhecida como a principal referência nacional no varejo de informática e hardware, inovando
                constantemente na forma como as pessoas compram e interagem com a tecnologia.</p>
        </div>

        <div class="mvv-card valores">
            <i class="fas fa-heart"></i>
            <h3>Nossos Valores</h3>
            <p>
                <strong>Transparência:</strong> Falamos a verdade sobre o que você realmente precisa.<br>
                <strong>Inovação:</strong> Sempre atualizados com o que há de mais novo no mercado.<br>
                <strong>Foco no Cliente:</strong> O seu setup dos sonhos é o nosso maior objetivo.
            </p>
        </div>
    </section>

</div>

<?php require __DIR__ . '/layout/footer_public.php'; ?>