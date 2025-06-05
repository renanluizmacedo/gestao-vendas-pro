<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class)->withTrashed();;
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();;
    }
}
