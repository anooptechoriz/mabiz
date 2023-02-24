<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceLanguages;
use App\Models\Subscription;
use App\Models\Service;

use Auth;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    public function index(Request $request)
    {
        $sort_array = ['firstname', 'service_name', 'package_name', 'subscription_date', 'expiry_date', 'status'];
        $sort_order = (($request->sort_order == "asc") ? 'ASC' : (($request->sort_order == "desc") ? 'DESC' : ''));

        $subscription = Subscription::select('subscriptions.*', 'users.firstname as user', 'service_languages.service_name as service', 'package_languages.package_name as package')
            ->join('users', 'users.id', 'subscriptions.user_id')
            ->join('services', 'services.id', 'subscriptions.service_id')

            ->leftjoin('service_languages', function ($join) {
                $join->on('service_languages.service_id', '=', 'services.id');
                $join->where('service_languages.language_id', '=', 1);
            })
            ->leftjoin('package_languages', function ($join) {
                $join->on('package_languages.package_id', '=', 'subscriptions.package_id');
                $join->where('package_languages.language_id', '=', 1);
            });

        if ($request->has('sort_by') && $request->has('sort_by') != '' && ($sort_order != '')) {
            if (in_array($request->sort_by, $sort_array)) {
                if ($request->has('sort_order') && $request->sort_order != '') {
                    $subscription->orderby('service_languages.service_name', $request->sort_order);
                    $subscription->orderby('users.firstname', $request->sort_order);
                    $subscription->orderby('package_languages.package_name', $request->sort_order);
                } else {
                    $subscription->orderBy('subscriptions.' . $request->sort_by, $sort_order);
                }
            }
        } else {
            $subscription->orderBy('subscriptions.Id', 'DESC');
        }
        if ($request->has('filter_service') && $request->filter_service != '') {
            $subscription->where('subscriptions.service_id', '=', $request->filter_service);
        }
        if ($request->has('filter_status') && $request->filter_status != '') {
            if ($request->filter_status == 'active') {
                $subscription->where('subscriptions.status', $request->filter_status);
            } else if ($request->filter_status == 'expired') {
                $subscription->where('subscriptions.status', $request->filter_status);
            }
        }
        if ($request->has('search_term') && $request->search_term != '') {
            $subscription->where('service_languages.service_name', 'LIKE', '%' . $request->search_term . '%');
        }
        $subscription = $subscription->latest()->paginate(10)->appends(request()->except('page'));
        // $service_languages = ServiceLanguages::orderBy('service_name')->get();
        $service_languages = Service::select('service_languages.*','services.id as service_id')
        ->join('service_languages', function ($join) {
            $join->on('service_languages.service_id', '=', 'services.id');
            $join->where('service_languages.language_id', '=', 1);
        })->where('services.status','active')->get();

        return view('admin.subscription.index', compact('subscription', 'service_languages'))->with('i', ($request->input('page', 1) - 1) * 30);
    }
    public function changestatus(Request $request)
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
                    Subscription::find($id)->update([
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
