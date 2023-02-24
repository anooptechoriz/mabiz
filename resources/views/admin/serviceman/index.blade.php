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
        <h1 class="h3 mb-2 text-gray-800 mt-5 mb-4">{{ __('Service Man') }}</h1>
        <div class="card shadow mb-5">
            <div class="card-body">
                <form action="" method="get" id="order_filter">
                    <div class="d-flex justify-content-between align-items-center">

                        <div class="form-group">
                            {{-- <div class="col-xs-2 col-sm-2 col-md-2" style="margin-bottom: 10px;"> --}}
                            <select name="filter_Status" class="form-control pr-5" id="filter_Status">
                                <option value="">--Choose Status--</option>
                                <option value='active' {{ request()->get('filter_Status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value='disabled' {{ request()->get('filter_Status') == 'disabled' ? 'selected' : '' }}>Disabled</option>
                                <option value='blocked' {{ request()->get('filter_Status') == 'blocked' ? 'selected' : '' }}>Blocked</option>

                                <option value='deleted' {{ request()->get('filter_Status') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                            </select>
                            {{-- </div> --}}
                        </div>
                        <div class="d-flex justify-content-end">
                            <div class="form-group mb-0">
                                <input type='text' class="form-control" placeholder="Search Keyword" name="search_keyword" value="{{ request()->get('search_keyword') }}">
                                <input type='hidden' id="sort_by" class="form-control"name="sort_by" value="{{ request()->get('sort_by') }}">
                                <input type='hidden' id="sort_order" class="form-control"name="sort_order" value="{{ request()->get('sort_order') }}">
                            </div>
                            <button type='submit' id="search_btn" class="btn btn-primary ml-2">{{ __('messages.search') }}</button>
                        </div>
                    </div>
                </form>
                <div class="table-responsive serviceman-table">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="text-secondary text-xxs font-weight-bolder opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter">#</a>
                                </th>
                                <th class="text-secondary text-xxs opacity-7 ">
                                    <a href="javascript:void(0)" class="dataTable-sorter">{{ __('messages.picture') }}</a>
                                </th>
                                <th class="text-secondary text-xxs opacity-7" style="min-width: 100px;">
                                    <a href="javascript:void(0)" class="dataTable-sorter asc" data-name="firstname">Name</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="email">{{ __('messages.email') }}</a>
                                    <span class="float-right" text-sm> <i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7" style="min-width: 120px;">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="gender">{{ __('messages.gender') }}</a>
                                    <span class="float-right" text-sm> <i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="phone">Phone</a>
                                    <span class="float-right" text-sm> <i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7" style="min-width: 150px;">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="dob">Date of birth</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7" style="min-width: 120px;">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="country_id">{{ __('messages.country') }}</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7" style="min-width: 120px;">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="service">{{ __('messages.service') }}</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7" style="min-width: 120px;">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="state">State</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th>
                                {{-- <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="region">Region</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th> --}}
                                <th class="text-secondary text-xxs opacity-7" style="min-width: 130px;">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="civil_card_no">Civil card no</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th>
                                {{-- <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="about">About</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th> --}}
                                <th class="text-secondary text-xxs opacity-7" style="min-width: 120px;">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="transport">Transport</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th>
                                {{-- <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="profile">Profile</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th> --}}
                                <th class="text-secondary text-xxs opacity-7" style="min-width: 130px;">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="expiry_date">Expiry Date</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th>
                                {{-- <th class="text-secondary text-xxs opacity-7" style="min-width: 150px;">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="coupon_code">{{ __('Coupon Code') }}</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th> --}}
                                <th class="text-secondary text-xxs opacity-7" style="min-width: 110px;">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="status">Status</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7" style="min-width: 150px;">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="created_at">Created Time</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs font-weight-bolder opacity-7 action-icon" style="min-width: 180px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $count=0; @endphp
                            @forelse ($serviceman as $rows)
                                @php $count++; @endphp
                                <tr>
                                    <td>{{ ($serviceman->currentpage() - 1) * $serviceman->perpage() + $count }}</td>
                                    <td class="list-table-image">
                                        @if ($rows->profile_pic != '')
                                            <img src="{{ asset('assets/uploads/profile/') }}/{{ $rows->profile_pic }}" alt="{{ $rows->profile_pic }}" />
                                        @else
                                            <img src="{{ asset('img/no-image.jpg') }}" alt="profile image" />
                                        @endif
                                    </td>
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
                                    {{-- <td>{{ $rows->subscribed_services($rows->id) }}</td> --}}
                                    <td>
                                        @php $services = $rows->subscribed_services($rows->id); @endphp
                                        @if (count($services) > 0)
                                            @foreach ($services as $list)
                                            {{ $list->service_name != '' ?  $list->service_name  : '' }}
                                            <br>
                                        @endforeach
                                        @endif

                                    </td>

                                    <td>{{ $rows->state }}</td>
                                    <td>{{ $rows->civil_card_no }}</td>
                                    <td>{{ $rows->transport }}</td>
                                    {{-- <td>{{ $rows->expiry_date }}</td> --}}
                                    {{-- <td>{{ $rows->coupon_code }}</td> --}}

                                    <td>
                                        @php $services = $rows->subscribed_services($rows->id); @endphp
                                        @if (count($services) > 0)
                                            @foreach ($services as $list)
                                                {{ $list->service_name != ''? '[' . $list->service_name . ' : ' . $list->expiry_date . '] ' : ''}}

                                                <br>
                                        @endforeach
                                        @endif

                                    </td>
                                    <td>{{ $rows->status }}</td>
                                    <td>{{ $rows->created_at }}</td>
                                    <td>
                                        <div id="outer_status_{{ $rows->id }}">
                                            <a href="{{ route('serviceman.view', $rows->id) }}" class="btn btn-warning mb-1" title="View User"><i class="fas fa-eye"></i></a>
                                            @if ($rows->status == 'active')
                                                <a href="javascript:void(0)" class="btn btn-warning mb-1" alt="Disable User" title="Disable User" onclick="changeStatus({{ $rows->id }},'disabled')"><i class="fas fa-minus-circle"></i></a>
                                                <a href="javascript:void(0)" class="btn btn-danger mb-1" alt="Delete User" title="Delete User" onclick="changeStatus({{ $rows->id }},'deleted')"><i class="fas fa-times-circle"></i></a>
                                            @elseif($rows->status == 'disabled')
                                                <a href="javascript:void(0)" class="btn btn-warning mb-1" alt="Activate User" title="Activate User" onclick="changeStatus({{ $rows->id }},'active')"><i class="fas fa-check-circle"></i></a>
                                                <a href="javascript:void(0)" class="btn btn-danger mb-1" alt="Delete User" title="Delete User" onclick="changeStatus({{ $rows->id }},'deleted')"><i class="fas fa-times-circle"></i></a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-danger text-center">No result found</td>
                                </tr>
                            @endforelse
                    </table>
                    {{ $serviceman->links('pagination::bootstrap-4') }}
                    </tbody>
                    </table>
                </div>
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

        function changeStatus(cid, status) {
            if (cid) {
                var outerhtml = $("#outer_status_" + cid).html();
                $("#outer_status_" + cid).html('<img src="{{ asset('img/ajax-loader.gif') }}" >');

                $.ajax({
                    type: "POST",
                    data: {
                        id: cid,
                        status: status,
                        "_token": "{{ csrf_token() }}"
                    },
                    url: "{{ route('serviceman.changeStatus') }}",
                    success: function(res) {
                        if (res.ajax_status == 'success') {
                            if (status == 'active') {
                                html = '<a href="{{ url('serviceman/view/') }}/' + cid + '" class="btn btn-warning btn-circle btn-md" title="view Customer"><i class="fas fa-eye"></i></a>';
                                html += '<a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" alt="Disable User" title="Disable User" onclick="changeStatus(' + cid + ',\'disabled\')"><i class="fas fa-minus-circle"></i></a>';
                                html += '<a href="javascript:void(0)" class="btn btn-danger btn-circle btn-md" alt="Delete User" title="Delete User" onclick="changeStatus(' + cid + ',\'deleted\')"><i class="fas fa-times-circle"></i></a>';
                                $("#outer_status_" + cid).html(html);
                                $("#display_status_" + cid).html(status);
                                $("#myElem").html(res.message);
                                $("#myElem").show().delay(3000).fadeOut();
                                location.reload();
                            } else if (status == 'disabled') {
                                html = '<a href="{{ url('serviceman/view/') }}/' + cid + '" class="btn btn-warning btn-circle btn-md" title="view Customer"><i class="fas fa-eye"></i></a>';
                                html += '<a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" alt="Activate User" title="Activate User" onclick="changeStatus(' + cid + ',\'active\')"><i class="fas fa-check-circle"></i></a>';
                                html += '<a href="javascript:void(0)" class="btn btn-danger btn-circle btn-md" alt="Delete User" title="Delete User" onclick="changeStatus(' + cid + ',\'deleted\')"><i class="fas fa-times-circle"></i></a>';
                                $("#outer_status_" + cid).html(html);
                                $("#display_status_" + cid).html(status);
                                $("#myElem").html(res.message);
                                $("#myElem").show().delay(3000).fadeOut();
                                location.reload();
                            } else {
                                html += '<a href="{{ url('serviceman/view/') }}/' + cid + '" class="btn btn-warning btn-circle btn-md" title="view Customer"><i class="fas fa-eye"></i></a>';
                                $("#outer_status_" + cid).html(html);
                                $("#display_status_" + cid).html(status);
                                $("#myElem").html(res.message);
                                $("#myElem").show().delay(3000).fadeOut();
                                // location.reload();
                            }
                        } else {
                            $("#outer_status_" + cid).html(outerhtml);
                            $("#myElem").html(res.message);
                            $("#myElem").show().delay(3000).fadeOut();
                            // location.reload();
                        }
                    }
                });
            }
        }
    </script>
@endsection
