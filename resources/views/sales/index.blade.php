@extends('adminlte::page')

@section('title', 'Vendas')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="font-weight-bold mb-0">Vendas</h1>
    </div>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Sucesso:</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Erro:</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif

    {{-- ALERTAS DE ERROS DE VALIDAÇÃO --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Erros encontrados:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif
    <div class="row my-3">
        <div class="col-md-12">
            <input type="text" id="searchInput" placeholder="Buscar clientes..." class="form-control mb-3" />

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Data</th>
                            <th>Vendedor</th>
                            <th>Valor Total</th>
                            <th>Parcelas</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sales as $sale)
                            <tr>
                                <td>{{ $sale->customer->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}</td>
                                <td>{{ $sale->user->name }}</td>
                                <td>R$ {{ number_format($sale->total, 2, ',', '.') }}</td>
                                <td>{{ $sale->installments }}</td>
                                <td class="d-flex">
                                    <div class="ml-2">
                                        <a href="#" class="btn btn-info d-flex align-items-center me-2"
                                            onclick="showInfoModal({{ $sale->id }})">
                                            <i class="fas fa-info-circle me-1"></i> Info
                                        </a>
                                    </div>

                                    <div class="ml-2">
                                        <a href="{{ route('sales.edit', $sale->id) }}"
                                            class="btn btn-primary d-flex align-items-center me-2">
                                            <i class="fas fa-edit me-1"></i> Editar
                                        </a>
                                    </div>

                                    <div class="ml-2">
                                        <a href="{{ route('sales.pdf', $sale->id) }}" target="_blank"
                                            class="btn btn-secondary d-flex align-items-center me-2">
                                            <i class="fas fa-file-pdf me-1"></i> Gerar PDF
                                        </a>
                                    </div>

                                    <div class="ml-2">
                                        <a href="#"
                                            onclick="showRemoveModal({{ $sale->id }}, '{{ $sale->customer->name }}')"
                                            class="btn btn-danger d-flex align-items-center">
                                            <i class="fas fa-trash me-1"></i> Excluir
                                        </a>
                                    </div>


                                    <form id="form_{{ $sale->id }}" action="{{ route('sales.destroy', $sale->id) }}"
                                        method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>



                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-center mt-3">
                    {{ $sales->links() }}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="infoModalLabel">📄 Detalhes da Venda</h5>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fechar</button>

                </div>
                <div class="modal-body">
                    <div class="mb-4 px-3 py-2 border rounded bg-light d-flex align-items-center">
                        <strong class="me-2">Vendedor:</strong>
                        <span id="saleSellerName" class="fw-semibold text-primary"></span>
                    </div>


                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-primary text-white fw-bold">
                                    🛒 Itens da Venda
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-hover table-striped mb-0" id="itemsTable">
                                        <thead class="table-primary">
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
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-primary text-white fw-bold">
                                    💳 Parcelas
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-hover table-striped mb-0" id="installmentsTable">
                                        <thead class="table-primary">
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


            </div>
        </div>
    </div>


    <div class="modal fade" id="removeModal" tabindex="-1" aria-labelledby="removeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="removeModalLabel">Remover Venda</h5>

                </div>
                <div class="modal-body">
                    <p id="removeText"></p>
                    <!-- Aqui está o input que precisa ter o id id_remove -->
                    <input type="hidden" id="id_remove">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-close btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" onclick="remove()">Confirmar Remoção</button>
                </div>
            </div>
        </div>
    </div>

@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        //const editModal = new bootstrap.Modal(document.getElementById('editModal'));
        const removeModal = new bootstrap.Modal(document.getElementById('removeModal'));
        const infoModal = new bootstrap.Modal(document.getElementById('infoModal'));

        const salesData = {!! json_encode($salesForJs, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!};





        function showEditModal(id, customer_id, sale_date, total, installments, observation) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_customer_id').value = customer_id;
            document.getElementById('edit_sale_date').value = sale_date;
            document.getElementById('edit_total').value = total;
            document.getElementById('edit_installments').value = installments;
            document.getElementById('edit_observation').value = observation;

            document.getElementById('editForm').action = `/sales/${id}`;
            editModal.show();
        }

        function showInfoModal(saleId) {
            console.log()
            "here"
            const sale = salesData.find(sale => sale.id === saleId);
            if (!sale) {
                alert('Venda não encontrada');
                return;
            }

            document.getElementById('saleSellerName').textContent = sale.user_name;

            const itemsBody = document.querySelector('#itemsTable tbody');
            const installmentsBody = document.querySelector('#installmentsTable tbody');
            itemsBody.innerHTML = '';
            installmentsBody.innerHTML = '';

            if (sale.items.length) {
                sale.items.forEach(item => {
                    itemsBody.insertAdjacentHTML('beforeend', `
                <tr>
                    <td>${item.product}</td>
                    <td>${item.quantity}</td>
                    <td>R$ ${parseFloat(item.unit_price).toFixed(2).replace('.', ',')}</td>
                    <td>R$ ${parseFloat(item.subtotal).toFixed(2).replace('.', ',')}</td>
                </tr>
            `);
                });
            } else {
                itemsBody.innerHTML = '<tr><td colspan="4" class="text-center">Nenhum item cadastrado</td></tr>';
            }

            if (sale.installments.length) {
                sale.installments.forEach(inst => {
                    installmentsBody.insertAdjacentHTML('beforeend', `
                <tr>
                    <td>${inst.number}</td>
                    <td>${inst.due_date}</td>
                    <td>R$ ${parseFloat(inst.amount).toFixed(2).replace('.', ',')}</td>
                </tr>
            `);
                });
            } else {
                installmentsBody.innerHTML = '<tr><td colspan="3" class="text-center">Nenhuma parcela cadastrada</td></tr>';
            }

            infoModal.show();
        }

        function showRemoveModal(id, name) {
            document.getElementById('id_remove').value = id;
            document.getElementById('removeText').innerHTML = `Deseja remover a venda do cliente <b>${name}</b>?`;
            removeModal.show();
        }

        function remove() {
            const id = document.getElementById("id_remove").value;
            document.getElementById("form_" + id).submit();
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) {
                return map[m];
            });
        }

        // Busca simples na tabela
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('table tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    </script>
@stop
