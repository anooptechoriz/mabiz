@extends('layouts.admin.index')
@section('content')
    <div class="container-fluid">
        <div class="col-md-8 m-auto">
            <div class="card shadow mb-4 mt-5">
                <div class="card-body">
                    <div class="table-responsive">
                        <div class="hed-text">
                            <h1 class="h4 mb-2 text-gray-800">{{ __('messages.add administrator') }}</h1>
                            <div class="admin-list-btn">
                                <a href="{{ route('admin.list') }}" class="btn btn-primary">{{ __('messages.administrators') }}</a>
                                <a href="{{ route('admin.roles') }}" class="btn btn-primary">{{ __('messages.roles') }}</a>
                                <a href="{{ route('admin.permissions') }}" class="btn btn-primary">{{ __('messages.permissions') }}</a>
                            </div>
                        </div>
                        <form class="form-horizontal mt-4" method="POST" action="" enctype="multipart/form-data">
                            @include('admin.administrators.form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
