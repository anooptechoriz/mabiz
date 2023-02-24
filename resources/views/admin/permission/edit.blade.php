@extends('layouts.admin.index')

@section('content')
    <div class="container-fluid">
        <div class="col-md-8 m-auto">
            <div class="card shadow mb-4 mt-5">
                <div class="card-body">
                    <h1 class="h4 mb-2 text-gray-800">{{ __('messages.edit permission') }}</h1>
                    {{-- <a class="btn btn-primary btn-circle btn-lg" href="{{ route('admin.permissions') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i></a> --}}
                    <form class="form-horizontal" method="POST" action="{{ route('permissions.edit', $permission->id) }}" enctype="multipart/form-data">
                        @csrf
                        @include('admin.permission.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
