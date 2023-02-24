<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Homeslider;
use App\Models\Homesliderimage;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Validator;

class HomesliderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    public function index(Request $request)
    {
        $sliders = Homeslider::latest()->paginate(8);
        $slider_image_data = Homesliderimage::all();
        return view('admin.homesliders.index', compact('sliders', 'slider_image_data'))
            ->with('i', ($request->input('page', 1) - 1) * 30);
    }
    public function create()
    {
        return view('admin.homesliders.create');
    }
    public function store(Request $request)
    {

        $this->validate($request, [
            'slider_title' => 'required|unique:homesliders,name',
            'type' => 'required',
            'image' => 'required']
            , ['image.0.required' => 'The image field is required.',
            ]);
        $data1 = Homeslider::create([
            'name' => $request->slider_title,
            'type' => $request->type,
        ]);

        // dd($data1);
        $last_inserted_id = $data1->id;
        $counter = 0;
        if ($request->file('image')) {
            foreach ($request->file('image') as $image_file) {
                $fileName = time() . $image_file->getClientOriginalName();
                $image_file->move(public_path('/assets/uploads/homesliders/'), $fileName);
                Homesliderimage::create([
                    'slider_id' => $last_inserted_id,
                    'banner' => $fileName,
                    'image' => $fileName,
                    'title' => $request->title_on_image[$counter],
                    'description' => $request->description[$counter],
                    'target' => $request->image_target[$counter],
                ]);
                $counter++;
            }
        }
        return redirect()->route('admin.homesliders')->with('success', 'Sliders added successfully');
    }
    public function show($id = null)
    {
        $slider_data = Homeslider::find($id);
        $slider_images = Homesliderimage::where('slider_id', $id)->get()->all();
        // dd($slider_images);
        return view('admin.homesliders.show', compact('slider_data', 'slider_images'));
    }
    public function edit($id = null)
    {
        $slider_data = Homeslider::find($id);
        $slider_images = Homesliderimage::where('slider_id', $id)->get()->all();
        return view('admin.homesliders.edit', compact('slider_data', 'slider_images'));
    }
    public function update(Request $request, $id = null)
    {
        $validator = Validator::make($request->all(), [
            'slider_title' => 'required|unique:homesliders,slider_title',
            'type' => 'required',
            'image' => 'required',
        ]);

        // $this->validate($request, [
        //     'slider_title'      =>'required|unique:homesliders,name',
        // ]);

        // Validator::make($request->all(), [
        //     'slider_title' => 'required',
        //     'type' => 'required',
        //     'image' => 'required',
        // ])->validate();

        // $validatedData = $request->validate([
        //     'slider_title' => 'required|unique:homesliders,name',
        //     'type' => 'required',
        //     'image' => 'required',
        // ]);

        $data1 = Homeslider::find($id)->update([
            'name' => $request->slider_title,
            'type' => $request->type,
        ]);
        $file = $request->file('image');
        $counter = 0;
        if ($file) {
            // dd($request->file('image'));
            foreach ($request->file('image') as $image_file) {
                $fileName = time() . $image_file->getClientOriginalName();
                $image_file->move(public_path('/assets/uploads/homesliders/'), $fileName);
                Homesliderimage::create([
                    'slider_id' => $id,
                    'image' => $fileName,
                    'title' => $request->title_on_image[$counter],
                    'description' => $request->description[$counter],
                    'target' => $request->image_target[$counter],
                ]);
                $counter++;
            }
        }
        $old_image_id = $request->old_image_id;
        $counter = 0;
        if ($old_image_id) {
            foreach ($request->old_image_id as $image_id) {
                Homesliderimage::find($image_id)->update([
                    'title' => $request->old_title_on_image[$counter],
                    'description' => $request->old_description[$counter],
                    'target' => $request->old_image_target[$counter],
                ]);
                $counter++;
            }
        }
        return redirect()->route('admin.homesliders')->with('success', 'Sliders updated successfully');
    }
    public function destroy($id = null)
    {
        $gallery = Homesliderimage::where('slider_id', '=', $id)->get()->all();
        foreach ($gallery as $type) {
            $file_path = public_path('/assets/uploads/homesliders/') . $type->images;
            File::delete($file_path);
        }
        Homeslider::find($id)->delete();

        Homesliderimage::where('slider_id', $id)->delete();
        return redirect()->route('admin.homesliders')
            ->with('success', 'Data deleted successfully');
    }
    public function removeMedia(Request $request)
    {
        $gallery = Homesliderimage::find($request->id);
        if (!empty($gallery)) {
            $file_path = public_path('/assets/uploads/sliders/') . $gallery->image;
            File::delete($file_path);
        }
        Homesliderimage::find($request->id)->delete();
        $message = "Successfully deleted";
        $ajax_status = 'success';
        $return_array = array('ajax_status' => $ajax_status, 'message' => $message);
        return response()->json($return_array);
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
                    $update = Homeslider::find($id)->update([
                        'status' => $request->status,
                    ]);
                    if ($update) {
                        Homeslider::where('id', '!=', $id)->update([
                            'status' => 'inactive',
                        ]);
                    }
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

    // public function changeStatus(Request $request){
    //     $returnarray['result'] = 'failed';
    //     if(!empty($request->id) && !empty($request->status)){
    //         $homesliderdetails = Homeslider::find($request->id);
    //         if($homesliderdetails){
    //             Homeslider::find($request->id)->update([
    //                 'status' => $request->status
    //             ]);
    //             $returnarray['result'] = 'success';
    //             $returnarray['message'] = 'Promotion banner status updated.';
    //         } else {
    //             $returnarray['result'] = 'failed';
    //             $returnarray['message'] = 'Promotion banner details not found.';
    //         }
    //     } else {
    //         $returnarray['result'] = 'failed';
    //         $returnarray['message'] = 'Status not updated. Promotion banner id or status is missed.';
    //     }
    //     return response()->json($returnarray);
    // }

}
