<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CouponCode;
use Illuminate\Http\Request;

class CouponCodeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $sort_array = ['code', 'description', 'discount', 'validity', 'conditions'];
        $sort_order = (($request->sort_order == "asc") ? 'ASC' : (($request->sort_order == "desc") ? 'DESC' : ''));

        $couponcodes = CouponCode::select('coupon_codes.*');
        if ($request->has('search_keyword') && $request->has('search_keyword') != '') {
            $couponcodes->where('code', 'LIKE', '%' . $request->search_keyword . '%')
                ->orwhere('description', 'LIKE', '%' . $request->search_keyword . '%')
                ->orwhere('discount', 'LIKE', '%' . $request->search_keyword . '%');
        }
        if ($request->has('sort_by') && $request->has('sort_by') != '' && ($sort_order != '')) {
            if (in_array($request->sort_by, $sort_array)) {
                $couponcodes->orderBy('coupon_codes.' . $request->sort_by, $sort_order);
            }
        } else {
            $couponcodes->orderBy('coupon_codes.Id', 'DESC');
        }
        $couponcodes = $couponcodes->latest()->paginate(10);
        return view('admin.CouponCodes.index', compact('couponcodes'))
            ->with('i', (request()->input('page', 1) - 1) * 30);
    }
    public function create()
    {
        return view('admin.CouponCodes.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:coupon_codes,code',
            'description' => 'required',
            'discount' => 'required|numeric|between:0,100',
            'validity' => 'required',
            'conditions' => 'required',

        ]);
        $form_data = array(
            'code' => $request->code,
            'description' => $request->description,
            'discount' => $request->discount,
            'validity' => $request->validity,
            'conditions' => $request->conditions,
        );
        CouponCode::create($form_data);
        return redirect()->route('admin.coupons')->with('success', 'New coupon added successfully.');
    }
    public function edit($id)
    {
        $couponcodes = CouponCode::find($id);
        return view('admin.CouponCodes.edit', compact('couponcodes'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required',
            'description' => 'required',
            'discount' => 'required|numeric|between:0,100',
            'validity' => 'required',
            'conditions' => 'required',

        ]);
        CouponCode::find($id)->update([
            'code' => $request->code,
            'description' => $request->description,
            'discount' => $request->discount,
            'validity' => $request->validity,
            'conditions' => $request->conditions,
        ]);
        return redirect()->route('admin.coupons')->with('success', 'Coupon Updated successfully.');
    }
    public function destroy($id)
    {
        CouponCode::where('id', $id)->delete();
        return redirect()->route('admin.coupons')->with('success', 'Coupon deleted successfully.');
    }

}
