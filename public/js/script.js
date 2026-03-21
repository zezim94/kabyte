// ==========================================
// 0. FUNÇÃO DE NOTIFICAÇÃO (TOAST)
// ==========================================
function showToast(mensagem, tipo = "success") {
  const container = document.getElementById("toast-container");
  if (!container) return; // Segurança caso o HTML não tenha atualizado

  // Ícones por tipo
  let icon = "";
  if (tipo === "success") icon = '<i class="fas fa-check-circle"></i>';
  if (tipo === "error") icon = '<i class="fas fa-times-circle"></i>';
  if (tipo === "warning") icon = '<i class="fas fa-exclamation-triangle"></i>';

  const toast = document.createElement("div");
  toast.className = `toast ${tipo}`;
  toast.innerHTML = `${icon} <span>${mensagem}</span>`;

  container.appendChild(toast);

  // Remove após 3.5 segundos
  setTimeout(() => {
    toast.classList.add("hide");
    setTimeout(() => toast.remove(), 300); // Espera animação CSS
  }, 3500);
}

// ==========================================
// 1. VARIÁVEIS GLOBAIS
// ==========================================
let carrinho = [];
let totalVenda = 0;

// Elementos Principais
const inputCodigo = document.getElementById("codigo");
const inputQtd = document.getElementById("qtd-input");
const tabela = document.querySelector("#tabela-produtos tbody");
const divTotal = document.getElementById("valor-total");
const divUltimo = document.querySelector("#ultimo-item span");

// Elementos Modais
const modalPesquisa = document.getElementById("modal-pesquisa");
const inputPesquisa = document.getElementById("input-pesquisa");
const listaPesquisa = document.getElementById("lista-pesquisa");
const modalPagamento = document.getElementById("modal-pagamento");

// Elementos Pagamento & Cliente
const selectPagamento = document.getElementById("forma-pagamento");
const areaDinheiro = document.getElementById("area-dinheiro");
const inputRecebido = document.getElementById("valor-recebido");
const displayTroco = document.getElementById("valor-troco");
const inputCliente = document.getElementById("input-busca-cliente");
const listaClientesResult = document.getElementById("lista-clientes-resultado");
const hiddenIdCliente = document.getElementById("id-cliente-selecionado");

// Foco Inicial
if (inputCodigo) inputCodigo.focus();

// ==========================================
// 2. EVENTOS GERAIS
// ==========================================
document.addEventListener("keydown", function (e) {
  if (e.key === "F2") abrirModalPagamento();
  if (e.key === "F4") abrirModalPesquisa();
  if (e.key === "Escape") fecharModais();
});

if (inputCodigo) {
  inputCodigo.addEventListener("keypress", function (e) {
    if (e.key === "Enter" && inputCodigo.value !== "") {
      processarInput();
    }
  });
}

// Botão Adicionar (Mouse)
const btnAdd = document.getElementById("btn-adicionar");
if (btnAdd) {
  btnAdd.addEventListener("click", function () {
    if (inputCodigo.value !== "") processarInput();
  });
}

// ==========================================
// 3. FUNÇÕES DE ENDEREÇO (API)
// ==========================================

async function carregarEnderecosCliente(id) {
  const lista = document.getElementById("listaEnderecosSalvos");
  lista.innerHTML = "Carregando...";

  try {
    const res = await fetch(`${BASE_URL}api/enderecos?id=${id}`);
    const enderecos = await res.json();

    lista.innerHTML = "";

    if (!enderecos || enderecos.length === 0) {
      lista.innerHTML = "<i>Nenhum endereço salvo.</i>";
      const radioNovo = document.getElementById("radioNovoEnd");
      if (radioNovo) radioNovo.click();
    } else {
      enderecos.forEach((end) => {
        const texto = `${end.logradouro}, ${end.numero} - ${end.bairro}`;
        const label = end.tipo_desc
          ? `<small style='color:#2980b9; font-weight:bold; display:block;'>${end.tipo_desc}</small>`
          : "";

        const div = document.createElement("div");
        div.className = "item-endereco";
        div.style.padding = "10px";
        div.style.borderBottom = "1px solid #eee";
        div.style.cursor = "pointer";

        // Ao clicar na div, seleciona o radio
        div.onclick = function () {
          selecionarRadio(this);
        };

        div.innerHTML = `
                    ${label}
                    <div style="display:flex; align-items:center;">
                        <input type="radio" name="endereco_selecionado" value="${end.id}" data-texto="${texto}" onchange="mostrarFormNovo(false)">
                        <span style="margin-left:5px;">${texto}</span>
                    </div>
                `;
        lista.appendChild(div);
      });
    }
  } catch (e) {
    console.error(e);
    lista.innerHTML = "Erro ao buscar endereços.";
    showToast("Erro ao buscar endereços", "error");
  }
}

