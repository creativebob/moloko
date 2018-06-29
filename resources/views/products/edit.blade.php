@extends('layouts.app')

@section('inhead')
    @include('includes.scripts.dropzone-inhead')
    @include('includes.scripts.fancybox-inhead')
    @include('includes.scripts.sortable-inhead')
@endsection

@section('title', 'Редактировать товар')

@section('breadcrumbs', Breadcrumbs::render('alias-edit', $page_info, $product))

@section('title-content')
    <div class="top-bar head-content">
        <div class="top-bar-left">
            <h2 class="header-content">РЕДАКТИРОВАТЬ товар &laquo{{ $product->name }}&raquo</h2>
        </div>
        <div class="top-bar-right">
        </div>
    </div>
@endsection

@section('content')
<div class="grid-x tabs-wrap">
    <div class="small-12 cell">
        <ul class="tabs-list" data-tabs id="tabs">
            <li class="tabs-title is-active"><a href="#options" aria-selected="true">Общая информация</a></li>
            <li class="tabs-title"><a data-tabs-target="compositions" href="#compositions">Состав</a></li>
            <li class="tabs-title"><a data-tabs-target="price-rules" href="#price-rules">Ценообразование</a></li>
            <li class="tabs-title"><a data-tabs-target="photos" href="#photos">Фотографии</a></li>
        </ul>
    </div>
</div>

