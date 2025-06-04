@extends('adminlte::page')

@section('title', 'Editar Venda')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-primary rounded shadow-sm">
        <h1 class="font-weight-bold text-white display-4 mb-0">Atualizar Venda</h1>
        <button type="submit" class="btn btn-lg btn-light font-weight-bold" id="btnAtualizarVenda">
            Salvar Alterações
        </button>
    </div>
@stop


@section('content')

    <div class="container-fluid">

        {{-- Seleções no topo --}}
        @include('sales.partials.select-client-product')

        @include('sales.partials.content-division')

        {{-- Conteúdo dividido --}}
    </div>

    {{-- Modal Parcelamento --}}
    @include('sales.partials.modal-parcelamento')
    <input type="hidden" id="idVendaHidden" value="{{ $sale->id ?? '' }}">

@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        window.urlUpdateVenda = "{{ route('sales.update', ['sale' => $sale->id]) }}";
    </script>
    <script src="{{ asset('js/vendas.js') }}"></script>
    <script>
        const vendaExistente = {!! json_encode($sale) !!};
        const produtosDisponiveis = {!! json_encode($products) !!};
    </script>


@stop
