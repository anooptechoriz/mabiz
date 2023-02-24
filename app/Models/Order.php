<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable=['service_id','user_id','package_id','firstname','lastname','civil_card_no','dob','gender','country_id','state','region','address','coupon_code','total_amount','total_tax_amount'
    ,'coupon_discount','grand_total','payment_gateway','client_reference_id','invoice_id','payment_status','fort_id','authorization_code','response_code','response_message'];

}
