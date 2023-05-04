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
        <h1 class="h3 mb-2 text-gray-800 mt-5 mb-4">{{ __('Subscriptions') }}</h1>
        <div class="card shadow">
            <div class="card-body">
                <form action="" method="get">
                    <div class="form-group row">
                        <div class="col-xs-2 col-sm-2 col-md-2 form-group row mr-2">
                            <input type="text" class="form-control" name="search_term" placeholder="Search Term..." value="{{ request()->get('search_term') }}">
                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-2" style="margin-bottom: 10px;">
                            <select name="filter_service" class="form-control" value="">
                                <option value="">Select Service....</option>
                                @foreach ($service_languages as $row)
                                    <option value="{{ $row->service_id }}" @if (request()->get('filter_service') == $row->service_id) selected @endif>{{ ucfirst($row->service_name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-2" style="margin-bottom: 10px;">
                            <select name="filter_status" class="form-control category" value="">
                                <option value="">Select Status....</option>
                                <option value="active" @if (request()->get('filter_status') == 'active') selected @endif>Active</option>
                                <option value="expired" @if (request()->get('filter_status') == 'expired') selected @endif>Expired</option>
                            </select>
                        </div>
                        <div class="d-flex">
                            <div class="form-group mb-0">
                                <input type='hidden' id="sort_by" class="form-control"name="sort_by" value="{{ request()->get('sort_by') }}">
                                <input type='hidden' id="sort_order" class="form-control"name="sort_order" value="{{ request()->get('sort_order') }}">
                            </div>
                            <input type='submit' id="search_btn" class="btn btn-info" style="display: none">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary ml-2">Filter</button>
                        </div>
                    </div>
                </form>
                {{-- <form action="" method="get" id="order_filter">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="form-group">
                        </div>
                        <div class="d-flex">
                            <div class="form-group mb-0">
                                <input type='hidden' id="sort_by" class="form-control"name="sort_by" value="{{ request()->get('sort_by') }}">
                                <input type='hidden' id="sort_order" class="form-control"name="sort_order" value="{{ request()->get('sort_order') }}">
                            </div>
                            <input type='submit' id="search_btn" class="btn btn-info" style="display: none">

                        </div>
                    </div>
                </form> --}}
                {{-- <div class="card-body"> --}}
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="text-secondary text-xxs font-weight-bolder opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter">#</a>
                                </th>
                                <th class="text-secondary text-xxs opacity-7 ">
                                    <a href="javascript:void(0)" class="dataTable-sorter asc" data-name="service_name">Service</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="firstname">User</a>
                                    <span class="float-right" text-sm> <i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="package_name">Package</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="subscription_date">Subscription date</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="expiry_date">Expiry date</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="expiry_date">Coupon Code</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="status">Status</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs font-weight-bolder opacity-7 action-icon">Action</th>
                            </tr>
                        </thead>
                        @php $count=0; @endphp
                        @forelse ($subscription as $rows)
                            @php $count++; @endphp
                            <tr>
                                <td>{{ ($subscription->currentpage() - 1) * $subscription->perpage() + $count }}</td>
                                <td>{{ $rows->service }}</td>
                                <td>{{ $rows->user }}</td>
                                <td>{{ $rows->package }}</td>
                                <td>{{ $rows->subscription_date }}</td>
                                <td>{{ $rows->expiry_date }}</td>
                                 <td>{{ $rows->coupon_code }}</td>
                                <td>{{ $rows->status }}</td>
                                <td>
                                    <div id="outer_status_{{ $rows->id }}">
                                        @if ($rows->status == 'active')
                                            <a href="javascript:void(0)" class="btn btn-warning" alt="Disable Subscription" title="Disable Subscription" onclick="changeStatus({{ $rows->id }},'expired')"><i class="fas fa-minus-circle"></i></a>
                                        @elseif($rows->status == 'expired')
                                            <a href="javascript:void(0)" class="btn btn-warning" alt="Activate Subscription" title="Activate Subscription" onclick="changeStatus({{ $rows->id }},'active')"><i class="fas fa-check-circle"></i></a>
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
                    {{ $subscription->links('pagination::bootstrap-4') }}
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
                    url: "{{ route('subscription.changestatus') }}",
                    success: function(res) {
                        html = '';
                        if (res.ajax_status == 'success') {
                            if (status == 'active') {
                                html += '<a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" alt="Disable Subscription" title="Disable Subscription" onclick="changeStatus(' + cid + ',\'expired\')"><i class="fas fa-minus-circle"></i></a>';
                                $("#outer_status_" + cid).html(html);
                                $("#display_status_" + cid).html(status);
                                $("#myElem").html(res.message);
                                $("#myElem").show().delay(3000).fadeOut();
                                location.reload();
                            } else if (status == 'expired') {
                                html += '<a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" alt="Activate Subscription" title="Activate Subscription" onclick="changeStatus(' + cid + ',\'active\')"><i class="fas fa-check-circle"></i></a>';
                                $("#outer_status_" + cid).html(html);
                                $("#display_status_" + cid).html(status);
                                $("#myElem").html(res.message);
                                $("#myElem").show().delay(3000).fadeOut();
                                location.reload();
                            } else {
                                $("#outer_status_" + cid).html(html);
                                $("#display_status_" + cid).html(status);
                                $("#myElem").html(res.message);
                                $("#myElem").show().delay(3000).fadeOut();
                            }
                        } else {
                            $("#outer_status_" + cid).html(outerhtml);
                            $("#myElem").html(res.message);
                            $("#myElem").show().delay(3000).fadeOut();
                        }
                    }
                });
            }
        }
    </script>
@endsection
