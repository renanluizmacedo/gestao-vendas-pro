<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleInstallment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'sale_id',
        'installment_number',
        'due_date',
        'amount',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
