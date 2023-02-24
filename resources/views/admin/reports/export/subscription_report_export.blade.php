<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800 mt-5 mb-4">{{ __('Subscriptions') }}</h1>
    <div class="card shadow">
        <div class="card-body">


            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>

                            <th class="text-secondary text-xxs opacity-7 ">
                                <a href="javascript:void(0)" class="dataTable-sorter asc" data-name="service_name">Service</a>
                            </th>
                            <th class="text-secondary text-xxs opacity-7">
                                <a href="javascript:void(0)" class="dataTable-sorter" data-name="firstname">User</a>
                            </th>
                            <th class="text-secondary text-xxs opacity-7">
                                <a href="javascript:void(0)" class="dataTable-sorter" data-name="package_name">Package</a>
                            </th>
                            <th class="text-secondary text-xxs opacity-7">
                                <a href="javascript:void(0)" class="dataTable-sorter" data-name="subscription_date">Subscription date</a>
                            </th>
                            <th class="text-secondary text-xxs opacity-7">
                                <a href="javascript:void(0)" class="dataTable-sorter" data-name="expiry_date">Expiry date</a>
                            </th>
                            <th class="text-secondary text-xxs opacity-7">
                                <a href="javascript:void(0)" class="dataTable-sorter" data-name="status">Status</a>
                            </th>
                        </tr>
                    </thead>
                    @php $count=0; @endphp
                    @forelse ($subscription as $rows)
                        @php $count++; @endphp
                        <tr>
                            {{-- <td>{{ ($subscription->currentpage() - 1) * $subscription->perpage() + $count }}</td> --}}
                            <td>{{ $rows->service }}</td>
                            <td>{{ $rows->user }}</td>
                            <td>{{ $rows->package }}</td>
                            <td>{{ $rows->subscription_date }}</td>
                            <td>{{ $rows->expiry_date }}</td>
                            <td>{{ $rows->status }}</td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-danger text-center">No result found</td>
                        </tr>
                    @endforelse
                </table>
            </div>
            {{-- </div> --}}
        </div>
    </div>
</div>
