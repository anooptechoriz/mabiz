@extends('layouts.admin.index')
@section('content')
    <div class="container-fluid">
        <div class="col-md-8 m-auto outer-section">
            @if (session('error'))
                <div class="alert alert-danger">
                    <p>{{ session('error') }}</p>
                </div>
            @endif
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-2 text-gray-800 mt-3 mb-2">{{ __('messages.profile') }}</h1>
                @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="card shadow mb-5">
                        <div class="prof-page-img">
                            {{-- <div class="prof-head-name">{{ __('messages.profile_informations') }}</div> --}}
                            @if ($admin->profile_pic != '')
                                <img src="{{ asset('assets/uploads/admin_profile/') }}/{{ $admin->profile_pic }}" alt="{{ $admin->profile_pic }}" width="200px" />
                            @endif
                        </div>
                        <div class="card-body p-5">
                            <form class="form-horizontal" enctype="multipart/form-data" method="POST" action="{{ route('admin.profile') }}">
                                @csrf
                                @include('admin.profile.editform')
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow mb-5">
                        <div class="card-body">
                            <div class="prof-edit-head mb-3">{{ __('messages.change_password') }} </div>
                            <form method="POST" action="{{ route('admin.changePassword') }}">
                                @csrf
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
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <strong for="old_password" class="col-form-label">Current Password</strong>
                                        <input name="current_password" id="old_password" class="form-control" placeholder="Current Password" type="password" autocomplete="off" />
                                        <i onclick="oldpasswordShow()" class='fa fa-eye' id="freg-pass-visibility"></i>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <strong for="new_password" class="col-form-label">New Password</strong>
                                        <input name="new_password" id="new_password" class="form-control" placeholder="New Password" type="password" autocomplete="off" />
                                        <i onclick="passwordShow()" class='fa fa-eye' id="reg-pass-visibility"></i>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <strong for="confirm_password" class="col-form-label">Confirm New Password</strong>
                                        <input name="confirm_new_password" id="confirm_password" class="form-control" placeholder="Confirm New Password" type="password" autocomplete="off" />
                                        <i onclick="confirmpasswordShow()" class="fa fa-eye" id="creg-pass-visibility"></i>
                                    </div>
                                </div>
                                <div class="back_btn justify-content-center prof-edit-btn mt-4">
                                    <button type="submit" class="btn btn-primary">{{ __('messages.update') }} {{ __('messages.password') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer_scripts')
    <script>
        $(".alert-success").show().delay(3000).fadeOut();

        function oldpasswordShow() {
            // alert(1);
            var x = document.getElementById("old_password");
            if (x.type === "password") {
                x.type = "text";
                $('#freg-pass-visibility').attr('class', 'fa fa-eye-slash');
            } else {
                x.type = "password";
                $('#freg-pass-visibility').attr('class', 'fa fa-eye');
            }
        }

        function passwordShow() {
            // alert(1);
            var x = document.getElementById("new_password");
            if (x.type === "password") {
                x.type = "text";
                $('#reg-pass-visibility').attr('class', 'fa fa-eye-slash');
            } else {
                x.type = "password";
                $('#reg-pass-visibility').attr('class', 'fa fa-eye');
            }
        }

        function confirmpasswordShow() {
            var x = document.getElementById("confirm_password");
            if (x.type === "password") {
                $('#creg-pass-visibility').attr('class', 'fa fa-eye-slash');
                x.type = "text";
            } else {
                x.type = "password";
                $('#creg-pass-visibility').attr('class', 'fa fa-eye');
            }
        }
    </script>
@endsection
