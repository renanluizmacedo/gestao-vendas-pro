@extends('adminlte::page')

@section('title', 'Nova Venda')

@section('content_header')

    <h1 class="font-weight-bold">Nova Venda</h1>
@stop

@section('content')

    <div class="container-fluid">

        {{-- Seleções no topo --}}
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <label for="selectCliente" class="form-label"><strong>Selecionar Cliente</strong></label>
                <select id="selectCliente" class="form-control">
                    <option selected disabled>Escolha um cliente</option>
                    @foreach ($customers as $cliente)
                        <option value="{{ $cliente->id }}" data-nome="{{ $cliente->name }}"
                            data-telefone="{{ $cliente->phone }}">
                            {{ $cliente->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label for="selectProduto" class="form-label"><strong>Selecionar Produto</strong></label>
                <select id="selectProduto" class="form-control">
                    <option selected disabled>Escolha um produto</option>
                    @foreach ($products as $produto)
                        <option value="{{ $produto->id }}" data-nome="{{ $produto->name }}"
                            data-preco="{{ $produto->price }}">
                            {{ $produto->name }} - R$ {{ number_format($produto->price, 2, ',', '.') }}
                        </option>
                    @endforeach
                </select>
                <button id="btnAdicionarProduto" class="btn btn-success btn-sm mt-2 w-100" onclick="adicionarProduto()"
                    disabled>
                    Adicionar Produto
                </button>
            </div>
        </div>

        {{-- Conteúdo dividido --}}
        <div class="row">
            {{-- Coluna esquerda --}}
            <div class="col-md-4 d-flex flex-column gap-3">

                {{-- Info Cliente --}}
                <div class="card border-info">
                    <div class="card-header bg-info text-white">Informações do Cliente</div>
                    <div class="card-body">
                        <p><strong>Nome:</strong> <span id="clienteNome">-</span></p>
                        <p><strong>Telefone:</strong> <span id="clienteTelefone">-</span></p>
                    </div>
                </div>

                {{-- Resumo da Venda --}}
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">Resumo da Venda</div>
                    <div class="card-body">
                        <p><strong>Total de Produtos:</strong> <span id="totalProdutos">0</span></p>
                        <p><strong>Valor Total:</strong> R$ <span id="valorTotal">0,00</span></p>
                    </div>
                </div>

                <button type="button" id="btnPagamento" class="btn btn-secondary mt-2" data-bs-toggle="modal"
                    data-bs-target="#parcelamentoModal" disabled>
                    Pagamento
                </button>

            </div>

            {{-- Coluna direita --}}
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-dark text-white">Produtos Adicionados</div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-striped table-bordered mb-0" id="tabelaProdutos">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Preço Unitário</th>
                                    <th>Quantidade</th>
                                    <th>Subtotal</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Modal Parcelamento --}}
    <div class="modal fade" id="parcelamentoModal" tabindex="-1" aria-labelledby="parcelamentoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title" id="parcelamentoModalLabel">Configurar Parcelamento</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <form id="formParcelamento">
                        <div class="mb-3">
                            <label for="parcelas" class="form-label">Número de Parcelas</label>
                            <select id="parcelas" class="form-control form-select-sm">
                                <option value="1">1x</option>
                                <option value="2">2x</option>
                                <option value="3">3x</option>
                                <option value="4">4x</option>
                                <option value="5">5x</option>
                                <option value="6">6x</option>
                                <option value="7">7x</option>
                                <option value="8">8x</option>
                                <option value="9">9x</option>
                                <option value="10">10x</option>
                                <option value="11">11x</option>
                                <option value="12">12x</option>
                                <option value="13">13x</option>
                                <option value="14">14x</option>
                                <option value="15">15x</option>
                                <option value="16">16x</option>
                                <option value="17">17x</option>
                                <option value="18">18x</option>
                                <option value="19">19x</option>
                                <option value="20">20x</option>
                                <option value="21">21x</option>
                                <option value="22">22x</option>
                                <option value="23">23x</option>
                                <option value="24">24x</option>
                            </select>

                        </div>

                        <p><strong>Valor Total da Venda:</strong> R$ <span id="valorTotalVenda">0,00</span></p>
                        <p hidden>
                            <strong>Valor da Parcela:</strong> R$ <span id="valorParcela">0,00</span>
                        </p>
                        <table class="table table-bordered w-100" id="tabelaVencimentos">
                            <thead>
                                <tr>
                                    <th>Parcela</th>
                                    <th>Data de Vencimento</th>
                                    <th>Valor da Parcela</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>


                    </form>
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" id="btnSalvarVenda">Salvar Venda</button>

                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let produtosAdicionados = [];

        document.addEventListener('DOMContentLoaded', () => {
            const selectCliente = document.getElementById('selectCliente');
            const selectProduto = document.getElementById('selectProduto');
            const btnAdicionar = document.getElementById('btnAdicionarProduto');
            const parcelasInput = document.getElementById('parcelas');
            const modalParcelamento = document.getElementById('parcelamentoModal');
            const tabelaVencimentos = document.querySelector('#tabelaVencimentos tbody');
            atualizarTabela();

            selectCliente.addEventListener('change', () => {
                const selected = selectCliente.selectedOptions[0];
                document.getElementById('clienteNome').innerText = selected.dataset.nome || '-';
                document.getElementById('clienteTelefone').innerText = selected.dataset.telefone || '-';
            });

            selectProduto.addEventListener('change', () => {
                btnAdicionar.disabled = !selectProduto.value;
            });

            parcelasInput.addEventListener('input', () => {
                inicializarParcelas();
            });

            modalParcelamento.addEventListener('shown.bs.modal', () => {
                inicializarParcelas();
            });

            tabelaVencimentos.addEventListener('click', (e) => {
                if (e.target.closest('.btn-remover-parcela')) {
                    const tr = e.target.closest('tr');
                    tr.remove();
                    atualizarNumerosParcelas();
                    parcelasInput.value = tabelaVencimentos.children.length;
                    atualizarValorParcela();
                }
            });

            tabelaVencimentos.addEventListener('input', (e) => {
                if (e.target.classList.contains('valor-parcela')) {
                    recalcularParcelasAPartirDeEdicao(e.target);
                }
            });


            function adicionarLinhaParcela(data = '', valor = 0) {
                const numeroParcela = tabelaVencimentos.children.length + 1;
                console.log('Valor para o input:', valor); // Verifique aqui

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${numeroParcela} x</td>
                    <td><input type="date" class="form-control form-control-sm data-vencimento" value="${data}"></td>
                    <td>
                        <input type="number" step="0.01" class="form-control form-control-sm valor-parcela" value="${valor.toFixed(2)}">
                    </td>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-danger btn-sm btn-remover-parcela">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                `;
                tabelaVencimentos.appendChild(tr);
            }


            function inicializarParcelas() {
                const parcelasCount = parseInt(parcelasInput.value) || 1;
                const valorTotal = obterValorTotalVenda();
                const tabelaVencimentos = document.querySelector('#tabelaVencimentos tbody');
                const linhasAtuais = Array.from(tabelaVencimentos.children);

                // Se precisar adicionar mais linhas
                if (parcelasCount > linhasAtuais.length) {
                    const valorParcela = valorTotal / parcelasCount;

                    // Preservar datas e valores já existentes
                    const datasExistentes = linhasAtuais.map(tr => tr.querySelector('.data-vencimento').value);
                    const valoresExistentes = linhasAtuais.map(tr => {
                        const valStr = tr.querySelector('.valor-parcela').value.replace('R$ ', '').replace(
                            /\./g, '').replace(',', '.');
                        return parseFloat(valStr) || 0;
                    });

                    // Limpar tbody para reconstruir
                    tabelaVencimentos.innerHTML = '';

                    for (let i = 0; i < parcelasCount; i++) {
                        const data = datasExistentes[i] || '';
                        const valor = valoresExistentes[i] || valorTotal / parcelasCount;
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
            }


            function atualizarNumerosParcelas() {
                Array.from(tabelaVencimentos.children).forEach((tr, index) => {
                    tr.children[0].textContent = `${index + 1} x`;
                });
            }

            function obterValorTotalVenda() {
                const valorTotalText = document.getElementById('valorTotal').innerText;
                return parseFloat(valorTotalText.replace(/\./g, '').replace(',', '.')) || 0;
            }

            function atualizarValorParcela() {
                const valorTotal = obterValorTotalVenda();
                const linhas = tabelaVencimentos.querySelectorAll('tr');
                const totalParcelas = linhas.length || 1;
                const valorParcela = valorTotal / totalParcelas;

                document.getElementById('valorParcela').innerText = valorParcela.toFixed(2).replace('.', ',');
                document.getElementById('valorTotalVenda').innerText = valorTotal.toFixed(2).replace('.', ',');

                linhas.forEach((tr) => {
                    const input = tr.querySelector('.valor-parcela');
                    if (input && !input.disabled) {
                        input.value = valorParcela.toFixed(2);
                    }
                });
            }

            function formatarValorMonetario(valor) {
                return `R$ ${valor.toFixed(2).replace('.', ',')}`;
            }

            function parseValorMonetario(valorStr) {
                if (!valorStr) return 0;
                // Remove espaços e converte vírgula para ponto, se houver
                valorStr = valorStr.replace(/\s/g, '').replace(',', '.').replace('R$', '');
                const valor = parseFloat(valorStr);
                return isNaN(valor) ? 0 : valor;
            }

            function configurarListenersDeParcelas() {
                const inputs = document.querySelectorAll('.valor-parcela');
                inputs.forEach((input) => {
                    input.removeEventListener('input', onInputEditado);
                    input.addEventListener('input', onInputEditado);
                });
            }

            function onInputEditado(e) {
                console.log('Input editado:', e.target);
                console.log('Valor digitado bruto:', e.target.value);
                recalcularParcelasAPartirDeEdicao(e.target);
            }

            function recalcularParcelasAPartirDeEdicao(inputEditado) {
                const linhas = Array.from(document.querySelectorAll('#tabelaVencimentos tbody tr'));
                const inputs = linhas.map(tr => tr.querySelector('.valor-parcela'));

                inputEditado.setAttribute('data-editado', 'true');

                const valorTotal = obterValorTotalVenda();

                const indexEditado = inputs.indexOf(inputEditado);

                let somaAnteriorEAAtual = 0;
                for (let i = 0; i <= indexEditado; i++) {
                    const valor = parseValorMonetario(inputs[i].value);
                    somaAnteriorEAAtual += valor;
                }

                const restante = valorTotal - somaAnteriorEAAtual;

                if (restante < 0) {
                    alert('A soma das parcelas excede o valor total.');
                    inputEditado.removeAttribute('data-editado');
                    atualizarValorParcela();
                    return;
                }

                const posteriores = inputs.slice(indexEditado + 1);

                if (posteriores.length === 0) return;

                let valorPadrao = parseFloat((restante / posteriores.length).toFixed(2));
                let somaDistribuida = valorPadrao * posteriores.length;
                let diferenca = restante - somaDistribuida;

                posteriores.forEach((input, index) => {
                    let valorFinal = valorPadrao;
                    if (index === posteriores.length - 1) {
                        valorFinal += diferenca;
                    }
                    input.value = valorFinal.toFixed(2);
                    input.removeAttribute('data-editado');
                });

                // 🔁 Resetar todos os inputs para editáveis antes
                inputs.forEach(input => {
                    input.removeAttribute('readonly');
                    input.classList.remove('bg-light');
                });

                // ❌ Desativar somente o último input
                const ultimoInput = inputs[inputs.length - 1];
                ultimoInput.setAttribute('readonly', true);
                ultimoInput.classList.add('bg-light');

                document.getElementById('valorTotalVenda').innerText = valorTotal.toFixed(2).replace('.', ',');

                configurarListenersDeParcelas();
            }

            function adicionarProduto() {
                const select = document.getElementById('selectProduto');
                const selected = select.selectedOptions[0];
                if (!selected) return;

                const id = selected.value;
                if (produtosAdicionados.find(p => p.id == id)) {
                    alert('Produto já adicionado');
                    return;
                }

                produtosAdicionados.push({
                    id,
                    nome: selected.dataset.nome,
                    preco: parseFloat(selected.dataset.preco),
                    quantidade: 1
                });

                atualizarTabela();
                select.selectedIndex = 0;
                btnAdicionar.disabled = true;
            }

            function atualizarTabela() {
                const tbody = document.querySelector('#tabelaProdutos tbody');
                tbody.innerHTML = '';

                produtosAdicionados.forEach(p => {
                    const subtotal = p.preco * p.quantidade;
                    const tr = document.createElement('tr');

                    tr.innerHTML = `
            <td>${p.nome}</td>
            <td>
                <input 
                    type="text" 
                    class="form-control form-control-sm preco-unitario" 
                    value="${p.preco.toFixed(2).replace('.', ',')}" 
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
            <td>R$ ${subtotal.toFixed(2).replace('.', ',')}</td>
            <td style="text-align:center; vertical-align: middle;">
                <button class="btn btn-danger btn-sm px-3 py-1" onclick="removerProduto('${p.id}')">
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
                document.querySelectorAll('.preco-unitario').forEach(input => {
                    input.addEventListener('blur', function() {
                        const id = this.dataset.id;
                        alterarPreco(id, this.value);
                    });
                });

                // Evento para alterar quantidade ao mudar valor
                document.querySelectorAll('.quantidade-produto').forEach(input => {
                    input.addEventListener('input', function() {
                        const id = this.dataset.id;
                        const valor = this.value;

                        // Só atualiza os dados internos e subtotal/resumo, sem mexer no valor do input
                        alterarQuantidade(id, valor);
                    });
                });

            }

            function alterarPreco(id, valor) {
                const preco = parseFloat(valor.replace(',', '.'));
                if (isNaN(preco) || preco < 0) return;

                const prod = produtosAdicionados.find(p => p.id == id);
                if (!prod) return;

                prod.preco = preco;
                atualizarTabela();
            }

            function alterarQuantidade(id, valor) {
                // Se o valor estiver vazio, zera a quantidade internamente e atualiza subtotal vazio
                if (valor.trim() === '') {
                    const prod = produtosAdicionados.find(p => p.id == id);
                    if (!prod) return;

                    prod.quantidade = 0; // ou pode remover o produto, ou manter 0

                    const inputQuantidade = document.querySelector(`.quantidade-produto[data-id="${id}"]`);
                    if (!inputQuantidade) return;

                    const tr = inputQuantidade.closest('tr');
                    const subtotalCell = tr.querySelector('td:nth-child(4)');
                    subtotalCell.textContent = ''; // deixa vazio

                    atualizarResumo(false);
                    return;
                }

                const qtd = parseInt(valor);
                if (isNaN(qtd) || qtd < 1) return;

                const prod = produtosAdicionados.find(p => p.id == id);
                if (!prod) return;

                prod.quantidade = qtd;

                const inputQuantidade = document.querySelector(`.quantidade-produto[data-id="${id}"]`);
                if (!inputQuantidade) return;

                const tr = inputQuantidade.closest('tr');
                const subtotalCell = tr.querySelector('td:nth-child(4)');
                const subtotal = prod.preco * prod.quantidade;

                subtotalCell.textContent = `R$ ${subtotal.toFixed(2).replace('.', ',')}`;

                atualizarResumo(false);
            }

            function removerProduto(id) {
                produtosAdicionados = produtosAdicionados.filter(p => p.id != id);
                atualizarTabela();
            }

            function atualizarResumo(atualizarInputs = true) {
                // Soma quantidade ignorando produtos com quantidade zero
                const totalProdutos = produtosAdicionados.reduce((acc, p) => acc + (p.quantidade > 0 ? p
                    .quantidade : 0), 0);
                // Soma valores, ignorando produtos com quantidade zero
                const valorTotal = produtosAdicionados.reduce((acc, p) => acc + (p.quantidade > 0 ? p.preco * p
                    .quantidade : 0), 0);

                document.getElementById('totalProdutos').innerText = totalProdutos > 0 ? totalProdutos : '';
                document.getElementById('valorTotal').innerText = valorTotal > 0 ? valorTotal.toFixed(2).replace(
                    '.', ',') : '';

                if (atualizarInputs) {
                    atualizarValorParcela();
                }

                const btnPagamento = document.getElementById('btnPagamento');
                btnPagamento.disabled = produtosAdicionados.length === 0 || totalProdutos === 0;
            }


            document.addEventListener('input', (e) => {
                if (e.target.classList.contains('preco-unitario')) {
                    const id = e.target.dataset.id;
                    const raw = e.target.value.replace(/[^\d,]/g, '').replace(',', '.');
                    const preco = parseFloat(raw);

                    const prod = produtosAdicionados.find(p => p.id == id);
                    if (!prod) return;

                    if (!isNaN(preco) && preco > 0) {
                        prod.preco = preco;

                        // Atualiza subtotal na linha sem recriar inputs
                        const tr = e.target.closest('tr');
                        const subtotalCell = tr.querySelector('td:nth-child(4)');
                        const subtotal = prod.preco * prod.quantidade;
                        subtotalCell.textContent = `R$ ${subtotal.toFixed(2).replace('.', ',')}`;

                        // Atualiza o resumo, mas sem alterar inputs (sem chamar atualizarTabela)
                        atualizarResumo(false);
                    }
                }
            });

            function adicionarEventoBlurPreco() {
                document.querySelectorAll('.preco-unitario').forEach(input => {
                    input.addEventListener('blur', function() {
                        const id = this.dataset.id;
                        alterarPreco(id, this.value);
                        atualizarTabela
                            ();
                    });
                });
            }
            window.adicionarProduto = adicionarProduto;
            window.alterarQuantidade = alterarQuantidade;
            window.removerProduto = removerProduto;
        });
        document.getElementById('btnSalvarVenda').addEventListener('click', async () => {
            const selectCliente = document.getElementById('selectCliente');
            const clienteId = selectCliente.value;

            if (!clienteId) {
                alert('Selecione um cliente');
                return;
            }

            if (produtosAdicionados.length === 0) {
                alert('Adicione ao menos um produto');
                return;
            }

            const produtos = produtosAdicionados.map(p => ({
                product_id: p.id,
                preco_unitario: p.preco,
                quantidade: p.quantidade
            }));

            const parcelas = [];
            document.querySelectorAll('#tabelaVencimentos tbody tr').forEach(tr => {
                const dataVenc = tr.querySelector('.data-vencimento').value;
                const valorParc = parseFloat(tr.querySelector('.valor-parcela').value.replace(',',
                    '.'));
                parcelas.push({
                    data_vencimento: dataVenc,
                    valor: valorParc
                });
            });

            const valorTotalText = document.getElementById('valorTotal').innerText;
            const total = parseFloat(valorTotalText.replace('.', '').replace(',', '.')) || 0;

            const numeroParcelas = parcelas.length;

            const dadosVenda = {
                customer_id: clienteId,
                produtos,
                total,
                parcelas,
                installments: numeroParcelas,
                sale_date: new Date().toISOString().slice(0, 10) // yyyy-mm-dd
            };

            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch('{{ route('sales.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify(dadosVenda)
                });

                // Defina aqui contentType, depois que response estiver disponível
                const contentType = response.headers.get('content-type') || '';

                if (!response.ok) {
                    if (contentType.includes('application/json')) {
                        const erro = await response.json();
                        alert('Erro ao salvar venda: ' + (erro.message || response.statusText));
                    } else {
                        const text = await response.text();
                        alert('Erro ao salvar venda: ' + text);
                    }
                    return;
                }

                if (contentType.includes('application/json')) {
                    const result = await response.json();
                    alert('Venda salva com sucesso!');
                    window.location.reload();
                } else {
                    alert('Venda salva com sucesso!');
                    window.location.reload();
                }

            } catch (err) {
                alert('Erro ao salvar venda: ' + err.message);
            }

        });
    </script>


@stop
