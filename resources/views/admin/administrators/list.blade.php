@extends('layouts.admin.index')

@section('content')
    @if (count($errors) > 0)
        <div class="alert alert-danger">
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
        <h1 class="h3 mb-2 text-gray-800 mt-5 mb-4">{{ __('messages.administrators') }}</h1>
        <div class="card shadow mb-4">
            <form action="" method="get" id="order_filter">
                <div class="form-group row">
                    <div class="col-xs-2 col-sm-2 col-md-2 form-group">

                        <input type='hidden' id="sort_by" class="form-control"name="sort_by" value="{{ request()->get('sort_by') }}">
                        <input type='hidden' id="sort_order" class="form-control"name="sort_order" value="{{ request()->get('sort_order') }}">
                    </div>
                    <input type='submit' id="search_btn" class="btn btn-info" style="display: none">
                </div>
            </form>
            <div class="card-body">
                <a href="{{ route('admin.create') }}" class="btn add-new" title="Add">Add New</a>
                {{-- <i class="fas fa-plus"></i> --}}
                <div class="float-right">
                    <a href="{{ route('admin.list') }}" class="btn btn-primary">{{ __('messages.administrators') }}</a>
                    <a href="{{ route('admin.roles') }}" class="btn btn-primary">{{ __('messages.roles') }}</a>
                    <a href="{{ route('admin.permissions') }}" class="btn btn-primary">{{ __('messages.permissions') }}</a>
                </div>
                <div class="table-responsive mt-4">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="text-secondary text-xxs font-weight-bolder opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter">#</a>
                                </th>
                                <th class="text-secondary text-xxs opacity-7 ">
                                    <a href="javascript:void(0)" class="dataTable-sorter">{{ __('messages.picture') }}</a>
                                </th>
                                <th class="text-secondary text-xxs opacity-7 ">
                                    <a href="javascript:void(0)" class="dataTable-sorter asc" data-name="name">{{ __('messages.name') }}</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="email">{{ __('messages.email') }}</a>
                                    <span class="float-right" text-sm> <i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="phone">{{ __('messages.phone') }}</a>
                                    <span class="float-right" text-sm> <i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="job_title">{{ __('Job title') }}</a>
                                    <span class="float-right" text-sm> <i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="role_id">{{ __('messages.role') }}
                                        <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="created_at">Created Time</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs font-weight-bolder opacity-7 action-icon">Action</th>
                            </tr>
                        </thead>
                        {{-- <tbody> --}}
                        @php $count=0; @endphp
                        @forelse ($admin as $rows)
                            @php $count++; @endphp
                            <tr>
                                <td>{{ ($admin->currentpage() - 1) * $admin->perpage() + $count }}</td>
                                <td class="list-table-image">
                                    @if ($rows->profile_pic != '')
                                        <img src="{{ asset('assets/uploads/admin_profile/') }}/{{ $rows->profile_pic }}" alt="{{ $rows->profile_pic }}" />
                                    @else
                                        <img src="{{ asset('img/no-image.jpg') }}" alt="profile image" />
                                    @endif
                                </td>
                                <td>{{ $rows->name }}</td>
                                <td>{{ $rows->email }}</td>
                                <td>{{ $rows->phone }}</td>
                                <td>{{ $rows->job_title }}</td>
                                <td>{{ ucfirst($rows->role) }}</td>
                                <td>{{ $rows->created_at }}</td>
                                <td style="width: 200px;">
                                    <form class="form-horizontal" method="POST" action="{{ route('admin.delete', $rows->id) }}">
                                        <a href="{{ route('admin.edit', ['id' => $rows->id]) }}" class="btn btn-warning btn-md" title="Edit"><i class="fas fa-pen"></i></a>
                                        <a href="{{ route('admin.show', ['id' => $rows->id]) }}" class="btn btn-info btn-md" title="View"><i class="fas fa-eye"></i></i></a>
                                        {{ csrf_field() }}
                                        <button type="submit" class="btn btn-danger btn-md" title="Delete" onclick="return confirm('Do you really want to delete?')"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-danger text-center">No result found</td>
                            </tr>
                        @endforelse
                    </table>
                    {{ $admin->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer_scripts')
    <script>
        $(".dataTable-sorter").on("click", function() {
            var o = $('#sort_order').val() == 'asc' ? 'desc' : 'asc';
            $('#sort_by').val($(this).attr('data-name'));
            $('#sort_order').val(o);
            $("#search_btn").trigger("click");
        });
    </script>
@endsection
