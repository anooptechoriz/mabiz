<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Navbar -->
    {{-- <div>
        {{__('messages.language')}} : <select onchange="changeLanguage(this.value)" id="language_drop">
            @foreach ($site_languages as $row)
            <option {{session()->has('lang_code')?(session()->get('lang_code')==$row->shortcode?'selected':''):''}} value="{{$row->shortcode}}">{{$row->language}}</option>
            @endforeach

        </select>
    </div> --}}
    <ul class="navbar-nav ml-auto">

        <!-- Nav Item - Search Dropdown (Visible Only XS) -->


        <li class="nav-item dropdown no-arrow d-sm-none">
            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i>
            </a>
            <!-- Dropdown - Messages -->
            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
                <form class="form-inline mr-auto w-100 navbar-search">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>


        {{-- @if ($row->image != '')
<img src="{{ asset('/assets/uploads/service/' . $row->image) }}" alt="{{ $row->service }}">
@else
<img src="{{ asset('img/no-image.jpg') }}" alt="profile image" />
@endif --}}
        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">

            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ ucfirst(Auth::user()->name) }}
                    {{-- {{ Auth::user()->name }}</span> --}}
                    @if (Auth::user()->profile_pic != '')
                        <img class="img-profile rounded-circle" src="{{ asset('assets/uploads/admin_profile/') }}/{{ Auth::user()->profile_pic }}">
                    @else
                        <img class="img-profile rounded-circle" src="{{ asset('img/no-image.jpg') }}">
                    @endif
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="{{ route('admin.profile') }}">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    {{ __('messages.profile') }}
                </a>

                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('admin.logout') }}" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    {{ __('messages.logout') }}
                </a>
            </div>
        </li>


    </ul>

</nav>
