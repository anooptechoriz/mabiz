@extends('layouts.admin.index')

@section('content')
    <div class="container-fluid">
        <div class="col-md-8 m-auto outer-section">
            <div class="card shadow mb-4 my-5">
                <div class="card-body">
                    <h1 class="h4 mb-4 text-gray-800">{{ __('Service Man') }}</h1>
                    {{-- <div class="row">
                        <div class="container"> --}}
                    <div class="wrapper row">
                        <div class="preview col-md-5">
                            <div class="preview-pic tab-content">
                                @if ($servicemanDetails->profile_pic != '')
                                    <img src="{{ asset('/assets/uploads/profile/') }}/{{ $servicemanDetails->profile_pic }}" alt="Profile Image" class="tab-pane active image_display" id="product_pic" />
                                @else
                                    <img src="{{ asset('img/no-image.jpg') }}" alt="profile image" />
                                @endif
                            </div>
                            <div class="details pl-0">
                                <div class="pr-contact-info custom-cont-page-info">
                                    <h5><span>Contact Information</span></h5>
                                    <h6><span>{{ __('messages.email') }}:</span> {{ $servicemanDetails->email }}</h6>
                                    <h6><span>{{ __('messages.phone') }}:</span> {{ $servicemanDetails->phone }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="details">
                                <h3 class="product-title">{{ $servicemanDetails->firstname }}</h3>
                                <div class="pr-contact-info">
                                    <h5><span>Basic Information</span></h5>
                                    <h6><span>{{ __('messages.date_of_birth') }}:</span> {{ $servicemanDetails->dob }}</h6>
                                    <h6><span>{{ __('messages.gender') }}:</span> {{ $servicemanDetails->gender }}</h6>
                                    <h6><span>{{ __('messages.country') }}:</span> {{ $servicemanDetails->country }}</h6>
                                    <h6><span>{{ __('State') }}:</span> {{ $servicemanDetails->state }}</h6>
                                    <h6><span>{{ __('messages.region') }}:</span> {{ $servicemanDetails->region }}</h6>
                                    <h6><span>{{ __('Civil card no') }}:</span> {{ $servicemanDetails->civil_card_no }}</h6>
                                    <h6><span>{{ __('About') }}:</span> {{ $servicemanDetails->about }}</h6>
                                    <h6><span>{{ __('Transport') }}:</span> {{ $servicemanDetails->transport }}</h6>
                                </div>
                                <div class="pr-contact-info mt-4">
                                    <h5><span>Work Information</span></h5>
                                    <h6><span>{{ __('Profile') }}:</span> {{ $servicemanDetails->profile }}</h6>
                                    <h6><span>{{ __('messages.service') }}:</span> {{ $servicemanDetails->service }}</h6>
                                    <h6><span>{{ __('Expiry Date') }}:</span> {{ $servicemanDetails->expiry_date }}</h6>
                                    <h6><span>{{ __('Coupon Code') }}:</span> {{ $servicemanDetails->coupon_code }}</h6>
                                    <h6><span>{{ __('messages.status') }}:</span> {{ $servicemanDetails->status }}</h6>
                                    <h6><span>{{ __('messages.registered on') }}:</span> {{ $servicemanDetails->created_at }}</span></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Service</th>
                                    <th>User</th>
                                    <th>Package</th>
                                    <th>Subscription date</th>
                                    <th>Expiry date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            @php $SL = 1 @endphp
                            @forelse ($services as $rows)
                                @php $count=0; @endphp
                                @php $count++; @endphp
                                <tr>
                                    <td>{{ $SL++ }}</td>
                                    <td>{{ $rows->service }}</td>
                                    <td>{{ $rows->user }}</td>
                                    <td>{{ $rows->package }}</td>
                                    <td>{{ $rows->subscription_date }}</td>
                                    <td>{{ $rows->expiry_date }}</td>
                                    <td>{{ $rows->status }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-danger text-center">No result found</td>
                                </tr>
                            @endforelse
                        </table>
                    </div>
                    <div class="w-100 back_btn justify-content-end mt-4">
                        <a class="btn btn-primary" href="{{ route('admin.service_man') }}" title="{{ __('messages.Back to customers') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i> Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