function selecionarRadio(div) {
  const radio = div.querySelector("input");
  if (radio) {
    radio.checked = true;
    mostrarFormNovo(false);
  }
}

function mostrarFormNovo(mostrar) {
  const form = document.getElementById("formNovoEndereco");
  if (form) form.style.display = mostrar ? "block" : "none";
}

// ==========================================
// 4. LÓGICA DE CARRINHO E BUSCA
// ==========================================

function processarInput() {
  let rawValue = inputCodigo.value.trim();
  let qtdFinal = parseInt(inputQtd.value) || 1;
  let codigoFinal = rawValue;

  // Lógica de Multiplicação (Ex: 5*789...)
  if (rawValue.includes("*")) {
    const partes = rawValue.split("*");
    if (partes.length === 2) {
      const qtdMulti = parseInt(partes[0]);
      const codMulti = partes[1];
      if (!isNaN(qtdMulti) && qtdMulti > 0) {
        qtdFinal = qtdMulti;
        codigoFinal = codMulti;
      }
    }
  }
  buscarProduto(codigoFinal, qtdFinal);
}

async function buscarProduto(codigo, quantidade = 1) {
  try {
    const res = await fetch(`${BASE_URL}api/buscar?codigo=${codigo}`);
    const data = await res.json();

    if (data.sucesso) {
      adicionarAoCarrinho(data.produto, quantidade);
      inputCodigo.value = "";
      inputQtd.value = "1";
      inputCodigo.focus();
      fecharModais(); // Fecha pesquisa se estiver aberta
    } else {
      showToast("Produto não encontrado!", "warning");
      inputCodigo.select();
    }
  } catch (error) {
    console.error("Erro ao buscar:", error);
    showToast("Erro ao comunicar com o servidor", "error");
  }
}

function adicionarAoCarrinho(produto, qtdAdicional) {
  const itemExistente = carrinho.find((item) => item.id == produto.id);
  const qtdAdd = parseInt(qtdAdicional);

  if (itemExistente) {
    itemExistente.qtd += qtdAdd;
    itemExistente.subtotal = itemExistente.qtd * itemExistente.preco;
  } else {
    carrinho.push({
      id: produto.id,
      nome: produto.nome,
      preco: parseFloat(produto.preco),
      qtd: qtdAdd,
      subtotal: parseFloat(produto.preco) * qtdAdd,
    });
  }
  atualizarTela(produto.nome);
}

function atualizarTela(ultimoNome) {
  tabela.innerHTML = "";
  totalVenda = 0;

  carrinho.forEach((item, index) => {
    totalVenda += item.subtotal;
    const tr = document.createElement("tr");
    tr.innerHTML = `
            <td>${index + 1}</td>
            <td>${item.nome}</td>
            <td>${item.qtd}</td>
            <td>R$ ${item.preco.toFixed(2)}</td>
            <td>R$ ${item.subtotal.toFixed(2)}</td>
        `;
    tabela.prepend(tr);
  });

  const totalFormatado = totalVenda.toLocaleString("pt-BR", {
    style: "currency",
    currency: "BRL",
  });
  divTotal.innerText = totalFormatado;
  divUltimo.innerText = ultimoNome;
}

// ==========================================
// 5. MODAIS (PESQUISA E PAGAMENTO)
// ==========================================

