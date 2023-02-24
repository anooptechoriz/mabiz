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
        <h1 class="h3 mb-2 text-gray-800 mt-5 mb-4">{{ __('messages.roles') }}</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <a href="{{ route('roles.create') }}" class="btn add-new" title="Add">Add New</a></a>
                    <div class="float-right">
                        <a href="{{ route('admin.list') }}" class="btn btn-primary">{{ __('messages.administrators') }}</a>
                        <a href="{{ route('admin.roles') }}" class="btn btn-primary">{{ __('messages.roles') }}</a>
                        <a href="{{ route('admin.permissions') }}" class="btn btn-primary">{{ __('messages.permissions') }}</a>
                    </div>
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('messages.role') }}</th>
                                <th>{{ __('messages.created at') }}</th>
                                <th class="action-icon">{{ __('messages.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $count = 0; @endphp
                            @if ($roles->isNotEmpty())
                                @foreach ($roles as $rows)
                                    <tr>
                                        <td>{{ ($roles->currentpage() - 1) * $roles->perpage() + $count + 1 }}</td>
                                        <td>{{ ucfirst($rows->name) }}</td>
                                        <td>{{ $rows->created_at }}</td>
                                        <td>
                                            <form class="form-horizontal" method="POST" action="{{ route('roles.delete', $rows->id) }}">
                                                <a href="{{ route('roles.edit', $rows->id) }}" class="btn btn-warning" title="Edit"><i class="fas fa-pen"></i></a>
                                                {{ csrf_field() }}
                                                <button type="submit" class="btn btn-danger" title="Delete" onclick="return confirm('Do you really want to delete?')"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    @php  $count++;  @endphp
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center text-danger">No records found</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    {{ $roles->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@endsection
