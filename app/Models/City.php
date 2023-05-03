<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id','city_name'
    ];
    public function country_details()
    {
        return $this->belongsTo('App\Models\Country', 'id');
    }
}
