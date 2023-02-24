<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class PackageLanguage extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $fillable=[
      'package_id','service_id','package_name','package_description','language_id'
      ];

}
