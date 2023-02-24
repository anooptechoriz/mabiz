<?php

namespace App\Http\Controllers\admin;

use App;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Order;
use App\Models\Role;
use App\Models\Service;
use App\Models\Subscription;
use App\Models\User;
use App\Rules\IsValidPassword;
use App\Rules\MatchOldPassword;
use Auth;
use DB;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    public function index(Request $request)
    {
        $data = [];
        $data['services'] = Service::select(DB::raw('distinct(count(subscriptions.service_id)) as subcount'), 'service_languages.service_name as service')
            ->join('service_languages', function ($join) {
                $join->on('service_languages.service_id', '=', 'services.id');
                $join->where('service_languages.language_id', '=', 1);
            })
            ->join('subscriptions', 'subscriptions.service_id', 'services.id')
            ->groupBy('subscriptions.service_id')->where('subscriptions.status', 'active')->limit(3)
            ->get();
            // dd($data['services']);
            $colors = $this->randColor(3);
            foreach ($data['services'] as $key=> $row) {
            $data['data'][] = $row->subcount;
            $data['label'][] = $row->service;
            $data['services'][$key]['color'] = $colors[$key];
        }

        // dd($data);
        // $colors = $this->randColor(3);

        $data['color'] = $colors;

        $data['chart_data'] = json_encode($data);
        $dateperiod = date('Y-m-d', strtotime("-365 days"));
        $dateGroup = '%M';

        $data['total_services'] = Service::where('status', '=', 'active')->count();
        $data['total_serviceman'] = User::where('user_type', 'service_man')->count();
        $data['total_customers'] = User::where('user_type', 'customer')->count();
        $data['subscriptions'] = Subscription::where('status', '=', 'active')->count();

        //----Get Orders graph records coDe----
        $dateperiod = date('Y-m-d', strtotime("-6 days"));
        $dateGroup = '%Y-%m-%d';
        if ($request->has('GraphType') && $request->GraphType == 'daily') {
            $dateperiod = date('Y-m-d', strtotime("-6 days"));
            $dateGroup = '%Y-%m-%d';
        } elseif ($request->has('GraphType') && $request->GraphType == 'weekly') {
            $dateperiod = date('Y-m-d', strtotime("-28 days"));
            $dateGroup = '%V';
        } elseif ($request->has('GraphType') && $request->GraphType == 'monthly') {

            $dateperiod = date('Y-m-d', strtotime("-365 days"));
            $dateGroup = '%M';
        }
        $subscriptionGraph = Subscription::select(DB::raw("COUNT('orders.id') as orders"),DB::raw("DATE_FORMAT(subscriptions.subscription_date, '$dateGroup') as date"))
        ->join('orders','subscriptions.order_id','orders.id')

        ->where('subscriptions.subscription_date','>=', $dateperiod)
            ->groupBy(DB::raw("DATE_FORMAT(subscriptions.subscription_date, '$dateGroup')"))
            ->get();


        $subscriptionGraphArray = [];
        foreach ($subscriptionGraph as $value) {
            $subscriptionGraphArray[$value->date] = $value->orders;
        }
        $GraphDataArray = array();
        for ($i = 0; $i >= -7; $i--) {
            $GraphDataArray[date('Y-m-d', strtotime($i . " days"))] = ((isset($subscriptionGraphArray[date('Y-m-d', strtotime($i . " days"))])) ? $subscriptionGraphArray[date('Y-m-d', strtotime($i . " days"))] : 0);
        }
        if ($request->has('GraphType') && $request->GraphType == 'daily') {
            $GraphDataArray = [];
            for ($i = 0; $i >= -7; $i--) {

                $GraphDataArray[date('Y-m-d', strtotime($i . " days"))] = ((isset($subscriptionGraphArray[date('Y-m-d', strtotime($i . " days"))])) ? $subscriptionGraphArray[date('Y-m-d', strtotime($i . " days"))] : 0);
            }
        } elseif ($request->has('GraphType') && $request->GraphType == 'weekly') {
            $GraphDataArray = [];
            for ($i = 0; $i >= -3; $i--) {
                $GraphDataArray['Week ' . (abs($i) + 1)] = ((isset($subscriptionGraphArray[date('W', strtotime($i . " weeks"))])) ? $subscriptionGraphArray[date('W', strtotime($i . " weeks"))] : 0);
            }
        } elseif ($request->has('GraphType') && $request->GraphType == 'monthly') {
            $GraphDataArray = [];
            for ($i = 0; $i > -12; $i--) {
                $GraphDataArray[date('F', strtotime($i . " month"))] = ((isset($subscriptionGraphArray[date('F', strtotime($i . " month"))])) ? $subscriptionGraphArray[date('F', strtotime($i . " month"))] : 0);
            }
        }
        $data['GraphDataArray'] = $GraphDataArray;
        // dd($GraphDataArray);
        $GraphDatacolor = $colors;

        $data['GraphDatacolor'] = $GraphDatacolor;

        //----Get Latest 10 orders coDe----



        $subscribedservices = Subscription::select('orders.*', 'service_languages.service_name as service','subscriptions.subscription_date','subscriptions.service_id')
        ->join('orders','subscriptions.order_id','orders.id')

           ->join('services','services.id','subscriptions.service_id')
           ->join('service_languages', function ($join) {
                $join->on('service_languages.service_id', '=', 'services.id');
                $join->where('service_languages.language_id', '=', 1);
            })
            ->latest()->limit(10)->get();

            $upcoming_renewals = Subscription::select('orders.*', 'service_languages.service_name as service','subscriptions.expiry_date','subscriptions.service_id')
            ->join('orders','subscriptions.order_id','orders.id')
            ->join('services','services.id','subscriptions.service_id')


                ->join('service_languages', function ($join) {
                    $join->on('service_languages.service_id', '=', 'services.id');
                    $join->where('service_languages.language_id', '=', 1);
                })
                ->where('subscriptions.expiry_date', '>=', now())
                // ->groupBy('subscriptions.id')
                ->orderBy('subscriptions.expiry_date', 'ASC')
                ->limit(10)->get();


        return view('admin.index', $data, compact('subscriptionGraph','upcoming_renewals',  'subscribedservices'));
    }
    public function randColor($numColors)
    {

        $chars = "ABCDEF0123456789";
        $size = strlen($chars);
        $str = array();

        for ($i = 0; $i < $numColors; $i++) {
            $str[$i] = '';
            $str[$i] .= "#";
            for ($j = 0; $j < 6; $j++) {

                $str[$i] .= $chars[rand(0, $size - 1)];
            }
        }

        return $str;
    }
    public function listAdmin(Request $request)
    {
        $sort_array = ['name', 'email', 'phone', 'job_title', 'role_id', 'status', 'created_at'];
        $sort_order = (($request->sort_order == "asc") ? 'ASC' : (($request->sort_order == "desc") ? 'DESC' : ''));

        $admin = Admin::select('admins.*', 'roles.name as role')
            ->leftjoin('roles', 'roles.id', 'admins.role_id')
            ->where('is_super', '!=', 1);
        if ($request->has('sort_by') && $request->has('sort_by') != '' && ($sort_order != '')) {
            if (in_array($request->sort_by, $sort_array)) {
                $admin->orderBy('admins.' . $request->sort_by, $sort_order);
            }
        } else {
            $admin->orderBy('admins.Id', 'DESC');
        }
        $admin = $admin->latest()->paginate(10)->appends(request()->except('page'));
        return view('admin.administrators.list', compact('admin'))->with('i', ($request->input('page', 1) - 1) * 10);
    }
    public function create()
    {
        $roles = Role::get()->all();
        return view('admin.administrators.create', compact('roles'));
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'admin_email' => 'required|unique:admins,email|regex:/(.+)@(.+)\.(.+)/i',
            'password' => ['required', new IsValidPassword],
            'phone' => 'required|numeric',
            'confirm_password' => ['required', 'same:confirm_password'],
            'role' => 'required',
        ]);
        $file = $request->file('profile_pic');
        $fileName = '';
        if ($file) {
            $this->validate($request, [
                'profile_pic' => 'mimes:jpeg,jpg,png,svg|max:2048',
            ]);
            $fileName = time() . '.' . $request->profile_pic->extension();

            $request->profile_pic->move(public_path('assets/uploads/admin_profile/'), $fileName);
        }
        Admin::create([
            'profile_pic' => $fileName,
            'name' => $request->name,
            'email' => $request->admin_email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'confirm_password' => Hash::make($request->confirm_password),
            'job_title' => $request->job_title,
            'bio' => $request->bio,
            'role_id' => $request->role,
        ]);
        return redirect()->route('admin.list')->with('success', 'Account added successfully');
    }
    public function show($id = null)
    {
        $admin = Admin::select('admins.*', 'roles.name as role')->leftJoin('roles', 'admins.role_id', '=', 'roles.id')->where('admins.id', '=', $id)->first();
        return view('admin.administrators.show', compact('admin'));
    }
    public function edit($id = null)
    {
        $admin = Admin::find($id);
        $roles = Role::get()->all();
        return view('admin.administrators.edit', compact('admin', 'roles'));
    }
    public function update(Request $request, $id = null)
    {
        $this->validate($request, [
            'name' => 'required',
            'admin_email' => 'required|unique:admins,email,' . $id . ',|regex:/(.+)@(.+)\.(.+)/i',
            'phone' => 'required|numeric',
            'role' => 'required',
            'password' => ['nullable', new IsValidPassword],
            'confirm_password' => ['same:confirm_password'],
        ]);
        $Admin = Admin::find($id);

        if ($Admin) {
            $file = $request->file('profile_pic');

            if ($file) {
                $this->validate($request, [
                    'profile_pic' => 'mimes:jpeg,jpg,png,svg|max:2048',
                ]);

                if ($Admin->profile_pic != '') {
                    $image_path = public_path('/assets/uploads/admin_profile/') . '/' . $Admin->profile_pic;
                    File::delete($image_path);
                }
                $fileName = time() . '.' . $request->profile_pic->extension();
                $request->profile_pic->move(public_path('/assets/uploads/admin_profile/'), $fileName);
                if ($request->password) {
                    Admin::find($id)->update([
                        'profile_pic' => $fileName,
                        'name' => $request->name,
                        'email' => $request->admin_email,
                        'phone' => $request->phone,
                        'password' => Hash::make($request->password),
                        'job_title' => $request->job_title,
                        'bio' => $request->bio,
                        'role_id' => $request->role,
                    ]);
                } else {
                    Admin::find($id)->update([
                        'profile_pic' => $fileName,
                        'name' => $request->name,
                        'email' => $request->admin_email,
                        'phone' => $request->phone,
                        'job_title' => $request->job_title,
                        'bio' => $request->bio,
                        'role_id' => $request->role,
                    ]);
                }
            } else {
                if ($file) {
                    $this->validate($request, [
                        'profile_pic' => 'mimes:jpeg,jpg,png,svg|max:2048',
                    ]);
                    if ($Admin->profile_pic != '') {
                        $image_path = public_path('/assets/uploads/admin_profile/') . '/' . $Admin->profile_pic;
                        File::delete($image_path);
                    }
                    $file = $request->file('profile_pic');
                    $fileName = time() . '.' . $request->profile_pic->extension();
                    $request->profile_pic->move(public_path('/assets/uploads/admin_profile/'), $fileName);
                    if ($request->password) {
                        Admin::find($id)->update([
                            'profile_pic' => $fileName,
                            'name' => $request->name,
                            'email' => $request->admin_email,
                            'phone' => $request->phone,
                            'password' => Hash::make($request->password),
                            'confirm_password' => Hash::make($request->confirm_password),
                            'job_title' => $request->job_title,
                            'bio' => $request->bio,
                            'role_id' => $request->role,
                        ]);
                    } else {
                        Admin::find($id)->update([
                            'profile_pic' => $fileName,
                            'name' => $request->name,
                            'email' => $request->admin_email,
                            'phone' => $request->phone,
                            'job_title' => $request->job_title,
                            'bio' => $request->bio,
                            'role_id' => $request->role,
                        ]);
                    }
                } else {
                    if ($request->password) {
                        Admin::find($id)->update([
                            'name' => $request->name,
                            'email' => $request->admin_email,
                            'phone' => $request->phone,
                            'password' => Hash::make($request->password),
                            'confirm_password' => Hash::make($request->confirm_password),
                            'job_title' => $request->job_title,
                            'bio' => $request->bio,
                            'role_id' => $request->role,
                        ]);
                    } else {
                        Admin::find($id)->update([
                            'name' => $request->name,
                            'email' => $request->admin_email,
                            'phone' => $request->phone,
                            'job_title' => $request->job_title,
                            'bio' => $request->bio,
                            'role_id' => $request->role,
                        ]);
                    }
                }
            }
            return redirect()->route('admin.list')->with('success', 'Account updated successfully');
        } else {
            return redirect()->back()->with('error', 'Update failed: Admin profile details not found. Please back to Admin list page and try to edit again.');
        }
    }
    public function destroy($id = null)
    {
        $Admin = Admin::find($id);
        if ($Admin) {
            if ($Admin->profile_pic != '') {
                $image_path = public_path('/assets/uploads/admin_profile/') . '/' . $Admin->profile_pic;
                File::delete($image_path);
            }
            Admin::find($id)->delete();

            return redirect()->route('admin.list')->with('success', 'Account deleted successfully');
        } else {
            return redirect()->back()->withErrors('Delete failed: Admin profile details not found.');
        }
    }
    public function profile()
    {
        if (Auth::guard('admin')->user()) {
            $user_id = Auth::guard('admin')->user()->id;
            $admin = Admin::find($user_id);
            if ($admin) {
                return view('admin.profile.profile', compact('admin'));
            } else {
                return view('admin.notfound_admin')->withErrors('Admin profile details not found.');
            }
        } else {
            return view('admin.notfound_admin')->withErrors('Account is not logged. Please login your account.');
        }
    }
    public function updateprofile(Request $request)
    {
        if (Auth::guard('admin')->user()) {
            $user_id = Auth::guard('admin')->user()->id;

            $Admin = Admin::find($user_id);
            if ($Admin) {
                $this->validate($request, [
                    'name' => 'required',
                    'admin_email' => 'required|regex:/(.+)@(.+)\.(.+)/i|unique:admins,email,' . $user_id,
                ]);
                $file = $request->file('profile_pic');
                if ($file) {
                    $this->validate($request, [
                        'profile_pic' => 'required|mimes:jpeg,jpg,png,svg|max:2048',
                    ]);
                    if ($Admin->profile_pic != '') {
                        $image_path = public_path('/assets/uploads/admin_profile/') . '/' . $Admin->profile_pic;
                        File::delete($image_path);
                    }
                    $file = $request->file('profile_pic');
                    $fileName = time() . '.' . $request->profile_pic->extension();
                    $request->profile_pic->move(public_path('/assets/uploads/admin_profile/'), $fileName);

                    Admin::find($user_id)->update([
                        'profile_pic' => $fileName,
                        'name' => $request->name,
                        'email' => $request->admin_email,
                        'phone' => $request->phone,
                        'job_title' => $request->job_title,
                        'bio' => $request->bio,
                    ]);
                } else {
                    Admin::find($user_id)->update([
                        'name' => $request->name,
                        'email' => $request->admin_email,
                        'phone' => $request->phone,
                        'job_title' => $request->job_title,
                        'bio' => $request->bio,
                    ]);
                }
                return redirect()->route('admin.profile')->with('success', trans('messages.profile') . ' ' . trans("messages.successfully") . ' ' . trans('messages.updated'));
            } else {
                return redirect()->back()->with('error', 'Update failed: Admin profile details not found.');
            }
        } else {
            return redirect()->back()->with('error', 'Update failed: Account is not logged. Please login your account.');
        }
    }
    public function changePassword(Request $request)
    {
        if (Auth::guard('admin')->user()) {
            $userID = Auth::guard('admin')->user()->id;

            $Admin = Admin::find($userID);
            if ($Admin) {
                $request->validate([
                    'current_password' => ['required', new MatchOldPassword],
                    'new_password' => ['required', new IsValidPassword()],
                    'confirm_new_password' => ['required', 'same:new_password'],
                ]);
                Admin::find($userID)->update(['password' => Hash::make($request->new_password)]);
                return redirect()->route('admin.profile')->with('success', trans("messages.password") . ' ' . trans("messages.successfully") . ' ' . trans('messages.updated'));
            } else {
                return redirect()->back()->with('error', 'Update failed: Admin profile details not found.');
            }
        } else {
            return redirect()->back()->with('error', 'Update failed: Account is not logged. Please login your account.');
        }
    }
    public function changeLang($langcode)
    {
        App::setLocale($langcode);
        session()->put("lang_code", $langcode);
        return redirect()->back();
    }
}
