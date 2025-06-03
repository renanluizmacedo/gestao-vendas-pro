@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="font-weight-bold mb-0">Vendas</h1>
    </div>
@stop


@section('content')
    <div class="row my-3">
        {{-- Selects --}}
        <div class="col-md-6 mb-3">
            <label for="selectCliente" class="form-label">Selecionar Cliente</label>
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
            <label for="selectProduto" class="form-label">Selecionar Produto</label>
            <select id="selectProduto" class="form-control">
                <option selected disabled>Escolha um produto</option>
                @foreach ($products as $produto)
                    <option value="{{ $produto->id }}" data-nome="{{ $produto->name }}" data-preco="{{ $produto->price }}">
                        {{ $produto->name }} - R$ {{ number_format($produto->price, 2, ',', '.') }}
                    </option>
                @endforeach
            </select>

            <button class="btn btn-sm btn-success mt-2" onclick="adicionarProduto()">Adicionar Produto</button>
        </div>
    </div>

    {{-- Cards --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-info">
                <div class="card-header bg-info text-white">Informações do Cliente</div>
                <div class="card-body">
                    <p><strong>Nome:</strong> <span id="clienteNome">-</span></p>
                    <p><strong>Telefone:</strong> <span id="clienteTelefone">-</span></p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">Resumo da Venda</div>
                <div class="card-body">
                    <p><strong>Total de Produtos:</strong> <span id="totalProdutos">0</span></p>
                    <p><strong>Valor Total:</strong> R$ <span id="valorTotal">0,00</span></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabela --}}
    <div class="card">
        <div class="card-header bg-secondary text-white">Produtos Adicionados</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered" id="tabelaProdutos">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Preço</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- conteúdo dinâmico via JS --}}
                </tbody>
            </table>
        </div>
    </div>
@stop


<script>
    let produtosAdicionados = [];
    let total = 0;

    document.getElementById('selectCliente').addEventListener('change', function() {
        const selected = this.selectedOptions[0];
        document.getElementById('clienteNome').innerText = selected.dataset.nome || '-';
        document.getElementById('clienteTelefone').innerText = selected.dataset.telefone || '-';
    });

    function adicionarProduto() {
        const select = document.getElementById('selectProduto');
        const selected = select.selectedOptions[0];
        if (!selected) return;

        const nome = selected.dataset.nome;
        const preco = parseFloat(selected.dataset.preco);
        const id = selected.value;

        // Evita duplicação
        const existe = produtosAdicionados.find(p => p.id == id);
        if (existe) {
            alert('Produto já adicionado!');
            return;
        }

        produtosAdicionados.push({
            id,
            nome,
            preco
        });
        atualizarTabela();
    }

    function removerProduto(id) {
        produtosAdicionados = produtosAdicionados.filter(p => p.id != id);
        atualizarTabela();
    }

    function atualizarTabela() {
        const tbody = document.querySelector('#tabelaProdutos tbody');
        tbody.innerHTML = '';
        total = 0;

        produtosAdicionados.forEach(produto => {
            total += produto.preco;
            tbody.innerHTML += `
                <tr>
                    <td>${produto.nome}</td>
                    <td>R$ ${produto.preco.toFixed(2).replace('.', ',')}</td>
                    <td><button class="btn btn-danger btn-sm" onclick="removerProduto(${produto.id})">Remover</button></td>
                </tr>
            `;
        });

        document.getElementById('totalProdutos').innerText = produtosAdicionados.length;
        document.getElementById('valorTotal').innerText = total.toFixed(2).replace('.', ',');
    }
</script>
