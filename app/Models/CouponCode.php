<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CouponCode extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $fillable = [
        'code',
        'description',
        'discount',
        'validity',
        'conditions',
    ];
}
