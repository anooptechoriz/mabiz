<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    public function index(Request $request)
    {
        $cities = City::select('cities.*','countries.name as country_name')->join('countries', 'countries.id', 'cities.country_id');
        if($request->get('filter_country_id'))
            $cities->where('country_id',$request->get('filter_country_id'));
        $cities = $cities->latest()->paginate(10)->appends(request()->except('page'));
        $countries = Country::get();
        return view('admin.city.index', compact('cities','countries'))->with('i', ($request->input('page', 1) - 1) * 10);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_id' => 'required',
            'city_name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->all(),
            ]);
        }
        $CityArray['city_name'] = $request->city_name;
        $CityArray['country_id'] = $request->country_id; 

        City::create($CityArray);
        return response()->json(['success' => 'City entered successfully.']);
    }
     public function destroy($id = null)
    {
        if ($id != null) {
            City::find($id)->delete(); 
            return redirect()->route('admin.cities')->with('success', 'City deleted successfully');
        } else {
            return redirect()->back()->withErrors('Delete failed. Something went wrong.');
        }
    }
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_id' => 'required',
            'city_name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->all(),
            ]);
        }
        City::find($request->edit_city_id)->update(['city_name' => $request->city_name, 'country_id' => $request->country_id]);
         return response()->json(['success' => 'City updated successfully.']);
    }
    
}
