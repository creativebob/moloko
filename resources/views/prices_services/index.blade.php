@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $page_info->description }}" />
{{-- Скрипты таблиц в шапке --}}
@include('includes.scripts.tablesorter-inhead')
@endsection

@section('title', $page_info->name)

{{-- @section('breadcrumbs', Breadcrumbs::render('index', $page_info)) --}}

@section('content-count')
{{-- Количество элементов --}}
{{ $prices_services->isNotEmpty() ? num_format($prices_services->total(), 0) : 0 }}
@endsection

@section('title-content')
{{-- Таблица --}}
{{-- Заголовок и фильтры --}}
<div data-sticky-container id="head-content">
    <div class="sticky sticky-topbar" id="head-sticky" data-sticky data-margin-top="2.4" data-sticky-on="small" data-top-anchor="head-content:top">
        <div class="top-bar head-content">
            <div class="top-bar-left">
                <h2 class="header-content">{{ $page_info->title }}
                    <span class="content-count" title="Общее количество">
                        {{ $prices_services->isNotEmpty() ? num_format($prices_services->total(), 0) : 0 }}
                    </span>
                </h2>

                @can('create', App\PricesService::class)

                {{ link_to_route($page_info->alias.'.create', '', $parameters = ['catalog_id' => $catalog_id], $attributes = ['class' => 'icon-add sprite']) }}

                @endcan
            </div>
            <div class="top-bar-right">
                @if (isset($filter))
                <a class="icon-filter sprite @if ($filter['status'] == 'active') filtration-active @endif"></a>
                @endif

                <input class="search-field" type="search" id="search_field" name="search_field" placeholder="Поиск" />

                <button type="button" class="icon-search sprite button"></button>
            </div>

        </div>

        <div id="port-result-search">
        </div>
        {{-- Подключаем стандартный ПОИСК --}}
        @include('includes.scripts.search-script')

        {{-- Блок фильтров --}}
        @if (isset($filter))

        {{-- Подключаем класс Checkboxer --}}
        @include('includes.scripts.class.checkboxer')

        <div class="grid-x">
            <div class="small-12 cell filters fieldset-filters" id="filters">
                <div class="grid-padding-x">
                    <div class="small-12 cell text-right">
                        {{ link_to(Request::url() . '?filter=disable', 'Сбросить', ['class' => 'small-link']) }}
                    </div>
                </div>
                <div class="grid-padding-x">
                    <div class="small-12 cell">
                        {{ Form::open(['url' => Request::url(), 'data-abide', 'novalidate', 'name'=>'filter', 'method'=>'GET', 'id' => 'filter-form', 'class' => 'grid-x grid-padding-x inputs']) }}

                        @include($page_info->alias.'.filters')

                        <div class="small-12 cell text-center">
                            {{ Form::submit('Фильтрация', ['class'=>'button']) }}
                            <input hidden name="filter" value="active">
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

        @endif
    </div>
</div>
@endsection

@section('content')
{{-- Таблица --}}
<div class="grid-x">
    <div class="small-12 cell">

        <table class="content-table tablesorter" id="content" data-sticky-container data-entity-alias="prices_services">

            <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
                    <th class="td-name" data-serversort="name">Название</th>
                    <th class="td-catalogs_item">Пункт каталога</th>
                    <th class="td-sync">Синхронизация</th>
                    <th class="td-control"></th>
                    <th class="td-delete"></th>
                </tr>
            </thead>

            <tbody data-tbodyId="1" class="tbody-width">

                @if(isset($prices_services) && $prices_services->isNotEmpty())
                @foreach($prices_services as $prices_service)

                <tr class="item @if($prices_service->moderation == 1)no-moderation @endif" id="prices_services-{{ $prices_service->id }}" data-name="{{ $prices_service->name }}">
                    <td class="td-drop">
                        <div class="sprite icon-drop"></div>
                    </td>

                    <td class="td-checkbox checkbox">
                        <input type="checkbox" class="table-check" name="prices_service_id" id="check-{{ $prices_service->id }}"

                        {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
                        @if(!empty($filter['booklist']['booklists']['default']))
                        Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked
                        @if (in_array($prices_service->id, $filter['booklist']['booklists']['default'])) checked
                        @endif
                        @endif
                        ><label class="label-check" for="check-{{ $prices_service->id }}"></label>
                    </td>

                    <td class="td-name">
                        {{ $prices_service->service->process->name }}
                        {{-- @can('update', $prices_service)
                        {{ link_to_route('prices_services.edit', $prices_service->name, $parameters = ['id' => $prices_service->id], $attributes = []) }}
                        @endcan

                        @cannot('update', $prices_service)
                        {{ $prices_service->name }}
                        @endcannot

                        %5B%5D
                        ({{ link_to_route('goods.index', $prices_service->articles_count, $parameters = ['prices_service_id' => $prices_service->id], $attributes = ['class' => 'filter_link light-text', 'title' => 'Перейти на список артикулов']) }}) --}}

                    </td>
                    <td class="td-catalogs_item">{{ $prices_service->catalogs_item->name }}</td>
                    <td class="td-sync">
                        <div class="grid-x" id="sync-{{ $prices_service->id }}">

                            @template ($prices_service)

                            <div class="small-6 cell sync-price">
                                <label>Цена
                                    {!! Form::number('price', $prices_service->price, []) !!}
                                </label>
                            </div>
                            <div class="small-6 cell sync-button">
                                <button class="button button-sync">Синхронизировать</button>
                            </div>

                            @else

                            @include('prices_services.sync')

                            @endtemplate

                        </div>


                    </td>

                    {{-- Элементы управления --}}
                    {{-- @include('includes.control.table_td', ['item' => $prices_service]) --}}

                    <td class="td-delete">

                        @include('includes.control.item_delete_table', ['item' => $prices_service])

                    </td>
                </tr>

                @endforeach
                @endif

            </tbody>

        </table>
    </div>
</div>

{{-- Pagination --}}
<div class="grid-x" id="pagination">
    <div class="small-6 cell pagination-head">
        <span class="pagination-title">Кол-во записей: {{ $prices_services->count() }}</span>
        {{ $prices_services->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
    </div>
</div>
@endsection

@section('modals')
{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete')
@endsection

@push('scripts')
{{-- Скрипт чекбоксов, сортировки и перетаскивания для таблицы --}}
@include('includes.scripts.tablesorter-script')
@include('includes.scripts.sortable-table-script')

{{-- Скрипт отображения на сайте --}}
@include('includes.scripts.ajax-display')

{{-- Скрипт системной записи --}}
@include('includes.scripts.ajax-system')

{{-- Скрипт чекбоксов --}}
@include('includes.scripts.checkbox-control')

{{-- Скрипт модалки удаления --}}
@include('includes.scripts.modal-delete-script')

<script>

    $(document).on('click', '.button-sync', function(event) {
        event.preventDefault();

        let item = $(this);
        let id = item.closest('.item').attr('id').split('-')[1];
        let price = item.closest('.item').find('.sync-price [name=price]').val();
        // let entity_alias = item.closest('.item').attr('id').split('-')[0];

        $.post('/admin/catalogs_services/{{ $catalog_id }}/prices_services_sync', {
            id: id,
            price: price
        }, function(html) {
            // alert(html);
            $('#sync-' + id).html(html);

        });
    });

</script>
@endpush
