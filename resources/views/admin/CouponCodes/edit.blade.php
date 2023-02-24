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
        <div class="col-md-8 m-auto">
            <div class="card shadow my-5">
                <div class="card-body">
                    <h1 class="h4 mb-2 text-gray-800">Update Coupon</h1>
                    <form method="post" action="{{ route('coupons.update', $couponcodes->id) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <strong>Code <span class="text-danger">*</span></strong>
                                    <input type="text" name="code" value="{{ $couponcodes->code }}" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <strong>Discount Percentage<span class="text-danger">*</span></strong>
                                    <input type="text" name="discount" value="{{ $couponcodes->discount }}" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <strong>Validity<span class="text-danger">*</span></strong>
                                    <input type="text" name="validity" id="my_date_picker" value="{{ $couponcodes->validity }}" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <strong>Description<span class="text-danger">*</span></strong>
                                    <textarea id="description" name="description" class="form-control">{{ $couponcodes->description }}</textarea>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <strong>Terms and Conditions<span class="text-danger">*</span></strong>
                                    <textarea id="conditions" name="conditions" class="form-control">{{ $couponcodes->conditions }}</textarea>
                                </div>
                            </div>
                            <div class="col-12 back_btn">
                                <button type="submit" value="Submit" class="btn btn-info">Submit</button>
                                <a class="btn btn-info" href="{{ route('admin.coupons') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i> Back</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer_scripts')
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#my_date_picker").datepicker({
                minDate: new Date(),
                dateFormat: 'yy-mm-dd'
            });
        })
    </script>
@endsection
