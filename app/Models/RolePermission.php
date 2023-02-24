<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class RolePermission extends Model
{
    use Notifiable;
    protected $fillable = [
        'role_id','permission_id'
    ];
}
