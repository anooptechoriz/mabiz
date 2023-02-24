<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;
    protected $fillable = [
        'service_id',
        'user_id',
        'package_id',
        'subscription_date',
        'expiry_date',
        'coupon_code',
        'type',
        'order_id',
        'transaction_id',
        'transaction_status',
        'status'
    ];
}
