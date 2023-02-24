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
        <h1 class="h3 mb-2 text-gray-800 mt-5 mb-4">{{ __('messages.customers') }}</h1>
        <div class="card shadow">
            <div class="card-body">
                <form action="" method="get" id="order_filter">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="form-group">
                            {{-- <div class="col-xs-2 col-sm-2 col-md-2" style="margin-bottom: 10px;"> --}}
                            <select name="filter_Status" class="form-control pr-5" id="filter_Status">
                                <option value="">--Choose Status--</option>
                                <option value='active' {{ request()->get('filter_Status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value='disabled' {{ request()->get('filter_Status') == 'disabled' ? 'selected' : '' }}>Disabled</option>
                                <option value='deleted' {{ request()->get('filter_Status') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                            </select>
                            {{-- </div> --}}
                        </div>
                        <div class="d-flex">
                            <div class="form-group mb-0">
                                {{-- <input type='text' class="form-control" placeholder="Search Keyword" name="search_keyword" value="{{ request()->get('search_keyword') }}"> --}}
                                <input type='hidden' id="sort_by" class="form-control"name="sort_by" value="{{ request()->get('sort_by') }}">
                                <input type='hidden' id="sort_order" class="form-control"name="sort_order" value="{{ request()->get('sort_order') }}">
                            </div>
                            {{-- <button type='submit' id="search_btn" class="btn btn-primary ml-2">{{ __('messages.search') }}</button> --}}
                        </div>
                    </div>
                    <div class="col-md-12 export-btn text-end">
                        <button type="submit" class="btn btn-primary" name="export" value="save">Export</button>
                    </div>
                </form>
                {{-- <div class="card-body"> --}}
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="text-secondary text-xxs font-weight-bolder opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter">#</a>
                                </th>

                                <th class="text-secondary text-xxs opacity-7 ">
                                    <a href="javascript:void(0)" class="dataTable-sorter asc" data-name="firstname">Name</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="email">{{ __('messages.email') }}</a>
                                    <span class="float-right" text-sm> <i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="gender">{{ __('messages.gender') }}</a>
                                    <span class="float-right" text-sm> <i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="phone">Phone</a>
                                    <span class="float-right" text-sm> <i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="dob">Date of birth</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="country_id">{{ __('messages.country') }}</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="about">About</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="state">State</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="region">Region</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="status">Status</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="created_at">Created Time</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th>
                            </tr>
                        </thead>
                        @php $count=0; @endphp
                        @forelse ($customers as $rows)
                            @php $count++; @endphp
                                <tr>
                                    <td>{{ ($customers->currentpage() - 1) * $customers->perpage() + $count }}</td>

                                    <td>{{ $rows->firstname }}</td>
                                    <td>{{ $rows->email }}</td>
                                    <td>{{ $rows->gender }}</td>
                                    <td>{{ $rows->phone }}</td>
                                    <td>{{ date('d-m-Y', strtotime($rows->dob)) }}</td>
                                    <td>
                                        @foreach ($rows->country_details as $value)
                                            {{ $value->name }}{{ $loop->last ? '' : ',' }}
                                        @endforeach
                                    </td>
                                    <td>{{ $rows->about }}</td>
                                    <td>{{ $rows->state }}</td>
                                    <td>{{ $rows->region }}</td>
                                    <td>{{ $rows->status }}</td>
                                    <td>{{ $rows->created_at }}</td>

                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-danger text-center">No result found</td>
                                </tr>
                            @endforelse
                        </table>
                    {{ $customers->links('pagination::bootstrap-4') }}
                </div>
                {{-- </div> --}}
            </div>
        </div>
    </div>
@endsection
@section('footer_scripts')
    <script>
        $('#filter_Status').on('change', function() {
            document.getElementById('order_filter').submit();
        });
        $(".dataTable-sorter").on("click", function() {
            var o = $('#sort_order').val() == 'asc' ? 'desc' : 'asc';
            $('#sort_by').val($(this).attr('data-name'));
            $('#sort_order').val(o);
            $("#search_btn").trigger("click");
        });


    </script>
@endsection
