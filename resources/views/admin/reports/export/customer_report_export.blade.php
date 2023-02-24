
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800 mt-5 mb-4">{{ __('messages.customers') }}</h1>
        <div class="card shadow">
            <div class="card-body">

                {{-- <div class="card-body"> --}}
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>

                            <tr>
                                <th class="text-secondary text-xxs opacity-7 ">
                                    <a href="javascript:void(0)" class="dataTable-sorter asc" data-name="firstname">Name</a>

                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="email">{{ __('messages.email') }}</a>

                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="gender">{{ __('messages.gender') }}</a>

                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="phone">Phone</a>

                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="dob">Date of birth</a>

                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="country_id">{{ __('messages.country') }}</a>

                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="about">About</a>

                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="state">State</a>

                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="region">Region</a>

                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="status">Status</a>

                                </th>
                                <th class="text-secondary text-xxs opacity-7">
                                    <a href="javascript:void(0)" class="dataTable-sorter" data-name="created_at">Created Time</a>

                                </th>
                            </tr>
                        </thead>
                        @php $count=0; @endphp
                        @forelse ($customers as $rows)
                            @php $count++; @endphp
                                <tr>
                                    {{-- <td>{{ ($customers->currentpage() - 1) * $customers->perpage() + $count }}</td> --}}

                                    <td>{{ $rows->firstname }}</td>
                                    <td>{{ $rows->email }}</td>
                                    <td>{{ $rows->gender }}</td>
                                    <td>{{ $rows->phone }}</td>
                                    <td>{{ date('d-m-Y', strtotime($rows->dob)) }}</td>
                                    <td>
                                        @foreach ($rows->country_details as $value)
                                            {{ $value->name }}{{ $loop->last ? '' : ',' }}
                                        @endforeach
                                    </td>
                                    <td>{{ $rows->about }}</td>
                                    <td>{{ $rows->state }}</td>
                                    <td>{{ $rows->region }}</td>
                                    <td>{{ $rows->status }}</td>
                                    <td>{{ $rows->created_at }}</td>

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

