{{-- <div class="row"> --}}

<div class="form-group">
    <strong>Profile Imge</strong>
    <input type="File" class="course-img" name="profile_pic">
    @if ($errors->has('profile_pic'))
        <span class="text-danger">{{ $errors->first('profile_pic') }}</span>
    @endif
</div>
<div class="w-100">
    <div class="form-group">
        <strong>{{ __('messages.name') }}:</strong>
        <input type="text" placeholder="Name" class="form-control" name="name" value="{{ old('name') != '' ? old('name') : (!empty($admin) ? $admin->name : '') }}">
        @if ($errors->has('name'))
            <span class="text-danger">{{ $errors->first('name') }}</span>
        @endif
    </div>
</div>
<div class="w-100">
    <div class="form-group">
        <strong>{{ __(__('Email')) }}:</strong>
        <input type="text" placeholder="Email" class="form-control" name="admin_email" value="{{ old('admin_email') != '' ? old('admin_email') : (!empty($admin) ? $admin->email : '') }}">
        @if ($errors->has('admin_email'))
            <span class="text-danger">{{ $errors->first('admin_email') }}</span>
        @endif
    </div>
</div>
<div class="w-100">
    <div class="form-group">
        <strong>{{ __('Phone') }}:</strong>
        <input id="phone" type="text" placeholder="Phone" class="form-control" name="phone" value="{{ old('phone') != '' ? old('phone') : (!empty($admin) ? $admin->phone : '') }}">
    </div>
</div>

<div class="w-100">
    <div class="form-group">
        <strong>{{ __('messages.jobtitle') }}:</strong>
        <input type="text" placeholder="{{ __('messages.jobtitle') }}" class="form-control" name="job_title" value="{{ old('job_title') != '' ? old('job_title') : (!empty($admin) ? $admin->job_title : '') }}">
    </div>
</div>
<div class="w-100">
    <div class="form-group">
        <strong>{{ __('messages.bio') }}:</strong>
        <textarea placeholder="{{ __('messages.bio') }}" class="form-control" name="bio">{{ old('bio') != '' ? old('bio') : (!empty($admin) ? $admin->bio : '') }}</textarea>
    </div>
</div>
<div class="back_btn w-100 justify-content-center mt-4 prof-page-btn">
    <button type="submit" class="btn btn-primary w-100">{{ __('messages.update') }}</button>
</div>

{{-- </div> --}}
