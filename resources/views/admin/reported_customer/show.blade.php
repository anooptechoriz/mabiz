@extends('layouts.admin.index')

@section('content')
    <div class="container-fluid">
        <div class="col-md-8 m-auto outer-section">
            <div class="card shadow my-5">
                <div class="card-body">
                    <h1 class="h4 mb-4 text-gray-800">{{ __('messages.customers') }}</h1>
                    {{-- <div class="row">
                        <div class="container"> --}}
                    <div class="wrapper row">
                        <div class=" col-md-5 preview">
                            <div class="preview-pic tab-content">
                                @if ($CustomerDetails->profile_pic != '')
                                    <img src="{{ asset('/assets/uploads/profile/') }}/{{ $CustomerDetails->profile_pic }}" alt="Profile Image" width="150px" class="tab-pane active image_display" id="product_pic" />
                                @else
                                    <img src="{{ asset('img/no-image.jpg') }}" alt="profile image" />
                                @endif
                            </div>
                            <div class="details pl-0 custom-cont-page-info">
                                <div class="pr-contact-info">
                                    <h5><span>Contact Information</span></h5>
                                    <h6><span>{{ __('messages.phone') }}:</span> {{ $CustomerDetails->phone }}</h6>
                                    <h6><span>{{ __('messages.email') }}:</span> {{ $CustomerDetails->email }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="details">
                                <h3 class="product-title">{{ $CustomerDetails->firstname }}</h3>
                                <div class="pr-contact-info">
                                    <h5><span>Basic Information</span></h5>
                                    <h6><span>{{ __('messages.gender') }}:</span> {{ $CustomerDetails->gender }}</h6>
                                    <h6><span>{{ __('messages.date_of_birth') }}:</span> {{ $CustomerDetails->dob }}</h6>
                                    <h6><span>{{ __('messages.region') }}:</span> {{ $CustomerDetails->region }}</h6>
                                    <h6><span>{{ __('State') }}:</span> {{ $CustomerDetails->state }}</h6>
                                    <h6><span>{{ __('messages.country') }}:</span> {{ $CustomerDetails->country }}</h6>
                                </div>
                                <div class="pr-contact-info">
                                    <h5><span>Work Information</span></h5>
                                    <h6><span>{{ __('About') }}:</span> {{ $CustomerDetails->about }}</h6>
                                    <h6><span>{{ __('messages.status') }}:</span> {{ $CustomerDetails->status }}</h6>
                                    <h6><span>{{ __('messages.registered on') }}:</span> {{ $CustomerDetails->created_at }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="w-100 back_btn justify-content-end mt-4">
                            <a class="btn btn-primary" href="{{ route('admin.customers') }}" title="{{ __('messages.Back to customers') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i> Back</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
