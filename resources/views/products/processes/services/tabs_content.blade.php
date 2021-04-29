{{-- Каталоги --}}
<div class="tabs-panel" id="tab-prices">
    <services-store-component></services-store-component>

    <div class="grid-x grid-padding-x tabs-margin-top">
        <div class="cell small-12">
            @include('products.processes.services.prices.prices')
        </div>
    </div>
</div>

{{-- Состав --}}
@if($process->kit)
    <div class="tabs-panel" id="tab-services">
        @include('products.processes.services.services.services')
    </div>
@else
{{--    @can('index', App\Workflow::class)--}}
{{--        <div class="tabs-panel" id="tab-workflows">--}}
{{--            @include('products.processes.services.workflows.workflows')--}}
{{--        </div>--}}
{{--    @endcan--}}
@endif

@can('index', App\Event::class)
    <div class="tabs-panel" id="tab-events">
        @include('products.processes.events.events.events')
    </div>
@endcan
