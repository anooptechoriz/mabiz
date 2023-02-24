<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaxController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    public function index(Request $request)
    {
        $sort_array = ['tax_name', 'percentage', 'status'];
        $sort_order = (($request->sort_order == "asc") ? 'ASC' : (($request->sort_order == "desc") ? 'DESC' : ''));

        $taxes = Tax::select('taxes.*');
        if ($request->has('sort_by') && $request->has('sort_by') != '' && ($sort_order != '')) {
            if (in_array($request->sort_by, $sort_array)) {
                $taxes->orderBy('taxes.' . $request->sort_by, $sort_order);
            }
        } else {
            $taxes->orderBy('taxes.Id', 'DESC');
        }
        $taxes = $taxes->latest()->paginate(10)->appends(request()->except('page'));
        return view('admin.taxes.index', compact('taxes'))->with('i', ($request->input('page', 1) - 1) * 10);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tax_name' => 'required|unique:taxes',
            'percentage' => 'required|numeric|between:0,100',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->all(),
            ]);
        }
        $TaxArray['tax_name'] = $request->tax_name;
        $TaxArray['percentage'] = $request->percentage;
        $TaxArray['status'] = 'active';

        Tax::create($TaxArray);
        return response()->json(['success' => 'Tax entered successfully.']);
    }
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tax_name' => 'required|unique:taxes,tax_name,' . $request->tax_id,
            'percentage' => 'required|numeric|between:0,100',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->all(),
            ]);
        }
        Tax::find($request->tax_id)->update(['tax_name' => $request->tax_name, 'percentage' => $request->percentage]);
        return redirect()->route('admin.taxes')->with('success', 'Tax updated successfully');
    }
    public function changestatus(Request $request)
    {
        $ajax_status = '';
        $message = '';
        $return_array = [];
        if (Auth::guard('admin')->user()) {
            $id = $request->id;
            $status = $request->status;
            if ($id != '' && $status != '') {
                Tax::find($id)->update([
                    'status' => $request->status,
                ]);
                $message = "Successfully " . $status . " the status";
                $ajax_status = 'success';
            } else {
                $message = "Unable to proceed";
                $ajax_status = 'failed';
            }
        } else {
            $message = "Please login into your account and try again";
            $ajax_status = 'failed';
        }
        $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
        return response()->json($return_array);
    }
}
