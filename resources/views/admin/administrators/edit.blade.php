@extends('layouts.admin.index')

@section('content')
    @if (session('error'))
        <div class="alert alert-danger">
            <ul>
                <li>{{ session('error') }}</li>
            </ul>
        </div>
    @endif
    <div class="container-fluid">
        <div class="col-md-8 m-auto outer-section">
            <div class="card shadow mb-4 mt-5">
                <div class="card-body">
                    <div class="hed-text">
                        <h1 class="h4 mb-2 text-gray-800">{{ __('messages.edit administrator') }}</h1>
                        <div class="admin-list-btn">
                            <a href="{{ route('admin.list') }}" class="btn btn-primary">{{ __('messages.administrators') }}</a>
                            <a href="{{ route('admin.roles') }}" class="btn btn-primary">{{ __('messages.roles') }}</a>
                            <a href="{{ route('admin.permissions') }}" class="btn btn-primary">{{ __('messages.permissions') }}</a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <form class="form-horizontal" method="POST" action="{{ route('admin.edit', $admin->id) }}" enctype="multipart/form-data">
                            @include('admin.administrators.editform2')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
