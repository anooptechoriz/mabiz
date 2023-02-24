@extends('layouts.admin.index')
@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ __('messages.dashboard') }}</h1>
        </div>
        <!-- Content Row -->
        <div class="row">
            <!--  -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Services</div>
                                <div class="h5 mb-0 font-weight-bold text-primary"></i>{{ number_format($total_services) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--  -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Service man Count</div>
                                <div class="h5 mb-0 font-weight-bold text-primary"></i>{{ number_format($total_serviceman) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Customers
                                </div>
                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <div class="h5 mb-0 font-weight-bold text-primary"></i>{{ number_format($total_customers) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Active Subscriptions</div>
                                <div class="h5 mb-0 font-weight-bold text-primary"></i>{{ number_format($subscriptions) }}</div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- -->
        </div>
        <div class="row">
            <!-- Area Chart -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Orders Graph <small>{{ isset($_GET['GraphType']) ? ucwords($_GET['GraphType']) : 'Daily' }} Wise</small></h6>
                        <div class="dropdown no-arrow">
                            <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                            </a>
                            <form id="OrdersGraph" method="get">
                                <input type="hidden" name="GraphType" id="GraphType" value="0">
                            </form>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                <a class="dropdown-item Selected_GraphType" href="javascript:void(0)" data-type="daily">Daily</a>
                                <a class="dropdown-item Selected_GraphType" href="javascript:void(0)" data-type="weekly">Weekly</a>
                                <a class="dropdown-item Selected_GraphType" href="javascript:void(0)" data-type="monthly">Monthly</a>
                            </div>
                        </div>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="myAreaChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Pie Chart -->
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Most subscribed 3 services</h6>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <div class="chart-pie pt-4 pb-2">
                            <canvas id="myPieChart"></canvas>
                        </div>
                        <div class="mt-4 text-center small">
                            @php $count=0; @endphp
                            @foreach ($services as $key => $row)
                                {{-- @php $count++; @endphp --}}
                                <span class="mr-2">
                                    <i class="fas fa-circle"style="color:{{$row->color}}"> {{$row->service}}</i>
                                    {{-- {{ $key }} --}}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Content Row -->
        <div class="row">
            <!-- Content Column -->
            <div class="col-lg-6 mb-4">
                <!-- Project Card Example -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Latest Purchases<small>(New 10 purchases)</small> </h6>
                    </div>
                    <div class="card-body admin-order-hist">
                        @forelse($subscribedservices as $orders_row)
                            {{-- <a href="{{ route('admin.orderhistory.details', $orders_row->id) }}"> --}}
                            <div class="col-md-12 order-home">
                                <div class="row">
                                    <div class="order-from mr-3"> <a href="{{ route('services.view', $orders_row->service_id) }}">{{ ucfirst($orders_row->service) }}</a>
                                        | {{ ucfirst($orders_row->firstname) }} | {{ date('d-m-Y', strtotime($orders_row->subscription_date)) }}<small>(subscription date)</small></div>
                                </div>
                            </div>
                            {{-- </a> --}}
                        @empty
                            <li>
                                <p>There are no active Purchases</p>
                            </li>
                        @endforelse
                        <div class="view-butn"><a href="{{ route('admin.subscription') }}" class="viewall-butn">View All</a></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <!-- Illustrations -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Upcoming Renewals<small>(New 10 Renewals)</small> </h6>
                    </div>
                    <div class="card-body admin-order-hist">
                        @forelse($upcoming_renewals as $row)
                            {{-- <a href="{{ route('admin.orderhistory.details', $orders_row->id) }}"> --}}
                            <div class="col-md-12 order-home">
                                <div class="row">
                                    <div class="order-from mr-3">
                                        <a href="{{ route('services.view', $row->service_id) }}">{{ ucfirst($row->service) }}</a>
                                        | {{ ucfirst($row->firstname) }} | {{ date('d-m-Y', strtotime($row->expiry_date)) }}<small>(Expiry date)</small></div>
                                </div>
                            </div>
                            {{-- </a> --}}
                        @empty
                            <li>
                                <p>There are no active Purchases</p>
                            </li>
                        @endforelse
                        <div class="view-butn"><a href="{{ route('admin.services') }}" class="viewall-butn">View All</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer_scripts')
    <script>
        //----Pie chart

        var chartData = JSON.parse(`<?php echo $chart_data; ?>`);

        // var total_services = {{ $total_services }};
        // var service_man = {{ $total_serviceman }};
        // var subscription = {{ $subscriptions }};

        //----Orders line Graph--
        var graph_labels = [];
        var graph_data = [];
        @foreach ($GraphDataArray as $key => $GraphDataArray_row)
            graph_labels.push('{{ $key }}');
            graph_data.push('{{ $GraphDataArray_row }}');
        @endforeach
        $('.Selected_GraphType').on('click', function() {
            var graphType = $(this).attr('data-type');
            $('#GraphType').val(graphType);
            $('#OrdersGraph').submit();
        });
    </script>
    <script src="{{ asset('sb_admin/js/sb-admin-2.min.js') }}"></script>
    <!-- Page level plugins -->
    <script src="{{ asset('sb_admin/chart.js/Chart.min.js') }}"></script>
    <!-- Page level custom scripts -->
    <script src="{{ asset('sb_admin/js/demo/chart-area-demo.js') }}"></script>
    <script src="{{ asset('sb_admin/js/demo/chart-pie-demo.js') }}"></script>
@endsection