function abrirModalPesquisa() {
  modalPesquisa.classList.add("active");
  modalPesquisa.style.display = "flex";
  inputPesquisa.value = "";
  inputPesquisa.focus();
  carregarListaProdutos("");
}

function abrirModalPagamento() {
  if (carrinho.length === 0) {
    showToast("Carrinho vazio!", "warning");
    return;
  }
  modalPagamento.style.display = "flex";
  document.getElementById("pagamento-total").innerText = divTotal.innerText;
  inputRecebido.value = "";
  displayTroco.innerText = "R$ 0,00";

  // Reseta cliente
  inputCliente.value = "";
  hiddenIdCliente.value = "";
  listaClientesResult.style.display = "none";

  // Reseta Entrega para Padrão (Retirada)
  const radioRetirada = document.querySelector(
    'input[name="tipo_entrega_main"][value="retirada"]',
  );
  if (radioRetirada) {
    radioRetirada.checked = true;
    // Função definida no HTML
    if (typeof mudarTipoEntrega === "function") mudarTipoEntrega("retirada");
  }

  inputRecebido.focus();
}

function fecharModais() {
  if (modalPesquisa) {
    modalPesquisa.classList.remove("active");
    modalPesquisa.style.display = "none";
  }
  if (modalPagamento) modalPagamento.style.display = "none";

  const modalEnd = document.getElementById("modalEntrega");
  if (modalEnd) modalEnd.style.display = "none";

  inputCodigo.focus();
}

function fecharModalPagamento() {
  modalPagamento.style.display = "none";
  inputCodigo.focus();
}

// --- PESQUISA DE PRODUTOS ---
let timeoutPesquisa;
inputPesquisa.addEventListener("keyup", function () {
  clearTimeout(timeoutPesquisa);
  timeoutPesquisa = setTimeout(
    () => carregarListaProdutos(inputPesquisa.value),
    300,
  );
});

async function carregarListaProdutos(termo) {
  try {
    if (termo === "")
      listaPesquisa.innerHTML =
        '<div style="padding:15px; text-align:center; color:#999;">Carregando...</div>';

    const res = await fetch(`${BASE_URL}api/pesquisar?termo=${termo}`);
    const data = await res.json();

    listaPesquisa.innerHTML = "";

    if (data.produtos && data.produtos.length > 0) {
      data.produtos.forEach((p) => {
        const div = document.createElement("div");
        div.style.padding = "10px";
        div.style.borderBottom = "1px solid #eee";
        div.style.cursor = "pointer";
        div.style.display = "flex";
        div.style.justifyContent = "space-between";

        const imgPath = p.imagem
          ? `${BASE_URL}public/uploads/${p.imagem}`
          : "https://via.placeholder.com/40?text=Img";

        div.innerHTML = `
                    <div style="display:flex; align-items:center; gap:12px;">
                        <img src="${imgPath}" width="45" height="45" style="object-fit:cover; border-radius:4px;">
                        <div>
                            <span style="font-weight:500;">${p.nome}</span><br>
                            <small style="color:#888;">${p.codigo_barras}</small>
                        </div>
                    </div>
                    <b style="color:#27ae60;">R$ ${parseFloat(p.preco).toFixed(2)}</b>
                `;

        div.onclick = () => {
          let qtdAtual = parseInt(inputQtd.value) || 1;
          buscarProduto(p.codigo_barras, qtdAtual);
        };
        listaPesquisa.appendChild(div);
      });
    } else {
      listaPesquisa.innerHTML =
        '<div style="padding:15px; text-align:center; color:#999;">Nenhum produto encontrado.</div>';
    }
  } catch (error) {
    console.error(error);
  }
}

