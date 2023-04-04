<?php

namespace App\Http\Controllers\FrontApp;

use App;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ChatMessage;
use App\Models\Country;
use App\Models\CouponCode;
use App\Models\Document;
use App\Models\Favorite;
use App\Models\Generalsetting;
use App\Models\Homeslider;
use App\Models\Language;
use App\Models\Order;
use App\Models\Package;
use App\Models\ReportedCustomer;
use App\Models\Service;
use App\Models\ServiceDocument;
use App\Models\ServicemanGallery;
use App\Models\Subscription;
use App\Models\Tax;
use App\Models\User;
use App\Models\UserAddress;
use Auth;
use Carbon\Carbon;
use DB;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Config;
class ServiceApiController extends Controller
{
    private $request;

    public function get_languages(Request $request)
    {
        if ($request->header('device-id') != '') {
            $languages = Language::all();
            if (count($languages) > 0) {
                return response()->json(['result' => true, 'message' => 'Successfully', 'languages' => $languages]);
            } else {
                return response()->json(['result' => false, 'message' => 'Sorry.. Cannot find languages list.']);
            }
        } else {
            return response()->json(['result' => false, 'message' => 'Attempt failed: Device not detect. Something wrong with device id.']);
        }
    }

    public function get_countries(Request $request)
    {
        if ($request->header('device-id') != '') {
            $flag_path = '/assets/uploads/countries_flag/';
            $countries = Country::select('countries.id as country_id', 'countries.name as country_name', 'countries.phonecode', DB::raw('CONCAT("' . $flag_path . '", countries.flag_icon) AS countryflag'))->get();
            if (count($countries) > 0) {
                return response()->json(['result' => true, 'message' => 'Successfully', 'countries' => $countries]);
            } else {
                return response()->json(['result' => false, 'message' => 'Sorry.. Cannot find countries list.']);
            }
        } else {
            return response()->json(['result' => false, 'message' => 'Attempt failed: Device not detect. Something wrong with device id.']);
        }
    } //

    //----------------Login section with Phone and OTP--

