<!-- Modal Edição de Venda -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="editForm" method="POST" action="">
                @csrf
                @method('PUT')

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editModalLabel">Editar Venda</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Fechar"></button>
                </div>

                <div class="modal-body">
                    {{-- Campos ocultos --}}
                    <input type="hidden" id="edit_id" name="id" />

                    {{-- Linha 1: Dados básicos da venda --}}
                    <div class="mb-3 row">
                        <div class="col-md-4">
                            <label for="edit_customer_id" class="form-label">Cliente</label>
                            <select id="edit_customer_id" name="customer_id" class="form-control" required>
                                {{-- Popule com os clientes via backend --}}
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="edit_sale_date" class="form-label">Data da Venda</label>
                            <input type="date" id="edit_sale_date" name="sale_date" class="form-control" required />
                        </div>
                    </div>

                    {{-- Linha 2: Observação --}}
                    <div class="mb-3">
                        <label for="edit_observation" class="form-label">Observação</label>
                        <textarea id="edit_observation" name="observation" rows="2" class="form-control"></textarea>
                    </div>

                    {{-- Partial 1: Info Cliente + Resumo + Produtos --}}
                    @include('sales.partials.info-modal')

                    {{-- Partial 2: Modal Parcelamento dentro do modal (embutido) --}}
                    @include('sales.partials.modal-parcelamento')

                    {{-- Partial 3: Aqui o campo de parcelas --}}
                    <div class="mb-3 mt-3">
                        <label for="edit_installments" class="form-label">Número de Parcelas</label>
                        <input type="number" id="edit_installments" name="installments" class="form-control"
                            min="1" max="24" />
                    </div>

                    <div>
                        <small class="text-muted">* Altere o valor das parcelas manualmente no parcelamento.</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-close btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="btnSalvarVenda">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>
