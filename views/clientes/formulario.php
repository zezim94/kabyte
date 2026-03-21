<?php
// Inicializa dados caso seja "Novo Cliente"
$id = $cliente['id'] ?? null;
$acao = $id ? "index.php?rota=cliente/salvar&id=$id" : "index.php?rota=cliente/salvar";
$titulo = $id ? "Editar Cliente" : "Novo Cliente";

$cliente = $cliente ?? []; // Evita erros de undefined index
$endereco = $endereco ?? []; // NOVO: Inicializa o endereço (Endereço Padrão)
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?> - KaByte</title>

    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/adm_cliente_form.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <?php require_once __DIR__ . '/../layout/header_admin.php'; ?>

    <div class="admin-container">

        <div class="form-card">

            <div class="form-header">
                <h2><?= $titulo ?></h2>
                <p>Preencha as informações abaixo para manter o cadastro atualizado.</p>
            </div>

            <div class="step-indicator">
                <div class="step-item active" id="dot-1">
                    <div class="step-circle">1</div>
                    <span class="step-label">Dados Pessoais</span>
                </div>
                <div class="step-line"></div>
                <div class="step-item" id="dot-2">
                    <div class="step-circle">2</div>
                    <span class="step-label">Endereço</span>
                </div>
            </div>

            <form method="POST" action="<?= $acao ?>" id="form-cliente">

                <input type="hidden" name="endereco_id" value="<?= htmlspecialchars($endereco['id'] ?? '') ?>">

                <div class="form-step active" id="step-1">
                    <div class="step-title">
                        <i class="fas fa-user-circle"></i> Informações Básicas
                    </div>

                    <div class="form-group">
                        <label for="nome">Nome Completo *</label>
                        <input type="text" name="nome" id="nome" required class="form-input" placeholder="Ex: Ana Souza"
                            value="<?= htmlspecialchars($cliente['nome'] ?? '') ?>">
                    </div>

                    <div class="form-row two-columns">
                        <div class="form-group">
                            <label for="cpf">CPF</label>
                            <input type="text" name="cpf" id="cpf" class="form-input" placeholder="000.000.000-00"
                                maxlength="14" value="<?= htmlspecialchars($cliente['cpf'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label for="telefone">Telefone / WhatsApp</label>
                            <input type="text" name="telefone" id="telefone" class="form-input"
                                placeholder="(00) 00000-0000" maxlength="15"
                                value="<?= htmlspecialchars($cliente['telefone'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" name="email" id="email" class="form-input" placeholder="cliente@email.com"
                            value="<?= htmlspecialchars($cliente['email'] ?? '') ?>">
                    </div>

                    <div class="form-footer right-align">
                        <button type="button" class="btn btn-next" onclick="irParaEtapa(2)">
                            Próximo <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <div class="form-step" id="step-2">
                    <div class="step-title">
                        <i class="fas fa-map-marker-alt"></i> Localização (Endereço Principal)
                    </div>

                    <div class="form-row address-grid">
                        <div class="form-group cep-group">
                            <label for="cep">CEP</label>
                            <div class="input-with-icon">
                                <input type="text" name="cep" id="cep" class="form-input" placeholder="00000-000"
                                    maxlength="9" onblur="buscarCep(this.value)"
                                    value="<?= htmlspecialchars($endereco['cep'] ?? '') ?>">
                                <i class="fas fa-search search-cep-icon"></i>
                            </div>
                            <small id="cep-loading" style="display:none; color: #3498db;">Buscando...</small>
                        </div>

                        <div class="form-group city-group">
                            <label for="cidade">Cidade</label>
                            <input type="text" name="cidade" id="cidade" class="form-input" readonly
                                value="<?= htmlspecialchars($endereco['cidade'] ?? '') ?>">
                        </div>

                        <div class="form-group uf-group">
                            <label for="uf">UF</label>
                            <input type="text" name="estado" id="uf" class="form-input" readonly
                                value="<?= htmlspecialchars($endereco['estado'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-row two-columns-biased">
                        <div class="form-group">
                            <label for="rua">Rua / Logradouro</label>
                            <input type="text" name="rua" id="rua" class="form-input"
                                value="<?= htmlspecialchars($endereco['rua'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="numero">Número</label>
                            <input type="text" name="numero" id="numero" class="form-input" placeholder="123"
                                value="<?= htmlspecialchars($endereco['numero'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-row two-columns">
                        <div class="form-group">
                            <label for="bairro">Bairro</label>
                            <input type="text" name="bairro" id="bairro" class="form-input"
                                value="<?= htmlspecialchars($endereco['bairro'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label for="complemento">Complemento</label>
                            <input type="text" name="complemento" id="complemento" class="form-input"
                                placeholder="Apto, Bloco..."
                                value="<?= htmlspecialchars($endereco['complemento'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-footer space-between">
                        <button type="button" class="btn btn-prev" onclick="irParaEtapa(1)">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </button>
                        <button type="submit" class="btn btn-save">
                            <i class="fas fa-save"></i> Salvar Cliente
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <script>
        // Controle de Etapas
        function irParaEtapa(numero) {
            // Validação simples da etapa 1
            if (numero === 2) {
                const nome = document.getElementById('nome').value;
                if (nome.trim() === '') {
                    alert('Por favor, preencha o Nome Completo.');
                    document.getElementById('nome').focus();
                    return;
                }
            }

            // Remove classes ativas
            document.querySelectorAll('.form-step').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.step-item').forEach(el => el.classList.remove('active'));

            // Ativa etapa atual
            document.getElementById('step-' + numero).classList.add('active');

            // Lógica visual do Stepper (Bolinhas)
            const dot1 = document.getElementById('dot-1');
            const dot2 = document.getElementById('dot-2');

            if (numero === 1) {
                dot1.classList.add('active');
                dot2.classList.remove('active');
            } else {
                dot1.classList.add('active'); // O 1 continua ativo se estivermos no 2
                dot2.classList.add('active');
            }
        }

        // Busca de CEP (ViaCEP API)
        async function buscarCep(cep) {
            cep = cep.replace(/\D/g, ''); // Remove tudo que não for dígito

            if (cep.length === 8) {
                const loading = document.getElementById('cep-loading');
                loading.style.display = 'block';

                try {
                    const res = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                    const data = await res.json();

                    loading.style.display = 'none';

                    if (!data.erro) {
                        document.getElementById('rua').value = data.logradouro;
                        document.getElementById('bairro').value = data.bairro;
                        document.getElementById('cidade').value = data.localidade;
                        document.getElementById('uf').value = data.uf; // Atualiza o estado

                        // Foca no número automaticamente
                        document.getElementById('numero').focus();
                    } else {
                        alert('CEP não encontrado.');
                    }
                } catch (e) {
                    loading.style.display = 'none';
                    console.error('Erro ao buscar CEP');
                }
            }
        }
    </script>
</body>

</html>