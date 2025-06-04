@extends('adminlte::page')

@section('title', 'Editar Venda')

@section('content_header')

    <h1 class="font-weight-bold">Atualizar Venda</h1>
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

@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.LaravelRoutes = {
            storeSale: "{{ route('sales.store') }}",
            csrfToken: "{{ csrf_token() }}"
        };
    </script>
    <script src="{{ asset('js/vendas.js') }}"></script>
    <script>
        const vendaExistente = {!! json_encode($sale) !!};
        const produtosDisponiveis = {!! json_encode($products) !!};
    </script>


@stop
