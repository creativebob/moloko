{{-- Заголовок и фильтры --}}
<div data-sticky-container id="head-content">
    <div class="sticky sticky-topbar" id="head-sticky" data-sticky data-margin-top="2.4" data-sticky-on="small" data-top-anchor="head-content:top">
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
                @if(count(request()->input())) filtration-active @endif
                    "></a>
{{--                <input class="search-field" type="search" id="search_field" name="search_field" placeholder="Поиск" />--}}
                {{-- <button type="button" class="icon-search sprite button"></button> --}}

            </div>


        </div>



        <div id="port-result-search">
        </div>
        {{-- Подключаем стандартный ПОИСК --}}
        @include('includes.scripts.search-script')

        {{-- Блок фильтров --}}
        <div class="grid-x">
            <div class="small-12 cell filters fieldset-filters" id="filters">
                <div class="grid-padding-x">
                    <div class="small-12 cell text-right">
                        <a href="{{ route('prices_goods.index', ['catalog_id' => $catalogGoods->id]) }}" class="small-link">Сбросить</a>
                    </div>
                </div>
                <div class="grid-padding-x">
                    <div class="small-12 cell">
                        {{ Form::open(['route' => ['prices_goods.index', $catalogGoods->id], 'data-abide', 'novalidate', 'name'=>'filter', 'method'=>'GET', 'id' => 'filter-form', 'class' => 'grid-x grid-padding-x inputs']) }}

                        @includeIf('system.pages.catalogs.goods.prices_goods.includes.filters')

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

            {{-- Дополнительные кнопки приходящие с контроллера --}}
            <div class="black-button-group small-12 cell">
                @if(isset($add_buttons))
                    @foreach($add_buttons as $add_button)
                        <a class="button tiny hollow right {{ $add_button['class'] }}" href="{{ $add_button['href'] }}">{{ $add_button['text'] }}</a>
                    @endforeach
                @endif
            </div>


        </div>
    </div>
</div>