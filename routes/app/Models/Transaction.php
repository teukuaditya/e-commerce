<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'customer_name',
        'gross_amount',
        'payment_type',
        'courier',
        'courier_service',
        'transaction_status',
        'transaction_time',
        'metadata',
    ];

    public function details()
    {
        return $this->hasMany(TransactionDetail::class, 'transaction_id', 'id');
    }
}

