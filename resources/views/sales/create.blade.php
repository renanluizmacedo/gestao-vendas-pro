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
        window.LaravelRoutes = {
            storeSale: "{{ route('sales.store') }}",
            csrfToken: "{{ csrf_token() }}"
        };
    </script>
    <script src="{{ asset('js/vendas.js') }}"></script>



@stop
