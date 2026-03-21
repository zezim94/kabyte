<?php
// Lógica de Inicialização
$id = $produto['id'] ?? null;
$acao = $id ? "index.php?rota=produto/salvar&id=$id" : "index.php?rota=produto/salvar";
$titulo = $id ? "Editar Produto" : "Novo Produto";
$btnTexto = $id ? "Salvar Alterações" : "Cadastrar Produto";

// Caminho da imagem atual
$imagemAtual = (!empty($produto['imagem']) && file_exists(__DIR__ . '/../../public/uploads/' . $produto['imagem']))
    ? BASE_URL . 'public/uploads/' . $produto['imagem']
    : null;

// Garante que a variável da galeria exista para não dar erro
$imagensAdicionais = $imagensAdicionais ?? [];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?> - KaByte</title>

    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/form_produto.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Estilo Específico para o Toggle de Promoção */
        .promo-section {
            background-color: #fff8e1;
            padding: 15px;
            border-radius: 8px;
            border: 1px dashed #f1c40f;
            margin-bottom: 20px;
        }
        .promo-header {
            display: flex; align-items: center; gap: 10px; margin-bottom: 10px; cursor: pointer;
        }
        .promo-header input[type="checkbox"] { width: 18px; height: 18px; cursor: pointer; }
        .promo-header label { font-weight: bold; color: #d35400; cursor: pointer; user-select: none; }
        .promo-input { display: none; transition: all 0.3s ease; }
        .promo-input.active { display: block; animation: fadeIn 0.3s; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }

        /* Estilos da Galeria Adicional */
        .galeria-admin { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px; }
        .galeria-item { 
            position: relative; width: 75px; height: 75px; border: 2px solid #ddd; 
            border-radius: 8px; overflow: hidden; transition: 0.3s; 
        }
        .galeria-item:hover { border-color: #e74c3c; }
        .galeria-item img { width: 100%; height: 100%; object-fit: cover; }
        .galeria-item input[type="checkbox"] { 
            position: absolute; top: 5px; right: 5px; cursor: pointer; transform: scale(1.3);
            box-shadow: 0 0 5px rgba(0,0,0,0.5);
        }
    </style>
</head>

<body class="bg-admin">

    <?php require_once __DIR__ . '/../layout/header_admin.php'; ?>

    <div class="admin-container">

        <div class="page-header">
            <div class="header-title">
                <h2><?= $titulo ?></h2>
                <p>Preencha os dados completos do produto.</p>
            </div>
            <a href="<?= BASE_URL ?>produtos" class="btn-voltar">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>

        <form action="<?= $acao ?>" method="POST" enctype="multipart/form-data" class="form-produto">
            
            <?php if ($id): ?>
                <input type="hidden" name="id" value="<?= $id ?>">
            <?php endif; ?>

            <div class="form-layout">

                <div class="col-main">
                    
                    <div class="card-box">
                        <h3 class="card-title"><i class="fas fa-info-circle"></i> Informações Básicas</h3>

                        <div class="form-group">
                            <label for="nome">Nome do Produto *</label>
                            <input type="text" name="nome" id="nome" required class="form-input"
                                placeholder="Ex: Bicicleta Aro 29"
                                value="<?= htmlspecialchars($produto['nome'] ?? '') ?>">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="codigo_barras">Código de Barras (EAN)</label>
                                <input type="text" name="codigo_barras" id="codigo_barras" class="form-input"
                                    placeholder="789..."
                                    value="<?= htmlspecialchars($produto['codigo_barras'] ?? '') ?>">
                            </div>

                            <div class="form-group">
                                <label for="categoria">Categoria</label>
                                <select name="categoria_id" id="categoria" class="form-input">
                                    <option value="">Selecione...</option>
                                    <?php if (!empty($categorias)): ?>
                                        <?php foreach ($categorias as $cat): ?>
                                            <?php $isSelected = (isset($produto['categoria_id']) && $produto['categoria_id'] == $cat['id']) ? 'selected' : ''; ?>
                                            <option value="<?= $cat['id'] ?>" <?= $isSelected ?>>
                                                <?= htmlspecialchars($cat['nome']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="descricao">Descrição Detalhada</label>
                            <textarea name="descricao" id="descricao" rows="5" class="form-input"
                                placeholder="Descreva as características técnicas, tamanho, cor..."><?= htmlspecialchars($produto['descricao'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="card-box mt-20">
                        <h3 class="card-title"><i class="fas fa-dollar-sign"></i> Preços e Promoção</h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="preco_custo">Preço de Custo (R$)</label>
                                <input type="number" step="0.01" name="preco_custo" id="preco_custo" class="form-input"
                                    placeholder="0,00" value="<?= $produto['preco_custo'] ?? '' ?>">
                                <small style="color: #999; font-size: 0.8rem;">Visível apenas para admins</small>
                            </div>

                            <div class="form-group">
                                <label for="preco_venda">Preço de Venda (R$) *</label>
                                <input type="number" step="0.01" name="preco" id="preco_venda" required
                                    class="form-input destaque-preco" placeholder="0,00"
                                    value="<?= $produto['preco'] ?? '' ?>">
                            </div>
                        </div>

                        <div class="promo-section">
                            <div class="promo-header" onclick="document.getElementById('chkPromocao').click()">
                                <input type="checkbox" name="promocao" id="chkPromocao" value="1" 
                                       onclick="togglePromocao(event)"
                                       <?= (!empty($produto['promocao']) && $produto['promocao'] == 1) ? 'checked' : '' ?>>
                                <label for="chkPromocao">Produto em Promoção?</label>
                            </div>

                            <div id="divPrecoPromo" class="promo-input">
                                <label for="preco_promocao" style="color: #d35400;">Preço Promocional (R$)</label>
                                <input type="number" step="0.01" name="preco_promocao" id="preco_promocao" 
                                       class="form-input" style="border-color: #f1c40f;"
                                       placeholder="Valor com desconto"
                                       value="<?= $produto['preco_promocao'] ?? '' ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="estoque">Quantidade em Estoque *</label>
                            <input type="number" name="estoque" id="estoque" required class="form-input"
                                placeholder="0" value="<?= $produto['estoque'] ?? '' ?>">
                        </div>
                    </div>
                </div>

                <div class="col-side">
                    <div class="card-box">
                        <h3 class="card-title"><i class="fas fa-camera"></i> Imagem Principal</h3>

                        <div class="image-upload-wrapper">
                            <label for="upload-img" class="upload-label">
                                <div class="img-preview" id="imgPreview">
                                    <?php if ($imagemAtual): ?>
                                        <img src="<?= $imagemAtual ?>" alt="Preview">
                                    <?php else: ?>
                                        <div class="placeholder-icon">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <span>Clique para enviar</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </label>
                            <input type="file" name="imagem" id="upload-img" accept="image/*" onchange="previewImagem(event)">
                            <p class="help-text">Imagem principal do catálogo (Max: 2MB)</p>
                        </div>
                    </div>

                   <div class="card-box mt-20">
                        <h3 class="card-title"><i class="fas fa-images"></i> Galeria Adicional</h3>
                        
                        <div class="form-group" style="margin-bottom: 10px;">
                            <label for="imagens_extras" style="display: block; background: #f8f9fa; border: 2px dashed #3498db; padding: 20px; text-align: center; border-radius: 8px; cursor: pointer; color: #3498db; font-weight: bold; transition: all 0.3s;" onmouseover="this.style.background='#e3f2fd'" onmouseout="this.style.background='#f8f9fa'">
                                <i class="fas fa-cloud-upload-alt fa-2x" style="display: block; margin-bottom: 8px;"></i>
                                Clique aqui para selecionar as fotos extras
                            </label>
                            
                            <input type="file" name="imagens_extras[]" id="imagens_extras" accept="image/*" multiple style="display: none;" onchange="previewMultiplasImagens(this)">
                            
                            <small style="color: #999; display: block; margin-top: 8px; text-align: center;">Dica: Arraste o mouse ou segure a tecla <b>CTRL</b> para selecionar várias fotos de uma vez.</small>
                        </div>

                        <div id="preview-multiplo" style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px;"></div>

                        <?php if (!empty($imagensAdicionais)): ?>
                            <div class="galeria-admin">
                                <p style="width: 100%; font-size: 0.8rem; color: #e74c3c; margin: 0;">Marque a caixa na foto para excluí-la:</p>
                                <?php foreach ($imagensAdicionais as $img): ?>
                                    <div class="galeria-item" title="Marque para excluir">
                                        <img src="<?= BASE_URL ?>public/uploads/<?= htmlspecialchars($img['imagem']) ?>">
                                        <input type="checkbox" name="remover_imagens_extras[]" value="<?= $img['id'] ?>">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="card-box mt-20 actions-card">
                        <button type="submit" class="btn-salvar">
                            <i class="fas fa-save"></i> <?= $btnTexto ?>
                        </button>

                        <?php if ($id): ?>
                            <button type="button" class="btn-cancelar" onclick="window.location.href='<?= BASE_URL ?>produtos'">Cancelar</button>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <script>
        function previewImagem(event) {
            const input = event.target;
            const preview = document.getElementById('imgPreview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="fade-in">`;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function togglePromocao(e) {
            if (e) e.stopPropagation();
            const chk = document.getElementById('chkPromocao');
            const divPromo = document.getElementById('divPrecoPromo');
            const inputPromo = document.getElementById('preco_promocao');

            if (chk.checked) {
                divPromo.classList.add('active');
                inputPromo.focus();
            } else {
                divPromo.classList.remove('active');
                inputPromo.value = '';
            }
        }

        window.addEventListener('DOMContentLoaded', () => {
            const chk = document.getElementById('chkPromocao');
            const divPromo = document.getElementById('divPrecoPromo');
            if (chk.checked) {
                divPromo.classList.add('active');
            }
        });

        // --- PREVIEW DAS IMAGENS DA GALERIA EXTRA ---
        function previewMultiplasImagens(input) {
            const container = document.getElementById('preview-multiplo');
            container.innerHTML = ''; // Limpa o preview antigo se o usuário escolher outras fotos

            if (input.files) {
                // Passa por cada arquivo selecionado
                for (let i = 0; i < input.files.length; i++) {
                    const reader = new FileReader();
                    
                    reader.onload = function (e) {
                        // Cria uma tag <img> para cada foto e coloca na tela
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.width = '75px';
                        img.style.height = '75px';
                        img.style.objectFit = 'cover';
                        img.style.borderRadius = '8px';
                        img.style.border = '2px solid #3498db';
                        
                        container.appendChild(img);
                    }
                    
                    reader.readAsDataURL(input.files[i]);
                }
            }
        }
    </script>
</body>
</html>