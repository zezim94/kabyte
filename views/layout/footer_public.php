<style>
    /* ==============================
       ESTILOS DO RODAPÉ (Footer)
       ============================== */
    .site-footer {
        background-color: #2c3e50;
        /* Cor escura profissional */
        color: #ecf0f1;
        padding: 40px 0 20px 0;
        margin-top: 50px;
        font-family: 'Segoe UI', sans-serif;
    }

    .footer-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
    }

    .footer-col h3 {
        color: #fff;
        font-size: 1.2rem;
        margin-bottom: 20px;
        border-bottom: 2px solid #e74c3c;
        /* Detalhe vermelho */
        display: inline-block;
        padding-bottom: 5px;
    }

    .footer-col p {
        color: #bdc3c7;
        font-size: 0.9rem;
        line-height: 1.6;
    }

    /* Links do Footer */
    .footer-links {
        list-style: none;
        padding: 0;
    }

    .footer-links li {
        margin-bottom: 10px;
    }

    .footer-links a {
        color: #bdc3c7;
        text-decoration: none;
        transition: color 0.3s;
    }

    .footer-links a:hover {
        color: #e74c3c;
        padding-left: 5px;
        /* Efeitinho de movimento */
    }

    /* Redes Sociais */
    .social-icons {
        display: flex;
        gap: 15px;
        margin-top: 15px;
    }

    .social-btn {
        width: 40px;
        height: 40px;
        background-color: rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        color: white;
        text-decoration: none;
        font-size: 1.2rem;
        transition: all 0.3s;
    }

    /* Cores Específicas ao passar o mouse */
    .social-btn.insta:hover {
        background: #d6249f;
        background: radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285AEB 90%);
        transform: translateY(-3px);
    }

    .social-btn.whats:hover {
        background-color: #25d366;
        transform: translateY(-3px);
    }

    .social-btn.face:hover {
        background-color: #1877f2;
        transform: translateY(-3px);
    }

    /* Copyright */
    .footer-bottom {
        text-align: center;
        margin-top: 40px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        font-size: 0.85rem;
        color: #95a5a6;
    }

    /* Pagamento */
    .payment-methods {
        margin-top: 10px;
        font-size: 1.5rem;
        color: #bdc3c7;
        letter-spacing: 10px;
    }
</style>

<footer class="site-footer">
    <div class="footer-content">

        <div class="footer-col">
            <h3>Sobre a Loja</h3>
            <p>
                A <strong><?= htmlspecialchars($config['nome_loja'] ?? 'Paty Bike') ?></strong> é especialista em peças
                e acessórios.
                Oferecemos qualidade, preço justo e entrega garantida para todo o Brasil.
            </p>
            <div class="payment-methods">
                <i class="fab fa-cc-visa"></i>
                <i class="fab fa-cc-mastercard"></i>
                <i class="fab fa-cc-amex"></i>
                <i class="fas fa-barcode"></i>
                <i class="fas fa-university"></i>
            </div>
        </div>

        <div class="footer-col">
            <h3>Links Rápidos</h3>
            <ul class="footer-links">
                <li><a href="<?= BASE_URL ?>vitrine"><i class="fas fa-chevron-right"></i> Início</a></li>
                <li><a href="<?= BASE_URL ?>sobre"><i class="fas fa-chevron-right"></i> Sobre</a></li>
                <li><a href="<?= BASE_URL ?>cliente/login"><i class="fas fa-chevron-right"></i> Minha Conta</a></li>
                <li><a href="<?= BASE_URL ?>vitrine&busca=oferta"><i class="fas fa-chevron-right"></i> Promoções</a>
                </li>
                <li><a href="#"><i class="fas fa-chevron-right"></i> Política de Troca</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h3>Fale Conosco</h3>
            <ul class="footer-links">
                <li>
                    <i class="fab fa-whatsapp" style="color:#25d366"></i>
                    <?= htmlspecialchars($config['whatsapp'] ?? '(00) 00000-0000') ?>
                </li>
                <li><i class="fas fa-envelope"></i> contato@kabyte.com.br</li>
                <li><i class="fas fa-map-marker-alt"></i> Entregamos em todo o Brasil</li>
            </ul>

            <div class="social-icons">
                <a href="https://www.instagram.com/kabytetech/" target="_blank" class="social-btn insta"
                    title="Siga no Instagram">
                    <i class="fab fa-instagram"></i>
                </a>

                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $config['whatsapp'] ?? '') ?>" target="_blank"
                    class="social-btn whats" title="Chame no Zap">
                    <i class="fab fa-whatsapp"></i>
                </a>

                <a href="#" class="social-btn face" title="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
            </div>
        </div>

    </div>

    <div class="footer-bottom">
        &copy; <?= date('Y') ?> <strong><?= htmlspecialchars($config['nome_loja'] ?? 'Paty Bike') ?></strong> - Todos os
        direitos reservados.
        <br>Desenvolvido com <i class="fas fa-heart" style="color: #e74c3c;"></i>
    </div>
</footer>

</body>

</html>