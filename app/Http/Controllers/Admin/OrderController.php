<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ServiceLanguages;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    public function index(Request $request)
    {
        $sort_array = ['firstname', 'service_name', 'package_name', 'coupon_code', 'grand_total', 'payment_status'];
        $sort_order = (($request->sort_order == "asc") ? 'ASC' : (($request->sort_order == "desc") ? 'DESC' : ''));
        $order = Order::select('orders.*', 'users.firstname as user', 'service_languages.service_name as service', 'package_languages.package_name as package')
            ->leftjoin('users', 'users.id', 'orders.user_id')
            ->leftjoin('service_languages', function ($join) {
                $join->on('service_languages.service_id', '=', 'orders.service_id');
                $join->where('service_languages.language_id', '=', 1);
            })
            ->leftjoin('package_languages', function ($join) {
                $join->on('package_languages.package_id', '=', 'orders.package_id');
                $join->where('package_languages.language_id', '=', 1);
            });
        // ->leftjoin('service_languages', 'service_languages.id', 'orders.service_id')
        // ->leftjoin('package_languages', 'package_languages.id', 'orders.package_id');

        if ($request->has('sort_by') && $request->has('sort_by') != '' && ($sort_order != '')) {
            if (in_array($request->sort_by, $sort_array)) {
                if ($request->has('sort_order') && $request->sort_order != '') {
                    $order->orderby('service_languages.service_name', $request->sort_order);
                    $order->orderby('users.firstname', $request->sort_order);
                    $order->orderby('package_languages.package_name', $request->sort_order);
                } else {
                    $order->orderBy('orders.' . $request->sort_by, $sort_order);
                }
            }
        } else {
            $order->orderBy('orders.Id', 'DESC');
        }
        if ($request->has('filter_status') && $request->filter_status != '') {

            if ($request->filter_status == 'success') {
                $order->where('orders.payment_status', $request->filter_status);
            } else if ($request->filter_status == 'pending') {

                $order->where('orders.payment_status', $request->filter_status);
            }
        }
        if ($request->has('search_term') && $request->search_term != '') {
            $order->where('service_languages.service_name', 'LIKE', '%' . $request->search_term . '%');
        }
        if ($request->has('filter_service') && $request->filter_service != '') {
            // $order->where('service_languages.service_name', '=', $request->filter_service);
            $order->where('orders.service_id', '=', $request->filter_service);
        }
        $order = $order->latest()->paginate(10)->appends(request()->except('page'));
        // $service_languages = ServiceLanguages::orderBy('service_name')->get();
        $service_languages = ServiceLanguages::where('service_languages.language_id', '=', 1)->get();
        return view('admin.order.index', compact('order', 'service_languages'))->with('i', ($request->input('page', 1) - 1) * 30);
    }
    public function show($id = null)
    {
        $order = Order::select('orders.*', 'users.firstname as user', 'service_languages.service_name as service', 'package_languages.package_name as package', 'countries.name as country')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->leftJoin('countries', 'orders.country_id', '=', 'countries.id')
            ->leftjoin('service_languages', function ($join) {
                $join->on('service_languages.service_id', '=', 'orders.service_id');
                $join->where('service_languages.language_id', '=', 1);
            })
            ->leftjoin('package_languages', function ($join) {
                $join->on('package_languages.package_id', '=', 'orders.package_id');
                $join->where('package_languages.language_id', '=', 1);
            })
            ->where('orders.id', '=', $id)->first();
        return view('admin.order.show', compact('order'));

    }
}
