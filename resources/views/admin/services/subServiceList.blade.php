<ul>
    @foreach ($subservices as $subservice)
        <li>
            <a href="javascript:void(0)" id="subservice" class="service_items  @if ($subservice->id == $old_sub_service) {{ 'active' }} @endif" data_item="{{ $subservice->id }}">{{ ucfirst($subservice->service) }}</a>
            @if (count($subservice->subservicesArray($subservice->id)))
                @include('admin.services.subServiceList', ['subservices' => $subservice->subservicesArray($subservice->id), 'old_sub_service' => $old_sub_service])
            @endif
        </li>
    @endforeach
</ul>
