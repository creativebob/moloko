@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $page_info->page_description }}" />
{{-- Скрипты таблиц в шапке --}}
@include('includes.scripts.tablesorter-inhead')
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('section', $parent_page_info, $site, $page_info))

@section('content-count')
{{-- Количество элементов --}}
{{ (isset($catalog)) ? num_format(($catalog->services_count + $catalog->goods_count + $catalog->raws_count), 0) : 0 }}
@endsection

@section('title-content')
{{-- Меню --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => null, 'type' => 'menu'])
@endsection

@section('control-content')
<div class="grid-x grid-padding-x">

    <div class="small-12 medium-6 cell inputs">
        <div class="grid-x grid-margin-x">
            @if($catalog->site->company->sites_count > 1)
            <div class="small-12 medium-6 cell">

            </div>
            @endif

            <div class="small-12 medium-6 cell">
                <label>Разделы каталога:
                    @include('includes.selects.catalogs', ['parent_id' => $catalog->id])

                </label>
            </div>
        </div>
    </div>
    <div class="small-12 medium-6 cell inputs">
        <label>Добавление продукта через поиск:
            <input type='text' name="product_name" id="search_add_product_field" placeholder="Название, артикул">
        </label>
        <div id="port-result-search-add-product">
        </div>

    </div>

    {{-- Подключаем ПОИСК продукции для добавления на сайт --}}
    {{--    @include('catalog_products.search-add-product-script') --}}

</div>
@endsection

@section('content')
{{-- Таблица --}}
<div class="grid-x">
    <div class="small-12 cell">
        <table class="content-table tablesorter" id="content" data-sticky-container data-entity-alias="services">
            <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
                    <th class="td-name">Название продукции</th>
                    <th class="td-type">Тип продукции</th>
                    <th class="td-cost">Цена</th>

                    @if(Auth::user()->god == 1)
                    <th class="td-company-id">Компания</th>
                    @endif

                    <th class="td-author">Автор</th>
                    <th class="td-control"></th>
                    <th class="td-delete"></th>
                </tr>
            </thead>

            <tbody data-tbodyId="1" class="tbody-width" id="content-core">
                {{-- Подрубаем ядро контента для ajax перезагрузки --}}
                @include('catalog_products.content_core')
            </tbody>

        </table>
    </div>
</div>


@endsection

@section('modals')
<section id="modal"></section>

{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete-ajax')

@endsection

@section('scripts')

{{-- Скрипт чекбоксов, сортировки и перетаскивания для таблицы --}}
@include('includes.scripts.tablesorter-script')
@include('includes.scripts.sortable-table-script')

{{-- Скрипт отображения на сайте --}}
@include('includes.scripts.ajax-display')

{{-- Скрипт системной записи --}}
@include('includes.scripts.ajax-system')

{{-- Скрипт чекбоксов --}}
@include('includes.scripts.checkbox-control')

@include('includes.scripts.inputs-mask')
@include('catalog_products.scripts')

<script type="text/javascript">

    var alias = '{{ $site->alias }}';
    var catalog_id = '{{ $catalog->id }}';

    function searchProduct(value) {

        // Если символов больше 3 - делаем запрос
        if (value.length > 2) {

            $.post('/admin/catalog_product/search_add_product', {catalog_id: catalog_id, text_fragment: value}, function(html){
                // Выводим пришедшие данные на страницу
                $('#port-result-search-add-product').html(html);
            });
        } else {
            $('#port-result-search-add-product').html('');
        };
    };

    // Проверка существования
    $(document).on('keyup', '#search_add_product_field', function() {

        // Выполняем запрос
        let timerId;
        clearTimeout(timerId);

        timerId = setTimeout(function() {
            searchProduct($('#search_add_product_field').val());
        }, 400);
    });

    // Добавление
    $(document).on('click', '.add-product-button', function() {

        // Получаем ID добавляемго продукта и его тип (goods / services / raws)
        var product_type = $(this).attr('id').split('-')[0];
        var product_id = $(this).attr('id').split('-')[1];
        var catalog_id = $('#select-catalogs').val();

        var item = $(this);

        $.post("/admin/catalog_product/add_product", {product_type: product_type, product_id: product_id, catalog_id: catalog_id}, function(html){

            if (html == 'empty') {
                // alert(html);
            } else {
                // Выводим пришедшие данные на страницу
                $('#content-core').html(html);
                item.remove();
            };
        });
    });

    // Мягкое удаление с ajax
    $(document).on('click', '[data-open="item-delete-ajax"]', function() {

        // Находим описание сущности, id и название удаляемого элемента в родителе
        var parent = $(this).closest('.item');
        var entity_alias = parent.attr('id').split('-')[0];
        var id = parent.attr('id').split('-')[1];
        var name = parent.data('name');
        $('.title-delete').text(name);
        $('.delete-button-ajax').attr('id', 'del-' + entity_alias + '-' + id);
    });

    // Подтверждение удаления и само удаление
    $(document).on('click', '.delete-button-ajax', function(event) {

        // Блочим отправку формы
        event.preventDefault();
        var entity_alias = $(this).attr('id').split('-')[1];
        var id = $(this).attr('id').split('-')[2];

        var buttons = $('button');
        buttons.prop('disabled', true);

        // Ajax
        $.ajax({
            url: '/admin/sites/' + alias + '/catalog_products/' + id,
            type: "DELETE",
            success: function (res) {
                $('#item-delete-ajax').foundation('close');
                buttons.prop('disabled', false);
                if (res == true) {
                    // Удаляем со страницы
                    $('#catalog_products-' + id).remove();
                } else {
                    alert('Ошибка удаления');
                };
            }
        });
    });

    $(document).on('change', '#select-catalogs', function(event) {
        event.preventDefault();
        window.location = "/admin/sites/" + alias + "/catalog_products/" + $(this).val();
    });

</script>
@endsection