// --- PESQUISA DE CLIENTES ---
let timeoutCliente;
inputCliente.addEventListener("keyup", function () {
  clearTimeout(timeoutCliente);
  const termo = inputCliente.value;

  if (termo === "") {
    hiddenIdCliente.value = "";
    listaClientesResult.style.display = "none";
    return;
  }

  timeoutCliente = setTimeout(async () => {
    const res = await fetch(`${BASE_URL}api/pesquisar_cliente?termo=${termo}`);
    const data = await res.json();

    listaClientesResult.innerHTML = "";
    if (data.clientes && data.clientes.length > 0) {
      listaClientesResult.style.display = "block";
      data.clientes.forEach((c) => {
        const div = document.createElement("div");
        div.style.padding = "10px";
        div.style.borderBottom = "1px solid #eee";
        div.style.cursor = "pointer";
        div.innerHTML = `<b>${c.nome}</b> <small>(${c.cpf || "S/ CPF"})</small>`;

        div.onmousedown = function () {
          inputCliente.value = c.nome;
          hiddenIdCliente.value = c.id;
          listaClientesResult.style.display = "none";
        };
        listaClientesResult.appendChild(div);
      });
    } else {
      listaClientesResult.style.display = "none";
    }
  }, 300);
});

// --- CÁLCULOS ---
function calcularTroco() {
  const recebido = parseFloat(inputRecebido.value);
  if (isNaN(recebido)) {
    displayTroco.innerText = "...";
    return;
  }
  const troco = recebido - totalVenda;
  if (troco >= 0) {
    displayTroco.style.color = "#27ae60";
    displayTroco.innerText = troco.toLocaleString("pt-BR", {
      style: "currency",
      currency: "BRL",
    });
  } else {
    displayTroco.style.color = "#c0392b";
    displayTroco.innerText =
      "Falta " +
      Math.abs(troco).toLocaleString("pt-BR", {
        style: "currency",
        currency: "BRL",
      });
  }
}

if (selectPagamento) {
  selectPagamento.addEventListener("change", function () {
    if (this.value === "Dinheiro") {
      inputRecebido.value = "";
      displayTroco.innerText = "R$ 0,00";
      inputRecebido.focus();
    } else {
      inputRecebido.value = totalVenda.toFixed(2);
      displayTroco.innerText = "---";
    }
    if (areaDinheiro) areaDinheiro.style.display = "block";
  });
}

if (inputRecebido) {
  inputRecebido.addEventListener("input", calcularTroco);
}

// ==========================================
// 6. FINALIZAR VENDA
// ==========================================

