@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="font-weight-bold mb-0">Clientes</h1>
        <a href="#" class="btn btn-warning text-light" data-bs-toggle="modal" data-bs-target="#createModal">
            Cadastrar
        </a>
    </div>
@stop

@section('content')

    <div class="row my-3">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th width="20%">Nome</th>
                            <th class="phone-hide" width="20%">CPF</th>
                            <th class="about-hide" width="20%">Telefone</th>
                            <th class="actions-hide" width="20%">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                            <tr>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->cpf_formatted }}</td>
                                <td>{{ $customer->phone }}</td>
                                <td class="d-flex actions-hide gap-2">
                                    <button type="button" class="btn btn-primary"
                                        onclick="showEditModal(
                                            {{ $customer->id }},
                                            '{{ addslashes($customer->name) }}',
                                            '{{ preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $customer->cpf) }}',
                                            '{{ $customer->phone }}'
                                        )">
                                        Editar
                                    </button>

                                    <button type="button" class="ml-4 btn btn-danger text-light" style="cursor:pointer"
                                        onclick="showRemoveModal('{{ $customer->id }}', '{{ addslashes($customer->name) }}')">
                                        Excluir
                                    </button>

                                    <form action="{{ route('customers.destroy', $customer->id) }}" method="POST"
                                        id="form_{{ $customer->id }}">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th width="20%">Nome</th>
                            <th class="phone-hide" width="20%">CPF</th>
                            <th class="about-hide" width="20%">Telefone</th>
                            <th class="actions-hide" width="20%">Ações</th>
                        </tr>
                    </tfoot>
                </table>
                <div class="d-flex justify-content-center mt-3">
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="removeModal" tabindex="-1" aria-labelledby="removeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="removeModalLabel">Operação de Remoção</h5>
                    <button type="button" class="btn-close" aria-label="Close" onclick="closeRemoveModal()"></button>
                </div>
                <input type="hidden" id="id_remove">
                <div class="modal-body text-secondary" id="removeModalBody">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeRemoveModal()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                            class="bi bi-arrow-left-square-fill" viewBox="0 0 16 16">
                            <path
                                d="M16 14a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12zm-4.5-6.5H5.707l2.147-2.146a.5.5 0 1 0-.708-.708l-3 3a.5.5 0 0 0 0 .708l3 3a.5.5 0 0 0 .708-.708L5.707 8.5H11.5a.5.5 0 0 0 0-1z" />
                        </svg>
                        &nbsp; Não
                    </button>
                    <button type="button" class="btn btn-danger" onclick="remove()">
                        Sim &nbsp;
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                            class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                            <path
                                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('customers.store') }}" method="POST" id="createForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title text-white" id="createModalLabel">Cadastrar Cliente</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="create_name" class="form-label">Nome do Cliente</label>
                            <input type="text" class="form-control" name="name" id="create_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="create_cpf" class="form-label">CPF</label>
                            <input type="text" class="form-control" name="cpf" id="create_cpf"
                                oninput="formatCPF(this)" required>
                        </div>
                        <div class="mb-3">
                            <label for="create_phone" class="form-label">Telefone</label>
                            <input type="text" class="form-control" name="phone" id="create_phone"
                                oninput="formatPhone(this)" required>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-danger text-white" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-success text-white">Salvar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white" id="editModalLabel">Editar Cliente</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Nome do Cliente</label>
                            <input type="text" class="form-control" name="name" id="edit_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_cpf" class="form-label">CPF</label>
                            <input type="text" class="form-control" name="cpf" id="edit_cpf"
                                oninput="formatCPF(this)" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_phone" class="form-label">Telefone</label>
                            <input type="text" class="form-control" name="phone" id="edit_phone"
                                oninput="formatPhone(this)" required>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-danger text-white" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-success text-white">Salvar Alterações</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const removeModal = new bootstrap.Modal(document.getElementById('removeModal'));
        const editModal = new bootstrap.Modal(document.getElementById('editModal'));

        function showRemoveModal(id, nome) {
            document.getElementById('id_remove').value = id;
            document.getElementById('removeModalBody').innerHTML = `Remover o cliente <b>${escapeHtml(nome)}</b>?`;
            removeModal.show();
        }

        function closeRemoveModal() {
            removeModal.hide();
        }

        function remove() {
            const id = document.getElementById('id_remove').value;
            if (!id) {
                console.error('ID do cliente não encontrado.');
                return;
            }
            const form = document.getElementById('form_' + id);
            if (form) {
                form.submit();
            } else {
                console.error('Formulário de remoção não encontrado para o ID:', id);
            }
            removeModal.hide();
        }

        function showEditModal(id, name, cpf, phone) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_cpf').value = cpf;
            document.getElementById('edit_phone').value = phone;

            const form = document.getElementById('editForm');
            form.action = `/customers/${id}`;

            editModal.show();
        }

        function formatCPF(input) {
            let value = input.value.replace(/\D/g, '');
            if (value.length > 11) value = value.slice(0, 11);
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            input.value = value;
        }

        function formatPhone(input) {
            let value = input.value.replace(/\D/g, '');
            if (value.length > 11) value = value.slice(0, 11);

            if (value.length > 6) {
                value = value.replace(/^(\d{2})(\d{5})(\d{0,4}).*/, '($1) $2-$3');
            } else if (value.length > 2) {
                value = value.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
            } else if (value.length > 0) {
                value = value.replace(/^(\d*)/, '($1');
            }

            input.value = value;
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
    </script>
@stop
