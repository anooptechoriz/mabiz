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
        <h1 class="h3 mb-2 text-gray-800">{{ __('messages.customers') }}</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('messages.picture') }}</th>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.email') }}</th>
                                <th>{{ __('messages.gender') }}</th>
                                <th>{{ __('messages.phone') }}</th>
                                <th>{{ __('messages.dob') }}</th>
                                <th>{{ __('messages.country') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.created at') }}</th>
                                <th class="action-icon">{{ __('messages.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $count = 0;
                                $skipped = $customers->currentPage() * $customers->perPage();
                            @endphp
                            @forelse($customers as $rows)
                                <tr>
                                    <td>{{ ($customers->currentpage() - 1) * $customers->perpage() + $count + 1 }}</td>
                                    <td class="list-table-image">
                                        @if ($rows->profile_pic != '')
                                            <img src="{{ asset('assets/uploads/profile/') }}/{{ $rows->profile_pic }}" alt="{{ $rows->profile_pic }}" />
                                        @else
                                            <img src="{{ asset('img/no-image.jpg') }}" alt="profile image" />
                                        @endif
                                    </td>
                                    <td>{{ $rows->name }}</td>
                                    <td>{{ $rows->email }}</td>
                                    <td>{{ $rows->gender }}</td>
                                    <td>{{ $rows->phone }}</td>
                                    <td>{{ date('d-m-Y', strtotime($rows->dob)) }}</td>
                                    <td>{{ $rows->country_name }}</td>
                                    <td>{{ $rows->status }}</td>
                                    <td>{{ $rows->created_at }}</td>
                                    <td>
                                        <div id="outer_status_{{ $rows->id }}">
                                            @if ($rows->status == 'active')
                                                <a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" alt="Disable User" title="Disable User" onclick="changeStatus({{ $rows->id }},'disabled')"><i class="fas fa-minus-circle"></i></a>
                                                <a href="javascript:void(0)" class="btn btn-danger btn-circle btn-md" alt="Delete User" title="Delete User" onclick="changeStatus({{ $rows->id }},'deleted')"><i class="fas fa-times-circle"></i></a>
                                            @elseif($rows->status == 'disabled')
                                                <a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" alt="Activate User" title="Activate User" onclick="changeStatus({{ $rows->id }},'active')"><i class="fas fa-check-circle"></i></a>
                                                <a href="javascript:void(0)" class="btn btn-danger btn-circle btn-md" alt="Delete User" title="Delete User" onclick="changeStatus({{ $rows->id }},'deleted')"><i class="fas fa-times-circle"></i></a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @php $count++; @endphp
                            @empty
                                <tr>
                                    <td colspan="9" class="text-danger text-center">No records found</td>
                                </tr>
                            @endforelse
                            {{ $customers->links('pagination::bootstrap-4') }}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer_scripts')
    <script>
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
                    url: "{{ route('customers.changeStatus') }}",
                    success: function(res) {
                        if (res.ajax_status == 'success') {
                            if (status == 'active') {

                                html = '<a href="{{ url('customers/view/') }}/' + cid + '" class="btn btn-warning btn-circle btn-md" title="view Customer"><i class="fas fa-eye"></i></a>';
                                html += '<a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" alt="Disable User" title="Disable User" onclick="changeStatus(' + cid + ',\'disabled\')"><i class="fas fa-minus-circle"></i></a>';
                                html += '<a href="javascript:void(0)" class="btn btn-danger btn-circle btn-md" alt="Delete User" title="Delete User" onclick="changeStatus(' + cid + ',\'deleted\')"><i class="fas fa-times-circle"></i></a>';

                                $("#outer_status_" + cid).html(html);
                                $("#display_status_" + cid).html(status);
                                $("#myElem").html(res.message);
                                $("#myElem").show().delay(3000).fadeOut();
                            } else if (status == 'disabled') {
                                html = '<a href="{{ url('customers/view/') }}/' + cid + '" class="btn btn-warning btn-circle btn-md" title="view Customer"><i class="fas fa-eye"></i></a>';
                                html += '<a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" alt="Activate User" title="Activate User" onclick="changeStatus(' + cid + ',\'active\')"><i class="fas fa-check-circle"></i></a>';
                                html += '<a href="javascript:void(0)" class="btn btn-danger btn-circle btn-md" alt="Delete User" title="Delete User" onclick="changeStatus(' + cid + ',\'deleted\')"><i class="fas fa-times-circle"></i></a>';

                                $("#outer_status_" + cid).html(html);
                                $("#display_status_" + cid).html(status);
                                $("#myElem").html(res.message);
                                $("#myElem").show().delay(3000).fadeOut();
                            } else {
                                html += '<a href="{{ url('customers/view/') }}/' + cid + '" class="btn btn-warning btn-circle btn-md" title="view Customer"><i class="fas fa-eye"></i></a>';
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