// --- LÓGICA PARA FINALIZAR A VENDA E IMPRIMIR ---
async function confirmarVenda() {
  // Verifica se o carrinho tem itens (assumindo que sua variável global chama 'carrinho')
  if (!carrinho || carrinho.length === 0) {
    showToast("Adicione produtos antes de finalizar.", "error");
    return;
  }

  const btnFinalizar = document.querySelector(
    'button[onclick="confirmarVenda()"]',
  );
  btnFinalizar.innerHTML =
    '<i class="fas fa-spinner fa-spin"></i> Processando...';
  btnFinalizar.disabled = true;

  // 1. Monta os dados para enviar para a API (PHP)
  const dadosVenda = {
    cliente_id: document.getElementById("id-cliente-selecionado").value || null,
    forma_pagamento: document.getElementById("forma-pagamento").value,
    valor_recebido: parseFloat(
      document.getElementById("valor-recebido").value || 0,
    ),
    // Aqui pegamos o valor total direto do texto (ex: R$ 150,00 -> 150.00)
    total: parseFloat(
      document
        .getElementById("pagamento-total")
        .innerText.replace("R$ ", "")
        .replace(".", "")
        .replace(",", "."),
    ),
    tipo_entrega: tipoEntregaAtual,
    endereco_entrega_texto: enderecoTextoAtual,
    data_entrega: document.getElementById("data-entrega").value || null,
    itens: carrinho, // Envia o array de produtos
  };

  try {
    // 2. Envia para o servidor salvar no banco
    const res = await fetch(`${BASE_URL}api/finalizarVenda`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(dadosVenda),
    });
    const dados = await res.json();

    if (dados.sucesso) {
      // 3. VENDA SALVA NO BANCO! Agora preenchemos o cupom para imprimir
      showToast("Venda finalizada com sucesso!", "success");

      // Preenche dados do cliente
      document.getElementById("cupom-data").innerText =
        new Date().toLocaleString("pt-BR");
      document.getElementById("cupom-cliente").innerText =
        document.getElementById("input-busca-cliente").value ||
        "Consumidor Final";

      // Preenche os itens
      let itensHTML = "";
      carrinho.forEach((item) => {
        itensHTML += `
                        <tr>
                            <td colspan="3"><b>${item.nome}</b></td>
                        </tr>
                        <tr>
                            <td>${item.quantidade}x</td>
                            <td>R$ ${parseFloat(item.preco).toFixed(2).replace(".", ",")}</td>
                            <td style="text-align:right">R$ ${(item.quantidade * item.preco).toFixed(2).replace(".", ",")}</td>
                        </tr>
                    `;
      });

      // Se for entrega, adiciona uma linha de observação no cupom
      if (tipoEntregaAtual === "entrega") {
        itensHTML += `
                        <tr><td colspan="3"><hr></td></tr>
                        <tr><td colspan="3"><b>ENTREGA:</b><br>${enderecoTextoAtual}</td></tr>
                    `;
      }

      document.getElementById("cupom-itens").innerHTML = itensHTML;

      // Preenche Totais
      document.getElementById("cupom-total").innerText =
        document.getElementById("pagamento-total").innerText;
      document.getElementById("cupom-recebido").innerText =
        document.getElementById("forma-pagamento").value;

      // 4. Chama a tela de impressão!
      setTimeout(() => {
        window.print();
        // Depois que imprimir, recarrega a página para o próximo cliente
        window.location.reload();
      }, 500);
    } else {
      showToast(dados.msg || "Erro ao salvar a venda no servidor.", "error");
      btnFinalizar.innerHTML = '<i class="fas fa-check"></i> Finalizar Venda';
      btnFinalizar.disabled = false;
    }
  } catch (e) {
    console.error(e);
    showToast("Erro de conexão com o servidor.", "error");
    btnFinalizar.innerHTML = '<i class="fas fa-check"></i> Finalizar Venda';
    btnFinalizar.disabled = false;
  }
}

function gerarNota(valorRecebido, nomeCliente, formaPagamento, statusVenda) {
  const dataHora = new Date().toLocaleString("pt-BR");
  const troco = valorRecebido - totalVenda;

  document.getElementById("cupom-data").innerText = dataHora;
  document.getElementById("cupom-cliente").innerText = nomeCliente.substring(
    0,
    25,
  );

  const tbody = document.getElementById("cupom-itens");
  tbody.innerHTML = "";
  carrinho.forEach((item) => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
            <td style="padding:2px 0;">${item.nome.substring(0, 18)}</td>
            <td align="right">${item.qtd}</td>
            <td align="right">${item.preco.toFixed(2)}</td>
            <td align="right">${item.subtotal.toFixed(2)}</td>
        `;
    tbody.appendChild(tr);
  });

  document.getElementById("cupom-total").innerText = totalVenda.toLocaleString(
    "pt-BR",
    { style: "currency", currency: "BRL" },
  );

  if (formaPagamento === "Dinheiro") {
    document.getElementById("cupom-recebido").innerText =
      valorRecebido.toLocaleString("pt-BR", {
        style: "currency",
        currency: "BRL",
      });

    const labelTroco = document.getElementById("cupom-troco");
    labelTroco.parentElement.style.display = "flex";

    if (valorRecebido >= totalVenda) {
      labelTroco.previousElementSibling.innerText = "Troco:";
      labelTroco.innerText = (valorRecebido - totalVenda).toLocaleString(
        "pt-BR",
        { style: "currency", currency: "BRL" },
      );
    } else {
      labelTroco.previousElementSibling.innerText = "RESTANTE:";
      labelTroco.innerText = (totalVenda - valorRecebido).toLocaleString(
        "pt-BR",
        { style: "currency", currency: "BRL" },
      );
    }
  } else {
    document.getElementById("cupom-recebido").innerText = formaPagamento;
    document.getElementById("cupom-troco").parentElement.style.display = "none";
  }

  window.print();
}
