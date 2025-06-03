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

    <div class="row my-3">

        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <th width="50%">Nome</th>
                        <th width="50%">Ações</th>

                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td>{{ $category->name }}</td>

                                <td class="d-flex actions-hide">
                                    <a href="#"
                                        onclick="showEditModal({{ $category->id }}, '{{ addslashes($category->name) }}')"
                                        class="btn btn-primary mr-3">Editar</a>
                                    <a nohref style="cursor:pointer"
                                        onclick="showRemoveModal('{{ $category->id }}', '{{ $category->name }}')"
                                        class="text-light btn btn-danger">

                                        Excluir
                                    </a>
                                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST"
                                        id="form_{{ $category->id }}">
                                        @csrf
                                        @method('DELETE')
                                    </form>

                                </td>
                            <tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <th width="50%">Nome</th>
                        <th width="50%">Ações</th>
                    </tfoot>
                </table>
                <div class="d-flex justify-content-center mt-3">
                    {{ $categories->links() }}
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="infoModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-primary">Mais Informações</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="infoModal" onclick="closeInfoModal()"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-secondary">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-block align-content-center"
                        onclick="closeInfoModal()">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="removeModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">Operação de Remoção</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="removeModal" onclick="closeRemoveModal()"
                        aria-label="Close"></button>
                </div>
                <input type="hidden" id="id_remove">
                <div class="modal-body text-secondary">
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary align-content-center" onclick="closeRemoveModal()">
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
                        <button type="button" class="btn-close btn btn-danger text-white" data-bs-dismiss="modal"
                            aria-label="Fechar">Fechar</button>
                        <button type="submit" class="btn btn-success text-white">Salvar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
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
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn-close btn btn-danger text-white" data-bs-dismiss="modal"
                                aria-label="Fechar">Fechar</button>
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

            const modalBody = document.querySelector('#removeModal .modal-body');
            modalBody.innerHTML = `Remover o produto <b>${nome}</b>?`;

            removeModal.show();
        }

        function closeRemoveModal() {
            removeModal.hide();
        }

        function remove() {
            const id = document.getElementById('id_remove').value;

            if (!id) {
                console.error('ID do produto não encontrado.');
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


        function showEditModal(id, name) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;


            const form = document.getElementById('editForm');
            form.action = `/categories/${id}`;

            editModal.show();
        }
    </script>
@stop
