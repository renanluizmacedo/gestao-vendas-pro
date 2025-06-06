@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="font-weight-bold mb-0">Categorias</h1>
        <a href="#" class="btn btn-warning text-light" data-bs-toggle="modal" data-bs-target="#createModal">
            Cadastrar
        </a>
    </div>
@stop

@section('content')
    {{-- ALERTAS DE SUCESSO E ERRO --}}
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
            <input type="text" id="searchInput" placeholder="Buscar produtos..." class="form-control mb-3" />

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th width="50%">Nome</th>
                            <th width="50%">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td>{{ $category->name }}</td>
                                <td class="d-flex actions-hide">
                                    <a href="#"
                                        onclick="showEditModal({{ $category->id }}, '{{ addslashes($category->name) }}')"
                                        class="btn btn-primary me-3">Editar</a>
                                    <a href="#" style="cursor:pointer"
                                        onclick="showRemoveModal('{{ $category->id }}', '{{ addslashes($category->name) }}')"
                                        class="text-light btn btn-danger">Excluir</a>
                                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST"
                                        id="form_{{ $category->id }}" style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Nome</th>
                            <th>Ações</th>
                        </tr>
                    </tfoot>
                </table>
                <div class="d-flex justify-content-center mt-3">
                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DE CRIAÇÃO --}}
    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title text-white" id="createModalLabel">Cadastrar Categoria</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome da Categoria</label>
                            <input type="text" class="form-control" name="name" id="name" required>
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

    {{-- MODAL DE EDIÇÃO --}}
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white" id="editModalLabel">Editar Categoria</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Nome da Categoria</label>
                            <input type="text" class="form-control" name="name" id="edit_name" required>
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

    {{-- MODAL DE REMOÇÃO --}}
    <div class="modal fade" id="removeModal" tabindex="-1" role="dialog" aria-labelledby="removeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white">Confirmar Remoção</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <input type="hidden" id="id_remove">
                <div class="modal-body text-secondary">
                    <!-- Texto inserido via JS -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" onclick="remove()">Sim, Remover</button>
                </div>
            </div>
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
            const modalBody = document.querySelector('#removeModal .modal-body');
            modalBody.innerHTML = `Remover a categoria <b>${nome}</b>?`;
            removeModal.show();
        }

        function remove() {
            const id = document.getElementById('id_remove').value;
            const form = document.getElementById('form_' + id);
            if (form) {
                form.submit();
            } else {
                console.error('Formulário não encontrado para o ID:', id);
            }
            removeModal.hide();
        }

        function showEditModal(id, name) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            const form = document.getElementById('editForm');
            form.action = `/categories/${id}`;
            editModal.show();
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
