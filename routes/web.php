<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SaleController;


Auth::routes();


Route::middleware(['auth'])->group(function () {


    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('sales', SaleController::class);
    Route::redirect('/home', route('sales.index'))->name('home');
    Route::redirect('/', route('sales.index'));
    Route::get('/sales/{id}/pdf', [SaleController::class, 'gerarPdf'])->name('sales.pdf');
});
