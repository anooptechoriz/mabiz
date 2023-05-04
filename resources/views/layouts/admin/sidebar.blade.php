<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    {{-- <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('admin') }}">
        <img src="{{ asset('img/logo.png') }}" width="100%">
    </a> --}}

    <!-- Divider -->
    {{-- <hr class="sidebar-divider my-0"> --}}

    <!-- Nav Item - Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.Index') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>{{ __('messages.dashboard') }}</span></a>
    </li>

    <!-- Divider -->
    {{-- <hr class="sidebar-divider"> --}}

    <!-- Heading -->

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.list') }}">
            <i class="fa fa-user" aria-hidden="true"></i>
            <span>{{ __('messages.administrators') }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.homesliders') }}">
            <i class="fas fa-sliders-h" aria-hidden="true"></i>
            <span>{{ __('Home slider') }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.services') }}">
            <i class="fas fa-cog"></i>
            <span>{{ __('messages.services') }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.customers') }}">
            <i class="fa fa-users" aria-hidden="true"></i>
            <span>{{ __('messages.customers') }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.service_man') }}">
            <i class="fas fa-users-cog"></i>
            <span>{{ __('Service Man') }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.coupons') }}">
            <i class="fas fa-credit-card" aria-hidden="true"></i>
            <span>{{ __('Coupon') }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.taxes') }}">
            <i class="fas fa-percentage"></i>
            <span>{{ __('Tax') }}</span>
        </a>
    </li>
     <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.cities') }}">
            <i class="fas fa-percentage"></i>
            <span>{{ __('Region') }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.order') }}">
            <i class="fas fa-bars"></i>
            <span>{{ __('Orders') }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.subscription') }}">
            <i class="fas fa-briefcase"></i>
            <span>{{ __('Subscriptions')}}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.reported.customer') }}">
            <i class="fas fa-briefcase"></i>
            <span>{{ __('Reported Customers')}}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.settings') }}">
            <i class="fa fa-tools" aria-hidden="true"></i>
            <span>{{ __('messages.general settings') }}</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#Reports" aria-expanded="true" aria-controls="Reports">
            <i class="fas fa-file-alt"></i>
            <span>Reports</span>
        </a>
        <div id="Reports" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.reports.customer') }}" >
                    <i class="fas fa-grip-horizontal"></i>
                        <span>Customer Reports</span>
                </a>
            </div>
        </div>
        <div id="Reports" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.reports.serviceman') }}" >
                    <i class="fas fa-grip-horizontal"></i>
                        <span>Serviceman Reports</span>
                </a>
            </div>
        </div>
        <div id="Reports" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.reports.subscription') }}" >
                    <i class="fas fa-grip-horizontal"></i>
                        <span>Subscription Reports</span>
                </a>
            </div>
        </div>
    </li>

    {{-- <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.testimonials') }}">
            <i class="fa fa-comment" aria-hidden="true"></i>
            <span>Testimonials</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.careers') }}">
            <i class="fa fa-briefcase" aria-hidden="true"></i>
            <span>Careers</span>
        </a>

    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.news') }}">
            <i class="fas fa-newspaper"></i>
            <span>News And Events</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.customers') }}">
            <i class="fa fa-users"></i>
            <span>Customers</span>
        </a>
    </li>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.stores') }}">
            <i class="fas fa-bars"></i>
            <span>Stores</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.orders') }}">
            <i class="fas fa-bars"></i>
            <span>Orders</span>
        </a>
    </li> --}}
    {{-- <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.promotionbanner') }}">
            <i class="fas fa-ad"></i>
            <span>Promotion Banners</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#Reports" aria-expanded="true" aria-controls="Reports">
            <i class="fas fa-file-alt"></i>
            <span>Reports</span>
        </a>
        <div id="Reports" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.reports.product') }}" >
                    <i class="fas fa-grip-horizontal"></i>
                        <span>Product Reports</span>
                </a>
            </div>
        </div>
        <div id="Reports" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.reports.order') }}" >
                    <i class="fas fa-grip-horizontal"></i>
                        <span>Order Reports</span>
                </a>
            </div>
        </div>
        <div id="Reports" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.reports.sales') }}" >
                    <i class="fas fa-grip-horizontal"></i>
                        <span>Sales Reports</span>
                </a>
            </div>
        </div>
    </li> --}}



    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
