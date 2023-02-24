<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Package extends Model
{

    use SoftDeletes;
    use HasFactory;
    protected $fillable = [
        'service_id',
        'tax_ids',
        'packages',
        'amount',
        'description',
        'validity',
        'amount',
        'offer_price',
    ];
    public function setCategoryAttribute($value)
    {
        $this->attributes['taxes'] = json_encode($value);
    }

    public function getCategoryAttribute($value)
    {
        return $this->attributes['taxes'] = json_decode($value);
    }
}
