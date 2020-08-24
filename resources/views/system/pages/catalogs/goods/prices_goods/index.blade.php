@extends('layouts.app')

@section('inhead')
	<meta name="description" content="{{ $pageInfo->description }}" />
@endsection

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('prices_goods-index', $catalog_goods, $pageInfo))

@section('content-count')
	{{-- Количество элементов --}}
	{{ $prices_goods->isNotEmpty() ? num_format($prices_goods->total(), 0) : 0 }}
@endsection

@section('title-content')

	{{-- Таблица --}}
	{{-- @include('includes.title-content', ['pageInfo' => $pageInfo, 'class' => App\PricesGoods::class, 'type' => 'table']) --}}
    @include('system.pages.catalogs.goods.prices_goods.includes.title-prices_goods', ['pageInfo' => $pageInfo, 'class' => $class])
@endsection


@section('content')

@if(isset($prices_goods))
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
		            @include('system.pages.catalogs.goods.prices_goods.select_user_filials')
		        </div>
			</div>
        </div>

    </div>
@endif


{{-- Таблица --}}
<div class="grid-x">
    <div class="small-12 cell">

        <table class="content-table tablesorter" id="content" data-sticky-container data-entity-alias="prices_goods">

            <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
                    <th class="td-photo">Фото</th>
                    <th class="td-name" data-serversort="name">Название</th>
                    <th class="td-unit">Ед. измерения</th>
                    <th class="td-weight">Вес</th>
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

                @if(isset($prices_goods) && $prices_goods->isNotEmpty())
	                @foreach($prices_goods as $cur_prices_goods)
{{--	                	@include('system.pages.catalogs.goods.prices_goods.price')--}}
                        <tr class="item @if($cur_prices_goods->moderation == 1)no-moderation @endif" id="prices_goods-{{ $cur_prices_goods->id }}" data-name="{{ $cur_prices_goods->catalogs_item->name }}">
    <td class="td-drop">
        <div class="sprite icon-drop"></div>
    </td>

    <td class="td-checkbox checkbox">
        <input type="checkbox" class="table-check" name="prices_service_id" id="check-{{ $cur_prices_goods->id }}"

               {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
               @if(!empty($filter['booklist']['booklists']['default']))
               {{--               Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked--}}
               @if (in_array($cur_prices_goods->id, $filter['booklist']['booklists']['default'])) checked
            @endif
            @endif
        ><label class="label-check" for="check-{{ $cur_prices_goods->id }}"></label>
    </td>

    <td class="td-photo tiny">
        <img src="{{ getPhotoPathPlugEntity($cur_prices_goods->goods, 'small') }}" alt="{{ isset($cur_prices_goods->goods->article->photo_id) ? $cur_prices_goods->goods->article->name : 'Нет фото' }}">
    </td>

    <td class="td-name">
        @can('update', $cur_prices_goods)
            <a href="prices_goods/{{ $cur_prices_goods->id }}/edit">{{ $cur_prices_goods->goods->article->name }}</a>
            {{--            <a href="{{ route('prices_goods.edit', ['catalog_id' => $cur_prices_goods->catalog_id, 'id' => $cur_prices_goods->id]) }}"></a>--}}
        @else
            {{ $cur_prices_goods->goods->article->name }}
        @endcan

        {{-- ({{ link_to_route('goods.index', $cur_prices_goods->articles_count, $parameters = ['prices_service_id' => $cur_prices_goods->id], $attributes = ['class' => 'filter_link light-text', 'title' => 'Перейти на список артикулов']) }}) %5B%5D --}}

        <br><span class="tiny-text">{{ $cur_prices_goods->goods->category->name }}</span>

    </td>

    <td class="td-unit">
        {{ $cur_prices_goods->goods->article->unit->abbreviation }}
    </td>

    <td class="td-weight">
        @if($cur_prices_goods->goods->article->unit_id != 8)
            {{ num_format($cur_prices_goods->goods->article->weight / $cur_prices_goods->goods->article->unit_weight->ratio, 0) }} {{ $cur_prices_goods->goods->article->unit_weight->abbreviation }}
        @endif
    </td>

    <td class="td-catalogs_item">{{ $cur_prices_goods->catalogs_item->name_with_parent }}</td>

    <td class="td-price">
{{--        @include('system.pages.catalogs.goods.prices_goods.price_span')--}}
        <span>{{ num_format($cur_prices_goods->price, 0) }}</span>
    </td>
    <td class="td-discount">
        <span>Индивидуальная: {{ num_format($cur_prices_goods->discount_percent, 0) }}% / {{ num_format($cur_prices_goods->discount_currency, 0) }} {{ $cur_prices_goods->currency->abbreviation }}</span><br>
        @isset($cur_prices_goods->discount_price)
            <span>На прайс: {{ num_format($cur_prices_goods->discount_price->percent, 0) }}% / {{ num_format($cur_prices_goods->discount_price->currency, 0) }} {{ $cur_prices_goods->currency->abbreviation }}</span><br>
        @endisset
        @isset($cur_prices_goods->discount_catalogs_item)
            <span>На раздел: {{ num_format($cur_prices_goods->discount_catalogs_item->percent, 0) }}% / {{ num_format($cur_prices_goods->discount_catalogs_item->currency, 0) }} {{ $cur_prices_goods->currency->abbreviation }}</span>
        @endisset
    </td>
    <td class="td-total">{{ num_format(($cur_prices_goods->total), 0) }}</td>
    {{--    <price-goods-price-component :price="{{ $cur_prices_goods->price }}"></price-goods-price-component>--}}
    <td class="td-points">
{{--        @include('system.pages.catalogs.goods.prices_goods.price_points')--}}
        <span>{{ num_format($cur_prices_goods->points, 0) }}</span>
    </td>

{{--    <td class="td-price-status">--}}
{{--        <button type="button" class="hollow tiny button price_goods-status--}}
{{--            @if($cur_prices_goods->status == 1) show @else hide @endif--}}
{{--            ">--}}
{{--            @if($cur_prices_goods->status == 1) Продано @else Доступен @endif</button>--}}
{{--    </td>--}}

{{--    <td class="td-hit">--}}
{{--        <button type="button" class="hollow tiny button price_goods-hit--}}
{{--            @if($cur_prices_goods->is_hit == 1) hit @endif--}}
{{--            ">--}}
{{--            @if($cur_prices_goods->is_hit == 1) Хит продаж @else Обычный @endif</button>--}}
{{--    </td>--}}

{{--    <td class="td-new">--}}
{{--        <button type="button" class="hollow tiny button price_goods-new--}}
{{--            @if($cur_prices_goods->is_new == 1) new @endif--}}
{{--            ">--}}
{{--            @if($cur_prices_goods->is_new == 1) Новинка @else Обычный @endif</button>--}}
{{--    </td>--}}

    <td class="td-likes">{{ $cur_prices_goods->likes_count }}</td>

    {{-- Элементы управления --}}
    @include('includes.control.table_td', ['item' => $cur_prices_goods])

    <td class="td-delete">

        @can('delete', $cur_prices_goods)
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
        <span class="pagination-title">Кол-во записей: {{ $prices_goods->count() }}</span>
        {{ $prices_goods->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
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

        $.get('/admin/catalogs_goods/' + catalog_id + '/prices_goods/create', {
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
        let url = "prices_goods?filial_id=" + fillial_id;
        $(location).attr('href',url);
    });

    // При клике на цену подставляем инпут
    // $(document).on('click', '#content .td-price span', function(event) {
    //     event.preventDefault();
    //
    //     var parent = $(this).closest('.item');
    //     var id = parent.attr('id').split('-')[1];
    //
    //     $.get('/admin/catalogs_goods/' + catalog_id + '/prices_goods/' + id + '/edit', function(html) {
    //         $('#prices_goods-' + id + ' .td-price').html(html).find('input[name=price]').focus();
    //     });
    // });

    // При изменении цены ловим enter
    // $(document).on('keydown', '#content .td-price [name=price]', function(event) {
    //
    //     var parent = $(this).closest('.item');
    //     var id = parent.attr('id').split('-')[1];
    //
    //     // если нажали Enter, то true
    //     if ((event.keyCode == 13) && (event.shiftKey == false)) {
    //         event.preventDefault();
    //         // event.stopPropagation();
    //         $.ajax({
    //             url: '/admin/catalogs_goods/' + catalog_id + '/prices_goods/' + id,
    //             type: "PATCH",
    //             data: {
    //                 price: $(this).val()
    //             },
    //             success: function(html){
    //                 $('#prices_goods-' + id).replaceWith(html);
    //             }
    //         });
    //     };
    // });
    //
    // // При потере фокуса при редактировании возвращаем обратно
    // $(document).on('focusout', '.td-price input[name=price]', function(event) {
    //     event.preventDefault();
    //
    //     var parent = $(this).closest('.item');
    //     var id = parent.attr('id').split('-')[1];
    //
    //     $.get('/admin/catalogs_goods/' + catalog_id + '/get_prices_goods/' + id, function(html) {
    //         $('#prices_goods-' + id + ' .td-price').html(html);
    //     });
    // });

    /**
     * Работа со столбцом points
     */
    // // При клике на внут. вал. подставляем инпут
    // $(document).on('click', '#content .td-points span', function(event) {
    //     event.preventDefault();
    //
    //     var parent = $(this).closest('.td-points');
    //     parent.find('span').hide();
    //     parent.find('input').show().focus();
    //
    // });
    //
    // // При изменении внут. вал. ловим enter
    // $(document).on('keydown', '#content .td-points [name=points]', function(event) {
    //
    //     var parent = $(this).closest('.item');
    //     var id = parent.attr('id').split('-')[1];
    //
    //     // если нажали Enter, то true
    //     if ((event.keyCode == 13) && (event.shiftKey == false)) {
    //         event.preventDefault();
    //         // event.stopPropagation();
    //         $.ajax({
    //             url: '/admin/catalogs_goods/' + catalog_id + '/prices_goods/' + id,
    //             type: "PATCH",
    //             data: {
    //                 points: $(this).val()
    //             },
    //             success: function(html){
    //                 $('#prices_goods-' + id).replaceWith(html);
    //             }
    //         });
    //     };
    // });
    //
    // // При потере фокуса при редактировании возвращаем обратно
    // $(document).on('focusout', '.td-points input[name=points]', function(event) {
    //     event.preventDefault();
    //
    //     var parent = $(this).closest('.td-points');
    //     parent.find('span').show();
    //     parent.find('input').hide();
    // });

    // Удаление ajax
    $(document).on('click', '[data-open="delete-price"]', function() {

        // находим описание сущности, id и название удаляемого элемента в родителе
        var parent = $(this).closest('.item');
        var id = parent.attr('id').split('-')[1];
        var name = parent.data('name');

        $('.title-price').text(name);
        $('#form-delete-price').attr('action', '/admin/catalogs_goods/' + catalog_id + '/prices_goods/' + id);
    });

    // Статус
    $(document).on('click', '.price_goods-status', function(event) {
        event.preventDefault();

        let item = $(this);
        let id = item.closest('.item').attr('id').split('-')[1];

        let status = item.hasClass("hide") ? 1 : 0;

        // alert(catalog_id + ' ' + id + ' ' + status);

        // Ajax
        $.post('/admin/catalogs_goods/' + catalog_id + '/prices_goods_status', {
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
    $(document).on('click', '.price_goods-hit', function(event) {
        event.preventDefault();

        let item = $(this);
        let id = item.closest('.item').attr('id').split('-')[1];

        let hit = item.hasClass("hit") ? 0 : 1;

        // alert(id + ' ' + hit);

        // Ajax
        $.post('/admin/catalogs_goods/' + catalog_id + '/prices_goods_hit', {
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
    $(document).on('click', '.price_goods-new', function(event) {
        event.preventDefault();

        let item = $(this);
        let id = item.closest('.item').attr('id').split('-')[1];

        let status = item.hasClass("new") ? 0 : 1;

        // alert(id + ' ' + hit);

        // Ajax
        $.post('/admin/catalogs_goods/' + catalog_id + '/prices_goods_new', {
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
