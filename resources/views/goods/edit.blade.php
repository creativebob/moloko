@extends('layouts.app')

@section('inhead')
@include('includes.scripts.dropzone-inhead')
@include('includes.scripts.fancybox-inhead')
@include('includes.scripts.sortable-inhead')
@endsection

@section('title', 'Редактировать товар')

@section('breadcrumbs', Breadcrumbs::render('alias-edit', $page_info, $cur_goods))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕДАКТИРОВАТЬ товар &laquo{{ $cur_goods->name }}&raquo</h2>
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
            <li class="tabs-title"><a data-tabs-target="price-rules" href="#price-rules">Ценообразование</a></li>
            <li class="tabs-title"><a data-tabs-target="compositions" href="#compositions">Состав</a></li>
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

            {{ Form::model($cur_goods, ['url' => ['/admin/goods/'.$cur_goods->id], 'data-abide', 'novalidate', 'files'=>'true', 'id' => 'cur-goods-form']) }}
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

                                <label>Категория
                                    <select name="goods_category_id" disabled>
                                        @php
                                        echo $goods_categories_list;
                                        @endphp
                                    </select>
                                </label>
                                <label>Группа
                                    {{ Form::select('goods_product_id', $goods_products_list, $cur_goods->goods_product_id) }}
                                </label>
                                <label>Название услуги
                                    {{ Form::text('name', null, ['required']) }}
                                </label>

                                <fieldset class="fieldset">
                                    <legend class="checkbox">
                                        {{ Form::checkbox('portion', 1, null, ['id' => 'portion']) }}
                                        <label for="portion"><span>Принимать порциями</span></label>

                                    </legend>

                                    <div class="grid-x grid-margin-x">
                                        <div class="small-12 medium-6 cell">
                                            <label>Имя порции
                                                {{ Form::text('lol', "", ['class'=>'text-field name-field compact', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}']) }}
                                            </label>
                                        </div>
                                        <div class="small-6 medium-3 cell">
                                            <label>Сокр. имя
                                                {{ Form::text('lol',  "", ['class'=>'text-field name-field compact', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}']) }}
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

                                <label>Производитель
                                    {{ Form::select('manufacturer_id', $manufacturers_list, $cur_goods->manufacturer_id, ['placeholder' => 'Выберите производителя'])}}
                                </label>

                            </div>



                            <div class="small-12 medium-6 cell">

                                <div class="small-12 cell">
                                    <label>Фотография
                                        {{ Form::file('photo') }}
                                    </label>
                                    <div class="text-center">
                                        <img id="photo" @if (isset($cur_goods->photo_id)) src="/storage/{{ $cur_goods->company->id }}/media/goods/{{ $cur_goods->id }}/img/medium/{{ $cur_goods->photo->name }}" @endif>
                                    </div>
                                </div>
                            </div>


                        </div>

                    </div>
                    {{-- Конец левого блока на первой вкладке --}}


                    {{-- Правый блок на первой вкладке --}}
                    <div class="small-12 large-6 cell">
                        {{ Form::open(['url' => 'goods', 'data-abide', 'novalidate', 'id' => 'cur-goods-form']) }}

                        <fieldset class="fieldset-access">
                            <legend>Артикул</legend>

                            <div class="grid-x grid-margin-x">
                                <div class="small-12 medium-4 cell">
                                    <label>Удобный (вручную)
                                        {{ Form::text('manually', null) }}
                                    </label>
                                </div> 
                                <div class="small-12 medium-4 cell">
                                    <label>Программный
                                        {{ Form::text('internal', null, ['required', 'disabled']) }}
                                    </label>
                                </div>
                                <div class="small-12 medium-4 cell">
                                    <label>Внешний
                                        {{ Form::text('external') }}
                                    </label>
                                </div>
                            </div>
                        </fieldset>

                        <div class="grid-x">
                            <div class="small-12 cell">
                                <label>Описание товара
                                    @include('includes.inputs.textarea', ['name'=>'description', 'value'=>$cur_goods->description, 'required'=>''])
                                </label>
                            </div>
                        </div>
                        @if (($cur_goods->goods_product->goods_category->metrics_count > 0) || ($cur_goods->metrics_values_count > 0))
                        <fieldset class="fieldset-access">
                            <legend>Метрики</legend>

                            @if ($cur_goods->draft == 1)

                            @foreach ($cur_goods->goods_product->goods_category->metrics as $metric)
                            @include('goods.metrics.metric-input', $metric)
                            @endforeach

                            @else

                            @foreach ($cur_goods->metrics_values as $metric)
                            @include('goods.metrics.metric-value', $metric)
                            @endforeach

                            @endif

                            {{-- @if ($cur_goods->metrics_values_count > 0)
                               @each('goods.metrics.metric-input', $cur_goods->goods_product->goods_category->metrics, 'metric')
                               @each('goods.metrics.metric-value', $cur_goods->metrics_values, 'metric')
                               @endif --}}

                           </fieldset>
                           @endif
                           <div id="cur-goods-inputs"></div>
                           <div class="small-12 cell tabs-margin-top text-center">
                            <div class="item-error" id="cur-goods-error">Такой артикул уже существует!<br>Измените значения!</div>
                        </div>
                        {{ Form::hidden('cur_goods_id', $cur_goods->id) }}

                    </div>
                    {{-- Конец правого блока на первой вкладке --}}

                    {{-- Чекбокс черновика --}}
                    <div class="small-12 cell checkbox">
                        {{ Form::checkbox('draft', 1, $cur_goods->draft, ['id' => 'draft']) }}
                        <label for="draft"><span>Черновик</span></label>
                    </div>

                    {{-- Чекбокс отображения на сайте --}}
                    @can ('publisher', $cur_goods)
                    <div class="small-12 cell checkbox">
                        {{ Form::checkbox('display', 1, $cur_goods->display, ['id' => 'display']) }}
                        <label for="display"><span>Отображать на сайте</span></label>
                    </div>
                    @endcan

                    {{-- Чекбокс модерации --}}
                    @can ('moderator', $cur_goods)
                    @if ($cur_goods->moderation == 1)
                    <div class="small-12 cell checkbox">
                        @include('includes.inputs.moderation', ['value'=>$cur_goods->moderation, 'name'=>'moderation'])
                    </div>
                    @endif
                    @endcan

                    {{-- Чекбокс системной записи --}}
                    @can ('god', $cur_goods)
                    <div class="small-12 cell checkbox">
                        @include('includes.inputs.system', ['value'=>$cur_goods->system_item, 'name'=>'system_item']) 
                    </div>
                    @endcan

                    {{-- Кнопка --}}
                    <div class="small-12 cell tabs-button tabs-margin-top">
                        {{ Form::submit('Редактировать товар', ['class'=>'button', 'id' => 'add-cur-goods']) }}
                    </div>

                </div>{{-- Закрытие разделителя на блоки --}}
            </div>{{-- Закрытите таба --}}

            <!-- Ценообразование -->
            <div class="tabs-panel" id="price-rules">
                <div class="grid-x grid-padding-x">
                    <div class="small-12 medium-6 cell">

                        <fieldset class="fieldset-access">
                            <legend>Базовые настройки</legend>

                            <div class="grid-x grid-margin-x">
                                <div class="small-12 medium-6 cell">
                                    <label>Себестоимость
                                        {{ Form::number('cost', $cur_goods->cost) }}
                                    </label>
                                </div>
                                <div class="small-12 medium-6 cell">
                                    <label>Цена
                                        {{ Form::number('price', $cur_goods->price) }}
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
            {{ Form::close() }}

            <!-- Состав -->
            <div class="tabs-panel" id="compositions">
                <div class="grid-x grid-padding-x">
                    <div class="small-12 medium-12 cell">

                        {{-- Состав --}}
                        <div class="small-12 medium-12 cell">
                            <ul class="menu right">

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
                                @if (isset($cur_goods->goods_product->goods_category->compositions))



                                @each('goods.compositions.composition', $cur_goods->goods_product->goods_category->compositions, 'composition')
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Фотографии -->
            <div class="tabs-panel" id="photos">
                <div class="grid-x grid-padding-x">

                    <div class="small-12 medium-7 cell">
                        {{ Form::open(['url' => '/admin/goods/add_photo', 'data-abide', 'novalidate', 'files'=>'true', 'class'=> 'dropzone', 'id' => 'my-dropzone']) }}
                        {{ Form::hidden('name', $cur_goods->name) }}
                        {{ Form::hidden('id', $cur_goods->id) }}
                        {{ Form::close() }}
                        <ul class="grid-x small-up-4 tabs-margin-top" id="photos-list">
                            @if (isset($cur_goods->album_id))

                            @include('goods.photos', $cur_goods)

                            @endif
                        </ul>
                    </div>

                    <div class="small-12 medium-5 cell">

                        {{-- Форма редактированя фотки --}}
                        {{ Form::open(['url' => '/admin/goods/edit_photo', 'data-abide', 'novalidate', 'id' => 'form-photo-edit']) }}

                        {{ Form::hidden('name', $cur_goods->name) }}
                        {{ Form::hidden('id', $cur_goods->id) }}
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
@include('goods.scripts')

<script>

    // Основные ностойки
    var cur_goods_id = '{{ $cur_goods->id }}';

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
            url: '/admin/ajax_delete_relation_metric',
            type: 'POST',
            data: {id: id, entity: 'goods', entity_id: cur_goods_id},
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
                    //   url: '/goods/' + cur_goods_id + '/edit',
                    //   type: 'GET',
                    //   data: $('#service-form').serialize(),
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
            url: '/admin/ajax_delete_relation_composition',
            type: 'POST',
            data: {id: id, cur_goods_id: cur_goods_id},
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
        if ($('#goods:visible').length) {

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/admin/ajax_get_cur_goods_inputs',
                type: 'POST',
                data: {cur_goods_id: cur_goods_id},
                success: function(html){
                    // alert(html);
                    $('#cur-goods-inputs').html(html);
                    $('#cur-goods-inputs').foundation();
                    // Foundation.reInit($('#service-inputs'));
                }
            })
        }
    });

    // Проверяем наличие артикула в базе при клике на кнопку добавления артикула
    // $(document).on('click', '#add-cur-goods', function(event) {
    //     event.preventDefault();
    //     // alert($('#cur-goods-form').serialize());
    //     // alert(cur_goods_id);

    //     $.ajax({
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         url: '/admin/goods/' + cur_goods_id,
    //         type: 'PATCH',
    //         data: $('#cur-goods-form').serialize(),
    //         success: function(data) {
    //             var result = $.parseJSON(data);
    //             // alert(result['error_status']);
    //             // alert(data['metric_values']);
    //             if (result['error_status'] == 1) {
    //                 $('#add-cur-goods').prop('disabled', true);
    //                 $('#cur-goods-error').css('display', 'block');
    //             } else {

    //             }
    //         }
    //     })
    // });

    $(document).on('change', '#cur-goods-form input', function() {
        // alert('lol');
        $('#add-cur-goods').prop('disabled', false);
        $('#cur-goods-error').css('display', 'none');
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
                url: '/admin/ajax_add_property',
                type: 'POST',
                data: {id: id, entity: 'goods'},
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
            url: '/admin/metrics',
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
                    url: '/admin/goods/' + cur_goods_id + '/edit',
                    type: 'GET',
                    data: $('#cur-goods-form').serialize(),
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
            url: '/admin/ajax_add_metric_value',
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
                url: '/admin/ajax_add_relation_metric',
                type: 'POST',
                data: {id: $(this).val(), entity: 'goods', entity_id: cur_goods_id},
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
                url: '/admin/ajax_delete_relation_metric',
                type: 'POST',
                data: {id: $(this).val(), entity: 'goods', entity_id: cur_goods_id},
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
                url: '/admin/ajax_add_page_composition',
                type: 'POST',
                data: {id: $(this).val(), entity: 'goods', cur_goods_id: cur_goods_id},
                success: function(html){

                    // alert(html);
                    $('#composition-table').append(html);
                }
            })
        } else {

            // Если нужно удалить состав
            $('#compositions-' + id).remove();
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
            url: '/admin/ajax_get_photo',
            type: 'POST',
            data: {id: id, entity: 'goods'},
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
            url: '/admin/ajax_update_photo/' + id,
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
    Dropzone.options.myDropzone = {
        paramName: 'photo',
        maxFilesize: {{ $settings_album['img_max_size'] }}, // MB
        maxFiles: 20,
        acceptedFiles: '{{ $settings_album['img_formats'] }}',
        addRemoveLinks: true,
        init: function() {
            this.on("success", function(file, responseText) {
                file.previewTemplate.setAttribute('id',responseText[0].id);

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/admin/goods/photos',
                    type: 'POST',
                    data: {cur_goods_id: cur_goods_id},
                    success: function(html){
                        // alert(html);
                        $('#photos-list').html(html);

                        // $('#first-add').foundation();
                        // $('#first-add').foundation('open');
                    }
                })
            });
            this.on("thumbnail", function(file) {
                if (file.width < {{ $settings_album['img_min_width'] }} || file.height < {{ $settings_album['img_min_height'] }}) {
                    file.rejectDimensions();
                } else {
                    file.acceptDimensions();
                }
            });
        },
        accept: function(file, done) {
            file.acceptDimensions = done;
            file.rejectDimensions = function() { done("Размер фото мал, нужно минимум {{ $settings_album['img_min_width'] }} px в ширину"); };
        }
    };

</script>
@endsection