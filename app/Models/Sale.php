<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    // Quais campos podem ser preenchidos em massa (mass assign)
    protected $fillable = [
        'user_id',
        'customer_id',
        'sale_date',
        'total',
        'payment_method',
        'installments',
        'observation',
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function saleInstallments()
    {
        return $this->hasMany(SaleInstallment::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
