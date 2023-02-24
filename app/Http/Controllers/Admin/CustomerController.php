<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $sort_array = ['firstname', 'email', 'phone', 'gender', 'dob', 'country_id', 'state', 'region', 'about', 'status', 'created_at'];
        $sort_order = (($request->sort_order == "asc") ? 'ASC' : (($request->sort_order == "desc") ? 'DESC' : ''));

        $customers = User::select('users.*');

        if ($request->has('search_keyword') && $request->has('search_keyword') != '') {
            $customers->where('firstname', 'LIKE', '%' . $request->search_keyword . '%');
            //   ->orwhere('email','LIKE','%'.$request->search_keyword.'%')
            //   ->orwhere('phone','LIKE','%'.$request->search_keyword.'%');
        }
        if ($request->has('filter_Status') && $request->filter_Status != '') {
            $customers->where('users.status', $request->filter_Status);

        }
        if ($request->has('sort_by') && $request->has('sort_by') != '' && ($sort_order != '')) {
            if (in_array($request->sort_by, $sort_array)) {
                $customers->orderBy('users.' . $request->sort_by, $sort_order);
            }
        } else {
            $customers->orderBy('users.Id', 'DESC');
        }
        $customers->where('user_type', 'customer');

        $customers = $customers->latest()->paginate(10)->appends(request()->except('page'));
        foreach ($customers as $key => $items) {
            $country_id = explode(",", $items->country_id);
            $customers[$key]['country_details'] = Country::whereIn('id', $country_id)->get();
        }
        return view('admin.customers.index', compact('customers'))->with('i', ($request->input('page', 1) - 1) * 10);

    }
    public function show($id)
    {
        if ($id) {
            $CustomerDetails = User::where('id', $id)->first();
            $CustomerDetails = User::select('users.*','countries.name as country')->join('countries','users.country_id','=','countries.id')->where('users.id','=', $id)->first();
            return view('admin.customers.show', compact('CustomerDetails'));
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
    public function reported_customer(Request $request)
    {
        $sort_array = ['firstname', 'email', 'phone', 'gender','country_id','status'];
        $sort_order = (($request->sort_order == "asc") ? 'ASC' : (($request->sort_order == "desc") ? 'DESC' : ''));

        $customers = User::select('users.*')
        ->join('reported_customers','reported_customers.customer_id','users.id');

        if ($request->has('search_keyword') && $request->has('search_keyword') != '') {
            $customers->where('firstname', 'LIKE', '%' . $request->search_keyword . '%');
            //   ->orwhere('email','LIKE','%'.$request->search_keyword.'%')
            //   ->orwhere('phone','LIKE','%'.$request->search_keyword.'%');
        }
        if ($request->has('filter_Status') && $request->filter_Status != '') {
            $customers->where('users.status', $request->filter_Status);

        }
        if ($request->has('sort_by') && $request->has('sort_by') != '' && ($sort_order != '')) {
            if (in_array($request->sort_by, $sort_array)) {
                $customers->orderBy('users.' . $request->sort_by, $sort_order);
            }
        } else {
            $customers->orderBy('users.Id', 'DESC');
        }
        // $customers->where('user_type', 'customer');

        $customers = $customers->groupBy('users.id')->latest()->paginate(10)->appends(request()->except('page'));
        foreach ($customers as $key => $items) {
            $country_id = explode(",", $items->country_id);
            $customers[$key]['country_details'] = Country::whereIn('id', $country_id)->get();
        }
        return view('admin.reported_customer.index', compact('customers'))->with('i', ($request->input('page', 1) - 1) * 10);

    }

}