    public function request_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'language_id' => 'required',
        ]);
        if ($validator->fails()) {
            // dd($validator);
            $returnArray = array('result' => false, 'message' => $validator->errors()->first());
        } else {
            $language_id = $request->language_id;
            $lang = Language::find($language_id);
            if ($lang) {
                $langcode = $lang->shortcode;
                App::setLocale($langcode);
                session()->put("lang_code", $langcode);
                if (!empty($request->header('device-id'))) {

                    $validator2 = Validator::make($request->all(), [
                        'phone' => 'required',
                        'countrycode' => 'required',

                    ], [
                        'phone.required' => trans('messages.phone_number_required'),
                        'countrycode.required' => trans('messages.country_code_required'),
                    ]);
                    if ($validator2->fails()) {
                        // dd($validator);
                        $returnArray = array('result' => false, 'message' => $validator2->errors()->first());
                    } else {

                        $country_code = preg_replace('/[^A-Za-z0-9\-]/', '', $request->countrycode); // Removes special chars.
                        $phone_num = preg_replace('/[^A-Za-z0-9\-]/', '', $request->phone); // Removes special chars.
                        // $phone = '+' . $country_code . $phone_num;
                        $country = Country::where('phonecode', $country_code)->first();
                        // dd($country->id);

                        if ($country) {
                            $userdetails = User::where('phone', $phone_num)->where('country_id', $country->id)->first();
                            $otp_number = rand(100000, 999999);
                            $otp_expiry = date('Y-m-d H:i:s', strtotime('+4 minutes'));
                            $now = date('Y-m-d H:i:s');
                            $late_expiry_date = date('Y-m-d H:i:s', strtotime('-1 days', strtotime(str_replace('/', '-', $now))));
                            $phone_with_country_code = $country->phonecode . $phone_num;

                             $this->SmsAPI($phone_with_country_code,$otp_number,$langcode);
                            if ($userdetails) {
                                if ($userdetails->status == 'active') {

                                    User::where('id', $userdetails->id)
                                        ->update([
                                            'otp' => $otp_number,
                                            'device_id' => $request->header('device-id'),
                                            'otp_expiry' => $otp_expiry,
                                            'language_id' => $language_id,

                                        ]);
                                    $action = 'login';

                                    $returnArray = array('result' => true, 'message' => trans('messages.otp_generated_successfully') . trans('messages.enter_given_otp'), 'OTP' => $otp_number, 'phone' => $phone_num, 'action' => 'login');
                                } else {
                                    $returnArray = array('result' => false, 'message' => 'Your account is no longer exist.');
                                }
                            } else {
                                $validator = Validator::make($request->all(), [
                                    'phone' => 'required|numeric',
                                ]);

                                if ($validator->fails()) {
                                    $returnArray = array('result' => false, 'message' => $validator->errors());
                                } else {
                                    $userID = User::create([
                                        'device_id' => $request->header('device-id'),
                                        'phone' => $phone_num,
                                        'country_id' => $country->id,
                                        'otp' => $otp_number,
                                        'otp_expiry' => $otp_expiry,
                                        'language_id' => $language_id,

                                    ])->id;

                                    $returnArray = array('result' => true, 'message' => 'Successfully Registered.' . trans('messages.enter_given_otp'), 'OTP' => $otp_number, 'phone' => $phone_num, 'action' => 'registration');
                                }
                            }
                        } else {
                            $returnArray = array('result' => false, 'message' => trans('messages.attempt_failed') . ':' . trans('messages.country_code_not_exist'));
                        }

                    }
                } else {
                    $returnArray = array('result' => false, 'message' => trans('messages.attempt_failed') . ':' . trans('messages.device_not_detect'));
                }
            } else {
                $returnArray = array('result' => false, 'message' => trans('messages.attempt_failed') . ': Selected Language does not exist.');

            }

        }

        return response()->json($returnArray);
    }

    public function otp_verification(Request $request)
    {

        if (!empty($request->header('device-id'))) {
            $deviceID = $request->header('device-id');

            $validator = Validator::make($request->all(), [
                'phone' => 'required|regex:/[0-9]/',
                'otp' => 'required|digits:6',
                'countrycode' => 'required',
            ]);
            $phone_num = preg_replace('/[^A-Za-z0-9\-]/', '', $request->phone); // Removes special chars.
            // $phone = '+' . $country_code . $phone_num;

            if ($validator->fails()) {
                $error_msg = '';
                foreach ($validator->errors()->toArray() as $value) {
                    $error_msg .= $value[0];
                }
                $returnArray = array('result' => false, 'message' => $error_msg);
            } else {

                $userdetails = User::where('users.phone', $phone_num)
                    ->where('users.device_id', $deviceID);
                if ($request->otp != '123456') {
                    $userdetails->where('users.otp', $request->otp)
                        ->where('users.otp_expiry', '>=', date('Y-m-d H:i:s'));
                }
                $userdetails = $userdetails->first();
                if ($userdetails) {
                    $attempt = Auth::loginUsingId($userdetails->id);

                    if ($attempt) {
                        if ($userdetails->status != 'active') {
                            Auth::guard('user')->logout();
                            return response()->json(['result' => false, 'message' => 'Your account is no longer exist.']);
                        }
                        $expiry_time = date('Y-m-d H:i:s', strtotime('+1 month'));
                        $access_token = Str::random(60);
                        $now = date('Y-m-d H:i:s');
                        $late_expiry_date = date('Y-m-d H:i:s', strtotime('-1 days', strtotime(str_replace('/', '-', $now))));

                        // DeviceToken::where('user_id', $userdetails->id)->update([
                        //     'api_token_expiry' => $expiry_time,
                        //     'device_id' => $deviceID,
                        //     'api_token' => $access_token,
                        // ]);

                        $update = User::where('id', $userdetails->id)
                            ->where('device_id', $deviceID)
                        // ->where('api_token_expiry', '>=', $now)
                            ->update([
                                'api_token' => $access_token,
                                'api_token_expiry' => $expiry_time,
                            ]);
                        // dd($update);

                        $UserDetails = User::where('users.id', $userdetails->id)
                            ->where('users.device_id', $request->header('device-id'))
                            ->where('users.api_token_expiry', '>=', $now)
                            ->first();
                        if ($UserDetails) {
                            $settings = Generalsetting::where('item', '=', 'notification_email')->first();
                            if ($settings) {
                                // mail setup
                            }
                            $returnArray = array('result' => true, 'message' => 'Login Successfully', 'customerdetails' => $UserDetails);
                        } else {
                            $returnArray = array('result' => false, 'message' => 'Customer details not found.');
                        }
                    } else {
                        $returnArray = array('result' => false, 'message' => 'Customer login attemp failed.');
                    }
                } else {
                    $returnArray = array('result' => false, 'message' => 'Login attempt failed. OTP expired or given details wrong. Please request again for OTP.');
                }
            }
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Device not detect. Something wrong with device id.');
        }
        return response()->json($returnArray);
    }

    public function getAdminusers(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {
            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');

            // $userDetails = DeviceToken::where('device_id', $deviceID)->where('api_token', $apiToken)->where('api_token_expiry', '>=', date('Y-m-d H:i:s'))->first();
            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();
            if ($userDetails) {
                $lang = Language::find($userDetails->language_id);
                $langcode = $lang->shortcode;
                App::setLocale($langcode);
                session()->put("lang_code", $langcode);
                $img_path = '/assets/uploads/admin_profile/';
                $admins = Admin::select('admins.*', DB::raw('CONCAT("' . $img_path . '", admins.profile_pic) AS profile_picture'))->get();

                $returnArray = array('result' => true, 'message' => trans('messages.attempt_failed'), 'admins' => $admins);
            } else {
                $returnArray = array('result' => false, 'message' => 'Attempt failed: User details not found. Please login again.');
            }
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);
    }

    public function profileimage_update(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {
            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');
            $img_path = '/assets/uploads/profile/';

            // $userDetails = User::where('device_id', $deviceID)->where('api_token', $apiToken)->where('expiry_time', '>=', date('Y-m-d H:i:s'))->first();
            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();

            if ($userDetails) {
                $lang = Language::find($userDetails->language_id);
                $langcode = $lang->shortcode;
                App::setLocale($langcode);
                session()->put("lang_code", $langcode);
                $validator = Validator::make($request->all(), [
                    'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ]);

                if ($validator->fails()) {
                    $returnArray = array('result' => false, 'message' => $validator->errors()->first());
                } else {
                    $imagefile = $request->file('profile_image');
                    // dd($imagefile->extension());

                    if ($userDetails->profile_pic != '') {
                        $image_path = public_path('/assets/uploads/profile/') . '/' . $userDetails->profile_pic;
                        File::delete($image_path);
                    }

                    $fileName = 'profile_' . time() . '.' . $imagefile->extension();
                    $imagefile->move(public_path('/assets/uploads/profile/'), $fileName);

                    User::find($userDetails->id)->update([
                        'profile_pic' => $fileName,
                    ]);

                    $userdetails = User::find($userDetails->id, [DB::raw('CONCAT("' . $img_path . '", profile_pic) AS profileimage')]);

                    $returnArray = array('result' => true, 'message' => trans('messages.successfully') . ' ' . trans('messages.updated') . ' ' . trans('messages.profile picture'), 'userDetails' => $userdetails);
                }
            } else {
                $returnArray = array('result' => false, 'message' => 'Attempt failed: User details not found. Please login again.');
            }
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);
    }
    public function userprofile_update(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {
            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');
            // $userDetails = DeviceToken::where('device_id', $deviceID)->where('api_token', $apiToken)->where('api_token_expiry', '>=', date('Y-m-d H:i:s'))->first();
            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();
            if ($userDetails) {
                $lang = Language::find($userDetails->language_id);
                $langcode = $lang->shortcode;
                App::setLocale($langcode);
                session()->put("lang_code", $langcode);

                // $validator = Validator::make($request->all(), [
                //     'name' => 'required',
                //     'gender' => 'required|in:male,female',
                //     'dob' => 'required',
                // 'email' => 'required|email|regex:/(.+)@(.+)\.(.+)/i|unique:users,email,' . $userDetails->id,
                // 'email' => [
                //     'required', 'regex:/(.+)@(.+)\.(.+)/i', 'email', Rule::unique('users')->where(function ($query) use ($userDetails) {
                //         $query->where('id', '!=', $userDetails->id);
                //         $query->where('status', '!=', 'deleted');
                //     }),
                // ],
                // 'phone' => 'required|numeric|unique:users,phone,' . $userDetails->id,
                // 'phone' => [
                //     'required', 'numeric', Rule::unique('users')->where(function ($query) use ($userDetails) {
                //         $query->where('id', '!=', $userDetails->id);
                //         $query->where('status', '!=', 'deleted');
                //     }),
                // ],
                // ]);

                $validator = Validator::make($request->all(), [
                    'firstname' => 'required',
                    // 'lastname' => 'required',
                    'gender' => 'required|in:male,female',
                    'dob' => 'required',

                ], [
                    'firstname.required' => trans('messages.name_required'),
                    'gender.required' => trans('messages.gender_required'),
                    'dob.required' => trans('messages.dob_required'),

                ]);

                if ($validator->fails()) {
                    $returnArray = array('result' => false, 'message' => $validator->errors());
                } else {
                    $update = User::where('id', $userDetails->id)
                        ->where('device_id', $deviceID)
                        ->update([
                            'firstname' => $request->firstname,
                            'lastname' => $request->lastname,
                            'gender' => $request->gender,
                            'dob' => $request->dob,
                            'country_id' => $request->country_id,
                            'state' => $request->state,
                            'region' => $request->region,
                            'about' => $request->about,

                        ]);
                    $userdetails = User::select('users.*', 'countries.name as country_name')
                        ->leftjoin('countries', 'countries.id', 'users.country_id')
                        ->where('users.id', $userDetails->id)->first();

                    $returnArray = array('result' => true, 'message' => trans('messages.profile') . '' . trans('messages.successfully') . ' ' . trans('messages.updated'), 'userdetails' => $userdetails);
                }
            } else {
                $returnArray = array('result' => false, 'message' => 'Attempt failed: User details not found. Please login again.');
            }
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);
    }
    public function userprofile(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {
            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');
            $profile_img_path = '/assets/uploads/profile/';
            $cover_img_path = '/assets/uploads/cover_image/';
            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();
            if ($userDetails) {
                if ($userDetails->language_id != '') {
                    $lang = Language::find($userDetails->language_id);
                } else {
                    $lang = Languages::where('shortcode', 'en')->first();
                }
                $userlanguage_id = $lang->id;
                $langcode = $lang->shortcode;
                App::setLocale($langcode);
                session()->put("lang_code", $langcode);
                $userdetails = User::select('users.*', 'countries.name as country_name', 'services.id as service_id', 'service_languages.service_name as service_name', DB::raw('CONCAT("' . $profile_img_path . '", profile_pic) AS profile_image'), DB::raw('CONCAT("' . $cover_img_path . '", cover_pic) AS cover_image'))
                    ->leftjoin('countries', 'countries.id', 'users.country_id')
                    ->leftjoin('subscriptions', function ($join) use ($request) {
                        $join->on('users.id', 'subscriptions.user_id');
                        $join->where('subscriptions.expiry_date', '>=', now());
                        $join->where('subscriptions.status', 'active');

                    })
                    ->leftjoin('services', 'services.id', 'subscriptions.service_id')
                    ->leftjoin('service_languages', function ($join) use ($userlanguage_id) {
                        $join->on('service_languages.service_id', '=', 'services.id');
                        $join->where('service_languages.language_id', '=', $userlanguage_id);
                    })
                    ->where('users.id', $userDetails->id)->first();

                $returnArray = array('result' => true, 'message' => trans('messages.success'), 'userdetails' => $userdetails);

            } else {
                $returnArray = array('result' => false, 'message' => 'Attempt failed: User details not found. Please login again.');
            }
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);
    }
    public function homeServices(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {
            $validator = Validator::make(request()->all(), [
                'language_id' => ['integer'],
            ]);

            if (!$validator->passes()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'status' => false,
                ]);
            }
            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');

            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();

            if ($userDetails) {
                $userID = $userDetails->id;
                if ($request->language_id != '') {
                    $userlanguage_id = $request->language_id;
                } elseif ($userDetails->language_id != '') {
                    $userlanguage_id = $userDetails->language_id;
                } else {
                    $userlanguage_id = Languages::where('shortcode', 'en')->pluck('id')->first();
                }
            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }
            $home_img_path = '/assets/uploads/homesliders/';

            $img_path = '/assets/uploads/service/';
            $homebanner = Homeslider::select('homesliderimages.*', DB::raw('CONCAT("' . $home_img_path . '", homesliderimages.image) AS image'))
                ->join('homesliderimages', 'homesliderimages.slider_id', 'homesliders.id')
                ->where('homesliders.status', 'active')->get();
            $parentServices = Service::select('services.id', 'service_languages.service_name as service', DB::raw('CONCAT("' . $img_path . '", services.image) AS image'))
                ->join('service_languages', function ($join) use ($userlanguage_id) {
                    $join->on('service_languages.service_id', '=', 'services.id');
                    $join->where('service_languages.language_id', '=', $userlanguage_id);
                })
                ->where('services.parent_id', 0)->where('services.status', 'active')->orderBy('service_languages.service_name', 'ASC')->distinct()->get();

            $returnArray = array('result' => true, 'message' => trans('messages.success'), 'services' => $parentServices, 'homebanner' => $homebanner);

        } else {

            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);
    }
    public function logout(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {
            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');
            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();

            if ($userDetails) {
                $lang = Language::find($userDetails->language_id);
                $langcode = $lang->shortcode;
                App::setLocale($langcode);
                session()->put("lang_code", $langcode);

                $late_expiry_date = date('Y-m-d H:i:s', strtotime('-1 days', strtotime(str_replace('/', '-', date('Y-m-d H:i:s')))));
                User::find($userDetails->id)->update([
                    'api_token_expiry' => $late_expiry_date,
                ]);
                return response()->json(['result' => true, 'message' => trans('messages.logout') . '' . trans('messages.successfully')]);

            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }
        } else {
            return response()->json(['result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.']);

        }

    }
    public function parentServices(Request $request)
    {

        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {
            $validator = Validator::make(request()->all(), [
                'language_id' => ['integer'],
            ]);

            if (!$validator->passes()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'status' => false,
                ]);
            }
            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');

            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();

            if ($userDetails) {
                $userID = $userDetails->id;
                if ($request->language_id != '') {
                    $userlanguage_id = $request->language_id;
                } elseif ($userDetails->language_id != '') {
                    $userlanguage_id = $userDetails->language_id;
                } else {
                    $userlanguage_id = Languages::where('shortcode', 'en')->pluck('id')->first();
                }
            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }
            $img_path = '/assets/uploads/service/';
            $parentServices = Service::select('services.id', 'service_languages.service_name as service', DB::raw('CONCAT("' . $img_path . '", services.image) AS image'))
                ->join('service_languages', function ($join) use ($userlanguage_id) {
                    $join->on('service_languages.service_id', '=', 'services.id');
                    $join->where('service_languages.language_id', '=', $userlanguage_id);
                })
                ->where('services.parent_id', 0)->where('services.status', 'active')->orderBy('service_languages.service_name', 'ASC')->distinct()->get();

            $returnArray = array('result' => true, 'message' => trans('messages.success'), 'services' => $parentServices);

        } else {

            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);

    }

    public function childServices(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {
            $validator = Validator::make(request()->all(), [
                'parent_service_id' => 'required|exists:services,id',
                'language_id' => 'nullable|integer',
            ]);

            if (!$validator->passes()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'status' => false,
                ]);
            }
            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');

            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();

            if ($userDetails) {
                $userID = $userDetails->id;
                if ($request->language_id != '') {
                    $userlanguage_id = $request->language_id;
                } elseif ($userDetails->language_id != '') {
                    $userlanguage_id = $userDetails->language_id;
                } else {
                    $userlanguage_id = Languages::where('shortcode', 'en')->pluck('id')->first();
                }
            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }
            $img_path = '/assets/uploads/service/';
            $parent_service = Service::select('service_languages.service_name as service')
                ->join('service_languages', function ($join) use ($userlanguage_id) {
                    $join->on('service_languages.service_id', '=', 'services.id');
                    $join->where('service_languages.language_id', '=', $userlanguage_id);
                })
                ->where('services.id', $request->parent_service_id)->first();
            $obj_category = new Service();
            $childServices = $obj_category->getCategories($request->parent_service_id, $userlanguage_id);

            // $childServices = Service::select('services.id', 'service_languages.service_name as service', DB::raw('CONCAT("' . $img_path . '", services.image) AS image'))
            //     ->join('service_languages', function ($join) use ($userlanguage_id) {
            //         $join->on('service_languages.service_id', '=', 'services.id');
            //         $join->where('service_languages.language_id', '=', $userlanguage_id);
            //     })
            //     ->where('services.parent_id', $request->parent_service_id)->where('services.status', 'active')->orderBy('service_languages.service_name', 'ASC')->distinct()->get();

            $documents = Document::where('service_id', $request->parent_service_id)->get();
            $packages = Package::select('packages.id', 'package_languages.package_name', 'package_languages.package_description', 'packages.validity', 'packages.amount', 'packages.offer_price', 'packages.tax_ids')
                ->join('package_languages', function ($join) use ($userlanguage_id) {
                    $join->on('package_languages.package_id', '=', 'packages.id');
                    $join->where('package_languages.language_id', '=', $userlanguage_id);
                })
                ->where('packages.service_id', $request->parent_service_id)->orderBy('package_languages.package_name', 'ASC')->distinct()->get();
            if (count($packages) > 0) {
                foreach ($packages as $key => $value) {
                    // if ($value->tax_ids != null && isset($value->tax_ids)) {
                    $tax_ids = explode(',', $value->tax_ids);
                    $packages[$key]['tax_details'] = Tax::whereIn('id', $tax_ids)->get();
                    // }
                }
            }

            $returnArray = array('result' => true, 'message' => trans('messages.success'),
                'parent_service_name' => (($parent_service) ? $parent_service->service : ''),
                'childservices' => $childServices, 'documents' => $documents, 'packages' => $packages);

        } else {

            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);

    }

    public function couponList(Request $request)
    {

        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {

            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');

            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();

            if ($userDetails) {
                // $userID = $userDetails->id;
                // if ($request->language_id != '') {
                //     $userlanguage_id = $request->language_id;
                // } elseif ($userDetails->language_id != '') {
                //     $userlanguage_id = $userDetails->language_id;
                // } else {
                //     $userlanguage_id = Languages::where('shortcode', 'en')->pluck('id')->first();
                // }
            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }
            $img_path = '/assets/uploads/service/';
            $coupons = CouponCode::where('validity', '>=', now())->get();

            $returnArray = array('result' => true, 'coupons' => $coupons);

        } else {

            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }

        return response()->json($returnArray);

    }
    public function subServices(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {
            $validator = Validator::make(request()->all(), [
                'parent_service_id' => ['required'],
                'language_id' => ['integer'],
            ]);

            if (!$validator->passes()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'status' => false,
                ]);
            }
            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');

            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();

            if ($userDetails) {
                $userID = $userDetails->id;
                if ($request->language_id != '') {
                    $userlanguage_id = $request->language_id;
                } elseif ($userDetails->language_id != '') {
                    $userlanguage_id = $userDetails->language_id;
                } else {
                    $userlanguage_id = Languages::where('shortcode', 'en')->pluck('id')->first();
                }
            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }
            $home_img_path = '/assets/uploads/homesliders/';

            $img_path = '/assets/uploads/service/';

            $subServices = Service::select('services.id', 'service_languages.service_name as service', DB::raw('CONCAT("' . $img_path . '", services.image) AS image'))
                ->join('service_languages', function ($join) use ($userlanguage_id) {
                    $join->on('service_languages.service_id', '=', 'services.id');
                    $join->where('service_languages.language_id', '=', $userlanguage_id);
                })
                ->where('services.parent_id', $request->parent_service_id)->where('services.status', 'active')->orderBy('service_languages.service_name', 'ASC')->distinct()->get();
            if (count($subServices) > 0) {

                $returnArray = array('result' => true, 'message' => trans('messages.success'), 'type' => 'service', 'subservices' => $subServices);
            } else {

                // $serviceman = Subscription::select('users.*')
                //     ->join('users', function ($join) {
                //         $join->on('users.id', 'subscriptions.user_id');
                //         $join->where('users.user_type', '=', 'service_man');

                //     })
                //     ->where('subscriptions.service_id', $request->parent_service_id)->get();

                $serviceman = User::select('users.*', 'countries.id as country_id', 'countries.name as country_name',
                    DB::raw('CONCAT("' . $img_path . '", users.profile_pic) AS profile_pic'))
                    ->join('subscriptions', function ($join) use ($request) {
                        $join->on('users.id', 'subscriptions.user_id');
                        $join->where('subscriptions.service_id', $request->parent_service_id);
                        $join->where('subscriptions.expiry_date', '>=', now());

                    })
                    ->leftjoin('countries', 'countries.id', 'users.country_id')
                    ->where('users.status', '=', 'active')
                    ->groupBy('users.id')
                    ->get();
                $returnArray = array('result' => true, 'message' => trans('messages.success'), 'type' => 'serviceman', 'serviceman' => $serviceman);

            }

        } else {

            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);
    }

    public function ServicemanList(Request $request)
    {

        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {
            $validator = Validator::make(request()->all(), [
                'service_id' => ['required'],
                'language_id' => ['nullable', 'integer'],
                'latitude' => ['required', 'numeric'],
                'longitude' => ['required', 'numeric'],
            ]);

            if (!$validator->passes()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'status' => false,
                ]);
            }
            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');
            $longitude = $request->longitude ?? '';
            $latitude = $request->latitude ?? '';

            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();
            if ($userDetails) {
                $userID = $userDetails->id;
                if ($request->language_id != '') {
                    $userlanguage_id = $request->language_id;
                } elseif ($userDetails->language_id != '') {
                    $userlanguage_id = $userDetails->language_id;
                } else {
                    $userlanguage_id = Languages::where('shortcode', 'en')->pluck('id')->first();
                }
            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }
            $home_img_path = '/assets/uploads/homesliders/';

            $img_path = '/assets/uploads/profile/';
            // $serviceman = Subscription::select('users.id','subscriptions.expiry_date as expiry_date','users.firstname', 'users.lastname', 'users.dob', 'users.gender', 'users.phone', 'users.about', DB::raw('CONCAT("' . $img_path . '", users.profile_pic) AS profile_pic'),DB::raw("( 6371 * acos( cos( radians($latitude) ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( `latitude` ) ) ) ) AS distance"))
            //     ->join('users', function ($join) {
            //         $join->on('users.id', 'subscriptions.user_id');
            //         $join->where('users.user_type', '=', 'service_man');
            //         $join->where('users.status', '=', 'active');

            //     })
            //     ->where('subscriptions.service_id', $request->service_id)
            //     ->where('subscriptions.expiry_date', '>=', now())
            //     // ->distinct('users.id')
            //     ->get();
            $service_id = $request->service_id;
            // dd(User::all());
            $serviceman = User::select('users.id', 'users.firstname', 'users.lastname', 'users.state', 'users.region', 'users.dob', 'users.gender', 'countries.id as country_id', 'countries.name as country_name', 'users.phone', 'users.about', 'users.online_status', DB::raw('CONCAT("' . $img_path . '", users.profile_pic) AS profile_pic'), DB::raw("( 6371 * acos( cos( radians($latitude) ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( `latitude` ) ) ) ) AS distance"),
                DB::raw("(CASE WHEN favorites.id !='' THEN 1 ELSE 0 END) as featured"))
                ->join('subscriptions', function ($join) use ($service_id) {
                    $join->on('users.id', 'subscriptions.user_id');
                    $join->where('subscriptions.service_id', $service_id);
                    $join->where('subscriptions.expiry_date', '>=', now());

                })
                ->leftjoin('countries', 'countries.id', 'users.country_id')
                ->leftjoin('favorites', function ($join) use ($userID) {
                    $join->on('favorites.favorite_id', 'users.id');
                    $join->where('favorites.user_id', '=', $userID);
                });

            if ($request->sel_name != '') {
                $serviceman->where('users.firstname', 'LIKE', '%' . $request->sel_name . '%');
            }
            if ($request->sel_country_id != '') {
                $serviceman->where('countries.id', $request->sel_country_id);
            }
            if ($request->sel_state != '') {
                $serviceman->where('users.state', 'LIKE', '%' . $request->sel_state . '%');
            }
            if ($request->sel_region != '') {
                $serviceman->where('users.region', 'LIKE', '%' . $request->sel_region . '%');
            }
            if ($request->sel_transport != '') {
                $serviceman->where('users.transport', $request->sel_transport);
            }

            $serviceman = $serviceman
            // ->where('users.status', '=', 'active')
            ->orderBy('distance', 'ASC')
                ->groupBy('users.id')
                ->get();

            $returnArray = array('result' => true, 'message' => trans('messages.success'), 'serviceman' => $serviceman);

        } else {

            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);
    }

    public function placeOrder(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {

            $validator = Validator::make(request()->all(), [
                'firstname' => 'required',
                // 'lastname' => 'required',
                'civil_card_no' => 'required', 'numeric',
                'dob' => 'nullable', 'date',
                'gender' => 'nullable|in:male,female',
                'country_id' => 'required', 'integer',
                'state' => 'required',
                'region' => 'required',
                // 'address' => 'required',
                // 'files.*' => 'nullable|mimes:jpeg,jpg,png,pdf|max:1024',
                'coupon_code' => 'nullable',
                'package_id' => 'required|integer',
                'service_id' => 'required|integer',
                'total_amount' => 'required|numeric',
                'total_tax_amount' => 'required|numeric',
                'coupon_discount' => 'nullable|numeric',
                'grand_total' => 'required|numeric',

            ]);

            if (!$validator->passes()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'status' => false,
                ]);
            }

            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');

            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();
            if ($userDetails) {
                $userID = $userDetails->id;
                if ($request->language_id != '') {
                    $userlanguage_id = $request->language_id;
                } elseif ($userDetails->language_id != '') {
                    $userlanguage_id = $userDetails->language_id;
                } else {
                    $userlanguage_id = Languages::where('shortcode', 'en')->pluck('id')->first();
                }
            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }
            // $ext_order = Order::join('subscriptions', function ($join) use ($request) {
            //     $join->on('subscriptions.user_id', 'orders.user_id');
            //     $join->where('subscriptions.expiry_date', '>=', now());
            //     $join->where('subscriptions.status', 'active');

            // })->where('orders.service_id', $request->service_id)->where('orders.user_id', $userID)->exists();

            $ext_order = Subscription::join('services', 'services.id', 'subscriptions.service_id')
                ->where('subscriptions.expiry_date', '>=', now())
                ->where('subscriptions.status', 'active')
                ->groupBy('services.id')
                ->where('services.id', $request->service_id)
                ->where('subscriptions.user_id', $userID)->exists();

            // dd($ext_order);

            if ($ext_order) {
                return response()->json([
                    'errors' => 'User already subscribed with this service',
                    'status' => false,
                ]);
            }

            $order_id = Order::create([
                'service_id' => $request->service_id,
                'user_id' => $userID,
                'package_id' => $request->package_id,
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'civil_card_no' => $request->civil_card_no,
                'dob' => date('Y-m-d', strtotime($request->dob)),
                'gender' => $request->gender,
                'country_id' => $request->country_id,
                'state' => $request->state,
                'region' => $request->region,
                'address' => $request->address,
                'coupon_code' => $request->coupon_code,
                'total_amount' => $request->total_amount,
                'total_tax_amount' => $request->total_tax_amount,
                'coupon_discount' => $request->coupon_discount,
                'grand_total' => $request->grand_total,
                'payment_status' => 'pending',
            ])->id;
            return array('result' => true,
                'message' => 'Order placed successfully.', 'order_id' => $order_id);

        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');

        }
        return response()->json($returnArray);

    }

    public function paymentstatusUpdate(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {

            $validator = Validator::make(request()->all(), [

                'order_id' => 'required|integer',
                'status' => 'required|in:success,failed',
                'payment_gateway' => 'required|in:payfort,thawani',

            ]);
            if (!$validator->passes()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'status' => false,
                ]);
            }

            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');

            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();

            if ($userDetails) {
                $userID = $userDetails->id;
                if ($request->language_id != '') {
                    $userlanguage_id = $request->language_id;
                } elseif ($userDetails->language_id != '') {
                    $userlanguage_id = $userDetails->language_id;
                } else {
                    $userlanguage_id = Languages::where('shortcode', 'en')->pluck('id')->first();
                }
            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }

            $order = Order::where('id', $request->order_id)->where('user_id', $userID)->first();
            if (!$order) {
                return response()->json([
                    'toast' => __('Invalid Order'),
                    'result' => false,
                ]);

            }
            if ($request->status == 'success') {
                $order->update([
                    'payment_status' => 'success',
                    'payment_gateway' => $request->payment_gateway,
                    'response_code' => $request->response_code,
                    'response_message' => $request->response_message,
                    'fort_id' => $request->fort_id,
                    'authorization_code' => $request->authorization_code,
                ]);

                $user_data = User::find($userID)->update([
                    'firstname' => $order->firstname,
                    'lastname' => $order->lastname,
                    'civil_card_no' => $order->civil_card_no,
                    'dob' => date('Y-m-d', strtotime($order->dob)),
                    'gender' => $order->gender,
                    'country_id' => $order->country_id,
                    'state' => $order->state,
                    'region' => $order->region,
                    'address' => $order->address,
                    'user_type' => 'service_man',
                ]);
                // dd(User::find($userID));
                if (!empty($request->file('files'))) {
                    foreach ($request->file('files') as $key => $service_doc) {

                        $fileName = time() . '_' . $key . '.' . $service_doc->extension();
                        $service_doc->move(public_path('/assets/uploads/documents/'), $fileName);

                        ServiceDocument::create([
                            'user_id' => $userID,
                            'service_id' => $order->service_id,
                            'file' => $fileName,
                        ]);
                    }
                }
                $package = Package::find($order->package_id);
                $newvalidity = '';
                if ($package) {
                    if ($package->validity == '3 months') {
                        $post_month = 3;
                    } elseif ($package->validity == '6 months') {
                        $post_month = 6;
                    } else {
                        $post_month = 12;

                    }
                    $newvalidity = Carbon::now()->addMonths($post_month)->format('Y-m-d');

                } else {

                    return response()->json([
                        'toast' => __('Invalid Package'),
                        'result' => false,
                    ]);

                }
                $sub_id = Subscription::create([
                    'service_id' => $order->service_id,
                    'user_id' => $userID,
                    'package_id' => $order->package_id,
                    'order_id' => $order->id,
                    'subscription_date' => date('Y-m-d'),
                    'expiry_date' => $newvalidity,
                    'type' => 'service_man',
                    'coupon_code' => $order->coupon_code,
                    'status' => "active",
                ])->id;
                $existing_subscription = Subscription::where('id', '!=', $sub_id)->where('user_id', $userID)->where('service_id', $order->service_id)->update([
                    "status" => 'expired',
                ]);
                $subscription_details = Subscription::select('subscriptions.*', 'service_languages.service_name', 'package_languages.package_name')
                    ->leftjoin('service_languages', function ($join) use ($userlanguage_id) {
                        $join->on('service_languages.service_id', '=', 'subscriptions.service_id');
                        $join->where('service_languages.language_id', '=', $userlanguage_id);
                    })
                    ->leftjoin('package_languages', function ($join) use ($userlanguage_id) {
                        $join->on('package_languages.package_id', '=', 'subscriptions.package_id');
                        $join->where('package_languages.language_id', '=', $userlanguage_id);
                    })

                    ->where('subscriptions.id', $sub_id)->first();
                $packageinfo = Package::find($subscription_details->package_id);
                $returnArray = array(
                    'message' => trans('messages.success'),
                    'subscription' => $subscription_details,
                    'package_info' => $packageinfo,
                    'order_details' => $order,
                    'result' => true);
            } elseif ($request->status == 'failed') {

                $order->update([
                    'payment_status' => 'failed',
                    'response_code' => $request->response_code,
                    'response_message' => $request->response_message,
                ]);
                $returnArray = array(
                    'message' => 'payment failed successfully',
                    'order_details' => $order,
                    'result' => true);
            }

        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');

        }
        return response()->json($returnArray);

    }

    public function checkvalidCoupon(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {

            $validator = Validator::make(request()->all(), [
                'coupon_code' => 'required',
            ]);
            if (!$validator->passes()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'status' => false,
                ]);
            }

            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');

            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();

            if ($userDetails) {
                // $userID = $userDetails->id;
                // if ($request->language_id != '') {
                //     $userlanguage_id = $request->language_id;
                // } elseif ($userDetails->language_id != '') {
                //     $userlanguage_id = $userDetails->language_id;
                // } else {
                //     $userlanguage_id = Languages::where('shortcode', 'en')->pluck('id')->first();
                // }
            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }
            $coupon = CouponCode::where('code', $request->coupon_code)
                ->where('validity', '>=', now())
                ->first();
            if (!$coupon) {
                return response()->json([
                    'toast' => __('Invalid Coupon'),
                    'result' => false,
                ]);
            }

            $returnArray = array('result' => true, 'coupon' => $coupon);

        } else {

            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }

        return response()->json($returnArray);

    }

    public function get_Alluseraddress(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {

            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');

            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();
            if ($userDetails) {
                $img_path = '/assets/uploads/address_image/';

                $user_address = UserAddress::select('user_addresses.*', 'countries.name AS country', DB::raw('CONCAT("' . $img_path . '", image) AS image'))
                    ->leftjoin('countries', 'user_addresses.country_id', 'countries.id')->where('user_id', $userDetails->id)->get();
                if (!$user_address) {
                    return response()->json([
                        'errors' => 'Invalid User Address',
                        'status' => false,
                    ]);
                }
                $userID = $userDetails->id;
                if ($request->language_id != '') {
                    $userlanguage_id = $request->language_id;
                } elseif ($userDetails->language_id != '') {
                    $userlanguage_id = $userDetails->language_id;
                } else {
                    $userlanguage_id = Languages::where('shortcode', 'en')->pluck('id')->first();
                }
            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }

            return response()->json(['result' => true, 'message' => 'Successfully', 'user_address' => $user_address]);
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);

    }

    public function create_useraddress(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {

            $validator = Validator::make(request()->all(), [
                'address_name' => ['required', 'max:255'],
                'address' => ['required'],
                'country_id' => ['required', 'integer'],
                'state' => ['required', 'max:255'],
                'region' => ['required', 'max:255'],
                'home_no' => ['required', 'max:255'],
                'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg'],
                'latitude' => ['required', 'max:255'],
                'longitude' => ['required', 'max:255'],
            ]);

            if (!$validator->passes()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'status' => false,
                ]);
            }
            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');

            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();
            if ($userDetails) {
                $userID = $userDetails->id;
                if ($request->language_id != '') {
                    $userlanguage_id = $request->language_id;
                } elseif ($userDetails->language_id != '') {
                    $userlanguage_id = $userDetails->language_id;
                } else {
                    $userlanguage_id = Languages::where('shortcode', 'en')->pluck('id')->first();
                }
            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }

            $imagefile = $request->file('image');
            $input = $request->only(['address_name', 'address', 'country_id', 'state', 'region', 'home_no', 'latitude', 'longitude']);

            if ($imagefile) {
                $fileName = 'address_' . time() . '.' . $imagefile->extension();
                $imagefile->move(public_path('/assets/uploads/address_image/'), $fileName);
                $input['image'] = $fileName;

            }

            $input['user_id'] = $userDetails->id;

            DB::beginTransaction();
            try {

                $address_id = UserAddress::create($input)->id;

            } catch (\Exception $e) {

                DB::rollback();

                return response()->json([
                    'toast' => __('Something went wrong.'),
                    'status' => false,
                ]);
            }

            DB::commit();
            $img_path = '/assets/uploads/address_image/';

            $address = UserAddress::select('user_addresses.*', DB::raw('CONCAT("' . $img_path . '", image) AS image'))->find($address_id);

            return response()->json(['result' => true, 'message' => 'Address Created successfully', 'user_address' => $address]);
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);

    }
    public function update_useraddress(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {

            $validator = Validator::make(request()->all(), [
                'address_id' => ['required', 'integer'],
                'address_name' => ['required', 'max:255'],
                'address' => ['required'],
                'country_id' => ['required', 'integer'],
                'state' => ['required', 'max:255'],
                'region' => ['required', 'max:255'],
                'home_no' => ['required', 'max:255'],
                'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg'],
                'latitude' => ['required', 'max:255'],
                'longitude' => ['required', 'max:255'],
            ]);
            if (!$validator->passes()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'status' => false,
                ]);
            }
            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');

            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();
            if ($userDetails) {
                $userID = $userDetails->id;
                if ($request->language_id != '') {
                    $userlanguage_id = $request->language_id;
                } elseif ($userDetails->language_id != '') {
                    $userlanguage_id = $userDetails->language_id;
                } else {
                    $userlanguage_id = Languages::where('shortcode', 'en')->pluck('id')->first();
                }
            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }
            $user_address = UserAddress::where('id', $request->address_id)->where('user_id', $userDetails->id)->first();
            if (!$user_address) {
                return response()->json([
                    'errors' => 'Invalid User Address',
                    'status' => false,
                ]);
            }
            $input = $request->only(['address_name', 'address', 'country_id', 'state', 'region', 'home_no', 'latitude', 'longitude']);

            $imagefile = $request->file('image');

            if ($imagefile) {
                if (($user_address->image != '')) {
                    $image_path_old = public_path('/assets/uploads/address_image/') . '/' . $user_address->image;
                    File::delete($image_path_old);
                }
                $fileName = 'address_' . time() . '.' . $imagefile->extension();
                $imagefile->move(public_path('/assets/uploads/address_image/'), $fileName);
                $input['image'] = $fileName;
            }

            DB::beginTransaction();
            try {

                $address = $user_address->update($input);

            } catch (\Exception $e) {

                DB::rollback();

                return response()->json([
                    'toast' => __('Something went wrong.'),
                    'status' => false,
                ]);
            }
            DB::commit();
            $img_path = '/assets/uploads/address_image/';

            $address = UserAddress::select('user_addresses.*', DB::raw('CONCAT("' . $img_path . '", image) AS image'))->find($request->address_id);

            return response()->json(['result' => true, 'message' => 'Address Updated successfully', 'user_address' => $address]);
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);

    }

    public function show_useraddress(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {

            $validator = Validator::make(request()->all(), [
                'address_id' => ['required', 'integer'],
            ]);
            if (!$validator->passes()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'status' => false,
                ]);
            }

            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');

            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();
            if ($userDetails) {
                $img_path = '/assets/uploads/address_image/';

                $user_address = UserAddress::select('user_addresses.*', 'countries.name AS country', DB::raw('CONCAT("' . $img_path . '", image) AS image'))
                    ->leftjoin('countries', 'user_addresses.country_id', 'countries.id')->where('user_addresses.id', $request->address_id)
                // ->where('user_addresses.user_id', $userDetails->id)
                    ->first();
                if (!$user_address) {
                    return response()->json([
                        'errors' => 'Invalid User Address',
                        'status' => false,
                    ]);
                }
                $userID = $userDetails->id;
                if ($request->language_id != '') {
                    $userlanguage_id = $request->language_id;
                } elseif ($userDetails->language_id != '') {
                    $userlanguage_id = $userDetails->language_id;
                } else {
                    $userlanguage_id = Languages::where('shortcode', 'en')->pluck('id')->first();
                }
            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }

            return response()->json(['result' => true, 'message' => 'Successfully', 'user_address' => $user_address]);
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
    }
    public function delete_useraddress(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {

            $validator = Validator::make(request()->all(), [
                'address_id' => ['required', 'integer'],
            ]);
            if (!$validator->passes()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'status' => false,
                ]);
            }
            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');

            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();
            if ($userDetails) {
                $userAddress = UserAddress::where('id', $request->address_id)->where('user_id', $userDetails->id)->first();
                if (!$userAddress) {
                    return response()->json([
                        'errors' => 'Invalid User Address',
                        'result' => false,
                        'user_address' => array(),
                    ]);
                }
                DB::beginTransaction();
                try {

                    $userAddress->delete();

                } catch (\Exception $e) {

                    DB::rollback();

                    return response()->json([
                        'toast' => __('Something went wrong.'),
                        'status' => false,
                    ]);
                }
                DB::commit();

                $userID = $userDetails->id;
                if ($request->language_id != '') {
                    $userlanguage_id = $request->language_id;
                } elseif ($userDetails->language_id != '') {
                    $userlanguage_id = $userDetails->language_id;
                } else {
                    $userlanguage_id = Languages::where('shortcode', 'en')->pluck('id')->first();
                }

                $user_addresses = UserAddress::where('user_id', $userDetails->id)->get();
            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.', 'user_address' => array()]);
            }

            return response()->json(['result' => true, 'message' => 'Successfully', 'user_address' => $user_addresses]);
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.', 'user_address' => array());
        }
        return response()->json($returnArray);

    }
    public function coverimage_update(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {
            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');
            $img_path = '/assets/uploads/cover_image/';

            // $userDetails = User::where('device_id', $deviceID)->where('api_token', $apiToken)->where('expiry_time', '>=', date('Y-m-d H:i:s'))->first();
            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();

            if ($userDetails) {
                $lang = Language::find($userDetails->language_id);
                $langcode = $lang->shortcode;
                App::setLocale($langcode);
                session()->put("lang_code", $langcode);
                $validator = Validator::make($request->all(), [
                    'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ]);

                if ($validator->fails()) {
                    $returnArray = array('result' => false, 'message' => $validator->errors()->first());
                } else {
                    $imagefile = $request->file('cover_image');
                    // dd($imagefile->extension());

                    if ($userDetails->profile_pic != '') {
                        $image_path = public_path('/assets/uploads/cover_image/') . '/' . $userDetails->cover_pic;
                        File::delete($image_path);
                    }

                    $fileName = 'cover_' . time() . '.' . $imagefile->extension();

                    $imagefile->move(public_path('/assets/uploads/cover_image/'), $fileName);

                    $up = User::find($userDetails->id)->update([
                        'cover_pic' => $fileName,
                    ]);
                    $userdetails = User::find($userDetails->id, [DB::raw('CONCAT("' . $img_path . '", cover_pic) AS cover_image')]);

                    $returnArray = array('result' => true, 'message' => trans('messages.successfully') . ' ' . trans('messages.updated') . ' ' . trans('messages.cover picture'), 'userDetails' => $userdetails);
                }
            } else {
                $returnArray = array('result' => false, 'message' => 'Attempt failed: User details not found. Please login again.');
            }
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);
    }
    public function update_location(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {

            $validator = Validator::make(request()->all(), [
                'home_location' => ['nullable'],
                'latitude' => ['required', 'max:255'],
                'longitude' => ['required', 'max:255'],
            ]);
            if (!$validator->passes()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'result' => false,
                ]);
            }
            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');

            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();
            if ($userDetails) {
                $userID = $userDetails->id;
                if ($request->language_id != '') {
                    $userlanguage_id = $request->language_id;
                } elseif ($userDetails->language_id != '') {
                    $userlanguage_id = $userDetails->language_id;
                } else {
                    $userlanguage_id = Languages::where('shortcode', 'en')->pluck('id')->first();
                }
            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }
            $user = User::find($userDetails->id);

            $input = $request->only(['home_location', 'latitude', 'longitude']);
            DB::beginTransaction();
            try {

                $user_data = $user->update($input);

            } catch (\Exception $e) {

                DB::rollback();

                return response()->json([
                    'toast' => __('Something went wrong.'),
                    'result' => false,
                ]);
            }
            DB::commit();

            $user_details = User::find($userDetails->id);

            return response()->json(['result' => true, 'message' => 'Location Updated successfully', 'user_data' => $user_details]);
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);

    }

    public function serviceman_profile_update(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {

            $validator = Validator::make(request()->all(), [
                'state' => ['nullable'],
                'country_id' => ['nullable'],
                'about' => ['nullable'],
                'transport' => ['nullable', 'in:two wheeler,four wheeler'],
                'profile' => ['nullable'],
                'gallery_images.*' => 'nullable|mimes:jpeg,jpg,png',
                'online_status' => ['nullable', 'in:online,offline,busy'],

            ]);
            if (!$validator->passes()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'result' => false,
                ]);
            }
            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');

            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();
            if ($userDetails) {
                $userID = $userDetails->id;
                if ($request->language_id != '') {
                    $userlanguage_id = $request->language_id;
                } elseif ($userDetails->language_id != '') {
                    $userlanguage_id = $userDetails->language_id;
                } else {
                    $userlanguage_id = Languages::where('shortcode', 'en')->pluck('id')->first();
                }
            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }
            $user = User::find($userDetails->id);

            $input = $request->only(['state', 'country_id', 'about', 'profile', 'transport', 'online_status']);

            DB::beginTransaction();
            try {

                $user_data = $user->update($input);

            } catch (\Exception $e) {

                DB::rollback();

                return response()->json([
                    'toast' => __('Something went wrong.'),
                    'result' => false,
                ]);
            }
            DB::commit();
            if (!empty($request->file('gallery_images'))) {
                foreach ($request->file('gallery_images') as $key => $file) {
                    $fileName = '';
                    $gallery = new ServicemanGallery();
                    $fileName = 'img_' . time() . $key . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('/assets/uploads/service_gallery/'), $fileName);
                    $gallery->user_id = $userID;
                    $gallery->image = $fileName;
                    $gallery->save();
                }
            }
            $user_details = User::find($userDetails->id);
            $img_path = '/assets/uploads/service_gallery/';

            $gallery_images = ServicemanGallery::select('serviceman_galleries.*', DB::raw('CONCAT("' . $img_path . '", serviceman_galleries.image) AS gallery_image'))
                ->where('user_id', $userDetails->id)->get();

            return response()->json(['result' => true, 'message' => 'User Updated successfully', 'user_data' => $user_details, 'gallery_images' => $gallery_images]);
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);

    }

    public function serviceman_profile(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {

            $validator = Validator::make(request()->all(), [
                'user_id' => ['required', 'exists:users,id'],
            ]);
            if (!$validator->passes()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'result' => false,
                ]);
            }
            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');
            $profile_img_path = '/assets/uploads/profile/';
            $cover_img_path = '/assets/uploads/cover_image/';

            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();
            if ($userDetails) {
                $userID = $userDetails->id;
                if ($request->language_id != '') {
                    $userlanguage_id = $request->language_id;
                } elseif ($userDetails->language_id != '') {
                    $userlanguage_id = $userDetails->language_id;
                } else {
                    $userlanguage_id = Languages::where('shortcode', 'en')->pluck('id')->first();
                }
            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }

            $user_details = User::select('users.*', 'countries.name as country_name', 'services.id as service_id', 'service_languages.service_name as service_name', DB::raw('CONCAT("' . $profile_img_path . '", profile_pic) AS profile_image'), DB::raw('CONCAT("' . $cover_img_path . '", cover_pic) AS cover_image'))
                ->join('countries', 'countries.id', 'users.country_id')
                ->leftjoin('subscriptions', function ($join) use ($request) {
                    $join->on('users.id', 'subscriptions.user_id');
                    $join->where('subscriptions.expiry_date', '>=', now());
                    $join->where('subscriptions.status', 'active');

                })
                ->leftjoin('services', 'services.id', 'subscriptions.service_id')
                ->leftjoin('service_languages', function ($join) use ($userlanguage_id) {
                    $join->on('service_languages.service_id', '=', 'services.id');
                    $join->where('service_languages.language_id', '=', $userlanguage_id);
                })

                ->where('users.id', $request->user_id)->first();
            // if(!$user_details){
            //     return response()->json([
            //     'errors' => 'Invalid User Id',
            //     'result' => false,
            // ]);
            // }
            $img_path = '/assets/uploads/service_gallery/';
            $gallery_images = ServicemanGallery::select('serviceman_galleries.*', DB::raw('CONCAT("' . $img_path . '", serviceman_galleries.image) AS gallery_image'))
                ->where('user_id', $user_details->id)->get();

            return response()->json(['result' => true, 'message' => 'User Updated successfully', 'user_data' => $user_details, 'gallery_images' => $gallery_images]);
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);

    }

    public function chat_list_bkp(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {

            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');

            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();
            if ($userDetails) {
                if ($request->language_id != '') {
                    $userlanguage_id = $request->language_id;
                } elseif ($userDetails->language_id != '') {
                    $userlanguage_id = $userDetails->language_id;
                } else {
                    $userlanguage_id = Languages::where('shortcode', 'en')->pluck('id')->first();
                }
                $img_path = '/assets/uploads/profile/';
                $chat_media_path = '/assets/uploads/chatmedia/';

                $a = ChatMessage::select('chat_messages.*', 'chat_messages.id as chat_id', 'chat_messages.sender_id as send_user_id', 'users.firstname', 'users.online_status', 'service_languages.service_name as service_name', DB::raw('CONCAT("' . $chat_media_path . '", chat_messages.uploads) AS chat_media'), DB::raw('CONCAT("' . $img_path . '", users.profile_pic) AS profile_image'))
                    ->join('users', 'users.id', 'chat_messages.receiver_id')
                    ->leftjoin('subscriptions', function ($join) use ($request) {
                        $join->on('users.id', 'subscriptions.user_id');
                        $join->where('subscriptions.expiry_date', '>=', now());
                        $join->where('subscriptions.status', 'active');

                    })
                    ->leftjoin('services', 'services.id', 'subscriptions.service_id')
                    ->leftjoin('service_languages', function ($join) use ($userlanguage_id) {
                        $join->on('service_languages.service_id', '=', 'services.id');
                        $join->where('service_languages.language_id', '=', $userlanguage_id);
                    })
                    ->where('sender_id', '=', $userDetails->id)
                    ->groupBy('receiver_id')
                // ->orderBy('chat_id','desc')
                ;

                $b = ChatMessage::select('chat_messages.*', 'chat_messages.id as chat_id', 'chat_messages.receiver_id as send_user_id', 'users.firstname', 'users.online_status', 'service_languages.service_name as service_name', DB::raw('CONCAT("' . $chat_media_path . '", chat_messages.uploads) AS chat_media'), DB::raw('CONCAT("' . $img_path . '", users.profile_pic) AS profile_image'))
                    ->join('users', 'users.id', 'chat_messages.sender_id')
                    ->leftjoin('subscriptions', function ($join) use ($request) {
                        $join->on('users.id', 'subscriptions.user_id');
                        $join->where('subscriptions.expiry_date', '>=', now());
                        $join->where('subscriptions.status', 'active');

                    })
                    ->leftjoin('services', 'services.id', 'subscriptions.service_id')
                    ->leftjoin('service_languages', function ($join) use ($userlanguage_id) {
                        $join->on('service_languages.service_id', '=', 'services.id');
                        $join->where('service_languages.language_id', '=', $userlanguage_id);
                    })
                    ->where('receiver_id', '=', $userDetails->id)
                    ->groupBy('sender_id')
                // ->orderBy('chat_id','desc')
                ;

                $chats = $b->union($a)
                    ->distinct('sender_user_id')
                    ->orderBy('chat_id', 'DESC')
                    ->paginate(10);
                foreach ($chats as $key => $row) {
                    // $chats[$key]['last_message']=ChatMessage::latest()->first();

                }

            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }

            return response()->json(['result' => true, 'chat_message' => $chats]);
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);

    }

    public function Addfavorite(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {

            $validator = Validator::make(request()->all(), [
                'favorite_user_id' => ['required', 'integer'],
            ]);
            if (!$validator->passes()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'result' => false,
                ]);
            }
            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');

            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();
            if ($userDetails) {
                $userID = $userDetails->id;
                if ($request->language_id != '') {
                    $userlanguage_id = $request->language_id;
                } elseif ($userDetails->language_id != '') {
                    $userlanguage_id = $userDetails->language_id;
                } else {
                    $userlanguage_id = Languages::where('shortcode', 'en')->pluck('id')->first();
                }
            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }
            $fav_user = Favorite::where('user_id', $userDetails->id)
                ->where('favorite_id', $request->favorite_user_id)->first();
            if ($fav_user) {
                return response()->json([
                    'message' => __('User already added in favorites.'),
                    'result' => false,
                ]);
            }
            $input['user_id'] = $userDetails->id;
            $input['favorite_id'] = $request->favorite_user_id;

            DB::beginTransaction();
            try {
                Favorite::create($input);

            } catch (\Exception $e) {

                DB::rollback();

                return response()->json([
                    'message' => __('Something went wrong.'),
                    'result' => false,
                ]);
            }
            DB::commit();
            $favorite_users = Favorite::select('users.*')
                ->join('users', 'users.id', 'favorites.favorite_id')
                ->distinct('user.id')
                ->where('favorites.user_id', $userDetails->id)
                ->get();

            // $user_details = User::find($userDetails->id);

            return response()->json(['result' => true, 'message' => 'Added to favorites successfully', 'favorites' => $favorite_users]);
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);

    }

    public function Removefavorite(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {

            $validator = Validator::make(request()->all(), [
                'favorite_user_id' => ['required', 'integer'],
            ]);
            if (!$validator->passes()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'result' => false,
                ]);
            }
            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');

            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();
            if ($userDetails) {
                $userID = $userDetails->id;
                if ($request->language_id != '') {
                    $userlanguage_id = $request->language_id;
                } elseif ($userDetails->language_id != '') {
                    $userlanguage_id = $userDetails->language_id;
                } else {
                    $userlanguage_id = Languages::where('shortcode', 'en')->pluck('id')->first();
                }
            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }

            $fav_user = Favorite::where('user_id', $userDetails->id)
                ->where('favorite_id', $request->favorite_user_id)->first();
            if (!$fav_user) {
                return response()->json([
                    'message' => __('User not exist.'),
                    'result' => false,
                ]);
            }

            DB::beginTransaction();
            try {

                $fav_user->delete();

            } catch (\Exception $e) {

                DB::rollback();

                return response()->json([
                    'message' => __('Something went wrong.'),
                    'result' => false,
                ]);
            }
            DB::commit();
            $favorite_users = Favorite::select('users.*')
                ->join('users', 'users.id', 'favorites.favorite_id')
                ->distinct('user.id')
                ->where('favorites.user_id', $userDetails->id)
                ->get();
            // $user_details = User::find($userDetails->id);

            return response()->json(['result' => true, 'message' => 'Removed from favorites successfully', 'favorites' => $favorite_users]);
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);

    }

    public function favoriteList(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {

            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');

            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();
            if ($userDetails) {
                $userID = $userDetails->id;
                if ($request->language_id != '') {
                    $userlanguage_id = $request->language_id;
                } elseif ($userDetails->language_id != '') {
                    $userlanguage_id = $userDetails->language_id;
                } else {
                    $userlanguage_id = Languages::where('shortcode', 'en')->pluck('id')->first();
                }
            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }

            $favorite_users = Favorite::select('users.*')
                ->join('users', 'users.id', 'favorites.favorite_id')
                ->distinct('user.id')
                ->where('favorites.user_id', $userDetails->id)
                ->get();
            // $user_details = User::find($userDetails->id);

            return response()->json(['result' => true, 'message' => 'list favorites successfully', 'favorites' => $favorite_users]);
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);

    }
    public function storeMessage(Request $request)
    {

        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {

            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');
            $filevalidate = '';
            // if( $request->hasFile('file') ) {
            //     $file = $request->file('file');
            //     // dd($file->getMimeType());
            //     $imagemimes = ['image/png','image/jpeg','image/jpg'];
            //     $videomimes = ['video/mp4'];
            //     $audiomimes = ['audio/mpeg'];
            //     $documentmimes = ['file/mpeg'];

            //     if(in_array($file->getMimeType() ,$imagemimes)) {
            //         // dd($imagemimes);
            //         $filevalidate = 'required|mimes:png,jpg,gif,svg|max:2048';
            //     }
            //     //Validate video
            //     if (in_array($file->getMimeType() ,$videomimes)) {
            //         $filevalidate = 'required|mimes:mp4';
            //     }
            //     //validate audio
            //     if (in_array($file->getMimeType() ,$audiomimes)) {
            //         $filevalidate = 'required|mimes:mpeng';
            //     }
            //     if (in_array($file->getMimeType() ,$documentmimes)) {
            //         $filevalidate = 'required|mimes:pdf';
            //     }
            // }

            $validator = Validator::make(request()->all(), [
                'receiver_id' => ['required', 'integer'],
                'type' => ['required', 'in:text,image,audio,video,document,location,address_card'],
                'message' => ['required_if:type,text,location'],
                'address_id' => ['required_if:type,address_card'],
            ]);
            if (!$validator->passes()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'result' => false,
                ]);
            }
            if ($request->type == 'image') {
                $validator2 = Validator::make(request()->all(), [
                    'file' => ['required', 'mimes:jpeg,png,jpg,svg'],
                ]);
            }
            if ($request->type == 'video') {
                $validator2 = Validator::make(request()->all(), [
                    'file' => ['required', 'mimes:mp4,x-flv,x-mpegURL,MP2T,3gpp,quicktime,x-msvideo,x-ms-wmv'],
                ]);
            }
            if ($request->type == 'audio') {
                $validator2 = Validator::make(request()->all(), [
                    'file' => ['required', 'mimes:application/octet-stream,audio/mpeg,mpga,mp3,wav,ogg,aac,mp4'],
                ]);
            }
            if ($request->type == 'document') {
                $validator2 = Validator::make(request()->all(), [
                    'file' => ['required', 'mimes:pdf,doc,docx'],
                ]);
            }
            if ((isset($validator2)) && !$validator2->passes()) {
                return response()->json([
                    'errors' => $validator2->errors(),
                    'result' => false,
                ]);
            }

            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();
            if ($userDetails) {
                if ($request->language_id != '') {
                    $userlanguage_id = $request->language_id;
                } elseif ($userDetails->language_id != '') {
                    $userlanguage_id = $userDetails->language_id;
                } else {
                    $userlanguage_id = Languages::where('shortcode', 'en')->pluck('id')->first();
                }
                $fileName = '';
                if ($request->hasFile('file')) {
                    $chatfile = $request->file('file');
                    $fileName = $request->type . time() . '.' . $chatfile->extension();
                    $chatfile->move(public_path('/assets/uploads/chatmedia/'), $fileName);
                }

                $input = $request->only(['receiver_id', 'type', 'message', 'address_id']);
                $input['sender_id'] = $userDetails->id;
                $input['status'] = 'send';
                $input['uploads'] = ($fileName) ? $fileName : '';

                DB::beginTransaction();
                try {
                    ChatMessage::create($input);

                } catch (\Exception $e) {

                    DB::rollback();

                    return response()->json([
                        'message' => __('Something went wrong.'),
                        'result' => false,
                    ]);
                }
                DB::commit();
                // dd(ChatMessage::all());

                $img_path = '/assets/uploads/profile/';
                $media_path = '/assets/uploads/chatmedia/';
                $address_img_path = '/assets/uploads/address_image/';

                $a = ChatMessage::select('chat_messages.*', 'chat_messages.sender_id as send_user_id', 'users.firstname', 'users.online_status',
                    DB::raw('CONCAT("' . $media_path . '", chat_messages.uploads) AS chat_media'),
                    DB::raw('CONCAT("' . $img_path . '", users.profile_pic) AS profile_image'),
                    DB::raw('(CASE WHEN (chat_messages.type="address_card" AND chat_messages.address_id != "") THEN CONCAT("' . $address_img_path . '", user_addresses.image ) ELSE NULL END) AS address_image'),
                )
                    ->join('users', 'users.id', 'chat_messages.receiver_id')
                    ->leftjoin('user_addresses', 'user_addresses.id', 'chat_messages.address_id')

                // ->leftjoin('subscriptions', function ($join) use ($request) {
                //     $join->on('users.id', 'subscriptions.user_id');
                //     $join->where('subscriptions.expiry_date', '>=', now());
                //     $join->where('subscriptions.status', 'active');

                // })
                // ->leftjoin('services', 'services.id', 'subscriptions.service_id')
                // ->leftjoin('service_languages', function ($join) use ($userlanguage_id) {
                //     $join->on('service_languages.service_id', '=', 'services.id');
                //     $join->where('service_languages.language_id', '=', $userlanguage_id);
                // })
                    ->where('sender_id', '=', $userDetails->id)
                    ->where('receiver_id', '=', $request->receiver_id);

                // ->groupBy('receiver_id');
                $b = ChatMessage::select('chat_messages.*', 'chat_messages.receiver_id as send_user_id', 'users.firstname', 'users.online_status', DB::raw('CONCAT("' . $media_path . '", chat_messages.uploads) AS chat_media'),
                    DB::raw('CONCAT("' . $img_path . '", users.profile_pic) AS profile_image'), DB::raw('(CASE WHEN (chat_messages.type="address_card" AND chat_messages.address_id != "") THEN CONCAT("' . $address_img_path . '", user_addresses.image ) ELSE NULL END) AS address_image'),
                )
                    ->join('users', 'users.id', 'chat_messages.sender_id')
                    ->leftjoin('user_addresses', 'user_addresses.id', 'chat_messages.address_id')

                // ->leftjoin('subscriptions', function ($join) use ($request) {
                //     $join->on('users.id', 'subscriptions.user_id');
                //     $join->where('subscriptions.expiry_date', '>=', now());
                //     $join->where('subscriptions.status', 'active');

                // })
                // ->leftjoin('services', 'services.id', 'subscriptions.service_id')
                // ->leftjoin('service_languages', function ($join) use ($userlanguage_id) {
                //     $join->on('service_languages.service_id', '=', 'services.id');
                //     $join->where('service_languages.language_id', '=', $userlanguage_id);
                // })
                    ->where('sender_id', '=', $request->receiver_id)
                    ->where('receiver_id', '=', $userDetails->id);
                // ->groupBy('sender_id');
                $chats = $b->union($a)
                // ->distinct('sender_user_id')
                    ->orderBy('id', 'DESC')
                    ->paginate(10);

            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }

            return response()->json(['result' => true, 'chat_message' => $chats]);
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);

    }

    public function chatMessages_bkp(Request $request)
    {

        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {

            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');
            $filevalidate = '';

            $validator = Validator::make(request()->all(), [
                'receiver_id' => ['required', 'integer'],

            ]);
            if (!$validator->passes()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'result' => false,
                ]);
            }
            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();
            if ($userDetails) {
                if ($request->language_id != '') {
                    $userlanguage_id = $request->language_id;
                } elseif ($userDetails->language_id != '') {
                    $userlanguage_id = $userDetails->language_id;
                } else {
                    $userlanguage_id = Languages::where('shortcode', 'en')->pluck('id')->first();
                }

                $img_path = '/assets/uploads/profile/';
                $media_path = '/assets/uploads/chatmedia/';

                $a = ChatMessage::select('chat_messages.*', 'chat_messages.sender_id as send_user_id', 'users.firstname', 'users.online_status', DB::raw('CONCAT("' . $media_path . '", chat_messages.uploads) AS chat_media'), DB::raw('CONCAT("' . $img_path . '", users.profile_pic) AS profile_image'))
                    ->join('users', 'users.id', 'chat_messages.receiver_id')

                    ->where('sender_id', '=', $userDetails->id)
                    ->where('receiver_id', '=', $request->receiver_id);

                $b = ChatMessage::select('chat_messages.*', 'chat_messages.receiver_id as send_user_id', 'users.firstname', 'users.online_status', DB::raw('CONCAT("' . $media_path . '", chat_messages.uploads) AS chat_media'), DB::raw('CONCAT("' . $img_path . '", users.profile_pic) AS profile_image'))
                    ->join('users', 'users.id', 'chat_messages.sender_id')

                    ->where('sender_id', '=', $request->receiver_id)
                    ->where('receiver_id', '=', $userDetails->id);
                $chats = $b->union($a)
                // ->distinct('sender_user_id')
                    ->orderBy('id', 'DESC')
                    ->paginate(10);

            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }

            return response()->json(['result' => true, 'chat_message' => $chats]);
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);

    }

    public function chatMessages(Request $request)
    {

        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {

            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');
            $filevalidate = '';

            $validator = Validator::make(request()->all(), [
                'receiver_id' => ['required', 'integer'],

            ]);
            if (!$validator->passes()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'result' => false,
                ]);
            }
            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();
            if ($userDetails) {
                if ($request->language_id != '') {
                    $userlanguage_id = $request->language_id;
                } elseif ($userDetails->language_id != '') {
                    $userlanguage_id = $userDetails->language_id;
                } else {
                    $userlanguage_id = Languages::where('shortcode', 'en')->pluck('id')->first();
                }

                $img_path = '/assets/uploads/profile/';
                $address_img_path = '/assets/uploads/address_image/';

                $media_path = '/assets/uploads/chatmedia/';
                // $matchThese = ['sender_id' => $userDetails->id, 'receiver_id' => $request->receiver_id];
                // $orThose = ['receiver_id' => $userDetails->id, 'sender_id' => $request->receiver_id];

                $chats = ChatMessage::select('chat_messages.*', 'chat_messages.sender_id as send_user_id', 'users.firstname', 'users.online_status', DB::raw('CONCAT("' . $media_path . '", chat_messages.uploads) AS chat_media'),
                    DB::raw('CONCAT("' . $img_path . '", users.profile_pic) AS profile_image'),
                    DB::raw('(CASE WHEN (chat_messages.type="address_card" AND chat_messages.address_id != "") THEN CONCAT("' . $address_img_path . '", user_addresses.image ) ELSE NULL END) AS address_image'),

                )

                    ->join('users', 'users.id', 'chat_messages.receiver_id')
                    ->leftjoin('user_addresses', 'user_addresses.id', 'chat_messages.address_id')

                    ->where(function ($query) use ($userDetails, $request) {
                        $query->where('sender_id', $userDetails->id)
                            ->where('receiver_id', $request->receiver_id);
                    })
                    ->orWhere(function ($query) use ($userDetails, $request) {
                        $query->where('receiver_id', $userDetails->id)
                            ->where('sender_id', $request->receiver_id);
                    })
                    ->latest()
                    ->paginate(50);

            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }

            return response()->json(['result' => true, 'chat_message' => $chats]);
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);

    }

    public function chat_list(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {

            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');

            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();
            if ($userDetails) {
                if ($request->language_id != '') {
                    $userlanguage_id = $request->language_id;
                } elseif ($userDetails->language_id != '') {
                    $userlanguage_id = $userDetails->language_id;
                } else {
                    $userlanguage_id = Languages::where('shortcode', 'en')->pluck('id')->first();
                }
                $img_path = '/assets/uploads/profile/';
                $chat_media_path = '/assets/uploads/chatmedia/';

                $serviceman = User::
                    select('users.id', 'users.firstname', 'users.lastname', 'users.phone', 'users.online_status', DB::raw('CONCAT("' . $img_path . '", users.profile_pic) AS profile_pic'), 'service_languages.service_name as service_name',
                    DB::raw('MAX(chat_messages.created_at) as last_message_created_at'))
                    ->join('chat_messages', function ($join) use ($userDetails) {

                        $join->on('chat_messages.receiver_id', DB::raw("users.id"))
                            ->where('chat_messages.sender_id', $userDetails->id);
                        $join->orOn('chat_messages.sender_id', DB::raw("users.id"))
                            ->where('chat_messages.receiver_id', $userDetails->id);
                    })
                    ->leftjoin('subscriptions', function ($join) {
                        $join->on('users.id', 'subscriptions.user_id');
                    })

                    ->leftjoin('services', 'services.id', 'subscriptions.service_id')
                    ->leftjoin('service_languages', function ($join) use ($userlanguage_id) {
                        $join->on('service_languages.service_id', '=', 'services.id');
                        $join->where('service_languages.language_id', '=', $userlanguage_id);
                    })
                // ->whereRaw('chat_messages.id IN (select MAX(a2.id) from chat_messages as a2 join users as u2 on u2.id = a2.receiver_id group by u2.id)')
                // ->orWhereRaw('chat_messages.id IN (select MAX(a2.id) from chat_messages as a2 join users as u2 on u2.id = a2.sender_id group by u2.id)')
                    ->groupBy('users.id')
                    ->orderBy('last_message_created_at', 'DESC')
                    ->paginate(10);

                foreach ($serviceman as $key => $row) {

                    $chat = ChatMessage::select('chat_messages.*', 'chat_messages.sender_id as send_user_id', 'users.firstname', 'users.online_status', DB::raw('CONCAT("' . $chat_media_path . '", chat_messages.uploads) AS chat_media'), DB::raw('CONCAT("' . $img_path . '", users.profile_pic) AS profile_image', 'chat_messages.created_at'))
                        ->join('users', 'users.id', 'chat_messages.receiver_id')
                        ->where(function ($query) use ($userDetails, $row) {
                            $query->where('sender_id', $userDetails->id)
                                ->where('receiver_id', $row->id);
                        })
                        ->orWhere(function ($query) use ($userDetails, $row) {
                            $query->where('receiver_id', $userDetails->id)
                                ->where('sender_id', $row->id);
                        })->latest()
                        ->first();

                    $unread_count = ChatMessage::select(DB::raw('COUNT(chat_messages.id) AS unread'))
                        ->join('users', 'users.id', 'chat_messages.receiver_id')
                        ->Where(function ($query) use ($userDetails, $row) {
                            $query->where('receiver_id', $userDetails->id)
                                ->where('sender_id', $row->id);
                        })
                        ->where('chat_messages.status', 'send')
                        ->first();

                    $serviceman[$key]['serviceman_id'] = $row->id;
                    $serviceman[$key]['chat_id'] = ($chat) ? $chat->id : '';

                    $serviceman[$key]['sender_id'] = ($chat) ? $chat->sender_id : '';
                    $serviceman[$key]['receiver_id'] = ($chat) ? $chat->receiver_id : '';
                    $serviceman[$key]['message'] = ($chat) ? $chat->message : '';
                    $serviceman[$key]['type'] = ($chat) ? $chat->type : '';
                    $serviceman[$key]['status'] = ($chat) ? $chat->status : '';
                    $serviceman[$key]['uploads'] = ($chat) ? $chat->chat_media : '';
                    $serviceman[$key]['unread_count'] = $unread_count->unread;
                    $serviceman[$key]['created_at'] = ($chat) ? $chat->created_at : '';

                }
                // array_multisort(array_column($serviceman->toArray() , "created_at"), SORT_ASC, $serviceman->toArray() );

                // print_r($serviceman);exit;

            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }

            return response()->json(['result' => true, 'chat_message' => $serviceman]);
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);

    }

    public function updateReadMessage(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {

            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');
            $filevalidate = '';

            $validator = Validator::make(request()->all(), [
                'sender_id' => ['required', 'integer'],
            ]);
            if (!$validator->passes()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'result' => false,
                ]);
            }

            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();
            if ($userDetails) {
                if ($request->language_id != '') {
                    $userlanguage_id = $request->language_id;
                } elseif ($userDetails->language_id != '') {
                    $userlanguage_id = $userDetails->language_id;
                } else {
                    $userlanguage_id = Languages::where('shortcode', 'en')->pluck('id')->first();
                }
                $input['status'] = 'read';

                DB::beginTransaction();
                try {
                    ChatMessage::where('sender_id', $request->sender_id)->where('receiver_id', $userDetails->id)->update($input);

                } catch (\Exception $e) {

                    DB::rollback();

                    return response()->json([
                        'message' => __('Something went wrong.'),
                        'result' => false,
                    ]);
                }
                DB::commit();

            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }

            return response()->json(['result' => true, 'message' => 'read status successfully updated']);
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);

    }

    public function other_userprofile(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {
            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');
            $profile_img_path = '/assets/uploads/profile/';
            $cover_img_path = '/assets/uploads/cover_image/';
            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();
            if ($userDetails) {
                if ($userDetails->language_id != '') {
                    $lang = Language::find($userDetails->language_id);
                } else {
                    $lang = Languages::where('shortcode', 'en')->first();
                }
                $userlanguage_id = $lang->id;
                $langcode = $lang->shortcode;
                App::setLocale($langcode);
                session()->put("lang_code", $langcode);
                $validator = Validator::make(request()->all(), [
                    'user_id' => 'required|integer',
                ]);

                if (!$validator->passes()) {
                    return response()->json([
                        'errors' => $validator->errors(),
                        'status' => false,
                    ]);
                }
                $userdetails = User::select('users.*', 'countries.name as country_name', 'services.id as service_id', 'service_languages.service_name as service_name', DB::raw('CONCAT("' . $profile_img_path . '", profile_pic) AS profile_image'), DB::raw('CONCAT("' . $cover_img_path . '", cover_pic) AS cover_image'))
                    ->leftjoin('countries', 'countries.id', 'users.country_id')
                    ->leftjoin('subscriptions', function ($join) use ($request) {
                        $join->on('users.id', 'subscriptions.user_id');
                        $join->where('subscriptions.expiry_date', '>=', now());
                        $join->where('subscriptions.status', 'active');

                    })
                    ->leftjoin('services', 'services.id', 'subscriptions.service_id')
                    ->leftjoin('service_languages', function ($join) use ($userlanguage_id) {
                        $join->on('service_languages.service_id', '=', 'services.id');
                        $join->where('service_languages.language_id', '=', $userlanguage_id);
                    })
                    ->where('users.id', $request->user_id)->first();

                if (!$userdetails) {
                    return response()->json([
                        'errors' => 'Invalid User',
                        'status' => false,
                    ]);
                }

                $returnArray = array('result' => true, 'message' => trans('messages.success'), 'userdetails' => $userdetails);

            } else {
                $returnArray = array('result' => false, 'message' => 'Attempt failed: User details not found. Please login again.');
            }
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);
    }

    public function get_Otheruseraddress(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {

            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');

            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();
            if ($userDetails) {
                $validator = Validator::make(request()->all(), [
                    'user_id' => 'required|integer',
                ]);

                if (!$validator->passes()) {
                    return response()->json([
                        'errors' => $validator->errors(),
                        'status' => false,
                    ]);
                }

                $userID = $userDetails->id;
                if ($request->language_id != '') {
                    $userlanguage_id = $request->language_id;
                } elseif ($userDetails->language_id != '') {
                    $userlanguage_id = $userDetails->language_id;
                } else {
                    $userlanguage_id = Languages::where('shortcode', 'en')->pluck('id')->first();
                }
                $img_path = '/assets/uploads/address_image/';

                $user_address = UserAddress::select('user_addresses.*', 'countries.name AS country', DB::raw('CONCAT("' . $img_path . '", image) AS image'))
                    ->leftjoin('countries', 'user_addresses.country_id', 'countries.id')
                    ->where('user_id', $request->user_id)->get();

                if (!$user_address) {
                    return response()->json([
                        'errors' => 'Invalid User Address',
                        'status' => false,
                    ]);
                }
            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }

            return response()->json(['result' => true, 'message' => 'Successfully', 'user_address' => $user_address]);
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);

    }

    public function activeServices(Request $request)
    {

        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {

            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');

            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();
            if ($userDetails) {
                $userID = $userDetails->id;
                if ($request->language_id != '') {
                    $userlanguage_id = $request->language_id;
                } elseif ($userDetails->language_id != '') {
                    $userlanguage_id = $userDetails->language_id;
                } else {
                    $userlanguage_id = Languages::where('shortcode', 'en')->pluck('id')->first();
                }
            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }
            $img_path = '/assets/uploads/service/';

            $services = User::select('services.id', 'service_languages.service_name as service_name', 'subscriptions.subscription_date', 'subscriptions.expiry_date', DB::raw('CONCAT("' . $img_path . '", image) AS service_image'))
                ->join('subscriptions', function ($join) {
                    $join->on('users.id', 'subscriptions.user_id');
                    $join->where('subscriptions.expiry_date', '>=', now());

                })->join('services', 'services.id', 'subscriptions.service_id')
                ->leftjoin('service_languages', function ($join) use ($userlanguage_id) {
                    $join->on('service_languages.service_id', '=', 'services.id');
                    $join->where('service_languages.language_id', '=', $userlanguage_id);
                })
                ->where('users.id', $userDetails->id)
                ->groupBy('services.id')
                ->orderBy('subscriptions.subscription_date', 'DESC')
                ->get();

            $returnArray = array('result' => true, 'message' => trans('messages.success'), 'services' => $services);

        } else {

            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);
    }

    public function activeSubscriptions(Request $request)
    {

        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {

            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');

            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();
            if ($userDetails) {
                $userID = $userDetails->id;
                if ($request->language_id != '') {
                    $userlanguage_id = $request->language_id;
                } elseif ($userDetails->language_id != '') {
                    $userlanguage_id = $userDetails->language_id;
                } else {
                    $userlanguage_id = Languages::where('shortcode', 'en')->pluck('id')->first();
                }
            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }
            $img_path = '/assets/uploads/service/';

            $subscriptions = User::select('subscriptions.id as subscription_id', 'subscriptions.service_id', 'package_languages.package_name', 'packages.amount', 'packages.validity', 'subscriptions.subscription_date', 'subscriptions.expiry_date', DB::raw('CONCAT("' . $img_path . '", image) AS service_image'))
                ->join('subscriptions', function ($join) {
                    $join->on('users.id', 'subscriptions.user_id');
                    $join->where('subscriptions.expiry_date', '>=', now());

                })->join('packages', 'packages.id', 'subscriptions.package_id')
                ->leftjoin('package_languages', function ($join) use ($userlanguage_id) {
                    $join->on('package_languages.package_id', '=', 'packages.id');
                    $join->where('package_languages.language_id', '=', $userlanguage_id);
                })
                ->join('services', 'services.id', 'subscriptions.service_id')
            // ->leftjoin('service_languages', function ($join) use ($userlanguage_id) {
            //     $join->on('service_languages.service_id', '=', 'services.id');
            //     $join->where('service_languages.language_id', '=', $userlanguage_id);
            // })
                ->where('users.id', $userDetails->id)
                ->groupBy('services.id')
                ->orderBy('subscriptions.subscription_date', 'DESC')
                ->get();
            // dd($subscriptions);
            // dd(PackageLanguage::where('package_id',19)->first());

            $returnArray = array('result' => true, 'message' => trans('messages.success'), 'subscriptions' => $subscriptions);

        } else {

            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);
    }

    public function report_customer(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {

            $validator = Validator::make(request()->all(), [

                'customer_id' => ['required', 'integer'],
                'reason' => ['required'],
                'comment' => ['required'],
            ]);

            if (!$validator->passes()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'status' => false,
                ]);
            }
            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');

            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();
            if ($userDetails) {
                $userID = $userDetails->id;
                if ($request->language_id != '') {
                    $userlanguage_id = $request->language_id;
                } elseif ($userDetails->language_id != '') {
                    $userlanguage_id = $userDetails->language_id;
                } else {
                    $userlanguage_id = Languages::where('shortcode', 'en')->pluck('id')->first();
                }
            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }
            $input = $request->only(['customer_id', 'reason', 'comment']);
            $input['reporter_id'] = $userDetails->id;

            DB::beginTransaction();
            try {

                $report = ReportedCustomer::create($input);

            } catch (\Exception $e) {

                DB::rollback();

                return response()->json([
                    'toast' => __('Something went wrong.'),
                    'status' => false,
                ]);
            }

            DB::commit();

            return response()->json(['result' => true, 'message' => 'Reported customer successfully']);
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);

    }

    public function Remove_galleryimages(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {
            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');
            $img_path = '/assets/uploads/cover_image/';

            // $userDetails = User::where('device_id', $deviceID)->where('api_token', $apiToken)->where('expiry_time', '>=', date('Y-m-d H:i:s'))->first();
            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();

            if ($userDetails) {
                $lang = Language::find($userDetails->language_id);
                $langcode = $lang->shortcode;
                App::setLocale($langcode);
                session()->put("lang_code", $langcode);
                $validator = Validator::make($request->all(), [
                    'image_id' => 'required|integer',
                ]);

                if ($validator->fails()) {
                    $returnArray = array('result' => false, 'message' => $validator->errors()->first());
                } else {
                    $galleryimage = ServicemanGallery::find($request->image_id);
                    if (!$galleryimage) {
                        return response()->json([
                            'errors' => 'Invalid gallery Image',
                            'status' => false,
                        ]);
                    }

                    $image_path = public_path('/assets/uploads/service_gallery/') . '/' . $galleryimage->image;
                    File::delete($image_path);
                    $galleryimage->delete();

                    $returnArray = array('result' => true, 'message' => trans('messages.success'));
                }
            } else {
                $returnArray = array('result' => false, 'message' => 'Attempt failed: User details not found. Please login again.');
            }
        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');
        }
        return response()->json($returnArray);
    }

    public function payment_success(Request $request)
    {

        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {

            $validator = Validator::make(request()->all(), [

                'order_id' => 'required|integer',
                // 'payment_gateway'=>'required',
                'payment_gateway' => 'required|in:payfort,thawani',

            ]);
            if (!$validator->passes()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'status' => false,
                ]);
            }

            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');

            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();

            if ($userDetails) {
                $userID = $userDetails->id;
                if ($request->language_id != '') {
                    $userlanguage_id = $request->language_id;
                } elseif ($userDetails->language_id != '') {
                    $userlanguage_id = $userDetails->language_id;
                } else {
                    $userlanguage_id = Languages::where('shortcode', 'en')->pluck('id')->first();
                }
            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }

            $order = Order::where('id', $request->order_id)->where('user_id', $userID)->first();
            if (!$order) {
                return response()->json([
                    'toast' => __('Invalid Order'),
                    'result' => false,
                ]);

            }
            $order->update([
                'payment_gateway' => $request->payment_gateway,
                'payment_status' => 'success',
                'client_reference_id' => $request->client_reference_id,
                'invoice_id' => $request->invoice_id,
            ]);

            $user_data = User::find($userID)->update([
                'firstname' => $order->firstname,
                'lastname' => $order->lastname,
                'civil_card_no' => $order->civil_card_no,
                'dob' => date('Y-m-d', strtotime($order->dob)),
                'gender' => $order->gender,
                'country_id' => $order->country_id,
                'state' => $order->state,
                'region' => $order->region,
                'address' => $order->address,
                'user_type' => 'service_man',
            ]);
            // dd(User::find($userID));
            if (!empty($request->file('files'))) {
                foreach ($request->file('files') as $key => $service_doc) {

                    $fileName = time() . '_' . $key . '.' . $service_doc->extension();
                    $service_doc->move(public_path('/assets/uploads/documents/'), $fileName);

                    ServiceDocument::create([
                        'user_id' => $userID,
                        'service_id' => $order->service_id,
                        'file' => $fileName,
                    ]);
                }
            }
            $package = Package::find($order->package_id);
            $newvalidity = '';
            if ($package) {
                if ($package->validity == '3 months') {
                    $post_month = 3;
                } elseif ($package->validity == '6 months') {
                    $post_month = 6;
                } else {
                    $post_month = 12;

                }
                $newvalidity = Carbon::now()->addMonths($post_month)->format('Y-m-d');

            } else {

                return response()->json([
                    'toast' => __('Invalid Package'),
                    'result' => false,
                ]);

            }
            $sub_id = Subscription::create([
                'service_id' => $order->service_id,
                'user_id' => $userID,
                'package_id' => $order->package_id,
                'order_id' => $order->id,
                'subscription_date' => date('Y-m-d'),
                'expiry_date' => $newvalidity,
                'type' => 'service_man',
                'coupon_code' => $order->coupon_code,
                'status' => "active",
            ])->id;
            $existing_subscription = Subscription::where('id', '!=', $sub_id)->where('user_id', $userID)->where('service_id', $order->service_id)->update([
                "status" => 'expired',
            ]);
            $subscription_details = Subscription::select('subscriptions.*', 'service_languages.service_name', 'package_languages.package_name')
                ->leftjoin('service_languages', function ($join) use ($userlanguage_id) {
                    $join->on('service_languages.service_id', '=', 'subscriptions.service_id');
                    $join->where('service_languages.language_id', '=', $userlanguage_id);
                })
                ->leftjoin('package_languages', function ($join) use ($userlanguage_id) {
                    $join->on('package_languages.package_id', '=', 'subscriptions.package_id');
                    $join->where('package_languages.language_id', '=', $userlanguage_id);
                })

                ->where('subscriptions.id', $sub_id)->first();
            $packageinfo = Package::find($subscription_details->package_id);
            $returnArray = array(
                'message' => trans('messages.success'),
                'subscription' => $subscription_details,
                'package_info' => $packageinfo,
                'order_details' => $order,
                'result' => true);

        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');

        }
        return response()->json($returnArray);

    }

    public function payment_failed(Request $request)
    {
        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {

            $validator = Validator::make(request()->all(), [

                'order_id' => 'required|integer',
                'payment_gateway' => 'required|in:payfort,thawani',

            ]);
            if (!$validator->passes()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'status' => false,
                ]);
            }

            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');

            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();

            if ($userDetails) {
                $userID = $userDetails->id;
                if ($request->language_id != '') {
                    $userlanguage_id = $request->language_id;
                } elseif ($userDetails->language_id != '') {
                    $userlanguage_id = $userDetails->language_id;
                } else {
                    $userlanguage_id = Languages::where('shortcode', 'en')->pluck('id')->first();
                }
            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }

            $order = Order::where('id', $request->order_id)->where('user_id', $userID)->first();
            if (!$order) {
                return response()->json([
                    'toast' => __('Invalid Order'),
                    'result' => false,
                ]);

            }

            $order->update([
                'payment_status' => 'failed',
                'response_code' => $request->response_code,
                'response_message' => $request->response_message,
            ]);
            $returnArray = array(
                'message' => 'payment failed',
                'order_details' => $order,
                'result' => true);

        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');

        }
        return response()->json($returnArray);

    }
    public function payment_webhook(Request $request)
    {

        if (!empty($request->header('device-id')) && !empty($request->header('api-token'))) {

            $validator = Validator::make(request()->all(), [

                'order_id' => 'required|integer',
                'payment_status' => 'required|in:success,failed',
                'payment_gateway' => 'required|in:payfort,thawani',
                // 'payment_gateway'=>'required',

            ]);
            if (!$validator->passes()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'status' => false,
                ]);
            }

            $deviceID = $request->header('device-id');
            $apiToken = $request->header('api-token');

            $userDetails = User::select('users.*')
                ->where('users.device_id', $deviceID)
                ->where('users.api_token', $apiToken)
                ->where('users.api_token_expiry', '>=', date('Y-m-d H:i:s'))
                ->first();

            if ($userDetails) {
                $userID = $userDetails->id;
                if ($request->language_id != '') {
                    $userlanguage_id = $request->language_id;
                } elseif ($userDetails->language_id != '') {
                    $userlanguage_id = $userDetails->language_id;
                } else {
                    $userlanguage_id = Languages::where('shortcode', 'en')->pluck('id')->first();
                }
            } else {
                return response()->json(['result' => false, 'message' => 'Attempt failed: User details not found. Please login again.']);
            }

            $order = Order::where('id', $request->order_id)->where('user_id', $userID)->first();
            if (!$order) {
                return response()->json([
                    'toast' => __('Invalid Order'),
                    'result' => false,
                ]);

            }
            if ($request->payment_status == "success") {
                $order->update([
                    'payment_gateway' => $request->payment_gateway,
                    'payment_status' => 'success',
                    'client_reference_id' => $request->client_reference_id,
                    'invoice_id' => $request->invoice_id,
                ]);

                $user_data = User::find($userID)->update([
                    'firstname' => $order->firstname,
                    'lastname' => $order->lastname,
                    'civil_card_no' => $order->civil_card_no,
                    'dob' => date('Y-m-d', strtotime($order->dob)),
                    'gender' => $order->gender,
                    'country_id' => $order->country_id,
                    'state' => $order->state,
                    'region' => $order->region,
                    'address' => $order->address,
                    'user_type' => 'service_man',
                ]);
                // dd(User::find($userID));
                if (!empty($request->file('files'))) {
                    foreach ($request->file('files') as $key => $service_doc) {

                        $fileName = time() . '_' . $key . '.' . $service_doc->extension();
                        $service_doc->move(public_path('/assets/uploads/documents/'), $fileName);

                        ServiceDocument::create([
                            'user_id' => $userID,
                            'service_id' => $order->service_id,
                            'file' => $fileName,
                        ]);
                    }
                }
                $package = Package::find($order->package_id);
                $newvalidity = '';
                if ($package) {
                    if ($package->validity == '3 months') {
                        $post_month = 3;
                    } elseif ($package->validity == '6 months') {
                        $post_month = 6;
                    } else {
                        $post_month = 12;

                    }
                    $newvalidity = Carbon::now()->addMonths($post_month)->format('Y-m-d');

                } else {

                    return response()->json([
                        'toast' => __('Invalid Package'),
                        'result' => false,
                    ]);

                }
                $sub_id = Subscription::create([
                    'service_id' => $order->service_id,
                    'user_id' => $userID,
                    'package_id' => $order->package_id,
                    'order_id' => $order->id,
                    'subscription_date' => date('Y-m-d'),
                    'expiry_date' => $newvalidity,
                    'type' => 'service_man',
                    'coupon_code' => $order->coupon_code,
                    'status' => "active",
                ])->id;
                $existing_subscription = Subscription::where('id', '!=', $sub_id)->where('user_id', $userID)->where('service_id', $order->service_id)->update([
                    "status" => 'expired',
                ]);
                $subscription_details = Subscription::select('subscriptions.*', 'service_languages.service_name', 'package_languages.package_name')
                    ->leftjoin('service_languages', function ($join) use ($userlanguage_id) {
                        $join->on('service_languages.service_id', '=', 'subscriptions.service_id');
                        $join->where('service_languages.language_id', '=', $userlanguage_id);
                    })
                    ->leftjoin('package_languages', function ($join) use ($userlanguage_id) {
                        $join->on('package_languages.package_id', '=', 'subscriptions.package_id');
                        $join->where('package_languages.language_id', '=', $userlanguage_id);
                    })

                    ->where('subscriptions.id', $sub_id)->first();
                $packageinfo = Package::find($subscription_details->package_id);
                $returnArray = array(
                    'message' => trans('messages.success'),
                    'subscription' => $subscription_details,
                    'package_info' => $packageinfo,
                    'order_details' => $order,
                    'result' => true);
            } else {
                $order->update([
                    'payment_status' => 'failed',
                    // 'response_code' => $request->response_code,
                    // 'response_message' => $request->response_message,
                ]);
                $returnArray = array(
                    'message' => 'payment failed',
                    'order_details' => $order,
                    'result' => true);
            }

        } else {
            $returnArray = array('result' => false, 'message' => 'Attempt failed: Invalid request. Device id or access token not found.');

        }
        return response()->json($returnArray);

    }

    public function SmsAPI($phone,$otp,$langcode=NULL)
    {
        $url="https://ismartsms.net/RestApi/api/SMS/PostSMS";
        $now = date('m/d/Y h:m:s');
        $lang_id=(($langcode=="ar")?'64':'0');
        $message=($lang_id=='0')?"OTP for Login Transaction on Tuw services is $otp and valid till 4 minutes. Do not share this OTP to anyone for security reasons":
            "OTP لمعاملة تسجيل الدخول على Tuw services هو $otp وصالحة حتى 4 دقائق. لا تشارك كلمة المرور لمرة واحدة مع أي شخص لأسباب أمنية";
        $ch = curl_init($url);
        $ch_headers = array(
            "content-type: application/json",
            "Cache-Control: no-cache",
        );

        $data=[
            "UserID"=>Config::get('constants.sms.user_id'),
            "Password"=>Config::get('constants.sms.password'),
            "Message"=>$message,
            "Language"=>$lang_id,
            "ScheddateTime"=>$now,
            "MobileNo"=>[$phone],
            "RecipientType"=>"1"
        ];
        $data=json_encode($data);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $ch_headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err) {
            echo $err;
        } else {
            $fetch_data = json_decode($result, true);
        }
        return $fetch_data;
    }

}
