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
                    <h1 class="h4 mb-2 text-gray-800">{{ __('messages.services') }}</h1>
                    <div class="table-responsive">
                        {{-- <div class="card-body"> --}}
                        <form action="" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                {{-- <div class="col-xs-8 col-sm-8 col-md-8">
                                    <div class="form-group">
                                        <strong>{{ __('messages.service') }}</strong>
                                        <input type="text" name="service" value="{{ old('service') }}" class="form-control">
                                    </div>
                                </div> --}}
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <strong>{{ __('messages.name in english') }}<span class="text-danger">*</span></strong>
                                        <input type='hidden' name="language" value="1">
                                        <input type="text" name="service_name_english" class="form-control"required>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <strong>{{ __('messages.name in arabic') }}<span class="text-danger">*</span></strong>
                                        <input type='hidden' name="language" value="2">
                                        <input type="text" name="service_name_arabic" class="form-control"required>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <strong>{{ __('messages.name in hindi') }}<span class="text-danger">*</span></strong>
                                        <input type='hidden' name="language" value="3">
                                        <input type="text" name="service_name_hindi" class="form-control"required>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group mb-0">
                                        <strong>{{ __('messages.service') }} {{ __('messages.available countries') }}</strong>
                                        <div id="country_selected">
                                        </div>
                                    </div>
                                    <div id="country_selected_list">
                                        @if (!empty(old('service_countries')) && old('service_countries.0') != '')
                                            @foreach (old('service_countries') as $key => $value)
                                                <div class="country_added">{{ old('service_countries.' . $key) }}
                                                    <input type="hidden" name="service_countries[]" value="{{ old('service_countries.' . $key) }}" />
                                                    <a href="javascript:void(0)" class="remove_country remove_country_db" data-id="{{ $value }}"><i class="fas fa-times-circle"></i></a>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <input name="" class="form-control" id="countries" placeholder="Choose country" type="text" value="{{ old('service_countries') }}" autocomplete="off" />
                                    <div class="form-group" id="setting_country">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <strong> {{ __('messages.image') }} </strong><span class="text-danger d-block my-1">(Max Image dimension width:50 x height:50 pixel, Max: 1MB)</span>
                                        <input type="file" name="image" value="" class="course-img">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <strong>{{ __('messages.parent service') }}</strong>
                                        <div class="card-body category-list-block">
                                            @php
                                                $old_sub_service = !empty(old('selected_service')) && old('selected_service') != '' ? old('selected_service') : 0;
                                            @endphp
                                            <li><a href="javascript:void(0)" id="service" data_item="0" class="service_items">Parent</a></li>
                                            @foreach ($parentservices as $row)
                                                <ul>
                                                    <li>
                                                        <a href="javascript:void(0)" id="service" data_item="{{ $row->id }}" class="service_items @if ($row->id == $old_sub_service) {{ 'active' }} @endif">{{ ucfirst($row->service) }}</a>
                                                        @if (count($row->subservicesArray($row->id)) > 0)
                                                            @include('admin.services.subServiceList', ['subservices' => $row->subservicesArray($row->id), 'old_sub_service' => $old_sub_service])
                                                        @endif
                                                    </li>
                                                </ul>
                                            @endforeach
                                            <input type="hidden" name="selected_service" value="" id="selected_service">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 back_btn">
                                    <button type="submit" value="{{ __('messages.submit') }}" class="btn btn-primary">Submit</button>
                                    <a class="btn btn-primary" href="{{ route('admin.services') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i> Back</a>
                                </div>
                            </div>
                        </form>
                        {{-- </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<style>
    .active {
        color: #FF0000 !important;
    }
</style>
@section('footer_scripts')
    <script type="text/javascript">
        $(document).on("click", ".service_items", function() {
            var service = $(this).attr("data_item");
            $('.active').removeClass("active");
            $(this).addClass("active");
            $("#selected_service").val(service);
        });
        var country_array = [];

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
@endsection
