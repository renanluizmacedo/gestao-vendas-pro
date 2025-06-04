    <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="infoModalLabel">📄 Detalhes da Venda</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Fechar"></button>
                </div>
                <div class="modal-body">

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light font-weight-bold">🛒 Itens da Venda</div>
                                <div class="card-body p-0">
                                    <table class="table table-hover table-striped mb-0" id="itemsTable">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>Produto</th>
                                                <th>Quantidade</th>
                                                <th>Preço Unitário</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-group-divider"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light font-weight-bold">💳 Parcelas</div>
                                <div class="card-body p-0">
                                    <table class="table table-hover table-striped mb-0" id="installmentsTable">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>Nº Parcela</th>
                                                <th>Data de Vencimento</th>
                                                <th>Valor</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-group-divider"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>