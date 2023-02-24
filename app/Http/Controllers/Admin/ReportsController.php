<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CustomerExport;
use App\Exports\ServicemanExport;
use App\Exports\SubscriptionExport;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Service;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    public function customer_reports(Request $request)
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

        // foreach ($customers as $key => $items) {
        //     $country_id = explode(",", $items->country_id);
        //     $customers[$key]['country_details'] = Country::whereIn('id', $country_id)->get();
        // }
        if ($request->has('export')) {
            $customers = $customers->latest()->get();
            foreach ($customers as $key => $items) {
                $country_id = explode(",", $items->country_id);
                $customers[$key]['country_details'] = Country::whereIn('id', $country_id)->get();
            }
            return Excel::download(new CustomerExport($customers), 'Customer_report_export.xlsx');
        } else {
            $customers = $customers->latest()->paginate(10)->appends(request()->except('page'));
            foreach ($customers as $key => $items) {
                $country_id = explode(",", $items->country_id);
                $customers[$key]['country_details'] = Country::whereIn('id', $country_id)->get();
            }

            return view('admin.reports.customer_report', compact('customers'))->with('i', ($request->input('page', 1) - 1) * 10);
        }

    }
    public function serviceman_reports(Request $request)
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

        if ($request->has('export')) {
            $serviceman = $serviceman->latest()->get();
            foreach ($serviceman as $key => $items) {
                $country_id = explode(",", $items->country_id);
                $serviceman[$key]['country_details'] = Country::whereIn('id', $country_id)->get();
            }
            return Excel::download(new ServicemanExport($serviceman), 'Serviceman_report_export.xlsx');
        } else {
            $serviceman = $serviceman->latest()->paginate(10)->appends(request()->except('page'));
            foreach ($serviceman as $key => $items) {
                $country_id = explode(",", $items->country_id);
                $serviceman[$key]['country_details'] = Country::whereIn('id', $country_id)->get();
            }

            return view('admin.reports.serviceman_report', compact('serviceman'))->with('i', ($request->input('page', 1) - 1) * 10);
        }

    }

    public function subscription_reports(Request $request)
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
        // $service_languages = ServiceLanguages::orderBy('service_name')->get();
        $service_languages = Service::select('service_languages.*','services.id as service_id')
            ->join('service_languages', function ($join) {
                $join->on('service_languages.service_id', '=', 'services.id');
                $join->where('service_languages.language_id', '=', 1);
            })->where('services.status', 'active')->get();

        if ($request->has('export')) {
            $subscription = $subscription->latest()->get();

            return Excel::download(new SubscriptionExport($subscription), 'Subscription_report_export.xlsx');
        } else {
            $subscription = $subscription->latest()->paginate(10)->appends(request()->except('page'));

            return view('admin.reports.subscription_report', compact('subscription', 'service_languages'))->with('i', ($request->input('page', 1) - 1) * 30);
        }

    }
}
