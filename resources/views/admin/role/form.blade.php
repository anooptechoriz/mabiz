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
    {{ csrf_field() }}
    <div class="row">
        <div class="col-12 col-sm-6">
            <div class="form-group">
                <strong>Name<span class="text-danger">*</span></strong>
                <input id="name" type="text" placeholder="Name" class="form-control" name="name" value="{{ old('name') != '' ? old('name') : (!empty($role) ? $role->name : '') }}" autofocus autocomplete="off">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col"><input type="checkbox" name="select-all" id="select-all" /> Select All</div>
    </div>
    <div class="row">
        @foreach ($permissions as $items)
            <div class="col-xs-3 col-sm-3 col-md-3">
                <div class="form-group">
                    <input type="checkbox" id="permission_{{ $items->id }}" name="permission[]" value="{{ $items->id }}" {{ !empty($role) && $items->rid != null ? 'checked' : '' }} /> <label for="permission_{{ $items->id }}">{{ $items->name }}</label>
                </div>
            </div>
        @endforeach
    </div>
    <div class="back_btn w-100">
        <button type="submit" class="btn btn-primary">{{ !empty($role) ? 'Update' : 'Submit' }}</button>
        <a class="btn btn-primary" href="{{ route('admin.roles') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i> Back</a>
    </div>
    @section('footer_scripts')
        <script>
            $('#select-all').click(function(event) {
                if (this.checked) {
                    // Iterate each checkbox
                    $(':checkbox').each(function() {
                        this.checked = true;
                    });
                } else {
                    $(':checkbox').each(function() {
                        this.checked = false;
                    });
                }
            });
        </script>
    @endsection
