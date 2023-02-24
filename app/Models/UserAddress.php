<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'address_name',
        'address',
        'country_id',
        'state',
        'region',
        'user_id',
        'home_no',
        'image',
        'latitude',
        'longitude'
    ];
}
