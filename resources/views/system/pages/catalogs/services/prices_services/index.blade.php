@extends('layouts.app')

@section('inhead')
    <meta name="description" content="{{ $pageInfo->description }}"/>
@endsection

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('prices_services-index', $catalogServices, $pageInfo))

@section('exсel')
    <a href="{{ route('prices_services.excelExport', $catalogServices->id, request()->input()) }}" class="button tiny">Выгрузить</a>
@endsection

@section('content-count')
    {{-- Количество элементов --}}
    {{ $pricesServices->isNotEmpty() ? num_format($pricesServices->total(), 0) : 0 }}
@endsection

@section('title-content')
    {{-- Таблица --}}
    @include('system.pages.catalogs.services.prices_services.includes.title')
@endsection

@section('content')

    @if(isset($pricesServices))
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
                        @include('system.pages.catalogs.services.prices_services.select_user_filials', ['catalog' => $catalogServices])
                    </div>
                </div>
            </div>

        </div>
    @endif

    {{-- Таблица --}}
    <div class="grid-x">
        <div class="small-12 cell">

            <table class="content-table tablesorter" id="content" data-sticky-container
                   data-entity-alias="prices_services">

                <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2"
                       data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
                    <th class="td-photo">Фото</th>
                    <th class="td-name" data-serversort="name">Название</th>
                    <th class="td-unit">Ед. измерения</th>
                    <th class="td-length">Продолжительность</th>
                    <th class="td-catalogs_item">Раздел прайса</th>
                    <th class="td-price">Цена</th>
                    <th class="td-discount">Скидка</th>
                    <th class="td-total">Цена со скидкой</th>
                    <th class="td-points">Внут. вал</th>
                    {{--                    <th class="td-price-status">Статус</th>--}}
                    {{--                    <th class="td-hit">Хит</th>--}}
                    {{--                    <th class="td-new">Новинка</th>--}}
                    <th class="td-likes">Лайки</th>
                    <th class="td-control"></th>
                    <th class="td-delete"></th>
                </tr>
                </thead>

                <tbody data-tbodyId="1" class="tbody-width">
                @foreach($pricesServices as $priceService)
                    <tr class="item @if($priceService->moderation == 1)no-moderation @endif" id="prices_goods-{{ $priceService->id }}" data-name="{{ $priceService->catalogs_item->name }}">
                        <td class="td-drop">
                            <div class="sprite icon-drop"></div>
                        </td>

                        <td class="td-checkbox checkbox">
                            <input type="checkbox" class="table-check" name="prices_service_id" id="check-{{ $priceService->id }}"

                                   {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
                                   @if(!empty($filter['booklist']['booklists']['default']))
                                   {{--               Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked--}}
                                   @if (in_array($priceService->id, $filter['booklist']['booklists']['default'])) checked
                                @endif
                                @endif
                            ><label class="label-check" for="check-{{ $priceService->id }}"></label>
                        </td>

                        <td class="td-photo tiny">
                            <img src="{{ getPhotoPathPlugEntity($priceService->service, 'small') }}" alt="{{ isset($priceService->service->process->photo_id) ? $priceService->service->process->name : 'Нет фото' }}">
                        </td>

                        <td class="td-name">
                            @can('update', $priceService)
                                <a href="{{ route('prices_services.edit', [$catalogServices->id, $priceService->id]) }}">{{ $priceService->service->process->name }}</a>
                                {{--            <a href="{{ route('prices_goods.edit', ['catalog_id' => $priceService->catalog_id, 'id' => $priceService->id]) }}"></a>--}}
                            @else
                                {{ $priceService->service->process->name }}
                            @endcan

                            {{-- ({{ link_to_route('goods.index', $priceService->processs_count, $parameters = ['prices_service_id' => $priceService->id], $attributes = ['class' => 'filter_link light-text', 'title' => 'Перейти на список артикулов']) }}) %5B%5D --}}

                            <br><span class="tiny-text">{{ $priceService->service->category->name }}</span>

                        </td>

                        <td class="td-unit">
                            {{ $priceService->service->process->unit->abbreviation }}
                        </td>

                        <td class="td-length">
{{--                            @if($priceService->service->process->unit_id != 8)--}}
{{--                                {{ num_format($priceService->service->process->length / $priceService->service->process->unit_weight->ratio, 0) }} {{ $priceService->service->process->unit_weight->abbreviation }}--}}
{{--                            @endif--}}
                        </td>

                        <td class="td-catalogs_item">{{ $priceService->catalogs_item->name_with_parent }}</td>

                        <td class="td-price">
                            {{--        @include('system.pages.catalogs.goods.prices_goods.price_span')--}}
                            <span>{{ num_format($priceService->price, 0) }}</span>
                        </td>
                        <td class="td-discount">
                            @isset($priceService->discount_price)
                                @switch($priceService->discount_price->mode)
                                    @case(1)
                                    <span>На товар: {{ num_format($priceService->discount_price->percent, 0) }}% / {{ num_format($priceService->price_discount, 0) }} {{ $priceService->currency->abbreviation }}</span><br>
                                    @break

                                    @case(2)
                                    <span>На товар: {{ num_format($priceService->discount_price->currency, 0) }} {{ $priceService->currency->abbreviation }}</span><br>
                                    @break
                                @endswitch
                            @endisset
                            @isset($priceService->discount_catalogs_item)
                                @switch($priceService->discount_catalogs_item->mode)
                                    @case(1)
                                    <span>На раздел: {{ num_format($priceService->discount_catalogs_item->percent, 0) }}% / {{ num_format($priceService->catalogs_item_discount, 0) }} {{ $priceService->currency->abbreviation }}</span><br>
                                    @break

                                    @case(2)
                                    <span>На раздел: {{ num_format($priceService->discount_catalogs_item->currency, 0) }} {{ $priceService->currency->abbreviation }}</span><br>
                                    @break
                                @endswitch
                            @endisset
                            @isset($priceService->discount_estimate)
                                @switch($priceService->discount_estimate->mode)
                                    @case(1)
                                    <span>На чек: {{ num_format($priceService->discount_estimate->percent, 0) }}% / {{ num_format($priceService->estimate_discount, 0) }} {{ $priceService->currency->abbreviation }}</span><br>
                                    @break

                                    @case(2)
                                    <span>На чек: {{ num_format($priceService->discount_estimate->currency, 0) }} {{ $priceService->currency->abbreviation }}</span><br>
                                    @break
                                @endswitch
                            @endisset
                        </td>
                        <td class="td-total">{{ num_format(($priceService->total), 0) }}</td>
                        {{--    <price-goods-price-component :price="{{ $priceService->price }}"></price-goods-price-component>--}}
                        <td class="td-points">
                            {{--        @include('system.pages.catalogs.goods.prices_goods.price_points')--}}
                            <span>{{ num_format($priceService->points, 0) }}</span>
                        </td>

                        {{--    <td class="td-price-status">--}}
                        {{--        <button type="button" class="hollow tiny button price_goods-status--}}
                        {{--            @if($priceService->status == 1) show @else hide @endif--}}
                        {{--            ">--}}
                        {{--            @if($priceService->status == 1) Продано @else Доступен @endif</button>--}}
                        {{--    </td>--}}

                        {{--    <td class="td-hit">--}}
                        {{--        <button type="button" class="hollow tiny button price_goods-hit--}}
                        {{--            @if($priceService->is_hit == 1) hit @endif--}}
                        {{--            ">--}}
                        {{--            @if($priceService->is_hit == 1) Хит продаж @else Обычный @endif</button>--}}
                        {{--    </td>--}}

                        {{--    <td class="td-new">--}}
                        {{--        <button type="button" class="hollow tiny button price_goods-new--}}
                        {{--            @if($priceService->is_new == 1) new @endif--}}
                        {{--            ">--}}
                        {{--            @if($priceService->is_new == 1) Новинка @else Обычный @endif</button>--}}
                        {{--    </td>--}}

                        <td class="td-likes">{{ $priceService->likes_count }}</td>

                        {{-- Элементы управления --}}
                        @include('includes.control.table_td', ['item' => $priceService])

                        <td class="td-delete">

                            @can('delete', $priceService)
                                <a class="icon-delete sprite" data-open="delete-price"></a>
                            @endcan

                        </td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="grid-x" id="pagination">
        <div class="small-6 cell pagination-head">
            <span class="pagination-title">Кол-во записей: {{ $pricesServices->count() }}</span>
            {{ $pricesServices->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
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

        var catalog_id = '{{ $catalogServices->id }}';

        // При клике на создание открываем модалку синхронизации
        $(document).on('click', '.icon-add', function (event) {

            $.get('/admin/catalogs_services/' + catalog_id + '/prices_services/create', {
                filial_id: $('#select-filials').val()
            }, function (html) {
                $('#modals').html(html);
                hidePrices();
                $('#modal-sync-price').foundation().foundation('open');
            });
        });

        function hidePrices() {
            $('#table-prices tbody').each(function (index) {
                $(this).hide();
            });
        };

        $(document).on('click', '.item-catalog', function (event) {
            // alert('kek');

            hidePrices();
            let id = $(this).attr('id');
            $('#table-prices .' + id).show();
        });

        // Перезагружаем страницу при смене select'a филиалов для пользователя
        $(document).on('change', '#select-filials', function (event) {
            event.preventDefault();

            let fillial_id = $('#select-filials').val();
            let url = "prices_services?filial_id=" + fillial_id;
            $(location).attr('href', url);
        });

        // При клике на цену подставляем инпут
        $(document).on('click', '#content .td-price span', function (event) {
            event.preventDefault();

            var parent = $(this).closest('.item');
            var id = parent.attr('id').split('-')[1];

            $.get('/admin/catalogs_services/' + catalog_id + '/prices_services/' + id + '/edit', function (html) {
                $('#prices_services-' + id + ' .td-price').html(html);
            });
        });

        // При изменении цены ловим enter
        $(document).on('keydown', '#content .td-price [name=price]', function (event) {

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
                    success: function (html) {
                        $('#prices_services-' + id).replaceWith(html);
                    }
                });
            }
            ;
        });

        // При потере фокуса при редактировании возвращаем обратно
        $(document).on('focusout', '.td-price input[name=price]', function (event) {
            event.preventDefault();

            var parent = $(this).closest('.item');
            var id = parent.attr('id').split('-')[1];

            $.get('/admin/catalogs_services/' + catalog_id + '/get_prices_service/' + id, function (html) {
                $('#prices_services-' + id + ' .td-price').html(html);
            });
        });

        // Удаление ajax
        $(document).on('click', '[data-open="delete-price"]', function () {

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
        $(document).on('click', '#content .td-points span', function (event) {
            event.preventDefault();

            var parent = $(this).closest('.td-points');
            parent.find('span').hide();
            parent.find('input').show().focus();

        });

        // При изменении внут. вал. ловим enter
        $(document).on('keydown', '#content .td-points [name=points]', function (event) {

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
                    success: function (html) {
                        $('#prices_services-' + id).replaceWith(html);
                    }
                });
            }
            ;
        });

        // При потере фокуса при редактировании возвращаем обратно
        $(document).on('focusout', '.td-points input[name=points]', function (event) {
            event.preventDefault();

            var parent = $(this).closest('.td-points');
            parent.find('span').show();
            parent.find('input').hide();
        });

        // Удаление ajax
        $(document).on('click', '[data-open="delete-price"]', function () {

            // находим описание сущности, id и название удаляемого элемента в родителе
            var parent = $(this).closest('.item');
            var id = parent.attr('id').split('-')[1];
            var name = parent.data('name');

            $('.title-price').text(name);
            $('#form-delete-price').attr('action', '/admin/catalogs_services/' + catalog_id + '/prices_services/' + id);
        });

        // Статус
        $(document).on('click', '.price_services-status', function (event) {
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
                }
                ;
            });
        });

        // Хит
        $(document).on('click', '.price_services-hit', function (event) {
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
                }
                ;
            });
        });

        // Хит
        $(document).on('click', '.price_services-new', function (event) {
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
                }
                ;
            });
        });


    </script>
@endpush
