<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <title>Venda #{{ $sale->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .section-title {
            background: #007bff;
            color: white;
            padding: 8px;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }

        .small {
            font-size: 10px;
            color: #666;
        }
    </style>
</head>

<body>
    <h1>Detalhes da Venda #{{ $sale->id }}</h1>

    <p><strong>Cliente:</strong> {{ $sale->customer->name }}</p>
    <p><strong>Vendedor:</strong> {{ $sale->user->name }}</p>
    <p><strong>Data da Venda:</strong> {{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}</p>

    <div>
        <div class="section-title">Itens da Venda</div>
        <table>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Preço Unitário</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td class="text-right">{{ $item->quantity }}</td>
                        <td class="text-right">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                        <td class="text-right">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <p class="text-right"><strong>Total: R$ {{ number_format($sale->total, 2, ',', '.') }}</strong></p>
    </div>

    <div>
        <div class="section-title">Parcelas</div>
        <table>
            <thead>
                <tr>
                    <th>Nº Parcela</th>
                    <th>Data de Vencimento</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->saleInstallments as $installment)
                    <tr>
                        <td class="text-right">{{ $installment->installment_number }}</td>
                        <td>{{ \Carbon\Carbon::parse($installment->due_date)->format('d/m/Y') }}</td>
                        <td class="text-right">R$ {{ number_format($installment->amount, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if (!empty($sale->observation))
        <div>
            <div class="section-title">Observações</div>
            <p>{{ $sale->observation }}</p>
        </div>
    @endif
</body>

</html>
