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
        <h1 class="h3 mb-2 text-gray-800 mt-5 mb-4">{{ __('Orders') }}</h1>
        <div class="card shadow mb-5">
            <div class="card-body">
                <form action="" method="get">
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="search_term" placeholder="Search Term..." value="{{ request()->get('search_term') }}">
                            </div>
                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-2" style="margin-bottom: 10px;">
                            <select name="filter_service" class="form-control" value="">
                                <option value="">Select Service....</option>
                                @foreach ($service_languages as $row)
                                    <option value="{{ $row->id }}" @if (request()->get('filter_service') == $row->id) selected @endif>{{ ucfirst($row->service_name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-2" style="margin-bottom: 10px;">
                            <select name="filter_status" class="form-control category" value="">
                                <option value="">Select status</option>
                                <option value="success" @if (request()->get('filter_status') == 'success') selected @endif>Success</option>
                                <option value="pending" @if (request()->get('filter_status') == 'pending') selected @endif>Pending</option>
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
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="coupon_code">Coupon</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="grand_total">Grand total</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="payment_status">Payment status</a>
                                    <span class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs font-weight-bolder opacity-7 action-icon">Action</th>
                            </tr>
                        </thead>
                        @php $count=0; @endphp
                        @forelse ($order as $rows)
                            @php $count++; @endphp
                            <tr>
                                <td>{{ ($order->currentpage() - 1) * $order->perpage() + $count }}</td>
                                <td>{{ $rows->service }}</td>
                                <td>{{ $rows->user }}</td>
                                <td>{{ $rows->package }}</td>
                                <td>{{ $rows->coupon_code }}</td>
                                <td>{{ $rows->grand_total }}</td>
                                <td>{{ $rows->payment_status }}</td>
                                <td style="width: 200px;">
                                    <a href="{{ route('order.show', ['id' => $rows->id]) }}" class="btn btn-warning btn-md" title="View"><i class="fas fa-eye"></i></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-danger text-center">No result found</td>
                            </tr>
                        @endforelse
                    </table>
                    {{ $order->links('pagination::bootstrap-4') }}
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

        // function changeStatus(cid, status) {
        //     if (cid) {
        //         var outerhtml = $("#outer_status_" + cid).html();
        //         $("#outer_status_" + cid).html('<img src="{{ asset('img/ajax-loader.gif') }}" >');
        //         $.ajax({
        //             type: "POST",
        //             data: {
        //                 id: cid,
        //                 status: status,
        //                 "_token": "{{ csrf_token() }}"
        //             },
        //             url: "{{ route('customers.changeStatus') }}",
        //             success: function(res) {
        //                 if (res.ajax_status == 'success') {
        //                     if (status == 'active') {

        //                         html = '<a href="{{ url('customers/view/') }}/' + cid + '" class="btn btn-warning btn-circle btn-md" title="view Customer"><i class="fas fa-eye"></i></a>';
        //                         html += '<a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" alt="Disable Customer" title="Disable Customer" onclick="changeStatus(' + cid + ',\'disabled\')"><i class="fas fa-minus-circle"></i></a>';
        //                         html += '<a href="javascript:void(0)" class="btn btn-danger btn-circle btn-md" alt="Delete Customer" title="Delete Customer" onclick="changeStatus(' + cid + ',\'deleted\')"><i class="fas fa-times-circle"></i></a>';
        //                         $("#outer_status_" + cid).html(html);
        //                         $("#display_status_" + cid).html(status);
        //                         $("#myElem").html(res.message);
        //                         $("#myElem").show().delay(3000).fadeOut();
        //                         location.reload();
        //                     } else if (status == 'disabled') {
        //                         html = '<a href="{{ url('customers/view/') }}/' + cid + '" class="btn btn-warning btn-circle btn-md" title="view Customer"><i class="fas fa-eye"></i></a>';
        //                         html += '<a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" alt="Activate Customer" title="Activate Customer" onclick="changeStatus(' + cid + ',\'active\')"><i class="fas fa-check-circle"></i></a>';
        //                         html += '<a href="javascript:void(0)" class="btn btn-danger btn-circle btn-md" alt="Delete Customer" title="Delete Customer" onclick="changeStatus(' + cid + ',\'deleted\')"><i class="fas fa-times-circle"></i></a>';
        //                         $("#outer_status_" + cid).html(html);
        //                         $("#display_status_" + cid).html(status);
        //                         $("#myElem").html(res.message);
        //                         $("#myElem").show().delay(3000).fadeOut();
        //                         location.reload();
        //                     } else {
        //                         html += '<a href="{{ url('customers/view/') }}/' + cid + '" class="btn btn-warning btn-circle btn-md" title="view Customer"><i class="fas fa-eye"></i></a>';
        //                         $("#outer_status_" + cid).html(html);
        //                         $("#display_status_" + cid).html(status);
        //                         $("#myElem").html(res.message);
        //                         $("#myElem").show().delay(3000).fadeOut();
        //                         // location.reload();
        //                     }
        //                 } else {
        //                     $("#outer_status_" + cid).html(outerhtml);
        //                     $("#myElem").html(res.message);
        //                     $("#myElem").show().delay(3000).fadeOut();
        //                     // location.reload();
        //                 }
        //             }

        //         });
        //     }
        // }
    </script>
@endsection
