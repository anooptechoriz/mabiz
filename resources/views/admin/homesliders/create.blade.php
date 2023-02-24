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
    @if (\Session::has('errormsg'))
        <div class="alert alert-danger">
            <ul>
                <li>{!! \Session::get('errormsg') !!}</li>
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
                    <h1 class="h4 mb-2 text-gray-800 mb-3">Home Sliders</h1>
                    {{-- <div class="float-right">
                    </div> --}}
                    <form action="" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <strong>Slider Title<span class="text-danger">*</span></strong> <input type="text" name="slider_title" value="{{ old('slider_title') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <strong>Banner/Slider<span class="text-danger">*</span></strong> <select name="type" class="form-control" id="choose">
                                        <option value="">Choose</option>
                                        <option value="slider">Slider</option>
                                        <option value="banner">Banner</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6" id="banner_outer" style="display:none">
                                <div class="form-group">
                                    <strong>Banner<span class="text-danger">*</span></strong>
                                    <input type="file" name="image[]" id="banner" class="form-control" multiple="multiple">
                                </div>
                            </div>
                            <div class="col-12" id="fields_extent">
                                <div class="form-group">
                                    <strong>Image <span class="text-danger">*</span></strong>
                                    <div class="input-group control-group increment">
                                        <input type="file" name="image[]" class="form-control h-100">
                                        <div class="input-group-btn">
                                            <button class="btn btn-success" type="button"><i class="fas fa-plus"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <strong>Title on Image</strong> <input type="text" name="title_on_image[]" value="" class="form-control">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <strong>Image Target </strong> <input type="text" name="image_target[]" value="" class="form-control">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <strong>Description </strong>
                                    <textarea name="description[]" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="clone-group" id="clone" style="display: none;">
                            <div class="row clone-group">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <strong>Image</strong>
                                        <div class="input-group control-group increment">
                                            <input type="file" name="image[]" class="form-control h-100">
                                            <div class="input-group-btn">
                                                <button class="btn btn-danger" type="button"><i class="fa fa-times-circle"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <strong>Title on Image</strong> <input type="text" name="title_on_image[]" value="" class="form-control">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <strong>Description </strong>
                                        <textarea name="description[]" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <strong>Image Target </strong> <input type="text" name="image_target[]" value="" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="back_btn w-100">
                            <button type="submit" value="Submit" class="btn btn-primary">Submit</button>
                            <a class="btn btn-primary" href="{{ route('admin.homesliders') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i> Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer_scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $(".btn-success").click(function() {
                var html = $("#clone").html();
                $("#fields_extent").append(html);
            });
            $("body").on("click", ".btn-danger", function() {
                $(this).parents(".clone-group").remove();
            });
        });
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
