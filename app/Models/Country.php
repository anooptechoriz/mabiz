<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','sortname','phonecode'
    ];
    public function country_details()
    {
        return $this->belongsTo('App\Models\Country', 'id');
    }
}
