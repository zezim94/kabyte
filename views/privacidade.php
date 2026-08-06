<?php require __DIR__ . '/layout/header_public.php'; ?>

<style>
    /* =========================================
       ESTILOS DA PÁGINA DE PRIVACIDADE
       ========================================= */
    .privacidade-hero {
        background: linear-gradient(to right, #2c3e50, #34495e);
        color: var(--cor-branco);
        padding: 60px 20px;
        text-align: center;
        border-bottom: 5px solid #3498db;
    }

    .privacidade-hero h1 {
        font-size: 2.5rem;
        margin-bottom: 15px;
        font-weight: 800;
        letter-spacing: 1px;
    }

    .privacidade-hero p {
        font-size: 1.1rem;
        max-width: 700px;
        margin: 0 auto;
        opacity: 0.9;
    }

    .privacidade-container {
        max-width: 900px;
        margin: 50px auto;
        padding: 0 20px;
    }

    .privacidade-card {
        background: var(--cor-fundo-card, #ffffff);
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
        margin-bottom: 40px;
    }

    .privacidade-card h2 {
        color: #2c3e50;
        font-size: 1.5rem;
        margin-top: 35px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
        border-bottom: 2px solid #f1f2f6;
        padding-bottom: 10px;
    }

    .privacidade-card h2:first-child {
        margin-top: 0;
    }

    .privacidade-card h2 i {
        color: #3498db;
    }

    .privacidade-card p {
        color: #555;
        line-height: 1.8;
        font-size: 1.05rem;
        margin-bottom: 15px;
        text-align: justify;
    }

    .privacidade-card ul {
        margin-bottom: 20px;
        padding-left: 20px;
    }

    .privacidade-card li {
        color: #555;
        line-height: 1.8;
        font-size: 1.05rem;
        margin-bottom: 8px;
    }

    /* Caixa de Destaque de Segurança */
    .caixa-seguranca {
        background: #f8f9fa;
        border: 1px solid #e1e8ed;
        border-radius: 12px;
        padding: 25px;
        margin: 25px 0;
        display: flex;
        align-items: flex-start;
        gap: 20px;
    }

    .caixa-seguranca .icone-shield {
        font-size: 3rem;
        color: #27ae60;
    }

    .caixa-seguranca h4 {
        margin: 0 0 10px 0;
        color: #2c3e50;
        font-size: 1.2rem;
    }

    .caixa-seguranca p {
        margin: 0;
        font-size: 0.95rem;
    }

    /* Selos de Segurança */
    .selos-container {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-top: 20px;
        justify-content: center;
    }

    .selo {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #fff;
        border: 1px solid #ddd;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        color: #333;
        font-size: 0.9rem;
    }

    .selo img {
        height: 25px;
    }
    
    @media (max-width: 600px) {
        .caixa-seguranca {
            flex-direction: column;
            text-align: center;
            align-items: center;
        }
    }
</style>

<div class="privacidade-hero">
    <h1>Política de Privacidade e Segurança</h1>
    <p>A sua confiança é o nosso maior ativo. Descubra como a KaByte protege os seus dados com tecnologia de ponta e rigorosos padrões de segurança.</p>
</div>

<div class="privacidade-container">

    <div class="privacidade-card">
        
        <h2><i class="fas fa-user-shield"></i> 1. O nosso compromisso com a LGPD</h2>
        <p>
            A <strong>KaByte</strong> respeita a sua privacidade e garante o sigilo total das informações que nos fornece. A nossa Política de Privacidade foi elaborada em estrita conformidade com a <strong>Lei Geral de Proteção de Dados (LGPD - Lei nº 13.709/2018)</strong>. Os seus dados pessoais são armazenados de forma segura e utilizados exclusivamente para processar as suas compras, realizar entregas e, caso você autorize, enviar ofertas relevantes.
        </p>

        <div class="caixa-seguranca">
            <i class="fas fa-lock icone-shield"></i>
            <div>
                <h4>Criptografia Avançada do seu CPF</h4>
                <p>
                    Sabemos que o CPF é uma informação sensível. Por isso, utilizamos <strong>Criptografia Determinística de Nível Militar (AES-256-CBC)</strong>. O seu CPF nunca é guardado em texto legível no nosso banco de dados; ele é transformado num código indecifrável. Nem mesmo os atendentes ou operadores de caixa da nossa loja conseguem ver o seu número completo (ele é mascarado pelo sistema, exibindo apenas algo como <b>***.456.789-**</b>). Em caso de qualquer tentativa de invasão aos nossos servidores, os seus dados estarão completamente blindados.
                </p>
            </div>
        </div>

        <h2><i class="fas fa-credit-card"></i> 2. Pagamentos Seguros via Mercado Pago</h2>
        <p>
            Na KaByte, a segurança do seu dinheiro vem em primeiro lugar. <strong>Nós não armazenamos os dados do seu cartão de crédito</strong> nos nossos servidores em nenhum momento. 
        </p>
        <p>
            Todo o processamento de pagamentos (Cartão de Crédito, Débito e PIX) é feito num ambiente externo, seguro e auditado pelo <strong>Mercado Pago</strong>, uma das maiores e mais seguras instituições financeiras da América Latina. O Mercado Pago possui a certificação <strong>PCI-DSS</strong> (Padrão de Segurança de Dados da Indústria de Cartões de Pagamento), garantindo que a sua transação é criptografada e monitorizada contra fraudes em tempo real.
        </p>

        <div class="selos-container">
            <div class="selo">
                <i class="fas fa-shield-alt" style="color: #3498db; font-size: 1.5rem;"></i>
                Navegação Segura (SSL)
            </div>
            <div class="selo">
                <i class="fas fa-handshake" style="color: #009ee3; font-size: 1.5rem;"></i>
                Parceiro Oficial Mercado Pago
            </div>
            <div class="selo">
                <i class="fas fa-file-contract" style="color: #27ae60; font-size: 1.5rem;"></i>
                Site 100% LGPD
            </div>
        </div>

        <h2><i class="fas fa-database"></i> 3. Que dados recolhemos e porquê?</h2>
        <p>
            Para que possamos oferecer-lhe a melhor experiência de compra e garantir a entrega dos seus produtos, recolhemos apenas os dados estritamente necessários:
        </p>
        <ul>
            <li><strong>Nome e E-mail:</strong> Para identificar o seu perfil, enviar a nota fiscal e os códigos de rastreio.</li>
            <li><strong>CPF:</strong> Exigência legal para a emissão de notas fiscais e envio por transportadoras/Correios (protegido por forte criptografia).</li>
            <li><strong>Telefone/WhatsApp:</strong> Para facilitar o contato rápido sobre o estado da sua entrega.</li>
            <li><strong>Endereço:</strong> Utilizado exclusivamente para calcular o frete e enviar os produtos.</li>
        </ul>

        <h2><i class="fas fa-share-alt"></i> 4. Compartilhamento de Informações</h2>
        <p>
            A KaByte <strong>não vende, aluga ou transfere</strong> os seus dados para terceiros. As suas informações são partilhadas apenas com empresas parceiras quando isso é essencial para concluir o seu pedido. Por exemplo:
        </p>
        <ul>
            <li><strong>Transportadoras e Correios:</strong> Recebem o seu nome, endereço e nota fiscal para realizar a entrega.</li>
            <li><strong>Plataforma de Pagamento (Mercado Pago):</strong> Recebe os dados da faturação para processar e aprovar o pagamento.</li>
        </ul>

        <h2><i class="fas fa-cookie-bite"></i> 5. Uso de Cookies</h2>
        <p>
            Utilizamos <em>cookies</em> no nosso site apenas para reconhecer o seu navegador e proporcionar uma experiência personalizada. Eles ajudam-nos a manter os itens no seu carrinho de compras mesmo se você fechar a aba do navegador por acidente. Pode apagar ou bloquear os cookies a qualquer momento nas configurações do seu navegador.
        </p>

        <h2><i class="fas fa-user-edit"></i> 6. Os seus Direitos</h2>
        <p>
            Você é o dono dos seus dados. A qualquer momento, acedendo à sua conta na aba "Meus Dados", você pode atualizar as suas informações, corrigir erros de digitação ou alterar o seu endereço. Caso deseje a exclusão permanente da sua conta e de todos os seus dados da nossa base, basta contactar a nossa equipa de suporte por e-mail ou WhatsApp, e processaremos o pedido imediatamente.
        </p>
        
    </div>

</div>

<?php require __DIR__ . '/layout/footer_public.php'; ?>