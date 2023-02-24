@extends('layouts.admin.index')

@section('content')
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>{{ __('messages.Whoops') }}!</strong> {{ __('messages.There were some problems with your input') }}.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (session('success'))
        <div class="alert alert-success">
            <ul>
                <li>{{ session('success') }}</li>
            </ul>
        </div>
    @endif
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800 mt-5 mb-4">{{ __('messages.permissions') }}</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <a href="{{ route('permissions.create') }}" class="btn add-new" title="Add">Add New</a>
                    <div class="float-right">
                        <a href="{{ route('admin.list') }}" class="btn btn-primary">{{ __('messages.administrators') }}</a>
                        <a href="{{ route('admin.roles') }}" class="btn btn-primary">{{ __('messages.roles') }}</a>
                        <a href="{{ route('admin.permissions') }}" class="btn btn-primary">{{ __('messages.permissions') }}</a>
                    </div>
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('messages.permissions') }}</th>
                                <th>{{ __('messages.slug') }}</th>
                                <th>{{ __('messages.created at') }}</th>
                                <th class="action-icon">{{ __('messages.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $count = 0; @endphp
                            @if ($permissions->count() != 0)
                                @foreach ($permissions as $rows)
                                    <tr>
                                        <td>{{ ($permissions->currentpage() - 1) * $permissions->perpage() + $count + 1 }}</td>
                                        <td>{{ $rows->name }}</td>
                                        <td>{{ $rows->slug }}</td>
                                        <td>{{ $rows->created_at }}</td>
                                        <td>
                                            <form class="form-horizontal" method="POST" action="{{ route('permissions.delete', $rows->id) }}">
                                                @csrf
                                                <a href="{{ route('permissions.edit', ['id' => $rows->id]) }}" class="btn btn-warning" title="Edit"><i class="fas fa-pen"></i></a>
                                                <button type="submit" class="btn btn-danger" title="Delete" onclick="return confirm('Do you really want to delete?')"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    @php $count++; @endphp
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-danger text-center">No records found</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    {{ $permissions->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@endsection
