<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;


class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {

        $products = Product::with('category')->get();
        $customers = Customer::all();
        $sales = Sale::with(['items.product', 'saleInstallments', 'customer', 'user'])->paginate(10);
        $salesForJs = $sales->getCollection()->map(function ($sale) {
            return [
                'id' => $sale->id,
                'customer_name' => $sale->customer->name,
                'user_name' => $sale->user->name,
                'sale_date' => \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y'),
                'total' => $sale->total,
                'payment_method' => $sale->payment_method,
                'observation' => $sale->observation,
                'items' => $sale->items->map(function ($item) {
                    return [
                        'product' => $item->product->name,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'subtotal' => $item->subtotal,
                    ];
                })->toArray(),
                'installments' => $sale->saleInstallments->map(function ($inst) {
                    return [
                        'number' => $inst->installment_number,
                        'due_date' => \Carbon\Carbon::parse($inst->due_date)->format('d/m/Y'),
                        'amount' => $inst->amount,
                    ];
                })->toArray(),
            ];
        })->toArray();

        return view('sales.index', compact('sales', 'salesForJs', 'products', 'customers'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::with('category')->get();
        $customers = Customer::all();

        return view('sales.create', compact('products', 'customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSaleRequest $request)
    {
        try {
            $data = $request->validated(); // <- ISSO FUNCIONA AQUI

            // Agora você pode acessar todos os campos sem precisar de validação (temporariamente)
            $sale = Sale::create([
                'user_id' => Auth::id(),
                'customer_id' => $data['customer_id'],
                'sale_date' => $data['sale_date'],
                'total' => $data['total'],
                'installments' => $data['installments'],
            ]);

            foreach ($data['produtos'] as $produto) {
                $sale->items()->create([
                    'product_id' => $produto['product_id'],
                    'quantity' => $produto['quantidade'],
                    'unit_price' => $produto['preco_unitario'],
                    'subtotal' => $produto['preco_unitario'] * $produto['quantidade'],
                ]);
            }

            foreach ($data['parcelas'] as $index => $parcela) {
                $sale->saleInstallments()->create([
                    'installment_number' => $index + 1,
                    'due_date' => $parcela['data_vencimento'],
                    'amount' => $parcela['valor'],
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Venda salva com sucesso!']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage(),
            ], 500);
        }
    }




    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $sale = Sale::with(['customer', 'user', 'items', 'saleInstallments'])->findOrFail($id);
        $customers = Customer::all();
        $paymentMethods = ['Dinheiro', 'Cartão', 'Boleto', 'Pix'];
        $products = Product::all();

        return view('sales.edit', compact('sale', 'customers', 'paymentMethods', 'products'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSaleRequest $request, Sale $sale)
    {
        $sale->customer_id = $request->customer_id;
        $sale->total = $request->total;
        $sale->installments = $request->installments;
        $sale->sale_date = $request->sale_date;
        $sale->save();

        $sale->items()->delete();

        $sale->items()->delete();

        foreach ($request->produtos as $produto) {
            $quantity = $produto['quantidade'];
            $unitPrice = $produto['preco_unitario'];
            $subtotal = $quantity * $unitPrice;

            $sale->items()->create([
                'product_id' => $produto['product_id'],
                'unit_price' => $unitPrice,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
            ]);
        }


        return response()->json([
            'request' => $request->all(),
            'sale' => $sale,
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        //
    }
}
