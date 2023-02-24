<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceLanguages extends Model
{
    use HasFactory;
    protected $fillable=[
      'service_id','service_name','language_id'
    ];
}
