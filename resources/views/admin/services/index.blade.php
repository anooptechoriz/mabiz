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
    @if (session('success'))
        <div class="alert alert-success">
            <ul>
                <li>{{ session('success') }}</li>
            </ul>
        </div>
    @endif
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800 mt-5 mb-4">{{ __('messages.services') }}</h1>
        <div class="card shadow mb-4 mb-5">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('services.create') }}" class="btn add-new">Add New</a>
                    <form method="get">
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

                <div class="table-responsive mt-4">
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
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="parent_id">{{ __('messages.parent service') }}</a>
                                    <span class="float-right" text-sm> <i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="image">{{ __('messages.image') }}</a>
                                    <span class="float-right" text-sm> <i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="country_ids">{{ __('Available Countries') }}</a>
                                    <span class="float-right" text-sm> <i class="fa fa-arrow-up"></i></span>
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="subscription">No:of Profiles</a>
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
                        @forelse ($services as $row)
                            @php $count++; @endphp
                            <tr>
                                <td>{{ ($services->currentpage() - 1) * $services->perpage() + $count }}</td>
                                <td>{{ $row->service_name }}</td>
                                <td>{{ $row->parent_service_name }}</td>
                                <td class="list-table-main-image">
                                    @if ($row->image != '')
                                        <img src="{{ asset('/assets/uploads/service/' . $row->image) }}" alt="{{ $row->service }}">
                                    @else
                                        <img src="{{ asset('img/no-image.jpg') }}" alt="profile image" />
                                    @endif
                                </td>
                                <td>
                                    @foreach ($row->country_details as $value)
                                        {{ $value->name }}{{ $loop->last ? '' : ',' }}
                                    @endforeach
                                </td>
                                <td>{{ $row->subscription }}</td>
                                <td>{{ $row->status }}</td>
                                <td>
                                    <div id="outer_status_{{ $row->id }}">
                                        <form action="{{ route('services.destroy', $row->id) }}" method="POST">
                                            @csrf
                                            <a href="{{ route('services.view', $row->id) }}" title="view" class="btn btn-warning"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('services.edit', $row->id) }}" title="edit" class="btn btn-warning"><i class="fas fa-pen"></i></a>
                                            <button type="submit" class="btn btn-danger" title="delete" onclick="return confirm('Do you really want to delete?')"><i class="fas fa-trash"></i></button>
                                            @if ($row->status == 'active')
                                                <a href="javascript:void(0)" class="btn btn-warning" alt="Disable service" title="Disable service" onclick="changeStatus({{ $row->id }},'disabled')"><i class="fas fa-minus-circle"></i></a>
                                            @elseif($row->status == 'disabled')
                                                <a href="javascript:void(0)" class="btn btn-warning" alt="Activate service" title="Activate service" onclick="changeStatus({{ $row->id }},'active')"><i class="fas fa-check-circle"></i></a>
                                            @endif
                                    </div>
                                    </form>
                                </td>
                            </tr>
                            {{-- @php
                                $count++;
                            @endphp --}}
                        @empty
                            <tr>
                                <td colspan="8" class="text-danger text-center">No result found</td>
                            </tr>
                        @endforelse
                    </table>
                    {{ $services->links('pagination::bootstrap-4') }}
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

        function changeStatus(cid, status) {
            if (status) {
                var outerhtml = $("#outer_status_" + cid).html();
                $("#outer_status_" + cid).html('<img src="{{ asset('img/ajax-loader.gif') }}" >');
                $.ajax({
                    type: "POST",
                    data: {
                        id: cid,
                        status: status,
                        "_token": "{{ csrf_token() }}"
                    },
                    url: "{{ route('service.changestatus') }}",
                    success: function(res) {
                        if (res.ajax_status == 'success') {
                            if (status == 'active') {
                                html = '<a href="{{ url('services/view/') }}/' + cid + '" class="btn btn-warning btn-circle btn-md" title="view Customer"><i class="fas fa-eye"></i></a>';
                                html += '<a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" alt="Disable service" title="Disable service" onclick="changeStatus(' + cid + ',\'disabled\')"><i class="fas fa-minus-circle"></i></a>';
                                html += '<a href="javascript:void(0)" class="btn btn-danger btn-circle btn-md" alt="Delete service" title="Delete service" onclick="changeStatus(' + cid + ',\'deleted\')"><i class="fas fa-times-circle"></i></a>';
                                $("#outer_status_" + cid).html(html);
                                $("#display_status_" + cid).html(status);
                                $("#myElem").html(res.message);
                                $("#myElem").show().delay(3000).fadeOut();
                                location.reload();
                            } else if (status == 'disabled') {
                                html = '<a href="{{ url('services/view/') }}/' + cid + '" class="btn btn-warning btn-circle btn-md" title="view Customer"><i class="fas fa-eye"></i></a>';
                                html += '<a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" alt="Activate service" title="Activate service" onclick="changeStatus(' + cid + ',\'active\')"><i class="fas fa-check-circle"></i></a>';
                                html += '<a href="javascript:void(0)" class="btn btn-danger btn-circle btn-md" alt="Delete service" title="Delete service" onclick="changeStatus(' + cid + ',\'deleted\')"><i class="fas fa-times-circle"></i></a>';
                                $("#outer_status_" + cid).html(html);
                                $("#display_status_" + cid).html(status);
                                $("#myElem").html(res.message);
                                $("#myElem").show().delay(3000).fadeOut();
                                location.reload();
                            } else {
                                html += '<a href="{{ url('services/view/') }}/' + cid + '" class="btn btn-warning btn-circle btn-md" title="view Customer"><i class="fas fa-eye"></i></a>';
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
