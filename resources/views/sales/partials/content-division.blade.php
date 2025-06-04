        <div class="row">
            {{-- Coluna esquerda --}}
            <div class="col-md-4 d-flex flex-column gap-3">

                {{-- Info Cliente --}}
                <div class="card border-info">
                    <div class="card-header bg-info text-white">Informações do Cliente</div>
                    <div class="card-body">
                        <p><strong>Nome:</strong> <span id="clienteNome">{{ $sale->customer->name ?? '-' }}</span></p>
                        <p><strong>Telefone:</strong> <span
                                id="clienteTelefone">{{ $sale->customer->phone ?? '-' }}</span></p>
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
