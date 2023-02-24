<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Generalsetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class GeneralsettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    public function settings()
    {
        $settings = Generalsetting::get();
        return view('admin.general_settings.settings', compact('settings'));
    }
    public function storesettings(Request $request)
    {
        $settings = Generalsetting::all();
        $cnt = 1;
        if ($settings) {

            $this->validate($request, [
                'notification_email' => 'nullable|regex:/(.+)@(.+)\.(.+)/i',
                'website_url' => 'nullable',
                'company_email' => 'nullable|regex:/(.+)@(.+)\.(.+)/i',
                'site_currency' => 'nullable',
                'site_currency_icon' => 'nullable',
                'compony_address' => 'nullable',

            ]);
            foreach ($settings as $items) {
                $fieldname = $items->item;

                $file = $request->file($fieldname);
                if ($file) {
                    //Delete existing image coDe--
                    $gensettings = Generalsetting::find($items->id);
                    if (!empty($gensettings) && $gensettings->value != '') {
                        $file_path = public_path('/assets/uploads/logo/') . $gensettings->value;
                        File::delete($file_path);
                    }
                    $this->validate($request, [
                        $fieldname => 'mimes:jpeg,jpg,png,svg|max:2048',
                    ]);
                    $fileName = 'c_logo_' . $cnt . time() . '.' . $request->$fieldname->extension();
                    $request->$fieldname->move(public_path('/assets/uploads/logo/'), $fileName);
                    Generalsetting::find($items->id)->update(['value' => $fileName]);
                } else if ($items->item != 'company_logo' && $items->item != 'footer_logo') {
                    switch ($items->item) {
                        case $items->item:
                            Generalsetting::find($items->id)->update(['value' => $request->$fieldname]);
                            break;
                    }
                }
                $cnt++;
            }
            return redirect()->route('admin.settings')->with('success', 'Settings updated successfully.');
        } else {
            return redirect()->route('admin.settings')->withErrors('General settings details not found.');
        }
    }
    public function remove_image(Request $request)
    {
        if ($request->id != '') {
            $Generalsetting = Generalsetting::find($request->id);
            if ($Generalsetting) {
                if ($Generalsetting->value != '') {
                    $imagefile = public_path('/assets/uploads/logo/') . $Generalsetting->value;
                    File::delete($imagefile);
                    Generalsetting::find($request->id)->update(['value' => '']);
                    $returnArray['result'] = true;
                    $returnArray['message'] = 'Image removed successfully.';
                } else {
                    $returnArray['result'] = false;
                    $returnArray['message'] = 'Failed. Image not found.';
                }
            } else {
                $returnArray['result'] = false;
                $returnArray['message'] = 'Failed. Details not found.';
            }
        } else {
            $returnArray['result'] = false;
            $returnArray['message'] = 'Failed. Something went wrong id not found.';
        }
        return response()->json($returnArray);
    }
}
