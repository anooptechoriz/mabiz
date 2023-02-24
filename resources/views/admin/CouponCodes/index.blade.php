@extends('layouts.admin.index')

@section('content')
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>{{ _('messages.Whoops') }}!</strong> {{ _('messages.There were some problems with your input') }}.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="alert alert-danger" id="myElem" style="display: none">
    </div>
    @if (session('success'))
        <div class="alert alert-success">
            <ul>
                <li>{{ session('success') }}</li>
            </ul>
        </div>
    @endif
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800 mt-5 mb-4">Coupon Codes</h1>
        <div class="card shadow mb-5">
            <div class="card-body">
                <a href="{{ route('coupons.create') }}" class="btn add-new">Add New</a>
                <div class="float-right">
                    <form action="" method="get" id="order_filter">
                        <div class="d-flex">
                            <div class="form-group mb-0">
                                <input type='text' class="form-control" placeholder="Search Keyword" name="search_keyword" value="{{ request()->get('search_keyword') }}">
                                <input type='hidden' id="sort_by" class="form-control"name="sort_by" value="{{ request()->get('sort_by') }}">
                                <input type='hidden' id="sort_order" class="form-control"name="sort_order" value="{{ request()->get('sort_order') }}">
                            </div>
                            <button type='submit' id="search_btn" class="btn btn-primary ml-2">{{ __('messages.search') }}</button>
                        </div>
                    </form>
                </div>
                <div class="table-responsive">
                    {{-- <div class="float-right">
                    </div>
                    <div class="card-body"> --}}
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="text-secondary text-xxs font-weight-bolder opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter">#</a>
                                </th>
                                <th class="text-secondary text-xxs opacity-7" style="width: 130px;">
                                    <a href="javascript:void(0)" class="dataTable-sorter asc" data-name="code">Code Name</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="description">Description</a>
                                    <span class="float-right" text-sm> <i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="discount">Discount Percentage</a>
                                    <span class="float-right" text-sm> <i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7" style="width: 130px;">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="validity">Validity</a>
                                    <span class="float-right" text-sm> <i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="conditions">Terms and Conditions</a>
                                    <span class="float-right" text-sm> <i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs font-weight-bolder opacity-7 action-icon">Action</th>
                            </tr>
                        </thead>
                        {{-- @if (!empty($couponcodes))
                            @php $count=0; @endphp
                            @foreach ($couponcodes as $couponcodes_row)
                                @php $count++; @endphp --}}
                        @php $count=0; @endphp
                        @forelse ($couponcodes as $couponcodes_row)
                            @php $count++; @endphp
                            <tr>
                                <td>{{ ($couponcodes->currentpage() - 1) * $couponcodes->perpage() + $count }}</td>
                                <td>{{ $couponcodes_row->code }}</td>
                                <td>{{ $couponcodes_row->description }}</td>
                                <td>{{ $couponcodes_row->discount }} <i class="fas fa-percent"></i></td>
                                <td>{{ $couponcodes_row->validity }}</td>
                                <td>{{ $couponcodes_row->conditions }}</td>
                                <td>
                                    <form action="{{ route('coupons.delete', $couponcodes_row->id) }}" method="POST">
                                        @csrf
                                        <a href="{{ route('coupons.edit', $couponcodes_row->id) }}" title="Edit" class="btn btn-warning"><i class="fas fa-pen"></i></a>
                                        <button type="submit" class="btn btn-danger" title="Delete"onclick="return confirm('Do you really want to delete?')"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-danger text-center">No result found</td>
                            </tr>
                        @endforelse
                    </table>
                    {{ $couponcodes->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer_scripts')
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

    <script>
        $(".dataTable-sorter").on("click", function() {
            var o = $('#sort_order').val() == 'asc' ? 'desc' : 'asc';
            $('#sort_by').val($(this).attr('data-name'));
            $('#sort_order').val(o);
            $("#search_btn").trigger("click");
        });
    </script>
@endsection
