<div class="grid-x tabs-wrap">
    <div class="small-12 cell">
        <ul class="tabs-list" data-tabs id="tabs">
            <li class="tabs-title is-active">
                <a href="#tab-general" aria-selected="true">Общая информация</a>
            </li>
            <li class="tabs-title">
                <a data-tabs-target="tab-photos" href="#tab-photos">Слайдер</a>
            </li>
            <li class="tabs-title">
                <a data-tabs-target="tab-prices_goods" href="#tab-prices_goods">Товары</a>
            </li>
            <li class="tabs-title">
                <a data-tabs-target="tab-triggers" href="#tab-triggers">Триггеры</a>
            </li>
        </ul>
    </div>
</div>

<div class="grid-x tabs-wrap inputs">
    <div class="small-12 cell tabs-margin-top">
        <div class="tabs-content" data-tabs-content="tabs">

            <div class="tabs-panel is-active" id="tab-general">
                @include('system.pages.marketings.promotions.tabs.general')
            </div>

            <div class="tabs-panel" id="tab-photos">
                @include('system.pages.marketings.promotions.tabs.photos')
            </div>

            <div class="tabs-panel" id="tab-prices_goods">
                @include('system.pages.marketings.promotions.tabs.prices_goods')
            </div>

            <div class="tabs-panel" id="tab-triggers">
                @include('system.pages.marketings.promotions.tabs.triggers')
            </div>
        </div>
    </div>
</div>

@push('scripts')
    @include('includes.scripts.inputs-mask')
@endpush
