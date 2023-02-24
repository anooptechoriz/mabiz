@extends('layouts.admin.index')
@section('content')
    <div class="container-fluid">
        <div class="col-md-8 m-auto">
            <div class="card shadow mb-4 my-5">
                <div class="card-body">
                    <h1 class="h4 mb-3 text-gray-800">Home Sliders</h1>
                    <div id="carouselExampleCaptions" class="carousel slide" data-ride="carousel">
                        <ol class="carousel-indicators">
                            @php $count = 0; @endphp
                            @foreach ($slider_images as $images)
                                <li data-bs-target="#carouselExampleCaptions" data-slide-to="0" @if ($count == 0) {{ 'class="active"' }} @endif></li>
                                @php $count++; @endphp
                            @endforeach
                        </ol>
                        <div class="carousel-inner home-slider">
                            @php $count = 0; @endphp
                            @foreach ($slider_images as $images)
                                <div class="carousel-item @if ($count == 0) {{ 'active' }} @endif">
                                {{-- <div class="carousel-item @if($count == 0) {{ 'active' }} @elseif($count == 0) @endif"> --}}
                                    @if ($images->image != '')
                                        <img src="{{ asset('/assets/uploads/homesliders/' . $images->image) }}" class="img-thumbnail w-100" />
                                    @endif
                                    <div class="carousel-caption d-none d-md-block">
                                        {{-- <h3 class="product-title">{{ $images->type }}</h3> --}}
                                        {{-- <h5>Title: {{ $images->name}}</h5> --}}
                                        <h6>Image Title: {{ $images->title }}</h6>
                                        <h6>Description: {{ $images->description }}</h6>
                                        <h6>Image Target: {{ $images->target }}</h6>
                                        {{-- @if ($images->target) --}}
                                        {{-- <a href="{{$images->target}}">Readmore</a> --}}
                                        {{-- @endif --}}
                                    </div>
                                </div>
                                @php $count++; @endphp
                            @endforeach

                        </div>
                        <a class="carousel-control-prev" href="#carouselExampleCaptions" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            {{-- <span class="visually-hidden">Previous</span> --}}
                        </a>
                        <a class="carousel-control-next" href="#carouselExampleCaptions" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            {{-- <span class="visually-hidden">Next</span> --}}
                        </a>
                    </div>
                    <div class="back_btn w-100 justify-content-end mt-4">
                        <a class="btn btn-primary" href="{{ route('admin.homesliders') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i> Back</a>
                    </div>
                </div>
            </div>
        </div>
    @endsection
