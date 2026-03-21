<?php
require __DIR__ . '/../layout/header_public.php';

// Garante que a variável endereços exista (vinda do controller)
$enderecos = $enderecos ?? [];
?>

<style>
    /* --- VARIAVEIS E GERAL --- */
    :root {
        --primary: #3498db;
        --secondary: #2c3e50;
        --success: #27ae60;
        --danger: #e74c3c;
        --light: #f8f9fa;
        --border: #e9ecef;
    }

    .profile-container {
        max-width: 900px;
        margin: 40px auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    /* --- ABAS (TABS) --- */
    .tabs-header {
        display: flex;
        background: #fff;
        border-bottom: 1px solid var(--border);
        padding: 0 20px;
    }

    .tab-btn {
        padding: 20px 25px;
        border: none;
        background: none;
        font-size: 1rem;
        font-weight: 600;
        color: #7f8c8d;
        cursor: pointer;
        transition: all 0.3s ease;
        border-bottom: 3px solid transparent;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .tab-btn:hover {
        color: var(--primary);
    }

    .tab-btn.active {
        color: var(--primary);
        border-bottom-color: var(--primary);
    }

    /* --- CONTEÚDO --- */
    .tab-content {
        padding: 40px;
        display: none;
        animation: slideUp 0.4s ease;
    }

    .tab-content.active {
        display: block;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* --- FORMULÁRIOS --- */
    .form-section-title {
        font-size: 1.4rem;
        color: var(--secondary);
        margin-bottom: 25px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: var(--secondary);
        font-weight: 600;
        font-size: 0.9rem;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid var(--border);
        border-radius: 8px;
        font-size: 1rem;
        transition: border-color 0.3s;
        box-sizing: border-box;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
    }

    .row-dupla {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .row-tripla {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr;
        gap: 20px;
    }

    /* --- BOTÕES --- */
    .btn {
        padding: 12px 25px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: #2980b9;
    }

    .btn-success {
        background: var(--success);
        color: white;
    }

    .btn-success:hover {
        background: #219150;
    }

    .btn-danger {
        background: #fff0f0;
        color: var(--danger);
        border: 1px solid #fadbd8;
        padding: 5px 10px;
        font-size: 0.85rem;
        border-radius: 6px;
    }

    .btn-danger:hover {
        background: var(--danger);
        color: white;
    }

    /* --- LISTA DE ENDEREÇOS (CARDS) --- */
    .address-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .address-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 20px;
        position: relative;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .address-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        border-color: var(--primary);
    }

    .addr-icon {
        font-size: 1.5rem;
        color: var(--primary);
        margin-bottom: 10px;
    }

    .addr-text {
        color: #555;
        font-size: 0.95rem;
        line-height: 1.5;
    }

    .addr-actions {
        position: absolute;
        top: 15px;
        right: 15px;
        display: flex;
        gap: 5px;
    }

    /* Formulário Novo Endereço (Toggle) */
    #form-novo-endereco {
        background: var(--light);
        padding: 25px;
        border-radius: 10px;
        border: 1px dashed #ccc;
        margin-top: 20px;
        display: none;
    }

    @media (max-width: 768px) {
        .tabs-header {
            flex-direction: column;
        }

        .row-dupla,
        .row-tripla {
            grid-template-columns: 1fr;
        }

        .tab-content {
            padding: 20px;
        }
    }
</style>

<?php if (isset($_GET['msg'])): ?>
    <div style="max-width:900px; margin: 20px auto; padding: 15px; border-radius: 8px; text-align:center;
        <?= ($_GET['sucesso'] == 1) ? 'background:#d4edda; color:#155724;' : 'background:#f8d7da; color:#721c24;' ?>">
        <?= htmlspecialchars($_GET['msg']) ?>
    </div>
<?php endif; ?>

<div class="profile-container">

    <div class="tabs-header">
        <button class="tab-btn active" onclick="abrirTab(event, 'pessoal')">
            <i class="fas fa-user-circle"></i> Meus Dados
        </button>
        <button class="tab-btn" onclick="abrirTab(event, 'enderecos')">
            <i class="fas fa-map-marker-alt"></i> Endereços
        </button>
        <button class="tab-btn" onclick="abrirTab(event, 'seguranca')">
            <i class="fas fa-shield-alt"></i> Segurança
        </button>
    </div>

    <div id="pessoal" class="tab-content active">
        <form action="<?= BASE_URL ?>cliente/atualizar_perfil" method="POST">
            <h3 class="form-section-title">Informações Básicas</h3>

            <div class="form-group">
                <label>Nome Completo</label>
                <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($cliente['nome']) ?>"
                    required>
            </div>

            <div class="row-dupla">
                <div class="form-group">
                    <label>CPF</label>
                    <input type="text" name="cpf" class="form-control"
                        value="<?= htmlspecialchars($cliente['cpf'] ?? '') ?>" readonly
                        style="background:#eee; cursor:not-allowed;" title="O CPF não pode ser alterado">
                </div>
                <div class="form-group">
                    <label>Telefone / WhatsApp</label>
                    <input type="text" name="telefone" class="form-control"
                        value="<?= htmlspecialchars($cliente['telefone'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label>E-mail (Login)</label>
                <input type="email" name="email" class="form-control"
                    value="<?= htmlspecialchars($cliente['email'] ?? '') ?>" readonly
                    style="background:#eee; cursor:not-allowed;" title="O E-mail de login não pode ser alterado">
            </div>

            <div style="text-align: right; margin-top: 20px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar Alterações
                </button>
            </div>
        </form>
    </div>

    <div id="enderecos" class="tab-content">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h3 class="form-section-title" style="margin:0;">Meus Endereços</h3>
            <button class="btn btn-success" onclick="toggleFormEndereco()">
                <i class="fas fa-plus"></i> Novo Endereço
            </button>
        </div>

        <div class="address-grid">
            <?php if (empty($enderecos)): ?>
                <div style="grid-column: 1 / -1; text-align: center; color: #999; padding: 20px;">
                    <i class="fas fa-map-marked-alt fa-2x" style="margin-bottom: 10px; display: block;"></i>
                    Você ainda não tem nenhum endereço cadastrado.
                </div>
            <?php else: ?>
                <?php foreach ($enderecos as $end): ?>
                    <div class="address-card" <?= $end['is_padrao'] == 1 ? 'style="border-color: var(--primary); background: #fdfdfd;"' : '' ?>>

                        <div class="addr-actions">
                            <?php if ($end['is_padrao'] == 1): ?>
                                <span class="badge"
                                    style="background:var(--primary); color:white; padding:4px 8px; border-radius:4px; font-size:0.75rem;">Principal</span>
                            <?php endif; ?>

                            <a href="<?= BASE_URL ?>cliente/excluir_endereco?id=<?= $end['id'] ?>" class="btn-danger"
                                onclick="return confirm('Excluir este endereço?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>

                        <div class="addr-icon"><i class="fas <?= $end['is_padrao'] == 1 ? 'fa-home' : 'fa-map-pin' ?>"></i>
                        </div>

                        <div class="addr-text">
                            <strong><?= htmlspecialchars($end['rua']) ?>, <?= htmlspecialchars($end['numero']) ?></strong><br>
                            <?= htmlspecialchars($end['bairro']) ?><br>
                            <?= htmlspecialchars($end['cidade']) ?> - <?= htmlspecialchars($end['estado'] ?? '') ?><br>
                            CEP: <?= htmlspecialchars($end['cep']) ?>
                            <?php if (!empty($end['complemento'])): ?>
                                <br><small style="color: #888;">Comp: <?= htmlspecialchars($end['complemento']) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div id="form-novo-endereco">
            <h4 style="margin-top:0; color:var(--primary);">Cadastrar Novo Endereço</h4>
            <hr>
            <form action="<?= BASE_URL ?>cliente/salvar_endereco" method="POST">

                <div class="row-dupla" style="grid-template-columns: 1fr 3fr;">
                    <div class="form-group">
                        <label>CEP</label>
                        <input type="text" name="cep" id="cep" class="form-control" onblur="buscarCep(this.value)"
                            required>
                    </div>
                    <div class="form-group" style="display:flex; align-items:end; padding-bottom:5px;">
                        <a href="https://buscacepinter.correios.com.br/app/endereco/index.php" target="_blank"
                            style="font-size:0.9rem; color:var(--primary);">Não sei o CEP</a>
                    </div>
                </div>

                <div class="row-dupla" style="grid-template-columns: 3fr 1fr;">
                    <div class="form-group">
                        <label>Rua/Logradouro</label>
                        <input type="text" name="rua" id="rua" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Número</label>
                        <input type="text" name="numero" class="form-control" required>
                    </div>
                </div>

                <div class="row-tripla">
                    <div class="form-group">
                        <label>Bairro</label>
                        <input type="text" name="bairro" id="bairro" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Cidade</label>
                        <input type="text" name="cidade" id="cidade" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Estado (UF)</label>
                        <input type="text" name="estado" id="estado" class="form-control" maxlength="2" required
                            placeholder="SP">
                    </div>
                </div>

                <div class="form-group">
                    <label>Complemento (Opcional)</label>
                    <input type="text" name="complemento" class="form-control"
                        placeholder="Apto, Bloco, Ponto de referência...">
                </div>

                <div style="display:flex; justify-content:flex-end; gap:10px; margin-top: 15px;">
                    <button type="button" class="btn" style="background:#ccc; color:#333;"
                        onclick="toggleFormEndereco()">Cancelar</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Salvar Endereço</button>
                </div>
            </form>
        </div>
    </div>

    <div id="seguranca" class="tab-content">
        <form action="<?= BASE_URL ?>cliente/atualizar_senha" method="POST">
            <h3 class="form-section-title">Alterar Senha</h3>

            <div class="alert"
                style="background:#fff3cd; color:#856404; padding:15px; border-radius:5px; margin-bottom:20px;">
                <i class="fas fa-lock"></i> Por segurança, você precisará fazer login novamente após alterar a senha.
            </div>

            <div class="form-group">
                <label>Nova Senha</label>
                <input type="password" name="nova_senha" class="form-control" required
                    placeholder="Mínimo 6 caracteres">
            </div>

            <div class="form-group">
                <label>Confirmar Nova Senha</label>
                <input type="password" name="confirma_senha" class="form-control" required placeholder="Repita a senha">
            </div>

            <div style="text-align: right;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i> Atualizar Senha
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    // --- LÓGICA DE ABAS ---
    function abrirTab(evt, tabName) {
        const contents = document.getElementsByClassName("tab-content");
        for (let i = 0; i < contents.length; i++) {
            contents[i].classList.remove("active");
        }

        const btns = document.getElementsByClassName("tab-btn");
        for (let i = 0; i < btns.length; i++) {
            btns[i].classList.remove("active");
        }

        document.getElementById(tabName).classList.add("active");
        evt.currentTarget.classList.add("active");
    }

    // --- TOGGLE FORM ENDEREÇO ---
    function toggleFormEndereco() {
        const form = document.getElementById('form-novo-endereco');
        if (form.style.display === 'block') {
            form.style.display = 'none';
        } else {
            form.style.display = 'block';
            document.getElementById('cep').focus();
        }
    }

    // --- BUSCA CEP COM API VIACEP ---
    function buscarCep(cep) {
        cep = cep.replace(/\D/g, '');
        if (cep !== "") {
            const validacep = /^[0-9]{8}$/;
            if (validacep.test(cep)) {
                // Feedback visual de carregamento
                document.getElementById('rua').value = "...";
                document.getElementById('bairro').value = "...";
                document.getElementById('cidade').value = "...";
                document.getElementById('estado').value = "...";

                const script = document.createElement('script');
                script.src = 'https://viacep.com.br/ws/' + cep + '/json/?callback=meu_callback';
                document.body.appendChild(script);
            } else {
                alert("Formato de CEP inválido.");
            }
        }
    }

    function meu_callback(conteudo) {
        if (!("erro" in conteudo)) {
            document.getElementById('rua').value = conteudo.logradouro;
            document.getElementById('bairro').value = conteudo.bairro;
            document.getElementById('cidade').value = conteudo.localidade;
            document.getElementById('estado').value = conteudo.uf; // Novo campo preenchido
            document.querySelector('[name="numero"]').focus(); // Joga o cursor para o numero
        } else {
            alert("CEP não encontrado.");
            document.getElementById('rua').value = "";
            document.getElementById('bairro').value = "";
            document.getElementById('cidade').value = "";
            document.getElementById('estado').value = "";
        }
    }
</script>

<?php require __DIR__ . '/../layout/footer_public.php'; ?>