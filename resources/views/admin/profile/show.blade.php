@extends('layouts.admin.index')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">{{ __('messages.customers') }}</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <a class="btn btn-primary btn-circle btn-lg" href="{{ route('admin.customers') }}" title="{{ __('messages.Back to customers') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>
                <div class="row">
                    <div class="container">
                        <div class="wrapper row">
                            <div class="preview col-md-6">
                                <div class="preview-pic tab-content">
                                    @if ($CustomerDetails->profile_pic != '')
                                        <img src="{{ asset('/assets/uploads/profile/') }}/{{ $CustomerDetails->profile_pic }}" alt="Profile Image" width="150px" class="tab-pane active image_display" id="product_pic" />
                                    @else
                                        <img src="{{ asset('img/no-image.jpg') }}" alt="profile image" />
                                    @endif
                                </div>
                            </div>
                            <div class="details col-md-6">
                                <h3 class="product-title">{{ $CustomerDetails->name }}</h3>
                                <div><span>{{ __('messages.gender') }}: {{ $CustomerDetails->gender }}</span></div>
                                <div><span>{{ __('messages.date_of_birth') }}: {{ $CustomerDetails->dob }}</span></div>
                                <div><span>{{ __('messages.country') }}: {{ $CustomerDetails->country_name }}</span></div>
                                <div><span>{{ __('messages.phone') }}: {{ $CustomerDetails->phone }}</span></div>
                                <div><span>{{ __('messages.email') }}: {{ $CustomerDetails->email }}</span></div>
                                <div><span>{{ __('messages.region') }}: {{ $CustomerDetails->region }}</span></div>
                                <div><span>{{ __('messages.status') }}: {{ $CustomerDetails->status }}</span></div>
                                <div><span>{{ __('messages.registered_on') }}: {{ $CustomerDetails->created_at }}</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
