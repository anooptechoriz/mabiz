<ul>
    @foreach ($subservices as $subservice)
        @if ($subservice->id != $services->id)
            <li>
                <a href="javascript:void(0)" id="subservice" class="service_items @if ($subservice->id == $service->parent_id) {{ 'active' }} @endif" data_item="{{ $subservice->id }}">{{ $subservice->service }}</a>
                @if (count($subservice->subservicesArray($subservice->id)) > 0)
                    @include('admin.services.subServiceListEdit', ['subservices' => $subservice->subservicesArray($subservice->id)])
                @endif
            </li>
        @endif
    @endforeach
</ul>
