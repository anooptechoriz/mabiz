<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Service;
use App\Models\User;
use Auth;
use DB;
use Illuminate\Http\Request;

class ServiceManController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    public function index(Request $request)
    {
        $sort_array = ['firstname', 'email', 'phone', 'gender', 'dob', 'country_id', 'state', 'region', 'about', 'status', 'created_at', 'service', 'expiry_date', 'coupon_code', 'civil_card_no', 'about', 'transport', 'profile'];
        $sort_order = (($request->sort_order == "asc") ? 'ASC' : (($request->sort_order == "desc") ? 'DESC' : ''));

        $serviceman = User::select('users.*');

        if ($request->has('search_keyword') && $request->has('search_keyword') != '') {
            $serviceman->where('firstname', 'LIKE', '%' . $request->search_keyword . '%');
            //    ->orwhere('email','LIKE','%'.$request->search_keyword.'%')
            //    ->orwhere('phone','LIKE','%'.$request->search_keyword.'%');
        }
        if ($request->has('filter_Status') && $request->filter_Status != '') {
            $serviceman->where('users.status', $request->filter_Status);

        }
        if ($request->has('sort_by') && $request->has('sort_by') != '' && ($sort_order != '')) {
            if (in_array($request->sort_by, $sort_array)) {
                $serviceman->orderBy('users.' . $request->sort_by, $sort_order);
            }
        } else {
            $serviceman->orderBy('users.Id', 'DESC');
        }
        $serviceman->where('user_type', 'service_man');

        $serviceman = $serviceman->latest()->paginate(10)->appends(request()->except('page'));
        foreach ($serviceman as $key => $items) {
            $country_id = explode(",", $items->country_id);
            $serviceman[$key]['country_details'] = Country::whereIn('id', $country_id)->get();
        }

        return view('admin.serviceman.index', compact('serviceman'))->with('i', ($request->input('page', 1) - 1) * 10);
    }
    public function show(User $user, $id)
    {
        if ($id) {
            $servicemanDetails = User::select('users.*', 'countries.name as country')->leftJoin('countries', 'users.country_id', '=', 'countries.id')->where('users.id', '=', $id)->first();
            $services = Service::select(DB::raw('distinct(count(subscriptions.service_id)) as subcount'), 'subscriptions.*', 'users.firstname as user', 'service_languages.service_name as service', 'package_languages.package_name as package')
                ->join('subscriptions', 'subscriptions.service_id', 'services.id')
                ->join('users', 'users.id', 'subscriptions.user_id')
                ->join('service_languages', function ($join) {
                    $join->on('service_languages.service_id', '=', 'services.id');
                    $join->where('service_languages.language_id', '=', 1);
                })
                ->join('packages', 'packages.id', 'subscriptions.package_id')
                ->join('package_languages', function ($join) {
                    $join->on('package_languages.package_id', '=', 'packages.id');
                    $join->where('package_languages.language_id', '=', 1);
                })
                ->where('users.id', $id)
            // ->groupBy('subscriptions.service_id')
                ->get();
            return view('admin.serviceman.show', compact('servicemanDetails', 'services'));
        }
    }

    public function changeStatus(Request $request)
    {
        $ajax_status = '';
        $message = '';
        $return_array = [];
        if (Auth::guard('admin')->user()->id) {
            $user_id = Auth::guard('admin')->user()->id;
            if (empty($user_id)) {
                $message = "Please login into your account and try again";
                $ajax_status = 'failed';
            } else {
                $id = $request->id;
                $status = $request->status;
                if ($id != '' && $status != '') {
                    User::find($id)->update([
                        'status' => $request->status,
                    ]);
                    $message = "Successfully updated";
                    $ajax_status = 'success';
                } else {
                    $message = "Unable to proceed";
                    $ajax_status = 'failed';
                }
            }
        } else {
            $message = "Please login into your account and try again";
            $ajax_status = 'failed';
        }
        $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
        return response()->json($return_array);
    }
}
