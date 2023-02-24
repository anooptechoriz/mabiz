@extends('layouts.admin.index')
@section('content')
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>{{ _('messages.Whoops') }}!</strong> {{ _('messages.There were some problems with your input') }}.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (session('success'))
        <div class="alert alert-success">
            <ul>
                <li>{{ session('success') }}</li>
            </ul>
        </div>
    @endif
    <div class="container-fluid">
        <div class="col-md-8 m-auto">
            <div class="card shadow mb-4 my-5">
                <div class="card-body">
                    <h1 class="h4 mb-2 text-gray-800">Update Sliders</h1>
                    <form action="" method="POST" enctype="multipart/form-data">
                        {{-- <form method="post" action="{{ route('homesliders.update', $slider_data->id) }}" enctype="multipart/form-data"> --}}

                        @csrf
                        {{-- @php dd($slider_data); @endphp --}}
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <strong>Slider Title<span class="text-danger">*</span></strong> <input type="text" name="slider_title" value="{{ $slider_data->name }}" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <strong>Banner/Slider<span class="text-danger">*</span></strong> <select name="type" class="form-control" id="choose" required>
                                        <option value="">Choose</option>
                                        <option value="slider" @if ($slider_data->type == 'slider') {{ 'selected' }} @endif>Slider</option>
                                        <option value="banner" @if ($slider_data->type == 'banner') {{ 'selected' }} @endif>Banner</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="col-12 col-sm-6" id="banner_outer" @if ($slider_data->banner_type != 'banner') style="display:none" @endif>
                                    <div class="form-group">
                                        @if ($slider_data->banner != '')
                                            <img src="{{ asset('/assets/uploads/homesliders/' . $slider_data->banner) }}" class="img-thumbnail" width="175" />
                                        @endif
                                        <strong>Banner<span class="text-danger">*</span></strong> <input type="file" name="image[]" id="banner" class="form-control" multiple="multiple">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" id="fields_extent">
                                <div id="myElem"></div>
                                @foreach ($slider_images as $images)
                                    <div class="row clone-group" id="outer_media_{{ $images->id }}">
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <strong>Image<span class="text-danger">*</span></strong>
                                                <div class="input-group control-group increment align-items-center">
                                                    @if ($images->image != '')
                                                        <div class="incr-img mb-2 ">
                                                            <img src="{{ asset('/assets/uploads/homesliders/' . $images->image) }}" class="img-thumbnail d-block" width="120" />
                                                        </div>
                                                    @endif
                                                    {{-- <input type="" name="image[]" class="form-control"> --}}
                                                    <div class="input-group-btn ml-2">
                                                        <button class="btn delete_ext text-white" type="button" onclick="removeMedia({{ $images->id }})"><i class="fa fa-times-circle"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <strong>Title on Image</strong> <input type="text" name="old_title_on_image[]" value="{{ $images->title }}" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <strong>Description </strong>
                                                <textarea name="old_description[]" class="form-control">{{ $images->description }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <strong>Image Target </strong> <input type="text" name="old_image_target[]" value="{{ $images->target }}" class="form-control">
                                            </div>
                                        </div>
                                        <input type="hidden" name="old_image_id[]" value="{{ $images->id }}" />
                                    </div>
                                @endforeach
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <strong>Image<span class="text-danger">*</span></strong>
                                    <div class="input-group control-group increment">
                                        <input type="file" name="image[]" class="form-control h-100">
                                        <div class="input-group-btn ml-2">
                                            <button class="btn btn-success" type="button"><i class="fas fa-plus"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <strong>Title on Image</strong> <input type="text" name="title_on_image[]" value="{{ old('title_on_image') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <strong>Description </strong>
                                    <textarea name="description[]" class="form-control">{{ old('description') }}</textarea>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <strong>Image Target </strong> <input type="text" name="image_target[]" value="{{ old('image_target') }}" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row clone-group" id="clone" style="display: none;">
                            <div class="row clone-group">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <strong>Image<span class="text-danger">*</span></strong>
                                        <div class="input-group control-group increment">
                                            <input type="file" name="image[]" class="form-control">
                                            <div class="input-group-btn">
                                                <button class="btn btn-danger delete_new" type="button"><i class="fa fa-times-circle"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <strong>Title on Image</strong> <input type="text" name="title_on_image[]" value="{{ old('title_on_image') }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <strong>Description </strong>
                                        <textarea name="description[]" class="form-control">{{ old('description') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <strong>Image Target </strong> <input type="text" name="image_target[]" value="{{ old('image_target') }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- </form> --}}
                        <div class="back_btn w-100">
                            <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
                            <a class="btn btn-primary" href="{{ route('admin.homesliders') }}" title=" Back to Listings"> <i class="fa fa-arrow-left" aria-hidden="true"></i> Back</a>
                            {{--  --}}
                        </div>
                </div>
            </div>

        </div>
        </form>
    </div>
    {{-- </div>
    </div> --}}
@endsection
@section('footer_scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $(".btn-success").click(function() {
                var html = $("#clone").html();
                $("#fields_extent").append(html);
            });
            $("body").on("click", ".delete_new", function() {
                $(this).parents(".clone-group").remove();
            });

        });

        function removeMedia(cid) {
            // alert(cid)
            if (cid) {
                var outerhtml = $("#outer_media_" + cid).html();
                $("#outer_media_" + cid).html('<img src="{{ asset('img/ajax-loader.gif') }}" >')
                $.ajax({
                    type: "post",
                    data: {
                        id: cid,
                        "_token": "{{ csrf_token() }}"
                    },
                    url: "{{ route('homesliders.removeMedia') }}", //Please see the note at the end of the post**
                    success: function(res) {
                        if (res.ajax_status == 'success') {
                            html = '';
                            $("#outer_media_" + cid).html(html);
                            $("#outer_media_" + cid).remove();
                            $("#myElem").html(res.message);
                            $("#myElem").show().delay(3000).fadeOut();
                        } else {
                            $("#outer_media_" + cid).html(outerhtml);
                            $("#myElem").html(res.message);
                            $("#myElem").show().delay(3000).fadeOut();
                        }
                    }
                });
            }
        }
        $(document).on("change", "#choose", function() {
            if ($(this).val() == 'slider') {
                $("#banner_outer").hide();
                $("#fields_extent").show();
            } else if ($(this).val() == 'banner') {
                $("#banner_outer").show();
                $("#fields_extent").hide();
            } else {
                $("#banner_outer").hide();
                $("#fields_extent").hide();
            }
        });
    </script>
@endsection
{{-- success:function(res){
    if (response.result == 'success') {
        elm.closest('.image_block').remove();

        if ($('.image_block').length < 1) {
            secBlock.remove();
        }
        if (response.type == 'plain') {
            $('#image_upload').show();
        } --}}
