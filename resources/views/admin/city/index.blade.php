
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
        <h1 class="h3 mb-2 text-gray-800 mt-5 mb-4">Cities</h1>
        <div class="card shadow mb-5">
            <div class="card-body">
                <form action="" method="get" id="order_filter">
                    <div class="form-group row">
                        <div class="col-xs-2 col-sm-2 col-md-2 form-group">
                             <select name="filter_country_id" id="filter_country_id" class="form-control">
                                    <option value="">Select Country</option>
                                    @foreach ($countries as $key => $row)
                                    <option value="{{$row->id}}" @php if($row->id == $_GET['filter_country_id']){echo "selected"; }@endphp>{{$row->name}}</option>
                                    @endforeach
                                </select>
                        </div> 
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
                                   Country   
                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                   City 
                                </th>
                                
                                <th class="text-secondary text-xxs font-weight-bolder opacity-7 action-icon">Action</th>
                            </tr>
                        </thead>
                         @php $count=0; @endphp
                        @forelse ($cities as $key => $row)
                         @php $count++; @endphp
                            <tr>
                                <td>{{ ($cities->currentpage() - 1) * $cities->perpage() + $count }}</td>
                                <td>{{ $row->country_name }}</td>
                                <td>{{ $row->city_name }}</td> 
                                <td>
                                     <a href="javascript:void(0)" class="btn btn-warning" title="Edit" data-toggle="modal" data-target="#TaxescreateModal2" onclick="setValue({{ $row->country_id }},'{{ $row->city_name }}','{{ $row->id }}')"><i class="fas fa-pen"></i></a>
                                    <form class="form-horizontal" method="POST" action="{{ route('city.delete', $row->id) }}">
                                         {{ csrf_field() }}
                                        <button type="submit" class="btn btn-danger" title="Delete" onclick="return confirm('Do you really want to delete?')"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                                 
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-danger">No records found</td>
                            </tr>
                        @endforelse
                    </table>
                    {{ $cities->links('pagination::bootstrap-4') }}
                    {{-- </div> --}}
                </div>
            </div>
        </div>
    </div>
  <div class="modal fade tax-creation add_tax" id="TaxescreateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create New City</h5>
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
                                <label class="mr-2 font-weight-bold">Country: </label>
                                <select name="country_id" id="country_id" class="form-control">
                                    <option value="">Select Country</option>
                                    @foreach ($countries as $key => $row)
                                    <option value="{{$row->id}}">{{$row->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 input-tax-creation">
                            <div class="form-group d-flex align-items-center">
                                <label class="mr-2 font-weight-bold">City Name: </label>
                                <input id="city_name" type="text" name="city_name" value="{{ old('city_name') }}" class="form-control">
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-city" >Submit</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade tax-creation add_tax" id="TaxescreateModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update City</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-danger">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-5">
                    <form>
                        <div class="alert alert-danger edit-print-error-msg" style="display:none">
                            <ul></ul>
                        </div>
                        <div class="col-md-12 input-tax-creation mb-3">
                            <div class="form-group d-flex align-items-center">
                                <label class="mr-2 font-weight-bold">Country: </label>
                                <select name="edit_country_id" id="edit_country_id" class="form-control">
                                    <option value="">Select Country</option>
                                    @foreach ($countries as $key => $row)
                                    <option value="{{$row->id}}">{{$row->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 input-tax-creation">
                            <div class="form-group d-flex align-items-center">
                                <label class="mr-2 font-weight-bold">City Name: </label>
                                <input id="edit_city_name" type="text" name="edit_city_name" value="{{ old('edit_city_name') }}" class="form-control">
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="edit_city_id" name="edit_city_id" value="" >
                    <button type="submit" class="btn btn-primary btn-city-edit" >Submit</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div> 
@endsection
@section('footer_scripts')
    {{-- <script src="{{ asset('js/sb-admin-2.min.js') }}"></script> --}}
    <script>
        

 $(document).on("change", "#filter_country_id", function(e) {
     $("#order_filter").submit();
 });
        $(document).on("click", ".btn-city", function(e) {
            e.preventDefault();
            var country_id = $("#country_id").val();
            var city_name = $("#city_name").val();
            if(city_name==''){
                $(".print-error-msg").find("ul").append('<li>Enter City Name</li>');
                $(".print-error-msg").show();
            }
            if(country_id==''){
                $(".print-error-msg").find("ul").append('<li>Choose Country</li>');
                 $(".print-error-msg").show();
            }
            $.ajax({
                type: 'POST',
                url: "{{ route('city.create') }}",
                data: {
                    country_id: country_id,
                    city_name: city_name,
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
        function setValue(country_id,city_name,city_id){
            $('#edit_city_name').val(city_name);
           //$("#edit_country_id").selectOption("country_id");
           document.getElementById("edit_country_id").value = country_id;
            $('#edit_city_id').val(city_id);
        }
        function printErrorMsg(msg) {
            $(".print-error-msg").find("ul").html('');
            $(".print-error-msg").css('display', 'block');
            $.each(msg, function(key, value) {
                $(".print-error-msg").find("ul").append('<li>' + value + '</li>');
            });
        }

        //{{-- Edit Tax --}}

        $(document).on("click", ".btn-city-edit", function(e) {
            e.preventDefault();
            var country_id = $("#edit_country_id").val();
            var city_name = $("#edit_city_name").val();
            var edit_city_id = $("#edit_city_id").val();
            if(city_name==''){
                $(".edit-print-error-msg").find("ul").append('<li>Enter City Name</li>');
                 $(".edit-print-error-msg").show();
            }
            if(country_id==''){
                $(".edit-print-error-msg").find("ul").append('<li>Choose Country</li>');
                $(".edit-print-error-msg").show();
            }
            // alert(percentage);
            $.ajax({
                type: 'POST',
                url: "{{ route('city.edit') }}",
                data: {
                    country_id: country_id,
                    city_name: city_name,
                    edit_city_id: edit_city_id,
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
                        printEditErrorMsg(data.error);
                    }
                }
            });
        });

        function printEditErrorMsg(msg) {
            $(".print-error-msg").find("ul").html('');
            $(".print-error-msg").css('display', 'block');
            $.each(msg, function(key, value) {
                $(".edit-print-error-msg").find("ul").append('<li>' + value + '</li>');
            });
        }
    </script>
@endsection
