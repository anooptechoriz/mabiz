@extends('layouts.admin.index')

@section('content')
    <div class="container-fluid">
        <div class="col-md-8 m-auto">
            <div class="card shadow mt-5 mb-5">
                <div class="card-body">
                    <div class="hed-text mb-3">
                        <h1 class="h4 mb-2 text-gray-800">Orders</h1>
                    </div>
                    <div class="wrapper row">
                        <div class="col-md-12">
                            <div class="details pl-0">
                                <div class="pr-contact-info">
                                    <h5 class="mt-0"><span>Basic Information</span></h5>
                                    <h6><span>Service:</span>{{ $order->service }}</h6>
                                    <h6><span>User:</span>{{ $order->user }}</h6>
                                    <h6><span>Package:</span>{{ $order->package }}</h6>
                                </div>
                                <div class="pr-contact-info">
                                    <h5><span>Work Information</span></h5>
                                    <h6><span>First Name:</span>{{ $order->firstname }}</h6>
                                    <h6><span>Last Name:</span>{{ $order->lastname }}</h6>
                                    <h6><span>Civil Card:</span>{{ $order->civil_card_no }}</h6>
                                    <h6><span>DOB:</span>{{ $order->dob }}</h6>
                                    <h6><span>Gender:</span>{{ $order->gender }}</h6>
                                    <h6><span>Country:</span>{{ $order->country }}</h6>
                                    <h6><span>State:</span>{{ $order->state }}</h6>
                                    <h6><span>Region:</span>{{ $order->region }}</h6>
                                    <h6><span>Address:</span>{{ $order->address }}</h6>
                                    <h6><span>Coupon Code:</span>{{ $order->coupon_code }}</h6>
                                    <h6><span>Total amount:</span>{{ $order->total_amount }}</h6>
                                    <h6><span>Tax amount:</span>{{ $order->total_tax_amount }}</h6>
                                    <h6><span>Coupon Discount:</span>{{ $order->coupon_discount }}</h6>
                                    <h6><span>Grand total:</span>{{ $order->grand_total }}</h6>
                                    <h6><span>Payment status:</span>{{ $order->payment_status }}</h6>
                                    <h6><span>Created at:</span>{{ $order->created_at }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 back_btn justify-content-end">
                        <a class="btn btn-primary" href="{{ route('admin.order') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i> Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
