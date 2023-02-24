<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;


class Tax extends Model
{
    // use SoftDeletes;
    use HasFactory;
    protected $fillable = [
        'tax_name',
        'percentage',
        'status',
    ];
}
