<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportedCustomer extends Model
{
    protected $table='reported_customers';
    protected $guarded = [];


    use HasFactory;
}
