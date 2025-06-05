<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'customer_id' => 'required|integer|exists:customers,id',
            'sale_date' => 'required|date',
            'total' => 'required|numeric',
            'installments' => 'required|integer|min:1',
            'produtos' => 'required|array|min:1',
            'produtos.*.product_id' => 'required|integer|exists:products,id',
            'produtos.*.quantidade' => 'required|integer|min:1',
            'produtos.*.preco_unitario' => 'required|numeric|min:0',
            'parcelas' => 'required|array|min:1',
            'parcelas.*.data_vencimento' => 'required|date',
            'parcelas.*.valor' => 'required|numeric|min:0',
        ];
    }
}
