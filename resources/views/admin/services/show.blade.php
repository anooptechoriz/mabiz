@extends('layouts.admin.index')

@section('content')
    {{-- @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>{{ _('messages.Whoops') }}!</strong> {{ _('messages.There were some problems with your input') }}.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif --}}
    {{-- @if (session('success'))
        <div class="alert alert-success">
            <ul>
                <li>{{ session('success') }}</li>
            </ul>
        </div>
    @endif --}}
    <div class="container-fluid">
        <div class="col-md-8 m-auto outer-section">
            <div class="card shadow mb-4 mb-5">
                <div class="card-body">
                    <h1 class="h4 mb-4 text-gray-800">{{ __('messages.service') }}</h1>
                    {{-- <div class="row">
                        <div class="container"> --}}
                    <div class="wrapper row">
                        <div class="preview col-md-5">
                            <div class="preview-pic tab-content">
                                @if ($serviceDetails->image != '')
                                    <img src="{{ asset('/assets/uploads/service/') }}/{{ $serviceDetails->image }}" alt="Service Image" width="150px" class="tab-pane active image_display" id="product_pic" />
                                @else
                                    <img src="{{ asset('img/no-image.jpg') }}" alt="profile image" />
                                @endif
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="details">
                                <h3 class="product-title">{{ $serviceDetails->service }}</h3>
                                <div class="pr-contact-info mt-3">
                                    <h5><span>Language Information</span></h5>
                                    @foreach ($servicelanguages as $row)
                                        @if ($row->shortcode == 'en')
                                            <h6><span>{{ __('Name in English') }}:</span> {{ $row->service_name }}</h6>
                                        @endif
                                        @if ($row->shortcode == 'ar')
                                            <h6><span>{{ __('Name in Arabic') }}:</span> {{ $row->service_name }}</h6>
                                        @endif
                                        @if ($row->shortcode == 'hi')
                                            <h6><span>{{ __('Name in Hindi') }}:</span> {{ $row->service_name }}</h6>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="pr-contact-info mt-3">
                                    <h5><span>Product Information</span></h5>
                                    <h6><span>{{ __('messages.parent service') }}:</span> {{ $serviceDetails->parent_service_name ? $serviceDetails->parent_service_name : 'N/A' }}</h6>
                                    <h6><span>{{ __('Available Countries') }}:</span>
                                        @foreach ($serviceDetails->country_details as $value)
                                            {{ $value->name }}{{ $loop->last ? '' : ',' }}
                                        @endforeach
                                    </h6>
                                    <h6><span>{{ __('messages.status') }}:</span> {{ $serviceDetails->status }}</h6>
                                    <h6><span>{{ __('messages.registered on') }}:</span> {{ $serviceDetails->created_at }}</h6>
                                </div>
                                {{-- add package model --}}
                                <div class="modal fade add_pack" id="exampleModal{{ $serviceDetails->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content" style="width:700px">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">{{ __('messages.add packages') }}</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body p-5">
                                                <form id="myForm">
                                                    <div class="alert alert-danger print-error-msg" style="display:none">
                                                        <ul></ul>
                                                    </div>
                                                    <input type='hidden' name='service_id' id="service_id" value="{{ $serviceDetails->id }}">
                                                    <div class="form-group row">
                                                        <label for="package_eng" class="col-md-4 col-form-label">{{ __('Package in English') }} <span class="text-danger"> * </span></label>
                                                        <div class="col-md-8">
                                                            <input type='hidden' name="language" value="1">
                                                            <input id="package_eng" type="text" class="form-control @error('packages') is-invalid @enderror" name="package_in_english" value="{{ old('packages') }}" required autocomplete="packages" autofocus>
                                                            @error('packages')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="package_arb" class="col-md-4 col-form-label">{{ __('Package in Arabic') }} <span class="text-danger"> * </span></label>
                                                        <div class="col-md-8">
                                                            <input type='hidden' name="language" value="2">
                                                            <input id="package_arb" type="text" class="form-control @error('packages') is-invalid @enderror" name="package_in_arabic" value="{{ old('packages') }}" required autocomplete="packages" autofocus>
                                                            @error('packages')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="package_hind" class="col-md-4 col-form-label">{{ __('Package in Hindi') }} <span class="text-danger"> * </span></label>
                                                        <div class="col-md-8">
                                                            <input type='hidden' name="language" value="3">
                                                            <input id="package_hind" type="text" class="form-control @error('packages') is-invalid @enderror" name="package_in_hindi" value="{{ old('packages') }}" required autocomplete="packages" autofocus>
                                                            @error('packages')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    {{-- </div>
                                                        <div class="modal-body"> --}}
                                                    <div class="form-group row">
                                                        <label for="description1" class="col-md-4 col-form-label">{{ __('Description in English') }}</label>
                                                        <div class="col-md-8">
                                                            <input type='hidden' name="language" value="1">
                                                            <textarea id="description1" class="form-control @error('description') is-invalid @enderror" name="description_in_english" value="{{ old('description') }}" autocomplete="description" autofocus></textarea>
                                                            @error('description')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    {{-- </div>
                                                        <div class="modal-body"> --}}
                                                    <div class="form-group row">
                                                        <label for="description2" class="col-md-4 col-form-label">{{ __('Description in  Arabic') }}</label>
                                                        <div class="col-md-8">
                                                            <input type='hidden' name="language" value="2">
                                                            <textarea id="description2" class="form-control @error('description') is-invalid @enderror" name="description_in_arabic" value="{{ old('description') }}" autocomplete="description" autofocus></textarea>
                                                            @error('description')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    {{-- </div>
                                                        <div class="modal-body"> --}}
                                                    <div class="form-group row">
                                                        <label for="description3" class="col-md-4 col-form-label">{{ __('Description in Hindi') }}</label>
                                                        <div class="col-md-8">
                                                            <input type='hidden' name="language" value="3">
                                                            <textarea id="description3" class="form-control @error('description') is-invalid @enderror" name="description_in_hindi" value="{{ old('description') }}" autocomplete="description" autofocus></textarea>
                                                            @error('description')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    {{-- </div>
                                                        <div class="modal-body"> --}}
                                                    <div class="form-group row">
                                                        <label for="validity" class="col-md-4 col-form-label">{{ __('messages.validity') }}</label>
                                                        <div class="col-md-8">
                                                            <div class="row" id="dynamic_field">
                                                                <div class="col-md-12">
                                                                    <select class="form-control" name="validity" id="validity">
                                                                        <option value="">-Select Validity-</option>
                                                                        <option value="3 months">3 Months</option>
                                                                        <option value="6 months">6 Months</option>
                                                                        <option value="1 year">1 Year</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            @error('validity')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    {{-- </div>
                                                        <div class="modal-body"> --}}
                                                    <div class="form-group row">
                                                        <label for="amount_id" class="col-md-4 col-form-label">{{ __('messages.amount') }} <span class="text-danger"> * </span></label>
                                                        <div class="col-md-8">
                                                            <input id="amount_id" type="text" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount') }}" required autocomplete="amount" autofocus>
                                                            @error('amount')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    {{-- </div>
                                                        <div class="modal-body"> --}}
                                                    <div class="form-group row">
                                                        <label for="offer_price_id" class="col-md-4 col-form-label">{{ __('messages.offer price') }}</label>
                                                        <div class="col-md-8">
                                                            <input id="offer_price_id" type="text" class="form-control @error('offer_price') is-invalid @enderror" name="offer_price" value="{{ old('offer_price') }}" autocomplete="offer_price" autofocus>
                                                            @error('offer_price')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    {{-- </div>
                                                        <div class="modal-body"> --}}
                                                    <div class="form-group row">
                                                        <label for="taxes" class="col-md-4 col-form-label">{{ __('Choose Tax') }}</label>
                                                        <div class="col-md-8">
                                                            @if ($taxes->isNotEmpty())
                                                                <div class="form-group serv-choose-tax">
                                                                    @foreach ($taxes as $Taxes_row)
                                                                        <span>
                                                                            <input type="checkbox" class="checkboxClass" id="{{ $Taxes_row->tax_name }}" name="taxes[]" value="{{ $Taxes_row->id }}" {{ !empty(old('taxes')) && in_array($Taxes_row->id, old('taxes')) ? 'checked' : '' }}>
                                                                            <label for="{{ $Taxes_row->tax_name }}">{{ $Taxes_row->tax_name }}
                                                                                ({{ $Taxes_row->percentage }}%)
                                                                            </label>
                                                                        </span>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary btn-submit" data-id="{{ $serviceDetails->id }}">Submit</button>
                                                <button type="button" class="btn btn-close" data-dismiss="modal">Close</button>
                                            </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- </div>
                        </div> --}}
                    </div>
                    {{-- end add package --}}
                    {{-- edit package model --}}
                    <div class="preview col-md-12 mt-5">
                        {{-- <div class="preview col-md-12"> --}}
                        <div class="tab-content">
                            <div class="alert alert-success print-message-msg" style="display:none" id="package_message"></div>
                            <div class="alert alert-success print-message-msg" style="display:none" id="pack_edit_message"></div>
                            @if (session('warning'))
                                <div class="alert alert-success">
                                    <ul>
                                        <li>{{ session('warning') }}</li>
                                    </ul>
                                </div>
                            @endif
                            <div class="table-responsive">
                                <div class="d-flex justify-content-between">
                                    <h6>{{ __('messages.packages') }}</h6>
                                    <button type="button" class="btn addpackage" data-id="{{ $serviceDetails->id }}" data-toggle="modal" data-target="#exampleModal{{ $serviceDetails->id }}" onclick="myFunction()">{{ __('messages.add packages') }}</button>
                                </div>
                                {{-- <table class="table table-condensed table-hover"> --}}
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('messages.packages') }}</th>
                                            <th>{{ __('messages.description') }}</th>
                                            <th>{{ __('messages.validity') }}</th>
                                            <th>{{ __('messages.amount') }}</th>
                                            <th>{{ __('messages.offer price') }}</th>
                                            <th>{{ __('Tax') }}</th>
                                            <th>{{ __('messages.action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $SL = 1 @endphp
                                        @forelse ($packages as $packages_Row)
                                            {{-- @if (!empty($packages))
                                                    @php $SL = 1 @endphp
                                                    @foreach ($packages as $packages_Row) --}}
                                            <tr>
                                                <td>{{ $SL++ }}</td>
                                                <td>{{ $packages_Row->package }}</td>
                                                <td>{{ $packages_Row->descriptions }}</td>
                                                <td>{{ $packages_Row->validity }}</td>
                                                <td>{{ $packages_Row->amount }}</td>
                                                <td>{{ $packages_Row->offer_price }}</td>
                                                <td>
                                                    @foreach ($packages_Row->tax_details as $value)
                                                        {{ $value->tax_name }}{{ $loop->last ? '' : ',' }}
                                                    @endforeach
                                                </td>
                                                <td>
                                                    <form action="{{ route('package.destroy', $packages_Row->id) }}" method="POST">
                                                        @csrf
                                                        <button type="button" class="btn btn-warning editpackage" title="Edit Package" data-id="{{ $packages_Row->id }}" data-toggle="modal" data-target="#examplepackageModaledit{{ $packages_Row->id }}" title="Edit Document"><i class="fas fa-pen"></i></button>
                                                        <button type="submit" class="btn btn-danger" title="Delete Package" onclick="return confirm('Do you really want to delete?')"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                </td>
                                                <div class="modal fade edit_pack" id="examplepackageModaledit{{ $packages_Row->id }}" tabindex="-1" role="dialog" aria-labelledby="packageModaleditLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content" style="width:700px">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="packageModaleditLabel">Edit Package</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body p-5">
                                                                <form>
                                                                    <div class="alert alert-danger print-error-msg" style="display:none">
                                                                        <ul></ul>
                                                                    </div>
                                                                    {{-- @csrf --}}
                                                                    <input type='hidden' name='service_id' id="service_id" value="{{ $serviceDetails->id }}">
                                                                    <input type='hidden' name='package_id' id="package_id" value="{{ $packages_Row->id }}">
                                                                    @foreach ($packages_Row['language_items'] as $row)
                                                                        @if ($row->language_id == '1')
                                                                            <div class="form-group row">
                                                                                <label for="package_english" class="col-md-4 col-form-label">{{ __('Package in English') }} <span class="text-danger"> * </span></label>
                                                                                <div class="col-md-8">
                                                                                    <input type='hidden' name="language" value="1">
                                                                                    <input id="package_english_{{ $packages_Row->id }}" type="text" class="form-control @error('packages') is-invalid @enderror" name="package_in_english" value="{{ $row->package_name }}" required autocomplete="packages" autofocus>
                                                                                    @error('packages')
                                                                                        <span class="invalid-feedback" role="alert">
                                                                                            <strong>{{ $message }}</strong>
                                                                                        </span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                        @if ($row->language_id == '2')
                                                                            <div class="form-group row">
                                                                                <label for="package_arbic" class="col-md-4 col-form-label">{{ __('Package in Arabic') }} <span class="text-danger"> * </span></label>
                                                                                <div class="col-md-8">
                                                                                    <input type='hidden' name="language" value="2">
                                                                                    <input id="package_arbic_{{ $packages_Row->id }}" type="text" class="form-control @error('packages') is-invalid @enderror" name="package_in_arabic" value="{{ $row->package_name }}" required autocomplete="packages" autofocus>
                                                                                    @error('packages')
                                                                                        <span class="invalid-feedback" role="alert">
                                                                                            <strong>{{ $message }}</strong>
                                                                                        </span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                        @if ($row->language_id == '3')
                                                                            <div class="form-group row">
                                                                                <label for="package_hindi" class="col-md-4 col-form-label">{{ __('Package in Hindi') }} <span class="text-danger"> * </span></label>
                                                                                <div class="col-md-8">
                                                                                    <input type='hidden' name="language" value="3">
                                                                                    <input id="package_hindi_{{ $packages_Row->id }}" type="text" class="form-control @error('packages') is-invalid @enderror" name="package_in_hindi" value="{{ $row->package_name }}" required autocomplete="packages" autofocus>
                                                                                    @error('packages')
                                                                                        <span class="invalid-feedback" role="alert">
                                                                                            <strong>{{ $message }}</strong>
                                                                                        </span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    @endforeach
                                                                    @foreach ($packages_Row['language_items'] as $row)
                                                                        @if ($row->language_id == '1')
                                                                            {{-- <div class="modal-body"> --}}
                                                                            <div class="form-group row">
                                                                                <label for="description_eng" class="col-md-4 col-form-label">{{ __('Description in English') }}</label>
                                                                                <div class="col-md-8">
                                                                                    <input type='hidden' name="language" value="1">
                                                                                    <textarea id="description_eng_{{ $packages_Row->id }}" class="form-control" name="description_in_english" autofocus> {{ $row->package_description }}  </textarea>
                                                                                    @error('description')
                                                                                        <span class="invalid-feedback" role="alert">
                                                                                            <strong>{{ $message }}</strong>
                                                                                        </span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            {{-- </div> --}}
                                                                        @endif
                                                                        @if ($row->language_id == '2')
                                                                            {{-- <div class="modal-body"> --}}
                                                                            <div class="form-group row">
                                                                                <label for="description_arb" class="col-md-4 col-form-label">{{ __('Description in Arabic') }}</label>
                                                                                <div class="col-md-8">
                                                                                    <input type='hidden' name="language" value="2">
                                                                                    <textarea id="description_arb_{{ $packages_Row->id }}" class="form-control" name="description_in_arabic" autofocus> {{ $row->package_description }}  </textarea>
                                                                                    @error('description')
                                                                                        <span class="invalid-feedback" role="alert">
                                                                                            <strong>{{ $message }}</strong>
                                                                                        </span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            {{-- </div> --}}
                                                                        @endif
                                                                        @if ($row->language_id == '3')
                                                                            {{-- <div class="modal-body"> --}}
                                                                            <div class="form-group row">
                                                                                <label for="description_hind" class="col-md-4 col-form-label">{{ __('Description in Hindi') }}</label>
                                                                                <div class="col-md-8">
                                                                                    <input type='hidden' name="language" value="3">
                                                                                    <textarea id="description_hind_{{ $packages_Row->id }}" class="form-control" name="description_in_hindi" autofocus> {{ $row->package_description }}  </textarea>
                                                                                    @error('description')
                                                                                        <span class="invalid-feedback" role="alert">
                                                                                            <strong>{{ $message }}</strong>
                                                                                        </span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            {{-- </div> --}}
                                                                        @endif
                                                                    @endforeach
                                                                    {{-- <div class="modal-body"> --}}
                                                                    <div class="form-group row">
                                                                        <label for="validitys" class="col-md-4 col-form-label">{{ __('messages.validity') }}</label>
                                                                        <div class="col-md-8">
                                                                            <div class="row" id="dynamic_field">
                                                                                <div class="col-md-12">
                                                                                    <select class="form-control" name="validity" id="validitys_{{ $packages_Row->id }}">
                                                                                        <option value="">-Select Validity-</option>
                                                                                        <option {{ $packages_Row->validity == '3 months' ? 'selected' : '' }} value="3 months"> 3 Months </option>
                                                                                        <option {{ $packages_Row->validity == '6 months' ? 'selected' : '' }} value="6 months"> 6 Months </option>
                                                                                        <option {{ $packages_Row->validity == '1 year' ? 'selected' : '' }} value="1 year"> 1 Year </option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            @error('validity')
                                                                                <span class="invalid-feedback" role="alert">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    {{-- </div> --}}
                                                                    <div class="form-group row">
                                                                        <label for="amounts" class="col-md-4 col-form-label">{{ __('messages.amount') }}<span class="text-danger">*</span></label>
                                                                        <div class="col-md-8">
                                                                            <input id="amounts_{{ $packages_Row->id }}" type="text" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ $packages_Row->amount }}" autocomplete="amount" autofocus>
                                                                            @error('amount')
                                                                                <span class="invalid-feedback" role="alert">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label for="offerprices" class="col-md-4 col-form-label">{{ __('messages.offer price') }}</label>
                                                                        <div class="col-md-8">
                                                                            <input id="offerprices_{{ $packages_Row->id }}" type="text" class="form-control @error('offer_price') is-invalid @enderror" name="offer_price" value="{{ $packages_Row->offer_price }}" autocomplete="offer_price" autofocus>
                                                                            @error('offer_price')
                                                                                <span class="invalid-feedback" role="alert">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label for="taxes" class="col-md-4 col-form-label">{{ __('Choose Tax') }}</label>
                                                                        <div class="col-md-8">
                                                                            @if ($taxes->isNotEmpty())
                                                                                @php
                                                                                    $tax_array = [];
                                                                                    $tax_array = explode(',', $packages_Row->tax_ids);
                                                                                @endphp
                                                                                <div class="form-group serv-choose-tax">
                                                                                    @foreach ($taxes as $Taxes_row)
                                                                                        <span><input type="checkbox" class="checkbox_edit_{{ $packages_Row->id }}" id="taxes_{{ $packages_Row->id }}" name="taxes[]" value="{{ $Taxes_row->id }}" {{ in_array($Taxes_row->id, $tax_array) ? 'checked' : '' }}>
                                                                                            <label for="{{ $Taxes_row->tax_name }}">{{ $Taxes_row->tax_name }} ({{ $Taxes_row->percentage }}%)</label>
                                                                                        </span>
                                                                                    @endforeach
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary btn-edit" data-id="{{ $packages_Row->id }}">Submit</button>
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                            </tr>
                            </div>
                        @empty
                            <tr>
                                <td colspan="8" class="text-danger text-center">No records found</td>
                            </tr>
                            @endforelse
                            </tbody>
                            </table>
                            {{-- </table> --}}
                        </div>
                        {{-- </div> --}}
                    </div>
                </div>

                {{-- end edit package --}}
                {{-- add subservice --}}
                <div class="preview col-md-12 mt-4">
                    {{-- <div class="preview col-md-12"> --}}
                    <div class="modal fade add_serv" id="subserviceModal{{ $serviceDetails->id }}" tabindex="-1" role="dialog" aria-labelledby="examplesubserviceModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content" style="width:900px">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="examplesubserviceModalLabel">{{ 'Sub Services' }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body p-5">
                                    <form id=subserviceForm method="POST" enctype="multipart/form-data">
                                        {{-- @csrf --}}
                                        <div class="alert alert-danger d-none" id="save_errorlist">
                                            <ul></ul>
                                        </div>
                                        <input type='hidden' name='service_id' id="service_id" value="{{ $serviceDetails->id }}">
                                        <div class="form-group row">
                                            <label for="subservic_eng" class="col-md-4 col-form-label">{{ __('messages.name in english') }} <span class="text-danger"> * </span></label>
                                            <div class="col-md-8">
                                                <input type='hidden' name="language" value="1">
                                                <input id="subservic_eng" type="text" class="form-control @error('service') is-invalid @enderror" name="service_name_english" required autocomplete="service" autofocus>
                                                @error('service')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="subservic_arb" class="col-md-4 col-form-label">{{ __('messages.name in arabic') }} <span class="text-danger"> * </span></label>
                                            <div class="col-md-8">
                                                <input type='hidden' name="language" value="2">
                                                <input id="subservic_arb" type="text" class="form-control @error('service') is-invalid @enderror" name="service_name_arabic" required autocomplete="service" autofocus>
                                                @error('service')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="subservic_hind" class="col-md-4 col-form-label">{{ __('messages.name in hindi') }} <span class="text-danger"> * </span></label>
                                            <div class="col-md-8">
                                                <input type='hidden' name="language" value="3">
                                                <input id="subservic_hind" type="text" class="form-control @error('service') is-invalid @enderror" name="service_name_hindi" required autocomplete="service" autofocus>
                                                @error('service')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="image" class="col-md-4 col-form-label">{{ __('messages.image') }}<span class="text-danger"> * </span></label>
                                            <div class="col-md-8">
                                                <span class="text-danger">(Max Image dimension width:50 x height:50 pixel, Max: 1MB)</span>
                                                <div class="form-group">
                                                    <input id="image" type="file" name="image" value="" class="form-control h-100">
                                                </div>
                                                {{-- <input id='fileid' type='file' name="image" hidden /> --}}
                                                @error('image')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- <div class="form-group row">
                                            <div class="col-xs-8 col-sm-8 col-md-8">
                                                <div class="form-group">
                                                    <strong> {{ __('messages.image') }} </strong>
                                                    <span class="text-danger">(Max Image dimension width:50 x height:50 pixel, Max: 1MB)</span>
                                                    <input type="file" name="image" value="" class="form-control">
                                                </div>
                                            </div> --}}
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary btn-subservice" data-id="{{ $serviceDetails->id }}">Submit</button>
                                    <button type="button" class="btn btn-close" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-content">
                        <div class="alert alert-success print-message-msg" style="display:none" id="subserv_message"></div>
                        <div class="table-responsive">
                            <div class="d-flex justify-content-between">
                                <h6>{{ __('messages.sub service') }}</h6>
                                <button type="button" class="btn addpackage subservice" data-id="{{ $serviceDetails->id }}" data-toggle="modal" data-target="#subserviceModal{{ $serviceDetails->id }}"><b>{{ __('messages.add sub service') }}</b></button>
                            </div>
                            {{-- <table class="table table-condensed table-hover"> --}}
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('messages.service') }}</th>
                                        <th>{{ __('messages.image') }}</th>
                                        <th>{{ __('messages.status') }}</th>
                                        <th class="action-icon">{{ __('messages.action') }}</th>
                                </thead>
                                <tbody>
                                    @php $SL = 1 @endphp
                                    @forelse ($services as $row)
                                        <tr>
                                            <td>{{ $SL++ }}</td>
                                            <td>{{ $row->service }}</td>
                                            <td class="list-table-main-image">
                                                @if ($row->image != '')
                                                    <img src="{{ asset('/assets/uploads/service/' . $row->image) }}" alt="{{ $row->service }}">
                                                @else
                                                    <img src="{{ asset('img/no-image.jpg') }}" alt="profile image" />
                                                @endif
                                            </td>
                                            <td>{{ $row->status }}</td>
                                            <td>
                                                @if ($row->status != 'deleted')
                                                    <form action="{{ route('services.destroy', $row->id) }}" title="Delete Subservice" method="POST">
                                                        @csrf
                                                        <a href="{{ route('services.view', $row->id) }}" title="View Subservice" class="btn btn-warning"><i class="fas fa-eye"></i></a>

                                                        <a href="{{ route('services.edit', $row->id) }}" title="Edit Subservice" class="btn btn-warning"><i class="fas fa-pen"></i></a>
                                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Do you really want to delete?')"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-danger text-center">No records found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{-- </div> --}}
                </div>
                {{-- end subserivce --}}
                {{-- edit document --}}
                <div class="preview col-md-12 mt-4">
                    {{-- <div class="preview col-md-12"> --}}
                    <div class="tab-content">
                        <div class="table-responsive">
                            <div class="alert alert-success print-message-msg" style="display:none" id="doc_message"></div>
                            <div class="alert alert-success print-message-msg" style="display:none" id="error_message"></div>
                            @if (session('success'))
                                {{-- <div x-data="{show: true}" x-init="setTimeout(() => show = false, 3000)" x-show="show"> --}}
                                <div class="alert alert-success">
                                    <ul>
                                        <li>{{ session('success') }}</li>
                                    </ul>
                                </div>
                            @endif
                            <div class="d-flex justify-content-between">
                                <h6>{{ __('Documents') }}</h6>
                                <button type="button" class="btn  addpackage add_document" data-id="{{ $serviceDetails->id }}" data-toggle="modal" data-target="#exampledocumentModal{{ $serviceDetails->id }}">{{ __('messages.add document') }}</button>
                                {{-- <i class="fa fa-telegram"></i> --}}
                            </div>
                            {{-- <table class="table table-condensed table-hover"> --}}
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Document') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $SL = 1 @endphp
                                    @forelse ($documents as $documents_Row)
                                        <tr>
                                            <td>{{ $SL++ }}</td>
                                            <td>{{ $documents_Row->document }}</td>
                                            <td>
                                                <form action="{{ route('document.destroy', $documents_Row->id) }}" method="POST">
                                                    @csrf
                                                    <button type="button" title="Edit Document" class="btn btn-warning editdocument" data-id="{{ $documents_Row->id }}" data-toggle="modal" data-target="#documentModaledit{{ $documents_Row->id }}" title="Edit Document"><i class="fas fa-pen"></i></button>
                                                    <button type="submit" title="Delete Document" class="btn btn-danger" onclick="return confirm('Do you really want to delete?')"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </td>
                                            <div class="modal fade edit_doc" id="documentModaledit{{ $documents_Row->id }}" tabindex="-1" role="dialog" aria-labelledby="documentModaleditLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content" style="width:700px">

                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="documentModaleditLabel">Update Document</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form>
                                                                <div class="alert alert-danger print-error-msg" style="display:none">
                                                                    <ul></ul>
                                                                </div>
                                                                <input type='hidden' name='service_id' id="service_id" value="{{ $serviceDetails->id }}">
                                                                <input type='hidden' name='document_id' id="document_id" value="{{ $documents_Row->id }}">
                                                                <div class="form-group">
                                                                    <label for="document_edit" class="col-form-label">{{ __('Document') }} <span class="text-danger"> * </span></label>
                                                                    <input id="document_edit_{{ $documents_Row->id }}" type="text" class="form-control @error('document') is-invalid @enderror" name="document" value="{{ $documents_Row->document }}" required autocomplete="document" autofocus>
                                                                    @error('document')
                                                                        <span class="invalid-feedback" role="alert">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="submit" class="btn btn-primary btn-document-edit" data-id="{{ $documents_Row->id }}">Submit</button>
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-danger text-center">No records found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{-- </div> --}}
                </div>
                {{-- end document --}}
                {{-- add document --}}
                <div class="modal fade add_doc" id="exampledocumentModal{{ $serviceDetails->id }}" tabindex="-1" role="dialog" aria-labelledby="exampledocumentModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content" style="width:700px">

                            <div class="modal-header">
                                <h5 class="modal-title" id="exampledocumentModalLabel">{{ __('Documents') }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body p-5">
                                <form>
                                    <div class="alert alert-danger print-error-msg" style="display:none">
                                        <ul></ul>
                                    </div>
                                    <input type='hidden' name='service_id' id="service_id" value="{{ $serviceDetails->id }}">
                                    <div class="form-group row">
                                        <label for="document_add" class="col-md-4 col-form-label">{{ __('messages.add document') }} <span class="text-danger"> * </span></label>
                                        <div class="col-md-8">
                                            <input id="document_add" type="text" class="form-control @error('document') is-invalid @enderror" name="document" value="{{ old('document') }}" required autocomplete="document" autofocus>
                                            @error('document')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary btn-documents" data-id="{{ $serviceDetails->id }}">Submit</button>
                                <button type="button" class="btn btn-close" data-dismiss="modal">Close</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
                {{-- end document --}}
                <div class="col-12 back_btn justify-content-end mt-4">
                    <a class="btn btn-primary" href="{{ route('admin.services') }}" title="{{ __('messages.Back to customers') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i> Back</a>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>

@endsection
@section('footer_scripts')
    <script>
        $(document).on('click', '.addpackage', function() {
            var service_id = $(this).attr('data-id');
            $('#service').val(service_id);
        });
        $(document).on('click', '.subservice', function() {
            var service_id = $(this).attr('data-id');
            $('#service').val(service_id);
        });
        $(document).on('click', '.add_document', function() {
            var service_id = $(this).attr('data-id');
            $('#service').val(service_id);
        });

        // {{-- TO EMPTY THE FIELDS --}}
        function myFunction() {
            document.getElementById("myForm").reset();
        }
        // {{-- ENDS --}}
        // {{-- ADD PACKAGES --}}

        $(document).on("click", ".btn-submit", function(e) {
            e.preventDefault();
            var package_in_english = $("#package_eng").val();
            var package_in_arabic = $("#package_arb").val();
            var package_in_hindi = $("#package_hind").val();
            var description_in_english = $("#description1").val();
            var description_in_arabic = $("#description2").val();
            var description_in_hindi = $("#description3").val();
            var amount = $("#amount_id").val();
            var offer_price = $("#offer_price_id").val();
            var service_id = $(this).attr("data-id");
            var validity = $('#validity').val();
            var taxes = new Array();
            $(".checkboxClass:checked").each(function() {
                taxes.push($(this).val());
            });
            // alert(checkbox_edit);
            $.ajax({
                type: 'POST',
                url: "{{ route('store.packages') }}",
                data: {
                    // tax_ids :tax_ids
                    service_id: service_id,
                    package_in_english: package_in_english,
                    package_in_arabic: package_in_arabic,
                    package_in_hindi: package_in_hindi,
                    description_in_english: description_in_english,
                    description_in_arabic: description_in_arabic,
                    description_in_hindi: description_in_hindi,
                    validity: validity,
                    amount: amount,
                    offer_price: offer_price,
                    taxes: taxes,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    if ($.isEmptyObject(data.error)) {
                        $('.add_pack').modal('hide');
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                        $("#package_message").html(data.success);
                        $("#package_message").show();

                    } else {
                        printErrorMsg(data.error);
                    }
                }
            });
        });

        function printErrorMsg(msg) {
            $(".print-error-msg").find("ul").html('');
            $(".print-error-msg").css('display', 'block');
            $.each(msg, function(key, value) {
                $(".print-error-msg").find("ul").append('<li>' + value + '</li>');
            });
        }
        // {{-- ENDS --}}
        // {{-- EDIT PACKAGES --}}

        $(document).on("click", ".btn-edit", function(e) {
            e.preventDefault();
            var package_id = $(this).attr("data-id");
            var package_in_english = $("#package_english_" + package_id).val();
            var package_in_arabic = $("#package_arbic_" + package_id).val();
            var package_in_hindi = $("#package_hindi_" + package_id).val();
            var description_in_english = $("#description_eng_" + package_id).val();
            var description_in_arabic = $("#description_arb_" + package_id).val();
            var description_in_hindi = $("#description_hind_" + package_id).val();
            var amount = $("#amounts_" + package_id).val();
            var validity = $("#validitys_" + package_id).val();
            var tax_ids = $("#taxes_" + package_id).val();
            var offer_price = $("#offerprices_" + package_id).val();
            var taxes = new Array();
            $(".checkbox_edit_" + package_id + ":checked").each(function() {
                taxes.push($(this).val());
            });
            $.ajax({
                type: 'POST',
                url: "{{ route('update.package') }}",
                data: {
                    package_id: package_id,
                    package_in_english: package_in_english,
                    package_in_arabic: package_in_arabic,
                    package_in_hindi: package_in_hindi,
                    description_in_english: description_in_english,
                    description_in_arabic: description_in_arabic,
                    description_in_hindi: description_in_hindi,
                    validity: validity,
                    amount: amount,
                    offer_price: offer_price,
                    taxes: taxes,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    if ($.isEmptyObject(data.error)) {
                        $("#pack_edit_message").html(data.success);
                        $("#pack_edit_message").show();
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                        $('.edit_pack').modal('hide');
                    } else {
                        printErrorMsg(data.error);
                    }
                }
            });
        });

        function printErrorMsg(msg) {
            $(".print-error-msg").find("ul").html('');
            $(".print-error-msg").css('display', 'block');
            $.each(msg, function(key, value) {
                $(".print-error-msg").find("ul").append('<li>' + value + '</li>');
            });
        }
        // {{-- ENDS --}}
        // {{-- ADD SUBSERVICES --}}

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $(document).on("click", ".btn-subservice", function(e) {
                e.preventDefault();
                var formData = new FormData($('#subserviceForm')[0]);
                $.ajax({
                    type: "POST",
                    url: "{{ route('sub_services.store') }}",
                    cache: false,
                    // dataType: 'json',
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function(response) {
                        if (response.status == 400) {
                            $('#save_errorlist').html("");
                            $('#save_errorlist').removeClass('d-none');
                            $.each(response.errors, function(key, err_value) {
                                $('#save_errorlist').append('<li>' + err_value + '</li>')
                            });
                        } else if (response.status == 200) {
                            $('#save_errorlist').html("");
                            $('#save_errorlist').addClass('d-none');
                            $('#subserviceForm').find('input').val('');
                            $("#subserv_message").html(response.message);
                            $("#subserv_message").show();
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                            $('.add_serv').modal('hide');
                        }
                    }
                });
            });
        });
        // {{-- ENDS --}}
        // {{-- ADD DOCUMENTS --}}

        $(document).on("click", ".btn-documents", function(e) {
            e.preventDefault();
            var document = $("#document_add").val();
            var service_id = $(this).attr("data-id");
            $.ajax({
                type: 'POST',
                url: "{{ route('store.document') }}",
                data: {
                    service_id: service_id,
                    document: document,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    if ($.isEmptyObject(data.error)) {
                        // window.location.reload();
                        $("#error_message").html(data.success);
                        $("#error_message").show();
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                        $('.add_doc').modal('hide');
                    } else {
                        printErrorMsg(data.error);
                    }
                }
            });
        });

        function printErrorMsg(msg) {
            $(".print-error-msg").find("ul").html('');
            $(".print-error-msg").css('display', 'block');
            $.each(msg, function(key, value) {
                $(".print-error-msg").find("ul").append('<li>' + value + '</li>');
            });
        }
        // {{-- ENDS --}}
        // {{-- EDIT DOCUMENTS --}}

        $(document).on("click", ".btn-document-edit", function(e) {
            e.preventDefault();
            var document_id = $(this).attr("data-id");
            var document = $("#document_edit_" + document_id).val();
            // alert(document);
            $.ajax({
                type: 'POST',
                url: "{{ route('update.document') }}",
                data: {
                    document_id: document_id,
                    document: document,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    if ($.isEmptyObject(data.error)) {
                        // location.reload();
                        $("#doc_message").html(data.success);
                        $("#doc_message").show();
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                        $('.edit_doc').modal('hide');
                    } else {
                        printErrorMsg(data.error);
                    }
                }
            });
        });

        function printErrorMsg(msg) {
            $(".print-error-msg").find("ul").html('');
            $(".print-error-msg").css('display', 'block');
            $.each(msg, function(key, value) {
                $(".print-error-msg").find("ul").append('<li>' + value + '</li>');
            });
        }
        // {{-- ENDS --}}
    </script>
@endsection
