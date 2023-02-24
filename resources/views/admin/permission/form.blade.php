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
@if (session('success'))
    <div class="alert alert-success">
        <ul>
            <li>{{ session('success') }}</li>
        </ul>
    </div>
@endif

<div class="row">
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <strong>{{ __('messages.name') }}:</strong>
            <input id="name" type="text" placeholder="Name" class="form-control" name="name" value="{{ old('name') != '' ? old('name') : (!empty($permission) ? $permission->name : '') }}" autofocus autocomplete="off">
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <strong>{{ __('messages.slug') }}:</strong>
            <input id="slug" type="text" placeholder="{{ __('messages.slug') }}" class="form-control" name="slug" value="{{ old('slug') != '' ? old('slug') : (!empty($permission) ? $permission->slug : '') }}">
        </div>
    </div>
</div>
<div class="back_btn w-100">
    <button type="submit" class="btn btn-primary">{{ !empty($permission) ? __('messages.update') : __('messages.submit') }}</button>
    <a class="btn btn-primary" href="{{ route('admin.permissions') }}" title="{{ __('messages.Back to Listings') }}"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</a>
</div>
