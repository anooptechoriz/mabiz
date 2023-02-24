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
    <div class="container-fluid">
        <div class="col-md-8 m-auto">
            <div class="card shadow mt-5">
                <div class="card-body">
                    <h1 class="h4 mb-2 text-gray-800">Coupon Codes</h1>
                    <div class="table-responsive">
                        <form method="post" action="{{ route('coupons.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <strong> Code<span class="text-danger">*</span></strong>
                                        <input type="text" name="code" class="form-control input-lg" value="{{ old('code') }}" />
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <strong> Discount Percentage<span class="text-danger">*</span></strong>
                                        <input type="text" name="discount" value="{{ old('discount') }}" class="form-control input-lg" />
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <strong> Validity<span class="text-danger">*</span></strong>
                                        <input type="text" name="validity" id="my_date_picker" value="{{ old('validity') }}" class="form-control input-lg" />
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <strong> Description<span class="text-danger">*</span></strong>
                                        <textarea name="description" class="form-control" id="mytextarea">{{ old('description') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <strong> Terms and Conditions<span class="text-danger">*</span></strong>
                                        <textarea name="conditions" class="form-control" id="mytextarea">{{ old('conditions') }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="back_btn w-100">
                                <button type="submit" name="add" class="btn btn-primary input-lg" value="Submit">Submit</button>
                                <a class="btn btn-primary" href="{{ route('admin.coupons') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i> Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer_scripts')
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
    <script>
        $(document).ready(function() {

            // $(function() {
            $("#my_date_picker").datepicker({
                minDate: new Date(),
                dateFormat: 'yy-mm-dd'
            });
        })
    </script>
@endsection
