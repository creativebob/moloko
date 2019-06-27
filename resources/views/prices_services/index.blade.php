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
                <a class="icon-add sprite"></a>
                @endcan

            </div>
            <div class="top-bar-right">
                @isset($filter))
                <a class="icon-filter sprite @if ($filter['status'] == 'active') filtration-active @endif"></a>
                @endisset

                <label>Филиалы
                    @include('prices_services.select_user_filials')
                </label>

                <input class="search-field" type="search" id="search_field" name="search_field" placeholder="Поиск" />

                <button type="button" class="icon-search sprite button"></button>
            </div>

        </div>

        <div id="port-result-search">
        </div>
        {{-- Подключаем стандартный ПОИСК --}}
        @include('includes.scripts.search-script')

        {{-- Блок фильтров --}}
        @isset($filter))

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

        @endisset
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
                    <th class="td-price">Цена</th>
                    <th class="td-control"></th>
                    <th class="td-delete"></th>
                </tr>
            </thead>

            <tbody data-tbodyId="1" class="tbody-width">

                @if(isset($prices_services) && $prices_services->isNotEmpty())
                @foreach($prices_services as $prices_service)

                <tr class="item @if($prices_service->moderation == 1)no-moderation @endif" id="prices_services-{{ $prices_service->id }}" data-name="{{ $prices_service->catalogs_item->name }}">
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
                    <td class="td-price">
                        @include('prices_services.price', ['some' => 'data'])



                        {{-- <div class="grid-x" id="sync-{{ $prices_service->id }}">

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

                        </div> --}}


                    </td>

                    {{-- Элементы управления --}}
                    {{-- @include('includes.control.table_td', ['item' => $prices_service]) --}}

                    <td class="td-delete">

                        @can('delete', $prices_service)
                        <a class="icon-delete sprite" data-open="delete-price"></a>
                        @endcan

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

<div id="modals"></div>
@endsection

@section('modals')
@include('includes.modals.modal_price_delete')
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

<script>

    var catalog_id = '{{ $catalog_id }}';

    // При клике на создание открываем модалку синхронизации
    $(document).on('click', '.icon-add', function(event) {

        $.get('/admin/catalogs_services/' + catalog_id + '/prices_services/create', {
            filial_id: $('#select-user_filials').val()
        }, function(html) {
            $('#modals').html(html);
            hidePrices();
            $('#modal-sync-price').foundation().foundation('open');
        });
    });

    function hidePrices() {
        $('#table-prices tbody').each(function(index, el) {
            $(this).hide();
        });
    };

    $(document).on('click', '.item-catalog', function(event) {
        // alert('kek');

        hidePrices();
        let id = $(this).attr('id');
        $('#table-prices .' + id).show();
    });

    // Перезагружаем страницу при смене select'a филиалов для пользователя
    $(document).on('change', '#select-user_filials', function(event) {
        event.preventDefault();

        let fillial_id = $('#select-user_filials').val();
        let url = "prices_services?filial_id=" + fillial_id;
        $(location).attr('href',url);
    });

    // При клике на цену подставляем инпут
    $(document).on('click', '#content .td-price span', function(event) {
        event.preventDefault();

        var parent = $(this).closest('.item');
        var id = parent.attr('id').split('-')[1];

        $.get('/admin/catalogs_services/' + catalog_id + '/prices_services/' + id + '/edit', function(html) {
            $('#prices_services-' + id + ' .td-price').html(html);
        });
    });

    // При изменении цены ловим enter
    $(document).on('keydown', '#content .td-price [name=price]', function(event) {

        var parent = $(this).closest('.item');
        var id = parent.attr('id').split('-')[1];

        // если нажали Enter, то true
        if ((event.keyCode == 13) && (event.shiftKey == false)) {
            event.preventDefault();
            // event.stopPropagation();
            $.ajax({
                url: '/admin/catalogs_services/' + catalog_id + '/prices_services/' + id,
                type: "PATCH",
                data: {
                    price: $(this).val()
                },
                success: function(html){
                    $('#prices_services-' + id + ' .td-price').html(html);
                }
            });
        };
    });

    // При потере фокуса при редактировании возвращаем обратно
    $(document).on('focusout', '.td-price input[name=price]', function(event) {
        event.preventDefault();

        var parent = $(this).closest('.item');
        var id = parent.attr('id').split('-')[1];

        $.get('/admin/catalogs_services/' + catalog_id + '/get_prices_service/' + id, function(html) {
            $('#prices_services-' + id + ' .td-price').html(html);
        });
    });

    // Удаление ajax
    $(document).on('click', '[data-open="delete-price"]', function() {

        // находим описание сущности, id и название удаляемого элемента в родителе
        var parent = $(this).closest('.item');
        var id = parent.attr('id').split('-')[1];
        var name = parent.data('name');

        $('.title-price').text(name);
        $('#form-delete-price').attr('action', '/admin/catalogs_services/' + catalog_id + '/prices_services/' + id);
    });


</script>
@endpush
