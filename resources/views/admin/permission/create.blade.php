@extends('layouts.admin.index')
@section('content')
    <div class="container-fluid">
        <div class="col-md-8 m-auto">
            <div class="card shadow mb-4 mt-5">
                <div class="card-body">
                    <h1 class="h4 mb-2 text-gray-800">{{ __('messages.add permission') }}</h1>
                    <form class="form-horizontal" method="POST" action="{{ route('permissions.create') }}">
                        @csrf
                        @include('admin.permission.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
