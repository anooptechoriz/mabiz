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
<div class="row">
    {{ csrf_field() }}
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <strong>{{ __('messages.name') }}</strong><span class="text-danger">*</span>
            <input id="name" type="text" placeholder="{{ __('messages.name') }}" class="form-control" name="name" value="{{ old('name') }}" autofocus>
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <strong>{{ __('messages.email') }}</strong><span class="text-danger">*</span>
            <input id="admin_email" type="text" placeholder="{{ __('messages.email') }}" class="form-control" name="admin_email" value="{{ old('admin_email') }}">
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <strong>{{ __('messages.password') }}</strong><span class="text-danger">*</span>
            <input id="password" type="password" placeholder="{{ __('messages.password') }}" class="form-control" name="password" value="{{ old('password') }}">
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
            <input id="phone" type="text" placeholder="{{ __('messages.phone') }}" class="form-control" name="phone" value="{{ old('phone') }}">
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <strong>{{ __('Job title') }}:</strong>
            <input id="job_title" type="text" placeholder="{{ __('messages.jobtitle') }}" class="form-control" name="job_title" value="{{ old('job_title') }}">
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <strong>{{ __('messages.role') }}</strong><span class="text-danger">*</span>
            <select class="form-control" name="role">
                <option value="">--{{ __('messages.select role') }}--</option>
                @foreach ($roles as $items)
                    <option value="{{ $items->id }}" @if (old('role') == $items->id) {{ 'Selected' }} @endif>{{ ucfirst($items->name) }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <strong>{{ __('messages.bio') }}:</strong>
            <textarea id="bio" placeholder="{{ __('messages.bio') }}" class="form-control" name="bio">{{ old('bio') }}</textarea>
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <strong>{{ __('messages.profile picture') }}:</strong>
            <input id="profile_pic" type="File" class="course-img" name="profile_pic">
        </div>
    </div>
    <div class="col-12 back_btn">
        <button type="submit" class="btn btn-primary">{{ __('messages.create') }}</button>
        <a class="btn btn-primary" href="{{ route('admin.list') }}" title="Back to List"> <i class="fa fa-arrow-left" aria-hidden="true"></i> Back</a>
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
