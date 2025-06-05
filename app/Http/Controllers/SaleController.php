<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use PDF;

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
            $data = $request->all();

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

            return redirect()->route('sales.index')->with('success', 'Venda salva com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Erro interno: ' . $e->getMessage()])->withInput();
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

    public function gerarPdf($id)
    {
        $sale = Sale::with(['customer', 'user', 'items.product', 'saleInstallments'])->findOrFail($id);

        $pdf = PDF::loadView('reports.sales', compact('sale'));

        // Pode forçar download:
        // return $pdf->download("venda_{$sale->id}.pdf");

        // Ou abrir no navegador:
        return $pdf->stream("venda_{$sale->id}.pdf");
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSaleRequest $request, Sale $sale)
    {
        try {
            $data = $request->all();

            // Atualizar dados principais da venda
            $sale->update([
                'customer_id' => $data['customer_id'],
                'total' => $data['total'],
                'installments' => $data['installments'],
                'sale_date' => $data['sale_date'],
            ]);

            // Remover registros antigos
            $sale->items()->delete();
            $sale->saleInstallments()->delete();

            // Recriar produtos
            foreach ($data['produtos'] as $produto) {
                $sale->items()->create([
                    'product_id' => $produto['product_id'],
                    'quantity' => $produto['quantidade'],
                    'unit_price' => $produto['preco_unitario'],
                    'subtotal' => $produto['preco_unitario'] * $produto['quantidade'],
                ]);
            }

            // Recriar parcelas
            foreach ($data['parcelas'] as $index => $parcela) {
                $sale->saleInstallments()->create([
                    'installment_number' => $index + 1,
                    'due_date' => $parcela['data_vencimento'],
                    'amount' => $parcela['valor'],
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Venda atualizada com sucesso!']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar a venda: ' . $e->getMessage(),
            ], 500);
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $sale = Sale::find($id);

        if (!$sale) {
            return response()->json([
                'success' => false,
                'message' => 'Venda não encontrada.'
            ], 404);
        }

        $sale->delete(); // método de instância para soft delete ou delete normal

        return redirect()->back()->with('success', 'Venda excluída com sucesso.');
    }
}
