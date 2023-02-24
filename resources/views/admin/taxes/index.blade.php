@extends('layouts.admin.index')
@section('content')
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>{{ _('messages.Whoops') }}!</strong>
            {{ _('messages.There were some problems with your input') }}.<br><br>
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
    <div class="alert alert-success" id="myElem" style="display: none"></div>
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800 mt-5 mb-4">Taxes</h1>
        <div class="card shadow mb-5">
            <div class="card-body">
                <form action="" method="get" id="order_filter">
                    <div class="form-group row">
                        <div class="col-xs-2 col-sm-2 col-md-2 form-group">

                            <input type='hidden' id="sort_by" class="form-control"name="sort_by" value="{{ request()->get('sort_by') }}">
                            <input type='hidden' id="sort_order" class="form-control"name="sort_order" value="{{ request()->get('sort_order') }}">
                        </div>
                        <input type='submit' id="search_btn" class="btn btn-info" style="display: none">
                    </div>
                </form>
                <div class="table-responsive">
                    <a href="" class="btn add-new" data-toggle="modal" data-target="#TaxescreateModal">Add New</a>
                    <div class="float-right">
                        {{-- <a href="{{ route('admin.taxes') }}" class="btn btn-primary" >Tax</a> --}}
                    </div>
                    {{-- <div class="card-body"> --}}
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="text-secondary text-xxs font-weight-bolder opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter">#</a>
                                </th>
                                <th class="text-secondary text-xxs opacity-7 ">
                                    <a href="javascript:void(0)" class="dataTable-sorter asc" data-name="tax_name">Tax Name</a>
                                    <a class="float-right" text-sm><i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="percentage">{{ __('Percentage') }}</a>
                                    <span class="float-right" text-sm> <i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="status">{{ __('Status') }}</a>
                                    <span class="float-right" text-sm> <i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs font-weight-bolder opacity-7 action-icon">Action</th>
                            </tr>
                        </thead>
                        @forelse ($taxes as $key => $tax)
                            <tr>
                                <td>{{ $key + $taxes->firstItem() }}</td>
                                <td>{{ $tax->tax_name }}</td>
                                <td>{{ $tax->percentage }}<i class="fas fa-percent"></i></td>
                                <td id="display_status_{{ $tax->id }}">{{ $tax->status }}</td>
                                <td>
                                    <button class="btn btn-warning" data-toggle="modal" data-target="#exampleModal{{ $tax->id }}" title="Edit tax"><i class="fas fa-edit"></i></button>
                                    <div id="outer_status_{{ $tax->id }}" class="float-left mr-2">
                                        @if ($tax->status == 'active')
                                            <a href="javascript:void(0)" class="btn btn-danger" alt="Disable post" title="Disable tax" onclick="changeStatus({{ $tax->id }},'disabled')"><i class="fas fa-minus-circle"></i></a>
                                        @elseif($tax->status == 'disabled')
                                            <a href="javascript:void(0)" class="btn btn-warning" alt="Activate post" title="Activate tax" onclick="changeStatus({{ $tax->id }},'active')"><i class="fas fa-check-circle"></i></a>
                                        @endif
                                    </div>
                                </td>
                                {{-- Tax edit modal start --}}
                                <div class="modal fade tax-updation add_tax" id="exampleModal{{ $tax->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Update tax</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true" class="text-danger">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body p-5">
                                                <form>
                                                    <div class="alert alert-danger print-error-msg" style="display:none">
                                                        <ul></ul>
                                                    </div>
                                                    <input type="hidden" name="tax_id" id="tax_id" value="{{ $tax->id }}">
                                                    <div class="col-md-12 input-tax-creation mb-3">
                                                        <div class="form-group d-flex align-items-center">
                                                            <label class="mr-2 font-weight-bold">Name</label>
                                                            <input id="tax_edit{{ $tax->id }}" type="text" name="tax_name" value="{{ $tax->tax_name }}" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 input-tax-creation mb-3">
                                                        <div class="form-group d-flex align-items-center">
                                                            <label class="mr-2 font-weight-bold">Percentage</label>
                                                            <input id="percentage_edit{{ $tax->id }}" type="text" name="percentage" value="{{ $tax->percentage }}" class="form-control">
                                                        </div>
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary px-4 btn-tax-edit" data-id="{{ $tax->id }}">Submit</button>
                                                <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Close</button>
                                            </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                {{-- Tax edit end --}}
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-danger">No records found</td>
                            </tr>
                        @endforelse
                    </table>
                    {{ $taxes->links('pagination::bootstrap-4') }}
                    {{-- </div> --}}
                </div>
            </div>
        </div>
    </div>
    {{-- Add new taxes start --}}
    <div class="modal fade tax-creation add_tax" id="TaxescreateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create New Tax</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-danger">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-5">
                    <form>
                        <div class="alert alert-danger print-error-msg" style="display:none">
                            <ul></ul>
                        </div>
                        <div class="col-md-12 input-tax-creation mb-3">
                            <div class="form-group d-flex align-items-center">
                                <label class="mr-2 font-weight-bold">Name: </label>
                                <input id="tax_add" type="text" name="tax_name" value="{{ old('tax_name') }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12 input-tax-creation">
                            <div class="form-group d-flex align-items-center">
                                <label class="mr-2 font-weight-bold">Percentage(%): </label>
                                <input id="percentage_add" type="text" name="percentage" value="{{ old('percentage') }}" class="form-control">
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-tax" data-id="{{ $tax->id }}">Submit</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    {{-- new taxes end --}}
@endsection
@section('footer_scripts')
    {{-- <script src="{{ asset('js/sb-admin-2.min.js') }}"></script> --}}
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

        function changeStatus(tax_id, status) {
            if (tax_id) {
                var outerhtml = $("#outer_status_" + tax_id).html();
                $("#outer_status_" + tax_id).html('<img src="{{ asset('/images/ajax-loader.gif') }}" >')
                $.ajax({
                    type: "post",
                    data: {
                        id: tax_id,
                        status: status,
                        "_token": "{{ csrf_token() }}"
                    },
                    url: "{{ route('taxes.changestatus') }}",
                    success: function(res) {
                        if (res.ajax_status == 'success') {
                            if (status == 'active') {
                                html = '<a href="javascript:void(0)" class="btn btn-danger" alt="Disable tax" title="Disable" onclick="changeStatus(' + tax_id + ',\'disabled\')"><i class="fas fa-minus-circle"></i></a>';
                                $("#outer_status_" + tax_id).html(html);
                                $("#display_status_" + tax_id).html(status);
                                $("#myElem").html(res.message);
                                $("#myElem").show().delay(3000).fadeOut();
                            } else {
                                html = '<a href="javascript:void(0)" class="btn btn-warning" alt="Activate tax" title="Activate" onclick="changeStatus(' + tax_id + ',\'active\')"><i class="fas fa-check-circle"></i></a>';
                                $("#outer_status_" + tax_id).html(html);
                                $("#display_status_" + tax_id).html(status);
                                $("#myElem").html(res.message);
                                $("#myElem").show().delay(3000).fadeOut();
                            }
                        } else {
                            $("#outer_status_" + tax_id).html(outerhtml);
                            $("#myElem").html(res.message);
                            $("#myElem").show().delay(3000).fadeOut();
                        }
                    }
                });
            }
        }

        // {{-- add tax --}}

        $(document).on("click", ".btn-tax", function(e) {
            e.preventDefault();
            var tax_name = $("#tax_add").val();
            var percentage = $("#percentage_add").val();
            $.ajax({
                type: 'POST',
                url: "{{ route('taxes.create') }}",
                data: {
                    tax_name: tax_name,
                    percentage: percentage,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    if ($.isEmptyObject(data.error)) {
                        $("#error_message").html(data.success);
                        $("#error_message").show();
                        $('.add_tax').modal('hide');
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        printErrorMsg(data.error);
                    }
                }
            });
        });

        function printErrorMsg(msg) {
            $(".print-error-msg").find("ul").html('');
            $(".print-error-msg").css('display', 'block');
            $.each(msg, function(key, value) {
                $(".print-error-msg").find("ul").append('<li>' + value + '</li>');
            });
        }

        //{{-- Edit Tax --}}

        $(document).on("click", ".btn-tax-edit", function(e) {
            e.preventDefault();
            var tax_id = $(this).attr("data-id");
            var tax_name = $("#tax_edit" + tax_id).val();
            var percentage = $("#percentage_edit" + tax_id).val();
            // alert(percentage);
            $.ajax({
                type: 'POST',
                url: "{{ route('taxes.update') }}",
                data: {
                    tax_id: tax_id,
                    tax_name: tax_name,
                    percentage: percentage,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    if ($.isEmptyObject(data.error)) {
                        $("#doc_message").html(data.success);
                        $("#doc_message").show();
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                        $('.edit_tax').modal('hide');
                    } else {
                        printErrorMsg(data.error);
                    }
                }
            });
        });

        function printErrorMsg(msg) {
            $(".print-error-msg").find("ul").html('');
            $(".print-error-msg").css('display', 'block');
            $.each(msg, function(key, value) {
                $(".print-error-msg").find("ul").append('<li>' + value + '</li>');
            });
        }
    </script>
@endsection
