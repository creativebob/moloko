{{-- Заголовок и фильтры --}}
<div data-sticky-container id="head-content">
    <div class="sticky sticky-topbar" id="head-sticky" data-sticky data-margin-top="2.4" data-sticky-on="small"
         data-top-anchor="head-content:top">
        <div class="top-bar head-content">
            <div class="top-bar-left">
                <h2 class="header-content">{{ $pageInfo->title }}
                    <span class="content-count" title="Общее количество">
                        @yield('content-count')
                    </span>
                </h2>

                {{-- Блок дополнительных кнопок --}}
                {{-- @can('create', $class)
                    <a class="icon-add sprite" data-open="modal-create" data-tooltip class="top" tabindex="2" title="Добавить позицию"></a>
                    <a href="/admin/{{ $pageInfo->alias}}_categories" class="icon-category sprite" data-tooltip class="top" tabindex="2" title="Настройка категорий"></a>
                    <a href="/admin/{{ $pageInfo->alias}}_consignments" class="icon-consignment sprite" data-tooltip class="top" tabindex="2" title="Накладные"></a>
                    <a href="/admin/stock_{{ $pageInfo->alias}}" class="icon-stock sprite" data-tooltip class="top" tabindex="2" title="Склад"></a>
                @endcan  --}}

            </div>
            <div class="top-bar-right">
                <a class="icon-filter sprite
                @if(!(count(request()->input()) == 1 && request()->input(['page'])) && count(request()->input())) filtration-active @endif
                    "></a>
                <search-prices-goods-component
                    :catalog-id="{{ $catalogGoods->id }}"
                ></search-prices-goods-component>
                {{-- <button type="button" class="icon-search sprite button"></button> --}}
            </div>
        </div>

        {{-- Блок фильтров --}}
        <div class="grid-x">
            <div class="small-12 cell filters fieldset-filters" id="filters">
                <div class="grid-padding-x">
                    <div class="small-12 cell text-right">
                        {!! Form::open(['route' => ['reset_filter', [$pageInfo->alias, 'catalog_id' => $catalogGoods->id]]]) !!}
                        {!! Form::submit('Сбросить', ['class'=>'small-link filter-reset']) !!}
                        {!! Form::close() !!}
                    </div>
                </div>
                <div class="grid-padding-x">
                    <div class="small-12 cell">
                        {{ Form::open(['route' => ['prices_goods.index', $catalogGoods->id], 'data-abide', 'novalidate', 'method'=>'GET', 'id' => 'filter-form', 'class' => 'grid-x grid-padding-x inputs']) }}

                        @include('system.pages.catalogs.goods.prices_goods.includes.filters')

                        <div class="small-12 cell text-center">
                            {{ Form::submit('Фильтрация', ['class'=>'button']) }}
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
                <div class="grid-x">
                    <a class="small-12 cell text-center filter-close">
                        <button type="button" class="icon-moveup sprite"></button>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
