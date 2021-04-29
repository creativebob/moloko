<li class="tabs-title">
    <a data-tabs-target="tab-prices" href="#tab-prices">Расположение в прайсах</a>
</li>

@if($process->kit)
    <li class="tabs-title">
        <a data-tabs-target="tab-services" href="#tab-services">Услуги</a>
    </li>
@else
{{--    @can('index', App\Workflow::class)--}}
{{--        <li class="tabs-title">--}}
{{--            <a data-tabs-target="tab-workflows" href="#tab-workflows">Рабочие процессы</a>--}}
{{--        </li>--}}
{{--    @endcan--}}
@endif

@can('index', App\Event::class)
    <li class="tabs-title">
        <a data-tabs-target="tab-events" href="#tab-events">События</a>
    </li>
@endcan
