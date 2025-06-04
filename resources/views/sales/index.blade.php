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
                            <input type="number" id="parcelas" class="form-control form-control-sm" min="1"
                                value="1">
                        </div>

                        <p><strong>Valor Total da Venda:</strong> R$ <span id="valorTotalVenda">0,00</span></p>
                        <p><strong>Valor da Parcela:</strong> R$ <span id="valorParcela">0,00</span></p>

                        <p><strong>Parcelas:</strong></p>
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

                const tr = document.createElement('tr');
                tr.innerHTML = `
                <td>${numeroParcela} x</td>
                <td><input type="date" class="form-control form-control-sm data-vencimento" value="${data}"></td>
                <td>
                    <input type="text" class="form-control form-control-sm valor-parcela" value="R$ ${valor.toFixed(2).replace('.', ',')}">
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
                        input.value = `R$ ${valorParcela.toFixed(2).replace('.', ',')}`;
                    }
                });
            }

            function recalcularParcelasAPartirDeEdicao(inputEditado) {
                const linhas = Array.from(tabelaVencimentos.querySelectorAll('tr'));
                const indexEditado = linhas.findIndex(tr => tr.contains(inputEditado));

                let valorTotal = obterValorTotalVenda();
                let valorEditado = parseFloat(inputEditado.value.replace('R$ ', '').replace(/\./g, '').replace(',',
                    '.')) || 0;

                let qtdRestante = linhas.length - 1;
                if (qtdRestante <= 0) return;

                let valorRestante = valorTotal - valorEditado;
                let valorParcelaRestante = valorRestante / qtdRestante;

                linhas.forEach((tr, idx) => {
                    const input = tr.querySelector('.valor-parcela');

                    if (idx === indexEditado) {
                        input.value = `R$ ${valorEditado.toFixed(2).replace('.', ',')}`;
                    } else {
                        input.value = `R$ ${valorParcelaRestante.toFixed(2).replace('.', ',')}`;
                    }
                });

                document.getElementById('valorParcela').innerText = '-';
                document.getElementById('valorTotalVenda').innerText = valorTotal.toFixed(2).replace('.', ',');
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
                    tbody.innerHTML += `
                    <tr>
                        <td>${p.nome}</td>
                        <td>R$ ${p.preco.toFixed(2).replace('.', ',')}</td>
                        <td>
                            <input type="number" min="1" value="${p.quantidade}" style="width:60px"
                            onchange="alterarQuantidade('${p.id}', this.value)">
                        </td>
                        <td>R$ ${subtotal.toFixed(2).replace('.', ',')}</td>
                        <td style="text-align:center; vertical-align: middle;">
                            <button class="btn btn-danger btn-sm px-3 py-1" onclick="removerProduto('${p.id}')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                `;
                });

                atualizarResumo();
            }

            function alterarQuantidade(id, valor) {
                const qtd = parseInt(valor);
                if (isNaN(qtd) || qtd < 1) return;

                const prod = produtosAdicionados.find(p => p.id == id);
                if (!prod) return;
                prod.quantidade = qtd;
                atualizarTabela();
            }

            function removerProduto(id) {
                produtosAdicionados = produtosAdicionados.filter(p => p.id != id);
                atualizarTabela();
            }

            function atualizarResumo() {
                const totalProdutos = produtosAdicionados.reduce((acc, p) => acc + p.quantidade, 0);
                const valorTotal = produtosAdicionados.reduce((acc, p) => acc + p.preco * p.quantidade, 0);

                document.getElementById('totalProdutos').innerText = totalProdutos;
                document.getElementById('valorTotal').innerText = valorTotal.toFixed(2).replace('.', ',');

                atualizarValorParcela();

                const btnPagamento = document.getElementById('btnPagamento');
                btnPagamento.disabled = produtosAdicionados.length === 0;
            }

            window.adicionarProduto = adicionarProduto;
            window.alterarQuantidade = alterarQuantidade;
            window.removerProduto = removerProduto;
        });
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-papbE4dUrbxvBPtqyb7+6qTce4HGXDq0TxEexzH8XvdODZLllfJcsjkz2/fnUz1FKOQYFDfZ07jw2u2vbuqKkg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
@stop
