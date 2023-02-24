@extends('layouts.admin.index')

@section('content')
    <div class="modal" id="view_licence"role="dialog">
        <div class="modal-dialog">
            <div class="modal-content"style="width: 800px;;height: 750px;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- Modal Header -->
                <div class="modal-body">
                    <embed src='{{ asset('assets/uploads/admin_licence/') }}/{{ $admin->licence }}'#toolbar=0 width="100%"height="550px">
                </div>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="col-md-8 m-auto">
            <div class="card shadow mt-5 mb-5">
                <div class="card-body">
                    <div class="hed-text mb-4">
                        <h1 class="h4 mb-2 text-gray-800">Administrator</h1>
                        <div class="float-right">
                            <a href="{{ route('admin.list') }}" class="btn btn-primary">{{ __('messages.administrators') }}</a>
                            <a href="{{ route('admin.roles') }}" class="btn btn-primary">{{ __('messages.roles') }}</a>
                            <a href="{{ route('admin.permissions') }}" class="btn btn-primary">{{ __('messages.permissions') }}</a>
                        </div>
                    </div>
                    <div class="wrapper row">
                        <div class="preview col-md-5">
                            <div class="preview-pic tab-content">
                                @if ($admin->profile_pic != '')
                                    {{-- <strong>Profile Picture:</strong><br> --}}
                                    <img src="{{ asset('assets/uploads/admin_profile/') }}/{{ $admin->profile_pic }}" alt="{{ $admin->profile_pic }}" width="120px" />
                                @endif
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="details">
                                <h3 class="product-title">{{ $admin->name }}</h3>
                                <div class="pr-contact-info">
                                    <h5><span>Basic Information</span></h5>
                                    <h6><span>Phone:</span>{{ $admin->phone }}</h6>
                                    <h6><span>Email:</span>{{ $admin->email }}</h6>
                                </div>
                                <div class="pr-contact-info">
                                    <h5><span>Work Information</span></h5>
                                    <h6><span>Job title:</span>{{ $admin->job_title }}</h6>
                                    <h6><span>Bio:</span>{{ $admin->bio }}</h6>
                                    <h6><span>Role:</span>{{ $admin->role }}</h6>
                                    <h6><span>Created at:</span>{{ $admin->created_at }}</h6>
                                    <h6><span></span></h6>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        @if ($admin->licence != '')
                                            <a type="button" class='btn btn-success'data-toggle="modal" data-target="#view_licence"><i class='fa fa-eye'></i> View Licence</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 back_btn justify-content-end">
                        <a class="btn btn-primary" href="{{ route('admin.list') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i> Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
