<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'plan_id',
        'transactions_id',
        'amount',
        'currency',
        'payment_method_type',
        'status',
        'mode',
        'subscription_id',
        'invoice_id',
        'payment_status',
        'created_at',
        'updated_at',
    ];
}
