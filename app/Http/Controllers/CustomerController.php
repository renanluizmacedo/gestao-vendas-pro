<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::paginate(15);
        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request)
    {
        $data = $request->validated();

        $data['cpf'] = preg_replace('/\D/', '', $data['cpf']);
        $data['phone'] = preg_replace('/\D/', '', $data['phone']);

        Customer::create($data);

        return redirect()->route('customers.index')
            ->with('success', 'Cliente cadastrado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer->update([
            'name' => $request->name,
            'cpf' => $request->cpf,
            'phone' => $request->phone,
        ]);

        return redirect()->route('customers.index')
            ->with('success', 'Cliente atualizado com sucesso!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Cliente removido com sucesso!');
    }
}
