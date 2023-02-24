@if (count($errors) > 0)
    <div class="alert alert-danger">
        <strong>{{ __('messages.Whoops') }}!</strong> {{ __('messages.There were some problems with your input') }}.<br><br>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="modal" id="view_licence"role="dialog">
    <div class="modal-dialog">
        <div class="modal-content"style="width: 800px;;height: 750px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- Modal Header -->
            <div class="modal-body">
                <embed src='{{ asset('assets/uploads/admin_licence/') }}/{{ $admin->licence }}'#toolbar=0 width="100%"height="550px">
            </div>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>
<div class="row">
    {{ csrf_field() }}
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <strong>{{ __('messages.name') }}:</strong><span class="text-danger">*</span>
            <input id="name" type="text" placeholder="{{ __('messages.name') }}" class="form-control" name="name" value="{{ old('name') != '' ? old('name') : $admin->name }}" autofocus>
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <strong>{{ __('messages.email') }}</strong><span class="text-danger">*</span>
            <input id="admin_email" type="text" placeholder="{{ __('messages.email') }}" class="form-control" name="admin_email" value="{{ old('admin_email') != '' ? old('admin_email') : $admin->email }}">
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <strong>{{ __('messages.password') }}:</strong><span class="text-danger">*</span>
            <input id="password" type="password" placeholder="{{ __('messages.password') }}" class="form-control" name="password">
            <i onclick="registerpasswordShow()" class='fa fa-eye' id="reg-pass-visibility"></i>
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <strong>{{ __('Confirm  Password') }}</strong><span class="text-danger">*</span>
            <input id="confirm_password" type="password" placeholder="{{ __('Confirm Password') }}" class="form-control" name="confirm_password" value="{{ old('password') }}">
            <i onclick="registerconfirmpasswordShow()" class="fa fa-eye" id="creg-pass-visibility"></i>
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <strong>{{ __('messages.phone') }}:</strong><span class="text-danger">*</span>
            <input id="phone" type="text" placeholder="{{ __('messages.phone') }}" class="form-control" name="phone" value="{{ old('phone') != '' ? old('phone') : $admin->phone }}">
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <strong>{{ __('Job title') }}:</strong>
            <input id="job_title" type="text" placeholder="{{ __('Job title') }}" class="form-control" name="job_title" value="{{ old('job_title') != '' ? old('job_title') : $admin->job_title }}">
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <strong>{{ __('messages.bio') }}:</strong>
            <textarea id="bio" placeholder="{{ __('messages.bio') }}" class="form-control" name="bio">{{ old('bio') != '' ? old('bio') : $admin->bio }}</textarea>
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <strong>{{ __('messages.role') }}:</strong><span class="text-danger">*</span>
            <select class="form-control" name="role">
                <option value="">--{{ __('messages.select role') }}--</option>
                @foreach ($roles as $items)
                    <option value="{{ $items->id }}" {{ !empty(old('role')) && old('role') == $items->id ? 'selected' : ($admin->role_id == $items->id ? 'Selected' : '') }}>{{ ucfirst($items->name) }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <strong class="d-block">{{ __('messages.profile picture') }}:</strong>
            @if ($admin->profile_pic != '')
                <img class="d-block mb-2" src="{{ asset('assets/uploads/admin_profile/') }}/{{ $admin->profile_pic }}" alt="{{ $admin->profile_pic }}" width="100px" />
            @endif
            <input id="profile_pic" type="File" class="course-img" name="profile_pic">
        </div>
    </div>
    <div class="col-12 back_btn">
        <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
        <a class="btn btn-primary" href="{{ route('admin.list') }}" title=" Back to Listings"> <i class="fa fa-arrow-left" aria-hidden="true"></i> Back</a>
    </div>
</div>
@section('footer_scripts')
    <script>
        function registerpasswordShow() {
            var x = document.getElementById("password");
            if (x.type === "password") {
                x.type = "text";
                $('#reg-pass-visibility').attr('class', 'fa fa-eye-slash');
            } else {
                x.type = "password";
                $('#reg-pass-visibility').attr('class', 'fa fa-eye');
            }
        }

        function registerconfirmpasswordShow() {
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
