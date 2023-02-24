@extends('layouts.admin.index')
@section('content')
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>{{ _('Whoops') }}!</strong> {{ _('There were some problems with your input') }}.<br><br>
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
        <h1 class="h3 mb-2 text-gray-800 mt-5 mb-4">Home Sliders</h1>
        <div class="card shadow mb-4 mb-5">
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
                    <a href="{{ route('homesliders.create') }}" class="btn add-new" title="Add">Add New</a>
                    {{-- <div class="card-body"> --}}
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Banner/Slider</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                            @php $count=0; @endphp
                            @forelse ($sliders as $row)
                                @php $count++; @endphp
                                <tr>
                                    <td>{{ ($sliders->currentpage() - 1) * $sliders->perpage() + $count }}</td>
                                    <td> {{ $row->name }}</td>
                                    <td> {{ $row->type }}</td>
                                    <td> {{ $row->status }}</td>
                                    <td style="width: 250px;">
                                        <div id="outer_status_{{ $row->id }}">
                                            <form action="{{ route('homesliders.destroy', $row->id) }}" method="POST">
                                                @csrf
                                                <a href="{{ url('/homesliders/view/' . $row->id) }}" class="btn btn-warning mr-1" title="View"><i class="fas fa-eye"></i></a>
                                                <a href="{{ url('/homesliders/edit/' . $row->id) }}" class="btn btn-warning mr-1" title="Edit"><i class="fas fa-pen"></i></a>
                                                <button type="submit" class="btn btn-danger" title="Delete" onclick="return confirm('Do you really want to delete?')"><i class="fas fa-trash"></i></button>
                                                @if ($row->status == 'active')
                                                    <a href="javascript:void(0)" class="btn btn-warning" alt="Disable" title="Disable" onclick="changeStatus({{ $row->id }},'inactive')"><i class="fas fa-minus-circle"></i></a>
                                                @elseif($row->status == 'inactive')
                                                    <a href="javascript:void(0)" class="btn btn-warning" alt="Activate" title="Activate" onclick="changeStatus({{ $row->id }},'active')"><i class="fas fa-check-circle"></i></a>
                                                @endif
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-danger text-center">No result found</td>
                                </tr>
                            @endforelse
                        </table>
                    {{ $sliders->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer_scripts')
    <script>
        function changeStatus(cid, status) {
            if (status) {
                //   alert(status);
                var outerhtml = $("#outer_status_" + cid).html();
                $("#outer_status_" + cid).html('<img src="{{ asset('img/ajax-loader.gif') }}" >');
                $.ajax({
                    type: "POST",
                    data: {
                        id: cid,
                        status: status,
                        "_token": "{{ csrf_token() }}"
                    },
                    url: "{{ route('homesliders.changestatus') }}",
                    success: function(res) {
                        if (res.ajax_status == 'success') {
                            if (status == 'active') {
                                html = '<a href="{{ url('homesliders/view/') }}/' + cid + '" class="btn btn-warning btn-circle btn-md" title="view Customer"><i class="fas fa-eye"></i></a>';
                                html += '<a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" alt="Disable" title="Disable" onclick="changeStatus(' + cid + ',\'inactive\')"><i class="fas fa-minus-circle"></i></a>';
                                html += '<a href="javascript:void(0)" class="btn btn-danger btn-circle btn-md" alt="Delete" title="Delete" onclick="changeStatus(' + cid + ',\'deleted\')"><i class="fas fa-times-circle"></i></a>';
                                $("#outer_status_" + cid).html(html);
                                $("#display_status_" + cid).html(status);
                                $("#myElem").html(res.message);
                                $("#myElem").show().delay(3000).fadeOut();
                                location.reload();
                            } else if (status == 'inactive') {
                                html = '<a href="{{ url('homesliders/view/') }}/' + cid + '" class="btn btn-warning btn-circle btn-md" title="view Customer"><i class="fas fa-eye"></i></a>';
                                html += '<a href="javascript:void(0)" class="btn btn-warning btn-circle btn-md" alt="Activate" title="Activate" onclick="changeStatus(' + cid + ',\'active\')"><i class="fas fa-check-circle"></i></a>';
                                html += '<a href="javascript:void(0)" class="btn btn-danger btn-circle btn-md" alt="Delete" title="Delete" onclick="changeStatus(' + cid + ',\'deleted\')"><i class="fas fa-times-circle"></i></a>';
                                $("#outer_status_" + cid).html(html);
                                $("#display_status_" + cid).html(status);
                                $("#myElem").html(res.message);
                                $("#myElem").show().delay(3000).fadeOut();
                                location.reload();
                            } else {
                                html += '<a href="{{ url('homesliders/view/') }}/' + cid + '" class="btn btn-warning btn-circle btn-md" title="view Customer"><i class="fas fa-eye"></i></a>';
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
