<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Document;
use App\Models\Language;
use App\Models\Package;
use App\Models\PackageLanguage;
use App\Models\Service;
use App\Models\ServiceLanguages;
use App\Models\Tax;
use Auth;
use DB;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    public function index(Request $request)
    {
        $sort_array = ['service_name', 'image', 'country_ids', 'subscription', 'parent_id', 'status'];
        $sort_order = (($request->sort_order == "asc") ? 'ASC' : (($request->sort_order == "desc") ? 'DESC' : ''));

        $services = Service::select(DB::raw('COUNT(subscriptions.id) as subscription'), 'services.*', 'service_languages.service_name as service_name', 'parent_service_languages.service_name as parent_service_name', )
            ->leftjoin('subscriptions', 'subscriptions.service_id', 'services.id')
            ->leftjoin('services as parent_services', 'parent_services.id', '=', 'services.parent_id')
            ->leftjoin('service_languages', function ($join) {
                $join->on('service_languages.service_id', '=', 'services.id');
                $join->where('service_languages.language_id', '=', 1);
            })
            ->leftjoin('service_languages as parent_service_languages', function ($join) {
                $join->on('parent_service_languages.service_id', '=', 'parent_services.id');
                $join->where('parent_service_languages.language_id', '=', 1);
            });
        if ($request->has('filter_Status') && $request->filter_Status != '') {
            $services->where('services.status', $request->filter_Status);

        }
        if ($request->has('sort_by') && $request->has('sort_by') != '' && ($sort_order != '')) {
            if (in_array($request->sort_by, $sort_array)) {

                if ($request->has('sort_order') && $request->sort_order != '') {
                    $services->orderby('service_languages.service_name', $request->sort_order);
                } else {
                    $services->orderBy('services.' . $request->sort_by, $sort_order);
                }
            }
        } else {
            $services->orderBy('services.Id', 'DESC');
        }
        if ($request->has('search_keyword') && $request->has('search_keyword') != '') {
            $services->where('service_languages.service_name', 'LIKE', '%' . $request->search_keyword . '%');
        }
        $services = $services->whereIn('services.status', ['active', 'disabled'])->orderBy('created_at', 'desc')->groupBy('services.id')->latest()->paginate(8)->appends(request()->except('page'));

        foreach ($services as $key => $items) {
            $country_ids = explode(",", $items->country_ids);
            $services[$key]['country_details'] = Country::whereIn('id', $country_ids)->get();
            // $count++;
        }
        // $services = $services->whereIn('services.status', ['active','disabled'])->orderBy('service_languages.service_name', 'ASC')->groupBy('services.id')->latest()->paginate(8)->appends(request()->except('page'));
        $taxes = Tax::where('status', 'active')->get();
        return view('admin.services.index', compact('services', 'taxes'))->with('i', ($request->input('page', 1) - 1) * 30);
    }
    public function create()
    {
        $parentservices = Service::select('services.*', 'service_languages.service_name as service', 'parent_service_languages.service_name as parent_service_name')
            ->leftjoin('services as parent_services', 'parent_services.id', '=', 'services.parent_id')
            ->leftjoin('service_languages', function ($join) {
                $join->on('service_languages.service_id', '=', 'services.id');
                $join->where('service_languages.language_id', '=', 1);
            })
            ->leftjoin('service_languages as parent_service_languages', function ($join) {
                $join->on('parent_service_languages.service_id', '=', 'parent_services.id');
                $join->where('parent_service_languages.language_id', '=', 1);
            })
            ->where('services.parent_id', 0)->where('services.status', 'active')->orderBy('services.service', 'ASC')->distinct()->get();
        $countries = Country::all();
        return view('admin.services.create', compact('parentservices', 'countries'));
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'service_countries.*' => 'required',
            'service_name.*' => 'required',
        ]);
        $file = $request->file('image');
        $fileName = '';
        if ($file) {
            $this->validate($request, [
                'image' => 'required|mimes:jpeg,png,svg|max:2048',
                // 'image' => 'mimes:jpeg,jpg,png,svg|max:1048|dimensions:max_width=50,max_height=50',
            ]);
            $fileName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('/assets/uploads/service/'), $fileName);
        }
        $parent_id = 0;
        if ($request->selected_service) {
            $parent_id = $request->selected_service;
        }
        $country_id = (isset($request->service_countries)) ? $request->service_countries : array();
        $updated_country_ids = implode(',', $country_id);
        $serviceID = Service::create([
            'service' => $request->service,
            'country_ids' => $updated_country_ids,
            'parent_id' => $parent_id,
            'image' => $fileName,
        ])->id;
        if ($request->service_name_english != '') {
            $language = Language::where('shortcode', 'en')->first();
            $servicelanguage = ServiceLanguages::create([
                'service_id' => $serviceID,
                'language_id' => $language->id,
                'service_name' => $request->service_name_english,
            ]);
        }
        if ($request->service_name_arabic != '') {
            $language = Language::where('shortcode', 'ar')->first();
            $servicelanguage = ServiceLanguages::create([
                'service_id' => $serviceID,
                'language_id' => $language->id,
                'service_name' => $request->service_name_arabic,
            ]);
        }
        if ($request->service_name_hindi != '') {
            $language = Language::where('shortcode', 'hi')->first();
            $servicelanguage = ServiceLanguages::create([

                'service_id' => $serviceID,
                'language_id' => $language->id,
                'service_name' => $request->service_name_hindi,
            ]);
        }
        if ($serviceID) {
            return redirect()->route('admin.services')->with('success', 'Service added successfully');
        } else {
            return redirect()->route('admin.services')->with('error', 'Something went wrong Service add failed');
        }
    }
    public function show($id)
    {
        $taxes = Tax::where('status', 'active')->get();
        $documents = Document::where('service_id', $id)->latest()->get();
        $parentServices = Service::where('status', 'active')->where('parent_id', 0)->get();
        $packages = Package::select('packages.*', 'package_languages.package_name as package', 'package_languages.package_description as descriptions', 'taxes.tax_name as taxes')
            ->leftjoin('taxes', 'taxes.id', 'packages.tax_ids')
            ->leftjoin('package_languages', function ($join) {
                $join->on('package_languages.package_id', '=', 'packages.id');
                $join->where('package_languages.language_id', '=', 1);
            })
            ->where('service_id', $id)->latest()->get();
        $count = 0;
        foreach ($packages as $key => $items) {
            $tax_ids = explode(',', $items->tax_ids);
            $packages[$key]['tax_details'] = Tax::whereIn('id', $tax_ids)->get();
            $languageItems = PackageLanguage::select('package_languages.package_name', 'package_languages.package_description', 'package_languages.language_id')
                ->where('package_id', $items->id)->get();
            $packages[$count]['language_items'] = $languageItems;
            $count++;
        }
        // dd($packages);
                $services = Service::select('services.*', 'service_languages.service_name as service', 'parent_service_languages.service_name as parent_service_name')
            ->leftjoin('services as parent_services', 'parent_services.id', '=', 'services.parent_id')
            ->leftjoin('service_languages', function ($join) {
                $join->on('service_languages.service_id', '=', 'services.id');
                $join->where('service_languages.language_id', '=', 1);
            })
            ->leftjoin('service_languages as parent_service_languages', function ($join) {
                $join->on('parent_service_languages.service_id', '=', 'parent_services.id');
                $join->where('parent_service_languages.language_id', '=', 1);
            })
            ->where('services.parent_id', $id)->where('services.status', 'active')->orderBy('created_at', 'desc')->distinct()->paginate(30)->appends(request()->except('page'));
            // dd($services);

        if ($id) {
            $serviceDetails = Service::select('services.*', 'service_languages.service_name as service', 'parent_service_languages.service_name as parent_service_name')
                ->leftjoin('services as parent_services', 'parent_services.id', '=', 'services.parent_id')
                ->leftjoin('service_languages', function ($join) {
                    $join->on('service_languages.service_id', '=', 'services.id');
                    $join->where('service_languages.language_id', '=', 1);
                })
                ->leftjoin('service_languages as parent_service_languages', function ($join) {
                    $join->on('parent_service_languages.service_id', '=', 'parent_services.id');
                    $join->where('parent_service_languages.language_id', '=', 1);
                })
                ->where('services.id', $id)->first();

            $country_ids = explode(",", $serviceDetails->country_ids);
            $serviceDetails->country_details = Country::whereIn('id', $country_ids)->get();

            $servicelanguages = ServiceLanguages::select('service_languages.*', 'languages.shortcode')->where('service_id', $id)
                ->join('languages', 'languages.id', 'service_languages.language_id')->get();
            return view('admin.services.show', compact('serviceDetails', 'packages', 'services', 'parentServices', 'documents', 'taxes', 'servicelanguages'));
        }
    }
    public function edit($id = null)
    {
        $servicelanguages = ServiceLanguages::select('service_languages.*', 'languages.shortcode')->where('service_id', $id)
            ->join('languages', 'languages.id', 'service_languages.language_id')->get();
        $services = Service::find($id);
        if ($services) {
            if ($services->status == 'active') {
                $parentServices = Service::select('services.*', 'service_languages.service_name as service', 'parent_service_languages.service_name as parent_service_name')
                    ->leftjoin('services as parent_services', 'parent_services.id', '=', 'services.parent_id')
                    ->leftjoin('service_languages', function ($join) {
                        $join->on('service_languages.service_id', '=', 'services.id');
                        $join->where('service_languages.language_id', '=', 1);
                    })
                    ->leftjoin('service_languages as parent_service_languages', function ($join) {
                        $join->on('parent_service_languages.service_id', '=', 'parent_services.id');
                        $join->where('parent_service_languages.language_id', '=', 1);
                    })
                    ->where('services.parent_id', 0)->where('services.status', 'active')->orderBy('services.service', 'ASC')->distinct()->get();
                $pre_countries = $services->country_ids;

                if ($pre_countries) {
                    if ($pre_countries != '') {
                        $arr_country = explode(",", $pre_countries);
                        $arr_countries = Country::whereIn('id', $arr_country)->get();
                    }
                } else {
                    $arr_countries = array();
                }
                return view('admin.services.edit', compact('services', 'parentServices', 'arr_countries', 'servicelanguages'));
            } else {
                return redirect()->back()->withErrors('Cannot edit this service. Not able to edit deleted category.');
            }
        } else {
            return redirect()->back()->withErrors('Cannot edit this service. Category details not found.');
        }
    }
    public function update(Request $request, $id = null)
    {
        $servicelanguage = ServiceLanguages::where('service_id', $id)->get();
        $serviceDetails = Service::find($id);
        if ($serviceDetails) {
            $file = $request->file('image');
            $country_id = (isset($request->service_countries)) ? $request->service_countries : array();
            $updated_country_ids = implode(',', $country_id);
            if ($file) {
                $this->validate($request, [
                    'image' => 'required|mimes:jpeg,png,svg|max:2048',
                    // 'service' => 'required|unique:services,service,' . $id,
                ]);
                if ($serviceDetails->image != '') {
                    $imagefile = public_path('/assets/uploads/service/') . '/' . $serviceDetails->image;
                    File::delete($imagefile);
                }
                $fileName = time() . '.' . $request->image->extension();
                $request->image->move(public_path('/assets/uploads/service/'), $fileName);
                $serviceID = Service::find($id)->update([
                    'image' => $fileName,
                    'parent_id' => $request->selected_service,
                    // 'tax_ids' => $taxIDs,
                    'service' => $request->name,
                    'country_ids' => $updated_country_ids,
                ]);
            } else {

                $serviceID = Service::find($id)->update([
                    'parent_id' => $request->selected_service,
                    'service' => $request->service,
                    // 'tax_ids' => $taxIDs,
                    'country_ids' => $updated_country_ids,
                ]);
            }
            if ($request->service_name_english != '') {

                $language = Language::where('shortcode', 'en')->first();
                ServiceLanguages::where('service_id', $id)->where('language_id', $language->id)->update([
                    'service_id' => $id,
                    'language_id' => $language->id,
                    'service_name' => $request->service_name_english,
                ]);
            }
            if ($request->service_name_arabic != '') {
                $language = Language::where('shortcode', 'ar')->first();
                ServiceLanguages::where('service_id', $id)->where('language_id', $language->id)->update([
                    'service_id' => $id,
                    'language_id' => $language->id,
                    'service_name' => $request->service_name_arabic,
                ]);

            }
            if ($request->service_name_hindi != '') {
                $language = Language::where('shortcode', 'hi')->first();
                ServiceLanguages::where('service_id', $id)->where('language_id', $language->id)->update([
                    'service_id' => $id,
                    'language_id' => $language->id,
                    'service_name' => $request->service_name_hindi,
                ]);
            }
            return redirect()->route('admin.services')->with('success', 'Service updated successfully');
        } else {
            return redirect()->back()->withErrors('Sorry.. Update failed. services details not found.');
        }
    }
    public function destroy($id = null)
    {
        $serviceDetails = Service::find($id);

        if ($serviceDetails) {
            $notDeletable = Service::where('parent_id', $serviceDetails->id)->where('status', 'active')->exists();
            if (!$notDeletable) {
                Service::find($id)->update([
                    'status' => 'deleted',
                ]);
                return redirect()->route('admin.services')->with('success', 'Service deleted successfully');
            } else {
                return redirect()->back()->withErrors('Delete failed. Cannot delete the Service. Child category found in this Service.');
            }
        } else {
            return redirect()->back()->withErrors('Sorry.. Delete failed. Service details not found.');
        }
    }
    public function searchContries(Request $request)
    {
        if ($request->ajax()) {

            $data = Country::where('name', 'LIKE', $request->country . '%')->get();

            $output = '';
            if (count($data) > 0) {
                $output = '<ul class="list-group">';
                foreach ($data as $row) {
                    $output .= '<li class="list-group-item" onclick="select_country(' . $row->id . ',\'' . $row->name . '\')"><span>' . ucfirst($row->name) . '<i class="fa fa-plus"></i></span></li>';
                }
                $output .= '</ul>';
            } else {
                $output .= '<li class="list-group-item">' . 'No results' . '</li>';
            }
            return $output;
        }
    }
    public function country_destroy(Request $request)
    {
        if ($request->ajax()) {
            $country_id = $request->religion_id;
            $religion_castes = Caste::where('religion_id', $religion_id)->get();

            foreach ($religion_castes as $caste) {
                $removed_castes[] = $caste->id;
            }
            return ['success' => true, 'removed_castes' => $removed_castes];
        }
    }
    public function packages_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package_in_english' => 'required',
            'package_in_arabic' => 'required',
            'package_in_hindi' => 'required',
            'amount' => 'required|numeric|gt:0',
            'offer_price' => 'numeric|nullable|lt:amount',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->all(),
            ]);
        }
        $taxIDs = null;
        if ($request->taxes) {
            $taxIDs = implode(",", $request->taxes);
        }
        $packageID = Package::create([
            'service_id' => $request->service_id,
            'tax_ids' => $taxIDs,
            'validity' => $request->validity,
            'amount' => $request->amount,
            'offer_price' => $request->offer_price,
        ])->id;

        if (($request->package_in_english != '') || ($request->description_in_english != '')) {
            $language = Language::where('shortcode', 'en')->first();
            $packagelanguage = PackageLanguage::create([
                'package_id' => $packageID,
                'language_id' => $language->id,
                'package_name' => $request->package_in_english,
                'package_description' => $request->description_in_english,
            ]);
        }
        if (($request->package_in_arabic != '') || ($request->description_in_arabic != '')) {
            $language = Language::where('shortcode', 'ar')->first();
            $packagelanguage = PackageLanguage::create([
                'package_id' => $packageID,
                'language_id' => $language->id,
                'package_name' => $request->package_in_arabic,
                'package_description' => $request->description_in_arabic,

            ]);
        }
        if (($request->package_in_hindi != '') || ($request->description_in_hindi != '')) {
            $language = Language::where('shortcode', 'hi')->first();
            $packagelanguage = PackageLanguage::create([
                'package_id' => $packageID,
                'language_id' => $language->id,
                'package_name' => $request->package_in_hindi,
                'package_description' => $request->description_in_hindi,

            ]);
        }
        return response()->json(['success' => 'Package created successfully.']);
    }
    public function package_update(Request $request, $id = null)
    {
        $validator = Validator::make($request->all(), [
            'package_in_english' => 'required',
            'package_in_arabic' => 'required',
            'package_in_hindi' => 'required',
            'amount' => 'required|numeric|gt:0',
            'offer_price' => 'numeric|nullable|lt:amount',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->all(),
            ]);
        }
        $taxIDs = null;
        if ($request->taxes) {
            $taxIDs = implode(",", $request->taxes);
        }
        $packageDetails = Package::find($request->package_id);
        if ($packageDetails) {
            $packageID = Package::find($request->package_id)->update([

                'tax_ids' => $taxIDs,
                'validity' => $request->validity,
                'amount' => $request->amount,
                'offer_price' => $request->offer_price,
            ]);
        }
        if (($request->package_in_english != '') || ($request->description_in_english != '')) {
            $language = Language::where('shortcode', 'en')->first();
            PackageLanguage::where('package_id', $request->package_id)->where('language_id', $language->id)->update([
                'language_id' => $language->id,
                'package_name' => $request->package_in_english,
                'package_description' => $request->description_in_english,

            ]);
        }
        if (($request->package_in_arabic != '') || ($request->description_in_arabic != '')) {
            $language = Language::where('shortcode', 'ar')->first();
            PackageLanguage::where('package_id', $request->package_id)->where('language_id', $language->id)->update([
                'language_id' => $language->id,
                'package_name' => $request->package_in_arabic,
                'package_description' => $request->description_in_arabic,

            ]);
        }
        if (($request->package_in_hindi != '') || ($request->description_in_hindi != '')) {
            $language = Language::where('shortcode', 'hi')->first();
            PackageLanguage::where('package_id', $request->package_id)->where('language_id', $language->id)->update([
                'language_id' => $language->id,
                'package_name' => $request->package_in_hindi,
                'package_description' => $request->description_in_hindi,

            ]);
        }
        return response()->json(['success' => 'Package updated successfully.']);
    }
    public function package_destroy($id)
    {
        Package::where('id', $id)->delete();
        return redirect()->back()->with('warning', 'Your  Package deleted successfully.');
    }
    public function sub_services_store(Request $request, $id = null)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'service_name_english' => 'required',
            'service_name_arabic' => 'required',
            'service_name_hindi' => 'required',
            'image' => 'required|mimes:jpeg,png,svg|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        }
        $file = $request->file('image');
        $fileName = '';
        if ($file) {

            $this->validate($request, [
                'image' => 'required|mimes:jpeg,png,svg|max:2048',
            ]);
            $fileName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('/assets/uploads/service/'), $fileName);
        }

        $parent_id = $request->service_id;
        $parent_service = Service::find($parent_id);
        if ($request->selected_service) {
            $parent_id = $request->selected_service;
        }
        $country_id = (isset($request->service_countries)) ? $request->service_countries : array();
        $updated_country_ids = implode(',', $country_id);
        $serviceID = Service::create([
            'country_ids' => $parent_service->country_ids,
            'parent_id' => $parent_id,
            'image' => $fileName,

        ])->id;
        if ($request->service_name_english != '') {
            $language = Language::where('shortcode', 'en')->first();
            $servicelanguage = ServiceLanguages::create([
                'service_id' => $serviceID,
                'language_id' => $language->id,
                'service_name' => $request->service_name_english,
            ]);
        }
        if ($request->service_name_arabic != '') {
            $language = Language::where('shortcode', 'ar')->first();
            $servicelanguage = ServiceLanguages::create([
                'service_id' => $serviceID,
                'language_id' => $language->id,
                'service_name' => $request->service_name_arabic,
            ]);
        }
        if ($request->service_name_hindi != '') {
            $language = Language::where('shortcode', 'hi')->first();
            $servicelanguage = ServiceLanguages::create([
                'service_id' => $serviceID,
                'language_id' => $language->id,
                'service_name' => $request->service_name_hindi,
            ]);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Sub Service entered successfully',
        ]);
    }
    public function document_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->all(),
            ]);
        }
        $documents = Document::create([
            'service_id' => $request->service_id,
            'document' => $request->document,
        ]);
        return response()->json(['success' => 'Document added successfully.']);
    }
    public function document_update(Request $request, $id = null)
    {
        $validator = Validator::make($request->all(), [
            'document' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->all(),
            ]);
        }
        $documentDetails = Document::find($request->document_id);
        if ($documentDetails) {
            $documentID = Document::find($request->document_id)->update([

                'document' => $request->document,
            ]);
        }
        return response()->json(['success' => 'Document edited successfully.']);
    }
    public function document_destroy($id)
    {
        Document::where('id', $id)->delete();
        return redirect()->back()->with('success', 'Your  Document deleted successfully.');
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
                    Service::find($id)->update([
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
    public function remove_image(Request $request)
    {
        $services = Service::find($request->id);
        if (!empty($services)) {
            $file_path = public_path('/assets/uploads/service/') . $services->image;
            File::delete($file_path);
        }
        Service::find($request->id)->update(['image' => '']);
        $message = "Successfully deleted";
        $ajax_status = 'success';
        $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
        return response()->json($return_array);
    }
}
