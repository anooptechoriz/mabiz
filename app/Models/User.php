<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
        'device_id',
        'gender',
        'country_id',
        'civil_card_no',
        'state',
        'region',
        'expiry_date',
        'profile_pic',
        'phone',
        'language_id',
        'api_token',
        'api_token_expiry',
        'otp',
        'otp_expiry',
        'status',
        'cover_pic',
        'home_location',
        'latitude',
        'longitude',
        'user_type',
        'profile',
        'transport',
        'about',
        'online_status'

    ];
    // public $sortable = ['name', 'email','gender', 'created_at', 'updated_at'];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function Varients(){
        return $this->hasMany('App\Models\Subscription','user_id');
    }
    public function getVarients(){
        $varients = $this->Varients()->orderBy('id','ASC')->get();
        // dd($varients);
        return $varients;
    }
    public function subscribed_services($id)
    {
        $services = Subscription::select('service_languages.service_name','subscriptions.expiry_date')
        ->join('services','services.id','subscriptions.service_id')
            ->leftjoin('service_languages', function ($join) {
                $join->on('service_languages.service_id', '=', 'services.id');
                $join->where('service_languages.language_id', '=', 1);
            })
            ->where('subscriptions.expiry_date', '>=', now())
                ->where('subscriptions.status', 'active')
            ->groupBy('services.id')
            ->where('subscriptions.user_id', $id)->get();
            // dd($services);


        return $services;
    }



}
