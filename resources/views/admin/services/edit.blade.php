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
            <div class="card shadow mb-4 mt-5">
                <div class="card-body">
                    <h1 class="h4 mb-2 text-gray-800">Update Service</h1>
                    <form method="post" action="" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            @foreach ($servicelanguages as $row)
                                @if ($row->shortcode == 'en')
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <strong>{{ __('messages.name in english') }}<span class="text-danger">*</span></strong>
                                            <input type='hidden' name="language" value="1">
                                            <input type="text" name="service_name_english" value="{{ $row->service_name }}" class="form-control" required>
                                        </div>
                                    </div>
                                @endif
                                @if ($row->shortcode == 'ar')
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <strong>{{ __('messages.name in arabic') }}<span class="text-danger">*</span></strong>
                                            <input type='hidden' name="language" value="2">
                                            <input type="text" name="service_name_arabic" value="{{ $row->service_name }}" class="form-control" required>
                                        </div>
                                    </div>
                                @endif
                                @if ($row->shortcode == 'hi')
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <strong>{{ __('messages.name in hindi') }}<span class="text-danger">*</span></strong>
                                            <input type='hidden' name="language" value="3">
                                            <input type="text" name="service_name_hindi" value="{{ $row->service_name }}" class="form-control" required>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                            <div class="col-12 col-sm-6">
                                <div id="myElem"></div>
                                <div id="outer_media_{{ $services->id }}">
                                    @if ($services->image != '')
                                        <img src="{{ asset('/assets/uploads/service/') }}/{{ $services->image }}" alt="{{ $services->image }}" width="50px" />
                                        <a href="javascript:void(0)" onclick="removeImage({{ $services->id }})" class="btn btn-danger btn-circle btn-md"><i class="far fa-times-circle"></i></a>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <strong>{{ __('messages.image') }}:</strong><span class="text-danger">(Max Image dimension width:50 x height:50 pixel, Max: 1MB)</span>
                                    <input id="profile_pic" type="File" class="course-img" name="image">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <strong>{{ __('messages.parent service') }}</strong>
                                    <div class="card-body category-list-block">
                                        <ul>
                                            <li><a href="javascript:void(0)" id="category" data_item="0" class="service_items @if ($services->parent_id == 0) {{ 'active' }} @endif">Parent</a></li>
                                        </ul>
                                        @foreach ($parentServices as $service)
                                            <ul>
                                                <li>
                                                    @if ($service->id != $services->id)
                                                        <a href="javascript:void(0)" id="category" data_item="{{ $service->id }}" class="service_items @if ($service->id == $services->parent_id) {{ 'active' }} @endif">{{ $service->service }}</a>
                                                    @endif
                                                    @if (count($service->subservicesArray($service->id)) > 0)
                                                        @include('admin.services.subServiceListEdit', ['subservices' => $service->subservicesArray($service->id)])
                                                    @endif
                                                </li>
                                            </ul>
                                        @endforeach
                                        <input type="hidden" name="selected_service" value="{{ $services->parent_id }}" id="selected_service">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <strong>{{ __('messages.service') }} {{ __('messages.available countries') }}</strong>
                                    <div class="login-input rel_selected">
                                        <div id="country_selected">
                                        </div>
                                        <div id="country_selected_list" style="color: #17a2b8">
                                            @if ((!empty(old('service_countries')) && old('service_countries.0') != '') || $errors->any())
                                                @if (!empty(old('service_countries')))
                                                    @foreach (old('service_countries') as $key => $value)
                                                        <div class="country_added">{{ old('service_countries_name.' . $key) }}<input type="hidden" name="service_countries_name[]" value="{{ old('service_countries_name.' . $key) }}" /><input type="hidden" name="service_countries[]" value="{{ $value }}" /><a href="javascript:void(0)" class="remove_country remove_country_db"><i class="fas fa-times-circle"></i></a></div>
                                                    @endforeach
                                                @endif
                                            @elseif (isset($arr_countries))
                                                @foreach ($arr_countries as $country_row)
                                                    <div id="religion_selected">
                                                        <div class="country_added">{{ $country_row->name }}<input type="hidden" name="service_countries_name[]" value="{{ $country_row->name }}" /><input type="hidden" name="service_countries[]" value="{{ $country_row->id }}"><a href="javascript:void(0)" class="remove_country remove_country_db" data-id="{{ $country_row->id }}"><i class="fas fa-times-circle"></i></a></div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                        <input name="" class="form-control" id="countries" placeholder="Service Countries" type="text" value="{{ old('service_countries') }}" autocomplete="off" />
                                    </div>
                                    <div id="setting_country">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="back_btn w-100">
                            <button type="submit" value="{{ __('messages.update') }}" class="btn btn-primary">Update</button>
                            <a class="btn btn-primary" href="{{ route('admin.services') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i> Back</a>
                        </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    </div>
    </div>
@endsection
@section('footer_scripts')
    <script type="text/javascript">
        $(document).on("click", ".service_items", function() {
            var service = $(this).attr("data_item");
            $('.active').removeClass("active");
            $(this).addClass("active");
            $("#selected_service").val(service);
        });

        function removeImage(cid) {
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
                    url: "{{ route('services.removeImage') }}", //Please see the note at the end of the post**
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

        function select_country(id, name) {
            var allow = true;
            country_array = $("input[name='service_countries[]']")
                .map(function() {
                    return $(this).val();
                }).get();
            $('.country_added').each(function() {
                if (name == $(this).text()) {
                    allow = false;
                }
            });
            if (!allow) {
                alert('This Country already exist. Please choose other.');
            } else {
                country_array.push(name);
                var html_content = '<div class="country_added">' + name + '<input type="hidden" name="service_countries_name[]" value="' + name + '" /><input type="hidden" name="service_countries[]" value="' + id + '" /><a href="javascript:void(0)" class="remove_country remove_country_db" data-id="' + id + '"><i class="fas fa-times-circle"></i></a></div>';
                $("#country_selected_list").append(html_content);
                $('#country_selected').hide();
                // SaveDistrict(name);
            }
        }
        $(document).ready(function() {
            $(document).on('keyup', '#countries', function() {
                var query = $(this).val();
                if (query != '') {
                    $.ajax({
                        url: "{{ route('countries.search') }}",
                        type: "GET",
                        data: {
                            'country': query
                        },
                        success: function(data) {
                            $('#setting_country').show();
                            $('#setting_country').html(data);
                        }
                    })
                } else {
                    $('#setting_country').hide();
                }
            });
            $(document).on("click", ".remove_country", function() {
                $(this).parents('.country_added').remove();
            });
        });
    </script>
    <style>
        .active {
            color: #FF0000 !important;
        }
    </style>
@endsection
