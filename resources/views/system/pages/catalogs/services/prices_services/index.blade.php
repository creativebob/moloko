@extends('layouts.app')

@section('inhead')
    <meta name="description" content="{{ $pageInfo->description }}" />
@endsection

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('prices_services-index', $catalog_services, $pageInfo))

@section('content-count')
    {{-- Количество элементов --}}
    {{ $prices_services->isNotEmpty() ? num_format($prices_services->total(), 0) : 0 }}
@endsection

@section('title-content')

    {{-- Таблица --}}
{{--    @include('includes.title-content', ['pageInfo' => $pageInfo, 'class' => App\PricesService::class, 'type' => 'table'])--}}
    @include('system.pages.catalogs.services.prices_services.includes.title-prices_services', ['pageInfo' => $pageInfo, 'class' => $class])

@endsection

@section('content')

@if(isset($prices_services))
    <div class="grid-x">
        <div class="small-12 medium-12 large-12 extra-content cell">
            <div class="grid-x">
                <div class="small-12 medium-4 large-3 cell">
                </div>
                <div class="small-12 medium-4 large-3 cell">
                </div>
                <div class="small-12 medium-4 large-4 cell">
                </div>
                <div class="small-12 medium-4 large-2 cell text-right">
                    @include('system.pages.catalogs.services.prices_services.select_user_filials')
                </div>
            </div>
        </div>

    </div>
@endif

{{-- Таблица --}}
<div class="grid-x">
    <div class="small-12 cell">

        <table class="content-table tablesorter" id="content" data-sticky-container data-entity-alias="prices_services">

            <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
                    <th class="td-photo">Фото</th>
                    <th class="td-name" data-serversort="name">Название</th>
                    <th class="td-unit">Ед. измерения</th>
                    <th class="td-lenght">Продолжительность</th>
                    <th class="td-catalogs_item">Раздел прайса</th>
                    <th class="td-price">Цена</th>
                    <th class="td-points">Внут. вал</th>
                    <th class="td-price-status">Статус</th>
                    <th class="td-hit">Хит</th>
                    <th class="td-new">Новинка</th>
                    <th class="td-control"></th>
                    <th class="td-delete"></th>
                </tr>
            </thead>

            <tbody data-tbodyId="1" class="tbody-width">

                @if(isset($prices_services) && $prices_services->isNotEmpty())
                @foreach($prices_services as $prices_service)

                @include('system.pages.catalogs.services.prices_services.price')

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
            filial_id: $('#select-filials').val()
        }, function(html) {
            $('#modals').html(html);
            hidePrices();
            $('#modal-sync-price').foundation().foundation('open');
        });
    });

    function hidePrices() {
        $('#table-prices tbody').each(function(index) {
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
    $(document).on('change', '#select-filials', function(event) {
        event.preventDefault();

        let fillial_id = $('#select-filials').val();
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
                    $('#prices_services-' + id).replaceWith(html);
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

    /**
     * Работа со столбцом points
     */
    // При клике на внут. вал. подставляем инпут
    $(document).on('click', '#content .td-points span', function(event) {
        event.preventDefault();

        var parent = $(this).closest('.td-points');
        parent.find('span').hide();
        parent.find('input').show().focus();

    });

    // При изменении внут. вал. ловим enter
    $(document).on('keydown', '#content .td-points [name=points]', function(event) {

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
                    points: $(this).val()
                },
                success: function(html){
                    $('#prices_services-' + id).replaceWith(html);
                }
            });
        };
    });

    // При потере фокуса при редактировании возвращаем обратно
    $(document).on('focusout', '.td-points input[name=points]', function(event) {
        event.preventDefault();

        var parent = $(this).closest('.td-points');
        parent.find('span').show();
        parent.find('input').hide();
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

    // Статус
    $(document).on('click', '.price_services-status', function(event) {
        event.preventDefault();

        let item = $(this);
        let id = item.closest('.item').attr('id').split('-')[1];

        let status = item.hasClass("hide") ? 1 : 0;

        // alert(catalog_id + ' ' + id + ' ' + status);

        // Ajax
        $.post('/admin/catalogs_services/' + catalog_id + '/prices_services_status', {
            id: id,
            status: status,
        }, function (result) {
            // Если нет ошибки
            if (result === true) {
                if (status === 1) {
                    item.removeClass('hide');
                    item.addClass('show');
                    item.text('Продано');
                } else {
                    item.removeClass('show');
                    item.addClass('hide');
                    item.text('Доступен');
                }
            } else {
                // Выводим ошибку на страницу
                alert(result);
            };
        });
    });

    // Хит
    $(document).on('click', '.price_services-hit', function(event) {
        event.preventDefault();

        let item = $(this);
        let id = item.closest('.item').attr('id').split('-')[1];

        let hit = item.hasClass("hit") ? 0 : 1;

        // alert(id + ' ' + hit);

        // Ajax
        $.post('/admin/catalogs_services/' + catalog_id + '/prices_services_hit', {
            id: id,
            is_hit: hit,
        }, function (result) {
            // Если нет ошибки
            if (result === true) {
                if (hit === 1) {
                    item.addClass('hit');
                    item.text('Хит продаж');
                } else {
                    item.removeClass('hit');
                    item.text('Обычный');
                }
            } else {
                // Выводим ошибку на страницу
                alert(result);
            };
        });
    });

    // Хит
    $(document).on('click', '.price_services-new', function(event) {
        event.preventDefault();

        let item = $(this);
        let id = item.closest('.item').attr('id').split('-')[1];

        let status = item.hasClass("new") ? 0 : 1;

        // alert(id + ' ' + hit);

        // Ajax
        $.post('/admin/catalogs_services/' + catalog_id + '/prices_services_new', {
            id: id,
            is_new: status,
        }, function (result) {
            // Если нет ошибки
            if (result === true) {
                if (status === 1) {
                    item.addClass('new');
                    item.text('Новинка');
                } else {
                    item.removeClass('new');
                    item.text('Обычный');
                }
            } else {
                // Выводим ошибку на страницу
                alert(result);
            };
        });
    });


</script>
@endpush
