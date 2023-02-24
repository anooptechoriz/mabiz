@extends('layouts.admin.index')

@section('content')
    <div class="container-fluid">
        <div class="col-md-8 m-auto">
            <div class="card shadow mb-4 mt-5">
                <div class="card-body">
                    <div class="hed-text mb-2">
                        <h1 class="h4 mb-2 text-gray-800">{{ __('messages.edit role') }}</h1>
                        <div class="float-right">
                            <a href="{{ route('admin.list') }}" class="btn btn-primary">{{ __('messages.administrators') }}</a>
                            <a href="{{ route('admin.roles') }}" class="btn btn-primary">{{ __('messages.roles') }}</a>
                            <a href="{{ route('admin.permissions') }}" class="btn btn-primary">{{ __('messages.permissions') }}</a>
                        </div>
                    </div>
                    {{-- <div class="pull-right">
                    <a class="btn btn-primary btn-circle btn-lg" href="{{ route('admin.roles') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>
                </div> --}}
                    <form class="form-horizontal" method="POST" action="{{ route('roles.edit', $role->id) }}">
                        @include('admin.role.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