<div class="grid-x tabs-wrap inputs">
    <div class="small-12 cell tabs-margin-top">
        <div class="tabs-content" data-tabs-content="tabs">

            @if ($errors->any())
            <div class="alert callout" data-closable>
                <h5>Неправильный формат данных:</h5>
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button class="close-button" aria-label="Dismiss alert" type="button" data-close>
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            {{ Form::model($product, ['url' => ['products/'.$product->id], 'data-abide', 'novalidate', 'files'=>'true', 'id' => 'product-form']) }}
            {{ method_field('PATCH') }}

            <!-- Общая информация -->
            <div class="tabs-panel is-active" id="options">

                {{-- Разделитель на первой вкладке --}}
                <div class="grid-x grid-padding-x">

                    {{-- Левый блок на первой вкладке --}}
                    <div class="small-12 large-6 cell">

                        {{-- Основная инфа --}}
                        <div class="grid-x grid-margin-x">
                            <div class="small-12 medium-6 cell">
                                <label>Название товара
                                    @include('includes.inputs.name', ['value'=>$product->name, 'name'=>'name', 'required'=>'required'])
                                </label>

                                <label>Категория товара
                                    <select name="products_category_id">
                                        @php
                                            echo $products_categories_list;
                                        @endphp
                                    </select>
                                </label>

                                <div class="grid-x grid-margin-x">
                                    <div class="small-12 medium-6 cell">
                                        <label>Мера
                                            {{ Form::select('units_category_id', $units_categories_list, $product->unit->units_category_id, ['id' => 'units-categories-list'])}}
                                        </label>
                                    </div>
                                    <div class="small-12 medium-6 cell">
                                        <label>Единица измерения
                                            {{ Form::select('unit_id', $units_list, $product->unit_id, ['id' => 'units-list']) }}
                                        </label>
                                    </div>
                                </div>

                                <fieldset class="fieldset">
                                    <legend>
                                        <div class="small-12 cell checkbox">
                                            {{ Form::checkbox('portion', 1, $product->display, ['id' => 'portion']) }}
                                            <label for="portion"><span>Принимать порциями</span></label>
                                        </div>                                   
                                    </legend>

                                    <div class="grid-x grid-margin-x">
                                        <div class="small-12 medium-6 cell">
                                            <label>Имя порции
                                                {{ Form::text('name', "", ['class'=>'text-field name-field compact', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}']) }}
                                            </label>
                                        </div>
                                        <div class="small-6 medium-3 cell">
                                            <label>Сокр. имя
                                                {{ Form::text('name',  "", ['class'=>'text-field name-field compact', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}']) }}
                                            </label>
                                        </div>
                                        <div class="small-6 medium-3 cell">
                                            <label>Кол-во
                                                {{-- Количество чего-либо --}}
                                                {{ Form::text('raw_count', 0, ['class'=>'digit-field name-field compact', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}']) }}
                                                <div class="sprite-input-right find-status" id="name-check"></div>
                                                <span class="form-error">Введите количество</span>
                                            </label>
                                        </div>
                                    </div>
                                </fieldset>

                                <div class="small-12 medium-6 cell">
                                    <label>Производитель
                                    {{ Form::select('manufacturer_id', $manufacturers_list, $product->manufacturer_id, ['placeholder' => 'Выберите производителя'])}}
                                    </label>
                                </div>
                            </div>

                            <div class="small-12 medium-6 cell">

                                <div class="small-12 cell">
                                    <label>Фотография продукта
                                        {{ Form::file('photo') }}
                                    </label>
                                    <div class="text-center">
                                        <img id="photo" @if (isset($product->photo_id)) src="/storage/{{ $product->company->id }}/media/products/{{ $product->id }}/img/medium/{{ $product->photo->name }}" @endif>
                                    </div>
                                </div>
                            </div>

                            <div class="small-12 cell">
                                <label>Описание товара
                                    @include('includes.inputs.textarea', ['name'=>'description', 'value'=>$product->description, 'required'=>''])
                                </label>
                            </div>
                        </div>

                    </div>
                    {{-- Конец левого блока на первой вкладке --}}


                    {{-- Правый блок на первой вкладке --}}
                    <div class="small-12 large-6 cell">
                        {{ Form::open(['url' => 'articles', 'data-abide', 'novalidate', 'id' => 'article-form']) }}

                        <fieldset class="fieldset-access">
                            <legend>Артикулы</legend>

                            <div class="grid-x grid-margin-x">
                                <div class="small-12 medium-4 cell">
                                    <label>Удобный (вручную)
                                        {{ Form::text('external') }}
                                    </label>
                                </div> 
                                <div class="small-12 medium-4 cell">
                                    <label>Программный
                                        {{ Form::text('name', null, ['required']) }}
                                    </label>
                                </div>
                                <div class="small-12 medium-4 cell">
                                    <label>Внешний
                                        {{ Form::text('external') }}
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="fieldset-access">
                            <legend>Метрики</legend>

                            @each('products.metric-input', $product->products_category->metrics, 'metric')
                        </fieldset>
                        <div id="article-inputs"></div>
                        <div class="small-12 cell tabs-margin-top text-center">
                            <div class="item-error" id="article-error">Такой артикул уже существует!<br>Измените значения!</div>
                        </div>
                        {{ Form::hidden('product_id', $product->id) }}

                        {{-- Кнопка --}}
                        <div class="small-12 cell tabs-button tabs-margin-top text-center">
                            {{ Form::submit('Создать артикул', ['class'=>'button', 'id' => 'add-article']) }}
                        </div>
                        {{ Form::close() }}

                    </div>
                    {{-- Конец правого блока на первой вкладке --}}



                    {{-- Чекбокс отображения на сайте --}}
                    @can ('publisher', $product)
                        <div class="small-12 cell checkbox">
                            {{ Form::checkbox('display', 1, $product->display, ['id' => 'display']) }}
                            <label for="display"><span>Отображать на сайте</span></label>
                        </div>
                    @endcan

                    {{-- Чекбокс модерации --}}
                    @can ('moderator', $product)
                        @if ($product->moderation == 1)
                            <div class="small-12 cell checkbox">
                                @include('includes.inputs.moderation', ['value'=>$product->moderation, 'name'=>'moderation'])
                            </div>
                        @endif
                    @endcan

                    {{-- Чекбокс системной записи --}}
                    @can ('god', $product)
                        <div class="small-12 cell checkbox">
                            @include('includes.inputs.system', ['value'=>$product->system_item, 'name'=>'system_item']) 
                        </div>
                    @endcan

                    {{-- Кнопка --}}
                    <div class="small-12 cell tabs-button tabs-margin-top">
                        {{ Form::submit('Редактировать продукцию', ['class'=>'button']) }}
                    </div>

                </div>{{-- Закрытие разделителя на блоки --}}
            </div>{{-- Закрытите таба --}}


            {{ Form::close() }}


            <!-- Состав -->
            <div class="tabs-panel" id="compositions">
                <div class="grid-x grid-padding-x">
                    <div class="small-12 medium-12 cell">

                        {{-- Состав --}}
                        <div class="small-12 medium-12 cell">
                            <ul class="menu right">
                                @foreach ($grouped_products_modes as $grouped_products_mode)
                                    <li>
                                        <a class="button" data-toggle="{{ $grouped_products_mode[0]->alias }}-dropdown">{{ $grouped_products_mode[0]->name }}</a>
                                        <div class="dropdown-pane" id="{{ $grouped_products_mode[0]->alias }}-dropdown" data-dropdown data-position="bottom" data-alignment="left" data-close-on-click="true">

                                            <ul class="checker" id="products-categories-list">
                                                @foreach ($grouped_products_modes[$grouped_products_mode[0]->alias][0]->products_categories as $products_category)
                                                @if ($products_category->products_count > 0)
                                                @include('products.products-category', $products_category)
                                                @endif
                                                @endforeach
                                            </ul>

                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <table class="composition-table">
                            <thead>
                                <tr> 
                                    <th>Категория:</th>
                                    <th>Продукт:</th>
                                    <th>Кол-во:</th>
                                    <th>Использование:</th>
                                    <th>Отход:</th>
                                    <th>Остаток:</th>
                                    <th>Операция над остатком:</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="composition-table">
                                {{-- Таблица метрик товара --}}
                                @if (!empty($product->compositions))
                                    @each('products.composition', $product->compositions, 'composition')
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Ценообразование -->
            <div class="tabs-panel" id="price-rules">
                <div class="grid-x grid-padding-x">
                    <div class="small-12 medium-6 cell">

                        <fieldset class="fieldset-access">
                            <legend>Базовые настройки</legend>

                            <div class="grid-x grid-margin-x">
                                <div class="small-12 medium-6 cell">
                                    <label>Себестоимость
                                        {{ Form::number('cost', null) }}
                                    </label>
                                </div>
                                <div class="small-12 medium-6 cell">
                                    <label>Цена
                                        {{ Form::number('price', null) }}
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>


            <!-- Фотографии -->
            <div class="tabs-panel" id="photos">
                <div class="grid-x grid-padding-x">

                    <div class="small-12 medium-7 cell">
                        {{ Form::open(['url' => '/product/add_photo', 'data-abide', 'novalidate', 'files'=>'true', 'class'=> 'dropzone', 'id' => 'my-dropzone']) }}
                        {{ Form::hidden('name', $product->name) }}
                        {{ Form::hidden('id', $product->id) }}
                        {{ Form::close() }}
                        <ul class="grid-x small-up-4 tabs-margin-top" id="photos-list">
                            @if (isset($product->album_id))

                                @include('products.photos', $product)

                            @endif
                        </ul>
                    </div>

                    <div class="small-12 medium-5 cell">

                        {{-- Форма редактированя фотки --}}
                        {{ Form::open(['url' => '/product/edit_photo', 'data-abide', 'novalidate', 'id' => 'form-photo-edit']) }}

                        @include('products.photo-edit', ['photo' => $photo])
                        {{ Form::hidden('name', $product->name) }}
                        {{ Form::hidden('id', $product->id) }}
                        {{ Form::close() }}
                    </div>

                </div>
            </div>



        </div>
    </div>
</div>



@endsection

@section('scripts')

    @include('includes.scripts.inputs-mask')
    @include('includes.scripts.upload-file')
    @include('products.scripts')
    @php
        $settings = config()->get('settings');
    @endphp

    <script>

        // Основные ностойки
        var product_id = '{{ $product->id }}';

        // При клике на удаление метрики со страницы
        $(document).on('click', '[data-open="delete-metric"]', function() {

        // Находим описание сущности, id и название удаляемого элемента в родителе
        var parent = $(this).closest('.item');
        var id = parent.attr('id').split('-')[1];

        // alert(id);

        $.ajax({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/ajax_delete_relation_metric',
        type: 'POST',
        data: {id: id, entity: 'products', entity_id: product_id},
        success: function(date){

        var result = $.parseJSON(date);
        // alert(result);

        if (result['error_status'] == 0) {

        // Удаляем элемент со страницы
        $('#metrics-' + id).remove();

        // В случае успеха обновляем список метрик
        // $.ajax({
        //   headers: {
        //     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //   },
        //   url: '/products/' + product_id + '/edit',
        //   type: 'GET',
        //   data: $('#product-form').serialize(),
        //   success: function(html){
        //     // alert(html);
        //     $('#properties-dropdown').html(html);
        //   }
        // })

        // Убираем отмеченный чекбокс в списке метрик
        $('#add-metric-' + id).prop('checked', false);

        } else {
        alert(result['error_message']);
        }; 
        }
        })
        });

        // При клике на удаление состава со страницы
        $(document).on('click', '[data-open="delete-composition"]', function() {

        // Находим описание сущности, id и название удаляемого элемента в родителе
        var parent = $(this).closest('.item');
        var id = parent.attr('id').split('-')[1];

        // alert(id);

        $.ajax({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/ajax_delete_relation_composition',
        type: 'POST',
        data: {id: id, product_id: product_id},
        success: function(date){

        var result = $.parseJSON(date);
        // alert(result);

        if (result['error_status'] == 0) {

        // Удаляем элемент со страницы
        $('#compositions-' + id).remove();

        // Убираем отмеченный чекбокс в списке метрик
        $('#add-composition-' + id).prop('checked', false);

        } else {
        alert(result['error_message']);
        }; 
        }
        })
        });

        // При клике на удаление состава со страницы
        $(document).on('click', '[data-open="delete-value"]', function() {

        // Удаляем элемент со страницы
        $(this).closest('.item').remove();
        });

        // Когда при клике по табам активная вкладка артикула
        $(document).on('change.zf.tabs', '.tabs-list', function() {
        if($('#articles:visible').length){

        $.ajax({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/ajax_get_article_inputs',
        type: 'POST',
        data: {product_id: product_id},
        success: function(html){
        // alert(html);
        $('#article-inputs').html(html);
        $('#article-inputs').foundation();
        // Foundation.reInit($('#article-inputs'));
        }
        })
        }
        });

        // Проверяем наличие артикула в базе при клике на кнопку добавления артикула
        $(document).on('click', '#add-article', function(event) {
        event.preventDefault();
        // alert($('#article-form').serialize());

        $.ajax({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/articles',
        type: 'POST',
        dataType: 'json', // ставим тип json, чтоб определить что пришло по итогу
        data: $('#article-form').serialize(),

        // В случае совпадения артикула принимаем json, и выдаем ошибку
        success: function(data, textStatus, jqXHR) {
        // alert(data['metric_values']);
        if (data['error_status'] == 1) {
        $('#add-article').prop('disabled', true);
        $('#article-error').css('display', 'block');
        }
        },

        // В случае несовпадения артикула пишем новый и вставляем его, но ответ придет html, поэтому ajax даст ошибку, т.к. ждет json
        error: function(html, textStatus, errorThrown) {

        // alert(JSON.stringify(html['responseText']));
        $('#article-table').append(JSON.stringify(html['responseText']));
        $('#article-form')[0].reset();
        }
        })
        });

        $(document).on('change', '#article-form input', function() {
        $('#add-article').prop('disabled', false);
        $('#article-error').css('display', 'none');
        });

        // При смнене свойства в select
        $(document).on('change', '#properties-select', function() {
        // alert($(this).val());

        var id = $(this).val();

        // Если вернулись на "Выберите свойство" то очищаем форму
        if (id == '') {
        $('#property-form').html('');
        } else {
        // alert(id);
        $('#property-id').val(id);

        $.ajax({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/ajax_add_property',
        type: 'POST',
        data: {id: id, entity: 'products'},
        success: function(html){
        // alert(html);
        $('#property-form').html(html);
        $('#properties-dropdown').foundation('close');
        }
        })
        }
        });

        // При клике на кнопку под Select'ом свойств
        $(document).on('click', '#add-metric', function(event) {
        event.preventDefault();

        // alert($('#properties-form').serialize());

        $.ajax({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/metrics',
        type: 'POST',
        data: $('#properties-form').serialize(),
        success: function(html){

        // alert(html);
        $('#metrics-table').append(html);
        $('#property-form').html('');

        // В случае успеха обновляем список метрик
        $.ajax({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/products/' + product_id + '/edit',
        type: 'GET',
        data: $('#product-form').serialize(),
        success: function(html){
        // alert(html);

        $('#properties-dropdown').html(html);
        }
        })
        }
        })
        });

        // При клике на кнопку под Select'ом свойств
        $(document).on('click', '#add-value', function(event) {
        event.preventDefault();

        // alert($('#properties-form input[name=value]').val());
        $.ajax({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/ajax_add_metric_value',
        type: 'POST',
        data: {value: $('#properties-form input[name=value]').val()},
        success: function(html){
        // alert(html);
        $('#values-table').append(html);
        $('#properties-form input[name=value]').val('');
        }
        })
        });

        // При клике на чекбокс метрики отображаем ее на странице
        $(document).on('click', '.add-metric', function() {

        // alert($(this).val());
        var id = $(this).val();

        // Если нужно добавить метрику
        if ($(this).prop('checked') == true) {
        $.ajax({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/ajax_add_relation_metric',
        type: 'POST',
        data: {id: $(this).val(), entity: 'products', entity_id: product_id},
        success: function(html){

        // alert(html);
        $('#metrics-table').append(html);
        $('#property-form').html('');
        }
        })
        } else {

        // Если нужно удалить метрику
        $.ajax({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/ajax_delete_relation_metric',
        type: 'POST',
        data: {id: $(this).val(), entity: 'products', entity_id: product_id},
        success: function(date){

        var result = $.parseJSON(date);
        // alert(result);

        if (result['error_status'] == 0) {

        $('#metrics-' + id).remove();
        } else {
        alert(result['error_message']);
        }; 
        }
        })
        }
        });

        // При клике на свойство отображаем или скрываем его метрики
        $(document).on('click', '.parent', function() {

        // Скрываем все метрики
        $('.checker-nested').hide();

        // Показываем нужную
        $('#' +$(this).data('open')).show();
        });

        // При клике на чекбокс метрики отображаем ее на странице
        $(document).on('click', '.add-composition', function() {

        // alert($(this).val());
        var id = $(this).val();

        // Если нужно добавить состав
        if ($(this).prop('checked') == true) {
        $.ajax({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/ajax_add_relation_composition',
        type: 'POST',
        data: {id: $(this).val(), product_id: product_id},
        success: function(html){

        // alert(html);
        $('#composition-table').append(html);
        }
        })
        } else {

        // Если нужно удалить состав
        $.ajax({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/ajax_delete_relation_composition',
        type: 'POST',
        data: {id: $(this).val(), product_id: product_id},
        success: function(date){

        var result = $.parseJSON(date);
        // alert(result);

        if (result['error_status'] == 0) {

        $('#compositions-' + id).remove();
        } else {
        alert(result['error_message']);
        }; 
        }
        })
        }
        });

        // При клике на фотку подствляем ее значения в блок редактирования
        $(document).on('click', '#photos-list img', function(event) {
        event.preventDefault();

        // Удаляем всем фоткам активынй класс
        $('#photos-list img').removeClass('active');

        // Наваливаем его текущей
        $(this).addClass('active');

        var id = $(this).data('id');

        // Получаем инфу фотки
        $.ajax({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/ajax_get_photo',
        type: 'POST',
        data: {id: id, entity: 'products'},
        success: function(html){

        // alert(html);
        $('#form-photo-edit').html(html);
        // $('#first-add').foundation();
        // $('#first-add').foundation('open');
        }
        })
        });

        // При сохранении информации фотки
        $(document).on('click', '#form-photo-edit .button', function(event) {
        event.preventDefault();

        var id = $(this).closest('#form-photo-edit').find('input[name=id]').val();
        // alert(id);

        // Записываем инфу и обновляем
        $.ajax({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/ajax_update_photo/' + id,
        type: 'PATCH',
        data: $(this).closest('#form-photo-edit').serialize(),
        success: function(html){
        // alert(html);
        $('#form-photo-edit').html(html);
        // $('#first-add').foundation();
        // $('#first-add').foundation('open');
        }
        })
        });

        // Оставляем ширину у вырванного из потока элемента
        var fixHelper = function(e, ui) {
        ui.children().each(function() {
        $(this).width($(this).width());
        });
        return ui;
        };

        // Включаем перетаскивание
        $("#values-table tbody").sortable({
        axis: 'y',
        helper: fixHelper, // ширина вырванного элемента
        handle: 'td:first', // указываем за какой элемент можно тянуть
        placeholder: "table-drop-color", // фон вырванного элемента
        update: function( event, ui ) {

        var entity = $(this).children('.item').attr('id').split('-')[0];
        }
        });

        // Настройки dropzone
        var minImageHeight = 795;
        Dropzone.options.myDropzone = {
        paramName: 'photo',
        maxFilesize: {{ $settings['img_max_size']->value }}, // MB
        maxFiles: 20,
        acceptedFiles: '{{ $settings['img_formats']->value }}',
        addRemoveLinks: true,
        init: function() {
        this.on("success", function(file, responseText) {
        file.previewTemplate.setAttribute('id',responseText[0].id);

        $.ajax({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/product/photos',
        type: 'post',
        data: {product_id: product_id},
        success: function(html){
        // alert(html);
        $('#photos-list').html(html);

        // $('#first-add').foundation();
        // $('#first-add').foundation('open');
        }
        })
        });
        this.on("thumbnail", function(file) {
        if (file.width < {{ $settings['img_min_width']->value }} || file.height < minImageHeight) {
        file.rejectDimensions();
        } else {
        file.acceptDimensions();
        }
        });
        },
        accept: function(file, done) {
        file.acceptDimensions = done;
        file.rejectDimensions = function() { done("Размер фото мал, нужно минимум {{ $settings['img_min_width']->value }} px в ширину"); };
        }
        };

    </script>
@endsection