let produtosAdicionados = [];

document.addEventListener("DOMContentLoaded", () => {
    const selectCliente = document.getElementById("selectCliente");
    const selectProduto = document.getElementById("selectProduto");
    const btnAdicionar = document.getElementById("btnAdicionarProduto");
    const parcelasInput = document.getElementById("parcelas");
    const modalParcelamento = document.getElementById("parcelamentoModal");
    const tabelaVencimentos = document.querySelector(
        "#tabelaVencimentos tbody"
    );

    atualizarTabela();

    selectCliente.addEventListener("change", () => {
        const selected = selectCliente.selectedOptions[0];
        document.getElementById("clienteNome").innerText =
            selected.dataset.nome || "-";
        document.getElementById("clienteTelefone").innerText =
            selected.dataset.telefone || "-";
    });

    selectProduto.addEventListener("change", () => {
        btnAdicionar.disabled = !selectProduto.value;
    });

    parcelasInput.addEventListener("input", () => {
        inicializarParcelas();
    });

    modalParcelamento.addEventListener("shown.bs.modal", () => {
        inicializarParcelas();
    });

    tabelaVencimentos.addEventListener("click", (e) => {
        if (e.target.closest(".btn-remover-parcela")) {
            const tr = e.target.closest("tr");
            tr.remove();
            atualizarNumerosParcelas();
            parcelasInput.value = tabelaVencimentos.children.length;
            atualizarValorParcela();
        }
    });

    tabelaVencimentos.addEventListener("input", (e) => {
        if (e.target.classList.contains("valor-parcela")) {
            recalcularParcelasAPartirDeEdicao(e.target);
        }
    });

    function adicionarLinhaParcela(data = "", valor = 0) {
        const numeroParcela = tabelaVencimentos.children.length + 1;
        const valorTotal = obterValorTotalVenda();

        const tr = document.createElement("tr");
        tr.innerHTML = `
        <td>${numeroParcela} x</td>
            <td>
                <input type="date" class="form-control form-control-sm data-vencimento" value="${data}" required>
            </td>        
            <td>
            <input 
                type="number" 
                step="0.01" 
                class="form-control form-control-sm valor-parcela" 
                value="${valor.toFixed(2)}"
                ${numeroParcela === 1 ? `min="${valorTotal.toFixed(2)}"` : ""}
            >
        </td>
        <td style="text-align:center;">
            <button type="button" class="btn btn-danger btn-sm btn-remover-parcela">
                <i class="fas fa-trash-alt"></i>
            </button>
        </td>
    `;
        tabelaVencimentos.appendChild(tr);

        const linhas = tabelaVencimentos.querySelectorAll("tr");
        const botoes = tabelaVencimentos.querySelectorAll(
            ".btn-remover-parcela"
        );

        botoes.forEach((btn) => {
            btn.disabled = linhas.length === 1;
        });

        const primeiraLinha = tabelaVencimentos.querySelector("tr");
        if (primeiraLinha) {
            const inputPrimeiraParcela =
                primeiraLinha.querySelector(".valor-parcela");
            if (linhas.length === 1) {
                // Só 1 parcela => readonly true + valor fixo = valor total + disabled
                inputPrimeiraParcela.value = valorTotal.toFixed(2);
                inputPrimeiraParcela.readOnly = true;
                inputPrimeiraParcela.disabled = true; // <-- aqui
                inputPrimeiraParcela.classList.add("bg-light");
                inputPrimeiraParcela.min = valorTotal.toFixed(2);
            } else {
                inputPrimeiraParcela.readOnly = false;
                inputPrimeiraParcela.disabled = false;
                inputPrimeiraParcela.classList.remove("bg-light");
                inputPrimeiraParcela.min = "0";
            }
        }
    }

    document.addEventListener("click", function (e) {
        if (e.target.closest(".btn-remover-parcela")) {
            const btn = e.target.closest(".btn-remover-parcela");
            btn.closest("tr").remove();

            // Atualiza diretamente após remover
            const linhas = tabelaVencimentos.querySelectorAll("tr");
            const botoes = tabelaVencimentos.querySelectorAll(
                ".btn-remover-parcela"
            );

            botoes.forEach((btn) => {
                btn.disabled = linhas.length === 1;
            });
        }
    });
    function validarDatasAntesDeSalvar() {
        const inputsDatas = document.querySelectorAll(".data-vencimento");

        for (const input of inputsDatas) {
            if (!input.value) {
                alert("Por favor, preencha todas as datas de vencimento.");
                input.focus();
                return false; // impede o envio do formulário
            }
        }
        return true; // todos preenchidos, pode enviar
    }
    function inicializarParcelas() {
        const parcelasCount = parseInt(parcelasInput.value) || 1;
        const valorTotal = obterValorTotalVenda();
        const tabelaVencimentos = document.querySelector(
            "#tabelaVencimentos tbody"
        );
        const linhasAtuais = Array.from(tabelaVencimentos.children);

        // Se precisar adicionar mais linhas
        if (parcelasCount > linhasAtuais.length) {
            const valorParcela = valorTotal / parcelasCount;

            // Preservar datas e valores já existentes
            const datasExistentes = linhasAtuais.map(
                (tr) => tr.querySelector(".data-vencimento").value
            );
            const valoresExistentes = linhasAtuais.map((tr) => {
                const valStr = tr
                    .querySelector(".valor-parcela")
                    .value.replace("R$ ", "")
                    .replace(/\./g, "")
                    .replace(",", ".");
                return parseFloat(valStr) || 0;
            });

            // Limpar tbody para reconstruir
            tabelaVencimentos.innerHTML = "";

            for (let i = 0; i < parcelasCount; i++) {
                const data = datasExistentes[i] || "";
                const valor =
                    valoresExistentes[i] || valorTotal / parcelasCount;
                adicionarLinhaParcela(data, valor);
            }
        } else {
            // Se diminuiu o número de parcelas, remove as últimas
            while (tabelaVencimentos.children.length > parcelasCount) {
                tabelaVencimentos.removeChild(tabelaVencimentos.lastChild);
            }
        }

        atualizarNumerosParcelas();
        atualizarValorParcela();

        // Bloquear input da primeira parcela se só tiver 1 parcela
        const linhas = tabelaVencimentos.querySelectorAll("tr");
        if (linhas.length === 1) {
            const primeiraParcelaInput =
                linhas[0].querySelector(".valor-parcela");
            primeiraParcelaInput.readOnly = true;
            primeiraParcelaInput.disabled = true;
            primeiraParcelaInput.classList.add("bg-light");
            primeiraParcelaInput.min = valorTotal.toFixed(2);
        } else {
            linhas.forEach((tr) => {
                const input = tr.querySelector(".valor-parcela");
                input.readOnly = false;
                input.disabled = false;
                input.classList.remove("bg-light");
                input.min = "0";
            });
        }

        // Controle do botão da primeira linha
        const botoesRemover = tabelaVencimentos.querySelectorAll(
            ".btn-remover-parcela"
        );
        if (botoesRemover.length > 0) {
            botoesRemover[0].disabled = linhas.length === 1;
            for (let i = 1; i < botoesRemover.length; i++) {
                botoesRemover[i].disabled = false;
            }
        }
    }

    function atualizarNumerosParcelas() {
        Array.from(tabelaVencimentos.children).forEach((tr, index) => {
            tr.children[0].textContent = `${index + 1} x`;
        });
    }

    function obterValorTotalVenda() {
        const valorTotalText = document.getElementById("valorTotal").innerText;
        return (
            parseFloat(valorTotalText.replace(/\./g, "").replace(",", ".")) || 0
        );
    }

    function atualizarValorParcela() {
        const valorTotal = obterValorTotalVenda();
        const linhas = tabelaVencimentos.querySelectorAll("tr");
        const totalParcelas = linhas.length || 1;
        const valorParcela = valorTotal / totalParcelas;

        document.getElementById("valorParcela").innerText = valorParcela
            .toFixed(2)
            .replace(".", ",");
        document.getElementById("valorTotalVenda").innerText = valorTotal
            .toFixed(2)
            .replace(".", ",");

        linhas.forEach((tr) => {
            const input = tr.querySelector(".valor-parcela");
            if (input && !input.disabled) {
                input.value = valorParcela.toFixed(2);
            }
        });
    }

    function parseValorMonetario(valorStr) {
        if (!valorStr) return 0;
        // Remove espaços e converte vírgula para ponto, se houver
        valorStr = valorStr
            .replace(/\s/g, "")
            .replace(",", ".")
            .replace("R$", "");
        const valor = parseFloat(valorStr);
        return isNaN(valor) ? 0 : valor;
    }

    function configurarListenersDeParcelas() {
        const inputs = document.querySelectorAll(".valor-parcela");
        inputs.forEach((input) => {
            input.removeEventListener("input", onInputEditado);
            input.addEventListener("input", onInputEditado);
        });
    }

    function onInputEditado(e) {
        recalcularParcelasAPartirDeEdicao(e.target);
    }
    function recalcularParcelasAPartirDeEdicao(inputEditado) {
        const linhas = Array.from(
            document.querySelectorAll("#tabelaVencimentos tbody tr")
        );
        const inputs = linhas.map((tr) => tr.querySelector(".valor-parcela"));

        inputEditado.setAttribute("data-editado", "true");

        const valorTotal = obterValorTotalVenda();

        const indexEditado = inputs.indexOf(inputEditado);

        let somaAnteriorEAAtual = 0;
        for (let i = 0; i <= indexEditado; i++) {
            const valor = parseValorMonetario(inputs[i].value);
            somaAnteriorEAAtual += valor;
        }

        const restante = valorTotal - somaAnteriorEAAtual;

        if (restante < 0) {
            alert("A soma das parcelas excede o valor total.");
            inputEditado.removeAttribute("data-editado");
            atualizarValorParcela();
            return;
        }

        const posteriores = inputs.slice(indexEditado + 1);

        if (posteriores.length === 0) return;

        let valorPadrao = parseFloat(
            (restante / posteriores.length).toFixed(2)
        );
        let somaDistribuida = valorPadrao * posteriores.length;
        let diferenca = restante - somaDistribuida;

        posteriores.forEach((input, index) => {
            let valorFinal = valorPadrao;
            if (index === posteriores.length - 1) {
                valorFinal += diferenca;
            }
            input.value = valorFinal.toFixed(2);
            input.removeAttribute("data-editado");
        });

        // 🔐 Impedir valor total inferior ao necessário
        const somaTotalFinal = inputs.reduce((acc, input) => {
            return acc + parseValorMonetario(input.value);
        }, 0);

        if (somaTotalFinal < valorTotal) {
            alert("A soma das parcelas está abaixo do valor total da venda.");
            atualizarValorParcela(); // opcional: volta ao estado original
            return;
        }

        // Estética e bloqueio do último campo
        inputs.forEach((input) => {
            input.removeAttribute("readonly");
            input.classList.remove("bg-light");
        });

        const ultimoInput = inputs[inputs.length - 1];
        ultimoInput.setAttribute("readonly", true);
        ultimoInput.classList.add("bg-light");

        document.getElementById("valorTotalVenda").innerText = valorTotal
            .toFixed(2)
            .replace(".", ",");

        configurarListenersDeParcelas();
    }

    function adicionarProduto() {
        const select = document.getElementById("selectProduto");
        const selected = select.selectedOptions[0];
        if (!selected) return;

        const id = selected.value;
        if (produtosAdicionados.find((p) => p.id == id)) {
            alert("Produto já adicionado");
            return;
        }

        produtosAdicionados.push({
            id,
            nome: selected.dataset.nome,
            preco: parseFloat(selected.dataset.preco),
            quantidade: 1,
        });

        atualizarTabela();
        select.selectedIndex = 0;
        btnAdicionar.disabled = true;
    }

    function atualizarTabela() {
        const tbody = document.querySelector("#tabelaProdutos tbody");
        tbody.innerHTML = "";

        produtosAdicionados.forEach((p) => {
            const subtotal = p.preco * p.quantidade;
            const tr = document.createElement("tr");

            tr.innerHTML = `
            <td>${p.nome}</td>
            <td>
                <input 
                    type="text" 
                    class="form-control form-control-sm preco-unitario" 
                    value="${p.preco.toFixed(2).replace(".", ",")}" 
                    data-id="${p.id}">
            </td>
            <td>
                <input 
                    type="number" 
                    min="1" 
                    value="${p.quantidade}" 
                    style="width:60px" 
                    data-id="${p.id}" 
                    class="quantidade-produto">
            </td>
            <td>R$ ${subtotal.toFixed(2).replace(".", ",")}</td>
            <td style="text-align:center; vertical-align: middle;">
                <button class="btn btn-danger btn-sm px-3 py-1" onclick="removerProduto('${
                    p.id
                }')">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        `;
            tbody.appendChild(tr);
        });

        // Reatribuir eventos

        adicionarEventosTabela();

        atualizarResumo();
    }

    function adicionarEventosTabela() {
        // Evento para alterar preço ao sair do campo (blur)
        document.querySelectorAll(".preco-unitario").forEach((input) => {
            input.addEventListener("blur", function () {
                const id = this.dataset.id;
                alterarPreco(id, this.value);
            });
        });

        // Evento para alterar quantidade ao mudar valor
        document.querySelectorAll(".quantidade-produto").forEach((input) => {
            input.addEventListener("input", function () {
                const id = this.dataset.id;
                const valor = this.value;

                // Só atualiza os dados internos e subtotal/resumo, sem mexer no valor do input
                alterarQuantidade(id, valor);
            });
        });
    }

    function alterarPreco(id, valor) {
        const preco = parseFloat(valor.replace(",", "."));
        if (isNaN(preco) || preco < 0) return;

        const prod = produtosAdicionados.find((p) => p.id == id);
        if (!prod) return;

        prod.preco = preco;
        atualizarTabela();
    }

    function alterarQuantidade(id, valor) {
        // Se o valor estiver vazio, zera a quantidade internamente e atualiza subtotal vazio
        if (valor.trim() === "") {
            const prod = produtosAdicionados.find((p) => p.id == id);
            if (!prod) return;

            prod.quantidade = 0; // ou pode remover o produto, ou manter 0

            const inputQuantidade = document.querySelector(
                `.quantidade-produto[data-id="${id}"]`
            );
            if (!inputQuantidade) return;

            const tr = inputQuantidade.closest("tr");
            const subtotalCell = tr.querySelector("td:nth-child(4)");
            subtotalCell.textContent = ""; // deixa vazio

            atualizarResumo(false);
            return;
        }

        const qtd = parseInt(valor);
        if (isNaN(qtd) || qtd < 1) return;

        const prod = produtosAdicionados.find((p) => p.id == id);
        if (!prod) return;

        prod.quantidade = qtd;

        const inputQuantidade = document.querySelector(
            `.quantidade-produto[data-id="${id}"]`
        );
        if (!inputQuantidade) return;

        const tr = inputQuantidade.closest("tr");
        const subtotalCell = tr.querySelector("td:nth-child(4)");
        const subtotal = prod.preco * prod.quantidade;

        subtotalCell.textContent = `R$ ${subtotal
            .toFixed(2)
            .replace(".", ",")}`;

        atualizarResumo(false);
    }

    function removerProduto(id) {
        produtosAdicionados = produtosAdicionados.filter((p) => p.id != id);
        atualizarTabela();
    }

    function atualizarResumo(atualizarInputs = true) {
        // Soma quantidade ignorando produtos com quantidade zero
        const totalProdutos = produtosAdicionados.reduce(
            (acc, p) => acc + (p.quantidade > 0 ? p.quantidade : 0),
            0
        );
        // Soma valores, ignorando produtos com quantidade zero
        const valorTotal = produtosAdicionados.reduce(
            (acc, p) => acc + (p.quantidade > 0 ? p.preco * p.quantidade : 0),
            0
        );

        document.getElementById("totalProdutos").innerText =
            totalProdutos > 0 ? totalProdutos : "";
        document.getElementById("valorTotal").innerText =
            valorTotal > 0 ? valorTotal.toFixed(2).replace(".", ",") : "";

        if (atualizarInputs) {
            atualizarValorParcela();
        }

        const btnPagamento = document.getElementById("btnPagamento");
        btnPagamento.disabled =
            produtosAdicionados.length === 0 || totalProdutos === 0;
    }

    document.addEventListener("input", (e) => {
        if (e.target.classList.contains("preco-unitario")) {
            const id = e.target.dataset.id;
            const raw = e.target.value.replace(/[^\d,]/g, "").replace(",", ".");
            const preco = parseFloat(raw);

            const prod = produtosAdicionados.find((p) => p.id == id);
            if (!prod) return;

            if (!isNaN(preco) && preco > 0) {
                prod.preco = preco;

                // Atualiza subtotal na linha sem recriar inputs
                const tr = e.target.closest("tr");
                const subtotalCell = tr.querySelector("td:nth-child(4)");
                const subtotal = prod.preco * prod.quantidade;
                subtotalCell.textContent = `R$ ${subtotal
                    .toFixed(2)
                    .replace(".", ",")}`;

                // Atualiza o resumo, mas sem alterar inputs (sem chamar atualizarTabela)
                atualizarResumo(false);
            }
        }
    });
    if (typeof vendaExistente !== "undefined") {
        console.log("📝 Venda existente detectada:", vendaExistente);

        const clienteOption = document.querySelector(
            `#selectCliente option[value="${vendaExistente.customer_id}"]`
        );
        if (clienteOption) {
            clienteOption.selected = true;
            document.getElementById("clienteNome").innerText =
                clienteOption.dataset.nome || "-";
            document.getElementById("clienteTelefone").innerText =
                clienteOption.dataset.telefone || "-";

            console.log("👤 Cliente selecionado:");
            console.log("  - Nome:", clienteOption.dataset.nome);
            console.log("  - Telefone:", clienteOption.dataset.telefone);
        } else {
            console.warn("⚠️ Cliente não encontrado na lista de opções.");
        }

        produtosAdicionados = vendaExistente.items.map((item) => {
            let precoParseado = parseFloat(item.unit_price);
            if (isNaN(precoParseado)) {
                console.warn(
                    `⚠️ Preço inválido para produto ${item.product_id}, setando 0`
                );
                precoParseado = 0;
            }

            let quantidadeParseada = parseInt(item.quantity);
            if (isNaN(quantidadeParseada)) {
                console.warn(
                    `⚠️ Quantidade inválida para produto ${item.product_id}, setando 0`
                );
                quantidadeParseada = 0;
            }

            const produtoInfo = produtosDisponiveis.find(
                (p) => p.id == item.product_id
            );

            const produtoFinal = {
                id: item.product_id,
                nome: produtoInfo ? produtoInfo.name : "Produto não encontrado",
                preco: precoParseado,
                quantidade: quantidadeParseada,
            };

            console.log("📦 Produto adicionado:", produtoFinal);
            return produtoFinal;
        });

        atualizarTabela();

        const parcelasInput = document.getElementById("parcelas");
        const tabelaVencimentos = document.querySelector(
            "#tabelaVencimentos tbody"
        );

        const parcelas = vendaExistente.sale_installments || [];
        console.log("💳 Parcelas carregadas da venda:", parcelas);

        parcelasInput.value = parcelas.length;

        tabelaVencimentos.innerHTML = "";
        parcelas.forEach((parcela, i) => {
            const valor = parseFloat(parcela.amount);
            console.log(
                `📅 Parcela ${i + 1}: Vencimento = ${
                    parcela.due_date
                }, Valor = ${valor}`
            );
            adicionarLinhaParcela(parcela.due_date, valor);
        });

        atualizarNumerosParcelas();
        atualizarValorParcela();

        document.getElementById("btnPagamento").disabled = false;
    }

    window.adicionarProduto = adicionarProduto;
    window.alterarQuantidade = alterarQuantidade;
    window.removerProduto = removerProduto;
    const btnSalvar = document.getElementById("btnSalvarVenda");
    const btnAtualizar = document.getElementById("btnAtualizarVenda");

    if (btnSalvar) {
        btnSalvar.addEventListener("click", salvarVenda);
    }

    if (btnAtualizar) {
        btnAtualizar.addEventListener("click", atualizarVenda);
    }
    async function salvarVenda() {
        console.log("Iniciando processo de salvar venda...");

        // Validação das datas antes de qualquer outra coisa
        const inputsDatas = document.querySelectorAll(".data-vencimento");
        for (const input of inputsDatas) {
            if (!input.value) {
                alert("Por favor, preencha todas as datas de vencimento.");
                input.focus();
                return; // interrompe o salvamento se alguma data estiver vazia
            }
        }

        const selectCliente = document.getElementById("selectCliente");
        const clienteId = selectCliente.value;
        console.log("Cliente selecionado:", clienteId);

        if (!clienteId) {
            alert("Selecione um cliente");
            return;
        }

        if (produtosAdicionados.length === 0) {
            alert("Adicione ao menos um produto");
            return;
        }

        const produtos = produtosAdicionados.map((p) => ({
            product_id: p.id,
            preco_unitario: p.preco,
            quantidade: p.quantidade,
        }));
        console.log("Produtos adicionados:", produtos);

        const parcelas = Array.from(
            document.querySelectorAll("#tabelaVencimentos tbody tr")
        ).map((tr, i) => {
            const data = tr.querySelector(".data-vencimento").value;
            const valor = parseFloat(
                tr.querySelector(".valor-parcela").value.replace(",", ".")
            );
            console.log(`Parcela ${i + 1}: Data: ${data}, Valor: ${valor}`);
            return { data_vencimento: data, valor };
        });

        const valorTotalText = document.getElementById("valorTotal").innerText;
        const total =
            parseFloat(valorTotalText.replace(/\./g, "").replace(",", ".")) ||
            0;
        console.log("Valor total calculado:", total);

        const somaParcelas = parcelas.reduce((acc, p) => acc + p.valor, 0);
        const arredondar = (num) => Math.round(num * 100) / 100;

        if (arredondar(somaParcelas) !== arredondar(total)) {
            alert(
                `A soma das parcelas (${somaParcelas.toFixed(
                    2
                )}) não corresponde ao valor total da venda (${total.toFixed(
                    2
                )}).`
            );
            return;
        }

        const dadosVenda = {
            customer_id: clienteId,
            produtos,
            total,
            parcelas,
            installments: parcelas.length,
            sale_date: new Date().toISOString().slice(0, 10),
        };
        console.log("Payload final da venda:", dadosVenda);

        const storeUrl = window.LaravelRoutes.storeSale;
        const token = window.LaravelRoutes.csrfToken;

        console.log("Endpoint de envio:", storeUrl);

        try {
            const response = await fetch(storeUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": token,
                },
                body: JSON.stringify(dadosVenda),
            });

            const contentType = response.headers.get("content-type") || "";
            console.log("Tipo de conteúdo retornado:", contentType);
            console.log("Status da resposta:", response.status);

            if (!response.ok) {
                const mensagem = contentType.includes("application/json")
                    ? (await response.json()).message
                    : await response.text();

                console.error("Erro do servidor:", mensagem);
                alert("Erro ao salvar venda: " + mensagem);
                return;
            }

            console.log("Venda salva com sucesso!");
            alert("Venda salva com sucesso!");
            window.location.reload();
        } catch (err) {
            console.error("Erro na requisição:", err);
            alert("Erro ao salvar venda: " + err.message || err);
        }
    }

    async function atualizarVenda() {
        const idVenda = document.getElementById("idVendaHidden").value;
        if (!idVenda) {
            alert("ID da venda não encontrado.");
            return;
        }

        // Verificação de datas vazias
        const inputsDatas = document.querySelectorAll(".data-vencimento");
        for (const input of inputsDatas) {
            if (!input.value) {
                alert("Por favor, preencha todas as datas de vencimento.");
                input.focus();
                return; // interrompe a atualização se alguma data estiver vazia
            }
        }

        const selectCliente = document.getElementById("selectCliente");
        const clienteId = selectCliente.value;

        if (!clienteId) {
            alert("Selecione um cliente");
            return;
        }

        if (produtosAdicionados.length === 0) {
            alert("Adicione ao menos um produto");
            return;
        }

        const produtos = produtosAdicionados.map((p) => ({
            product_id: p.id,
            preco_unitario: p.preco,
            quantidade: p.quantidade,
        }));

        const parcelas = Array.from(
            document.querySelectorAll("#tabelaVencimentos tbody tr")
        ).map((tr) => ({
            data_vencimento: tr.querySelector(".data-vencimento").value,
            valor: parseFloat(
                tr.querySelector(".valor-parcela").value.replace(",", ".")
            ),
        }));

        const valorTotalText = document.getElementById("valorTotal").innerText;
        const total =
            parseFloat(valorTotalText.replace(/\./g, "").replace(",", ".")) ||
            0;

        const dadosVenda = {
            customer_id: clienteId,
            produtos,
            total,
            parcelas,
            installments: parcelas.length,
            sale_date: new Date().toISOString().slice(0, 10),
        };

        const token = document.querySelector('meta[name="csrf-token"]').content;

        const url = `/sales/${idVenda}`;
        const response = await fetch(url, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": token,
            },
            body: JSON.stringify(dadosVenda),
        });

        const contentType = response.headers.get("content-type") || "";

        if (!response.ok) {
            const mensagem = contentType.includes("application/json")
                ? (await response.json()).message
                : await response.text();
            alert("Erro ao atualizar venda: " + mensagem);
            return;
        }

        alert("Venda atualizada com sucesso!");
        window.location.reload();
    }
});
