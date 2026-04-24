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

<style>
    .chatbot-btn {
        position: fixed;
        bottom: 25px;
        right: 25px;
        background-color: #3498db; /* Azul tecnologia */
        color: white;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 1.8rem;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        cursor: pointer;
        z-index: 1050;
        transition: transform 0.3s;
    }
    .chatbot-btn:hover { transform: scale(1.1); }

    .chatbot-window {
        position: fixed;
        bottom: 100px;
        right: 25px;
        width: 350px;
        max-width: 90vw;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        display: flex;
        flex-direction: column;
        z-index: 1050;
        opacity: 0;
        pointer-events: none;
        transform: translateY(20px);
        transition: all 0.3s ease;
        height: 450px;
    }
    .chatbot-window.active {
        opacity: 1;
        pointer-events: all;
        transform: translateY(0);
    }

    .chatbot-header {
        background: #1e293b;
        color: white;
        padding: 15px;
        border-radius: 12px 12px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .chatbot-header h4 { margin: 0; font-size: 1.1rem; }
    .chatbot-close { background: none; border: none; color: white; cursor: pointer; font-size: 1.2rem; }

    .chatbot-body {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        background: #f4f7f6;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .chat-msg { max-width: 85%; padding: 10px 15px; border-radius: 15px; font-size: 0.95rem; line-height: 1.4; }
    .chat-msg.ia { background: white; border: 1px solid #eee; align-self: flex-start; border-bottom-left-radius: 2px; color: #333;}
    .chat-msg.user { background: #3498db; color: white; align-self: flex-end; border-bottom-right-radius: 2px; }

    .chatbot-footer {
        padding: 10px;
        background: white;
        border-top: 1px solid #eee;
        border-radius: 0 0 12px 12px;
        display: flex;
        gap: 10px;
    }
    .chatbot-input {
        flex: 1;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 20px;
        outline: none;
    }
    .chatbot-send {
        background: #3498db;
        color: white;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
    }
</style>

<div class="chatbot-btn" onclick="toggleChatbot()">
    <i class="fas fa-robot"></i>
</div>

<div class="chatbot-window" id="chatbotWindow">
    <div class="chatbot-header">
        <h4><i class="fas fa-robot"></i> Assistente KaByte</h4>
        <button class="chatbot-close" onclick="toggleChatbot()"><i class="fas fa-times"></i></button>
    </div>
    <div class="chatbot-body" id="chatbotBody">
        <div class="chat-msg ia">Olá! Sou o assistente de IA da KaByte. Está procurando algum hardware, mouse, ou teclado específico? 💻</div>
    </div>
    <div class="chatbot-footer">
        <input type="text" id="chatbotInput" class="chatbot-input" placeholder="Digite sua pergunta..." onkeypress="handleEnter(event)">
        <button class="chatbot-send" onclick="enviarMensagemIA()"><i class="fas fa-paper-plane"></i></button>
    </div>
</div>

<script>
    function toggleChatbot() {
        document.getElementById('chatbotWindow').classList.toggle('active');
    }

    function handleEnter(e) {
        if (e.key === 'Enter') enviarMensagemIA();
    }

    async function enviarMensagemIA() {
        const input = document.getElementById('chatbotInput');
        const msg = input.value.trim();
        if (!msg) return;

        const chatBody = document.getElementById('chatbotBody');

        // 1. Adiciona a mensagem do usuário na tela
        chatBody.innerHTML += `<div class="chat-msg user">${msg}</div>`;
        input.value = '';
        chatBody.scrollTop = chatBody.scrollHeight; // Rola pra baixo

        // 2. Mostra que a IA está digitando
        const idDigitando = 'msg-' + Date.now();
        chatBody.innerHTML += `<div class="chat-msg ia" id="${idDigitando}"><i class="fas fa-ellipsis-h fa-fade"></i></div>`;
        chatBody.scrollTop = chatBody.scrollHeight;

        try {
            // 3. Envia para o PHP
            const res = await fetch('<?= BASE_URL ?>api/chatbot', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ mensagem: msg })
            });
            const dados = await res.json();

            // 4. Atualiza o balãozinho com a resposta final
            const balaoIA = document.getElementById(idDigitando);
            if (dados.sucesso) {
                let htmlFormatado = dados.resposta
                    .replace(/\n/g, '<br>') // Aplica as quebras de linha
                    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>') // Transforma **texto** em negrito
                    // Expressão Regular (Magia) para transformar a imagem Markdown em um Card HTML bonito:
                    .replace(/!\[([^\]]*)\]\(([^)]+)\)/g, '<div style="text-align: center; margin: 12px 0; background: #fff; padding: 10px; border-radius: 8px; border: 1px solid #e0e0e0;"><img src="$2" alt="$1" style="max-width: 100%; max-height: 120px; object-fit: contain;"></div>');
                
                balaoIA.innerHTML = htmlFormatado;
            } else {
                balaoIA.innerHTML = '<i>Oops, deu um erro aqui: ' + dados.msg + '</i>';
            }
        } catch (e) {
            document.getElementById(idDigitando).innerHTML = '<i>Falha na conexão com a internet.</i>';
        }
        
        chatBody.scrollTop = chatBody.scrollHeight;
    }
</script>

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