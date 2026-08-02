<?php require __DIR__ . '/layout/header_public.php'; ?>

<style>
    /* =========================================
       ESTILOS DA PÁGINA DE POLÍTICAS
       ========================================= */
    .politica-hero {
        background: linear-gradient(to right, var(--cor-primaria-hover), var(--cor-primaria));
        color: var(--cor-branco);
        padding: 60px 20px;
        text-align: center;
        border-bottom: 5px solid var(--cor-oferta);
    }

    .politica-hero h1 {
        font-size: 2.5rem;
        margin-bottom: 15px;
        font-weight: 800;
        letter-spacing: 1px;
    }

    .politica-hero p {
        font-size: 1.1rem;
        max-width: 700px;
        margin: 0 auto;
        opacity: 0.9;
    }

    .politica-container {
        max-width: 900px;
        margin: 50px auto;
        padding: 0 20px;
    }

    .politica-card {
        background: var(--cor-fundo-card);
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        margin-bottom: 40px;
    }

    .politica-card h2 {
        color: var(--cor-primaria);
        font-size: 1.6rem;
        margin-top: 30px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
        border-bottom: 2px solid #eee;
        padding-bottom: 10px;
    }

    .politica-card h2:first-child {
        margin-top: 0;
    }

    .politica-card p {
        color: var(--cor-texto-secundario);
        line-height: 1.8;
        font-size: 1.05rem;
        margin-bottom: 15px;
        text-align: justify;
    }

    .politica-card ul {
        margin-bottom: 20px;
        padding-left: 20px;
    }

    .politica-card li {
        color: var(--cor-texto-secundario);
        line-height: 1.8;
        font-size: 1.05rem;
        margin-bottom: 8px;
    }

    .passo-a-passo {
        background: #f8f9fa;
        padding: 20px;
        border-left: 4px solid var(--cor-info);
        border-radius: 0 8px 8px 0;
        margin-top: 20px;
    }

    .passo-a-passo p {
        margin: 0;
        color: #333;
    }

    .destaque-prazo {
        font-weight: bold;
        color: var(--cor-oferta);
    }
</style>

<div class="politica-hero">
    <h1>Trocas e Devoluções</h1>
    <p>Compre com tranquilidade. A KaByte valoriza a sua satisfação e respeita os seus direitos como consumidor.</p>
</div>

<div class="politica-container">

    <div class="politica-card">

        <h2><i class="fas fa-box-open"></i> 1. Condições Gerais</h2>
        <p>
            A equipe da <strong>KaByte</strong> testa e verifica cuidadosamente os lotes de todos os nossos periféricos e acessórios antes do envio. No entanto, se você precisar trocar ou devolver um produto, nossa política foi criada com base no Código de Defesa do Consumidor (CDC) para garantir que seus direitos sejam respeitados.
        </p>
        <p>
            Para que a troca ou devolução seja aceita, o produto deve cumprir as seguintes exigências:
        </p>
        <ul>
            <li>Estar na embalagem original, sem rasgos ou danos severos (salvo abertura natural do pacote).</li>
            <li>Conter todos os manuais, cabos, peças e acessórios que o acompanham.</li>
            <li>Não apresentar indícios de mau uso, quedas ou contato com líquidos.</li>
        </ul>

        <h2><i class="fas fa-undo"></i> 2. Direito de Arrependimento (Desistência)</h2>
        <p>
            Comprou um teclado, mouse ou fone e percebeu que não era exatamente o que queria? Não tem problema. Segundo o Art. 49 do CDC, nas compras realizadas pela internet, você tem o direito de se arrepender da compra em até <span class="destaque-prazo">7 (sete) dias corridos</span>, contados a partir da data de recebimento do pedido.
        </p>
        <p>
            Neste caso, o valor pago será integralmente reembolsado (incluindo o frete), desde que o produto seja devolvido em perfeito estado e sem sinais de uso contínuo.
        </p>

        <h2><i class="fas fa-tools"></i> 3. Troca por Defeito de Fabricação</h2>
        <p>
            Se o seu acessório apresentar alguma falha de funcionamento não causada por mau uso, você tem até <span class="destaque-prazo">90 (noventa) dias corridos</span> após o recebimento para acionar a garantia legal da KaByte.
        </p>
        <p>
            <strong>Atenção:</strong> A garantia não cobre danos causados por fatores externos ou uso indevido. Exemplos de situações <strong>não</strong> cobertas:
        </p>
        <ul>
            <li>Cabos USB ou conectores rompidos/descascados por força excessiva.</li>
            <li>Teclados ou mouses danificados por derramamento de líquidos (água, café, refrigerante).</li>
            <li>Equipamentos queimados por picos de energia ou uso em carregadores incompatíveis.</li>
            <li>Desgaste natural de espumas de fones de ouvido devido ao uso.</li>
        </ul>

        <h2><i class="fas fa-truck-loading"></i> 4. Avarias no Transporte</h2>
        <p>
            Recomendamos que você recuse o recebimento do pedido caso a embalagem dos Correios ou da transportadora chegue aberta ou visivelmente danificada. Caso você receba o pacote e perceba que o produto foi quebrado durante o transporte, entre em contato conosco em até <span class="destaque-prazo">48 horas</span> após o recebimento, enviando fotos da caixa e do produto avariado.
        </p>

        <h2><i class="fas fa-money-bill-wave"></i> 5. Formas de Reembolso</h2>
        <p>
            Após o recebimento e análise do produto em nosso estoque (o que pode levar até 3 dias úteis), o reembolso será processado conforme a forma de pagamento original:
        </p>
        <ul>
            <li><strong>Cartão de Crédito:</strong> O estorno será solicitado à administradora do cartão e poderá constar em até 2 (duas) faturas subsequentes.</li>
            <li><strong>PIX ou Boleto:</strong> O reembolso será feito via transferência bancária ou PIX para a conta do titular da compra em até 5 (cinco) dias úteis.</li>
        </ul>

        <h2><i class="fas fa-headset"></i> 6. Como Solicitar</h2>
        <div class="passo-a-passo">
            <p>Para iniciar um processo de troca ou devolução, envie uma mensagem para o nosso <strong>WhatsApp</strong> ou <strong>E-mail</strong> informando:</p>
            <ul style="margin-top: 10px; margin-bottom: 0;">
                <li>O número do seu pedido ou CPF.</li>
                <li>O motivo da troca/devolução.</li>
                <li>Fotos ou um breve vídeo que mostrem o estado do produto (obrigatório em casos de defeito).</li>
            </ul>
            <p style="margin-top: 10px;">Nossa equipe enviará as instruções e o código de postagem reversa para que você possa enviar o item de volta sem custos de frete (em casos de defeito ou arrependimento dentro dos 7 dias).</p>
        </div>

    </div>

</div>

<?php require __DIR__ . '/layout/footer_public.php'; ?>