<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;use DB;

class Service extends Model
{
    use HasFactory;
    protected $fillable = [
        'service', 'image', 'country_ids', 'parent_id', 'status',
    ];

    public function subservices()
    {
        return $this->hasMany('App\Models\Service', 'parent_id')->where('status', 'active');
    }
    public function getCategories($parent_id,$userlanguage_id=null)
    {
        $img_path = '/assets/uploads/profile/';

        $categories = Service::select('services.id','service_languages.service_name as service_name','services.image','services.parent_id','services.status',DB::raw('CONCAT("' . $img_path . '", image) AS service_image'))
        ->where('parent_id', $parent_id)->where('status','active')
        ->join('service_languages', function ($join) use ($userlanguage_id) {
            $join->on('service_languages.service_id', '=', 'services.id');
            $join->where('service_languages.language_id', '=', $userlanguage_id);
        })
        ->get();

        $categories = $this->addRelation($categories,$userlanguage_id);

        return $categories;
    }
    public function subservicesArray($id)
    {
        $services = Service::select('service.*')
            ->leftjoin('services as parent_services', 'parent_services.id', '=', 'services.parent_id')
            ->leftjoin('service_languages', function ($join) {
                $join->on('service_languages.service_id', '=', 'services.id');
                $join->where('service_languages.language_id', '=', 1);
            })
            ->leftjoin('service_languages as parent_service_languages', function ($join) {
                $join->on('parent_service_languages.service_id', '=', 'parent_services.id');
                $join->where('parent_service_languages.language_id', '=', 1);
            })
            ->select('services.*', 'service_languages.service_name as service', 'parent_service_languages.service_name as parent_service_name')
            ->where('services.parent_id', $id)->get();
        return $services;
    }
    protected function addRelation($services,$userlanguage_id=null)
    {
        $services->map(function ($item, $key)use($userlanguage_id) {
            $sub = $this->selectChild($item->id,$userlanguage_id);

            return $item = $this->array_add($item, 'child_services', $sub);
        });

        return collect($services);
    }

    protected function array_add($array, $key, $value)
    {
        return Arr::add($array, $key, $value);
    }
    public function selectChild($id,$userlanguage_id=null)
    {
        $img_path = '/assets/uploads/services/';

        $services = Service::select('services.id','service_languages.service_name as service_name','services.parent_id','services.status',DB::raw('CONCAT("' . $img_path . '", image) AS service_image'))
        ->join('service_languages', function ($join) use ($userlanguage_id) {
            $join->on('service_languages.service_id', '=', 'services.id');
            $join->where('service_languages.language_id', '=', $userlanguage_id);
        })->where('parent_id', $id)->where('status','active')->get();



        $services = $this->addRelation($services,$userlanguage_id);

        return $services;
    }


}
