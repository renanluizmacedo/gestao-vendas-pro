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

            <button id="btnAdicionarProduto" class="btn btn-sm btn-success mt-2" onclick="adicionarProduto()" disabled>
                Adicionar Produto
            </button>
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
                        <th>Preço Unitário</th>
                        <th>Quantidade</th>
                        <th>Total</th>
                        <th>Ação</th>
                    </tr>
                </thead>

                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@stop


<script>
    let produtosAdicionados = [];
    let total = 0;

    document.addEventListener('DOMContentLoaded', function() {
        const selectCliente = document.getElementById('selectCliente');
        const selectProduto = document.getElementById('selectProduto');
        const btnAdicionar = document.getElementById('btnAdicionarProduto');

        // Atualiza informações do cliente
        selectCliente.addEventListener('change', function() {
            const selected = this.selectedOptions[0];
            document.getElementById('clienteNome').innerText = selected.dataset.nome || '-';
            document.getElementById('clienteTelefone').innerText = selected.dataset.telefone || '-';
        });

        // Ativa ou desativa o botão conforme a seleção de produto
        selectProduto.addEventListener('change', function() {
            const selected = this.selectedOptions[0];
            btnAdicionar.disabled = selected.disabled;
        });
    });

    function adicionarProduto() {
        const select = document.getElementById('selectProduto');
        const selected = select.selectedOptions[0];
        if (!selected || selected.disabled) return;

        const nome = selected.dataset.nome;
        const preco = parseFloat(selected.dataset.preco);
        const id = selected.value;

        const existe = produtosAdicionados.find(p => p.id == id);
        if (existe) {
            alert('Produto já adicionado!');
            return;
        }

        produtosAdicionados.push({
            id,
            nome,
            preco,
            quantidade: 1
        });
        atualizarTabela();

        select.selectedIndex = 0;
        document.getElementById('btnAdicionarProduto').disabled = true;
    }

    function alterarQuantidade(id, novaQuantidade) {
        if (novaQuantidade === '') return;

        novaQuantidade = parseInt(novaQuantidade);
        if (isNaN(novaQuantidade) || novaQuantidade < 1) return;

        const produto = produtosAdicionados.find(p => p.id == id);
        if (produto) {
            produto.quantidade = novaQuantidade;
            atualizarTabela();
        }
    }

    function removerProduto(id) {
        produtosAdicionados = produtosAdicionados.filter(p => p.id != id);
        atualizarTabela();
    }

function alterarPreco(id, novoPreco, formatar = false) {
    if (!novoPreco) return;

    let precoConvertido = parseFloat(novoPreco.replace(',', '.'));

    if (isNaN(precoConvertido) || precoConvertido < 0.01) return;

    const produto = produtosAdicionados.find(p => p.id == String(id));
    if (produto) {
        produto.preco = precoConvertido;

        // Só re-renderiza a tabela se for para formatar
        if (formatar) {
            atualizarTabela();
        } else {
            // Atualiza apenas totais dinamicamente
            const subtotal = produto.preco * produto.quantidade;
            total = produtosAdicionados.reduce((acc, p) => acc + p.preco * p.quantidade, 0);
            document.getElementById('valorTotal').innerText = total.toFixed(2).replace('.', ',');
        }
    }
}




    function atualizarTabela() {
        const tbody = document.querySelector('#tabelaProdutos tbody');
        tbody.innerHTML = '';
        total = 0;

        produtosAdicionados.forEach(produto => {
            const subtotal = produto.preco * produto.quantidade;
            total += subtotal;

            tbody.innerHTML += `
        <tr>
            <td>${produto.nome}</td>
            <td>
                <input 
                    type="text" 
                    class="form-control form-control-sm" 
                    value="${produto.preco.toFixed(2).replace('.', ',')}" 
                    oninput="alterarPreco('${produto.id}', this.value, false)"
                    onblur="alterarPreco('${produto.id}', this.value, true)">

            </td>
            <td>
                <input 
                    type="number" 
                    class="form-control form-control-sm" 
                    min="1" 
                    value="${produto.quantidade}" 
                    oninput="alterarQuantidade('${produto.id}', this.value)">
            </td>
            <td>R$ ${(subtotal).toFixed(2).replace('.', ',')}</td>
            <td>
                <button class="btn btn-danger btn-sm" onclick="removerProduto('${produto.id}')">Remover</button>
            </td>
        </tr>
        `;
        });

        const quantidadeTotal = produtosAdicionados.reduce((acc, p) => acc + p.quantidade, 0);
        document.getElementById('totalProdutos').innerText = quantidadeTotal;
        document.getElementById('valorTotal').innerText = total.toFixed(2).replace('.', ',');


    }
</script>
