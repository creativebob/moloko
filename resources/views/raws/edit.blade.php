@extends('layouts.app')

@section('inhead')
@include('includes.scripts.dropzone-inhead')
@include('includes.scripts.fancybox-inhead')
@include('includes.scripts.sortable-inhead')
@include('includes.scripts.chosen-inhead')
@endsection

@section('title', 'Редактировать сырьё')

@section('breadcrumbs', Breadcrumbs::render('alias-edit', $page_info, $raw->raws_article))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕДАКТИРОВАТЬ сырьё &laquo{{ $raw->raws_article->name }}&raquo</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@php
$disabled = $raw->raws_article->draft == null;
@endphp

@section('content')
<div class="grid-x tabs-wrap">
    <div class="small-12 cell">
        <ul class="tabs-list" data-tabs id="tabs">
            <li class="tabs-title is-active">
                <a href="#options" aria-selected="true">Общая информация</a>
            </li>
            <li class="tabs-title">
                <a data-tabs-target="price-rules" href="#price-rules">Ценообразование</a>
            </li>
            <li class="tabs-title">
                <a data-tabs-target="catalogs" href="#catalogs">Каталоги</a>
            </li>
            <li class="tabs-title">
                <a data-tabs-target="photos" href="#photos">Фотографии</a>
            </li>
        </ul>
    </div>
</div>

<div class="grid-x tabs-wrap inputs">
    <div class="small-12 cell tabs-margin-top">
        <div class="tabs-content" data-tabs-content="tabs">

            {{ Form::model($raw, [
                'route' => ['raws.update', $raw->id],
                'data-abide',
                'novalidate',
                'files '=> 'true',
                'id' => 'form-raw'
            ]
            ) }}
            {{ method_field('PATCH') }}

            {{-- Общая информация --}}
            <div class="tabs-panel is-active" id="options">

                {{-- Разделитель на первой вкладке --}}
                <div class="grid-x grid-padding-x">

                    {{-- Левый блок на первой вкладке --}}
                    <div class="small-12 large-6 cell">

                        {{-- Основная инфа --}}
                        <div class="grid-x grid-margin-x">
                            <div class="small-12 medium-6 cell">

                                <label>Название сырья
                                    {{ Form::text('name', $raw->raws_article->name, ['required', $disabled]) }}
                                </label>

                                <label>Группа
                                    @include('includes.selects.raws_products', ['raws_category_id' => $raw->raws_article->raws_product->raws_category_id, 'set_status' => $raw->raws_article->raws_product->set_status, 'raws_product_id' => $raw->raws_article->raws_product_id])
                                </label>

                                <label>Категория
                                    @include('includes.selects.raws_categories', ['raws_category_id' => $raw->raws_article->raws_product->raws_category_id])
                                </label>

                                @include('includes.selects.manufacturers', ['manufacturer_id' => $raw->raws_article->manufacturer_id, 'draft' => $raw->raws_article->draft])

                                {!! Form::hidden('id', null, ['id' => 'item-id']) !!}

                            </div>

                            <div class="small-12 medium-6 cell">

                                <div class="small-12 cell">
                                    <label>Фотография
                                        {{ Form::file('photo') }}
                                    </label>
                                    <div class="text-center">
                                        <img id="photo" @isset($raw->photo_id)) src="/storage/{{ $raw->company->id }}/media/raws/{{ $raw->id }}/img/medium/{{ $raw->photo->name }}" @endisset>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    {{-- Конец левого блока на первой вкладке --}}


                    {{-- Правый блок на первой вкладке --}}
                    <div class="small-12 large-6 cell">
                        {{ Form::open(['url' => 'raws', 'data-abide', 'novalidate', 'id' => 'form-raw']) }}

                        <fieldset class="fieldset-access">
                            <legend>Артикул</legend>

                            <div class="grid-x grid-margin-x">
                                <div class="small-12 medium-4 cell">
                                    <label>Удобный (вручную)
                                        {{ Form::text('manually', null, [$disabled]) }}
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
                                <label>Описание сырья
                                    @include('includes.inputs.textarea', ['name' => 'description', 'value' => $raw->description])
                                </label>
                            </div>
                        </div>
                        @php
                        $metric_relation = ($raw->raws_article->raws_product->set_status == 'one') ? 'one_metrics' : 'set_metrics';
                        @endphp

                        @if ($raw->raws_article->metrics->isNotEmpty() || $raw->raws_article->raws_product->raws_category->$metric_relation->isNotEmpty())

                        @include('includes.scripts.class.metric_validation')

                        <fieldset class="fieldset-access">
                            <legend>Метрики</legend>

                            <div id="metrics-list">

                                {{-- Если уже сохранили метрики товара, то тянем их с собой --}}
                                @if ($raw->raws_article->metrics->isNotEmpty())
                                @foreach ($raw->raws_article->metrics->unique() as $metric)
                                @include('raws.metrics.metric_input', $metric)
                                @endforeach

                                @else

                                @if ($raw->raws_article->raws_product->raws_category->$metric_relation->isNotEmpty())
                                @foreach ($raw->raws_article->raws_product->raws_category->$metric_relation as $metric)
                                @include('raws.metrics.metric_input', $metric)
                                @endforeach
                                @endif

                                @endif

                            </div>
                        </fieldset>

                        @endif

                        <div id="raw-inputs"></div>
                        <div class="small-12 cell tabs-margin-top text-center">
                            <div class="item-error" id="raw-error">Такой артикул уже существует!<br>Измените значения!</div>
                        </div>
                        {{ Form::hidden('raw_id', $raw->id) }}
                    </div>
                    {{-- Конец правого блока на первой вкладке --}}

                    {{-- Чекбокс черновика --}}
                    @if ($raw->raws_article->draft == 1)
                    <div class="small-12 cell checkbox">
                        {{ Form::checkbox('draft', 1, $raw->raws_article->draft, ['id' => 'draft']) }}
                        <label for="draft"><span>Черновик</span></label>
                    </div>
                    @endif

                    {{-- Чекбоксы управления --}}
                    @include('includes.control.checkboxes', ['item' => $raw])

                    {{-- Кнопка --}}
                    <div class="small-12 cell tabs-button tabs-margin-top">
                        {{ Form::submit('Редактировать сырьё', ['class'=>'button', 'id' => 'add-raws']) }}
                    </div>

                </div>
            </div>

            {{-- Ценообразование --}}
            <div class="tabs-panel" id="price-rules">
                <div class="grid-x grid-padding-x">
                    <div class="small-12 medium-6 cell">

                        <fieldset class="fieldset-access">
                            <legend>Базовые настройки</legend>

                            <div class="grid-x grid-margin-x">
                                <div class="small-12 medium-6 cell">
                                    <label>Себестоимость
                                        {{ Form::number('cost', $raw->cost) }}
                                    </label>
                                </div>
                                <div class="small-12 medium-6 cell">
                                    <label>Цена за (<span id="unit">{{ ($raw->portion_status == null) ?$raw->raws_article->raws_product->unit->abbreviation : 'порцию' }}</span>)
                                        {{ Form::number('price', $raw->price) }}
                                    </label>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="fieldset portion-fieldset" id="portion-fieldset">
                            <legend class="checkbox">
                                {{ Form::checkbox('portion_status', 1, $raw->portion_status, ['id' => 'portion', $disabled ? 'disabled' : '']) }}
                                <label for="portion">
                                    <span id="portion-change">Принимать порциями</span>
                                </label>

                            </legend>

                            <div class="grid-x grid-margin-x" id="portion-block">
                                <div class="small-12 cell @if ($raw->portion_status == null) portion-hide @endif">
                                    <label>Имя&nbsp;порции
                                        {{ Form::text('portion_name', $raw->portion_name, ['class'=>'text-field name-field compact', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', $disabled ? 'disabled' : '']) }}
                                    </label>
                                </div>
                                <div class="small-6 cell @if ($raw->portion_status == null) portion-hide @endif">
                                    <label>Сокр.&nbsp;имя
                                        {{ Form::text('portion_abbreviation',  $raw->portion_abbreviation, ['class'=>'text-field name-field compact', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', $disabled ? 'disabled' : '']) }}
                                    </label>
                                </div>
                                <div class="small-6 cell @if ($raw->portion_status == null) portion-hide @endif">
                                    <label>Кол-во,&nbsp;{{ $raw->raws_article->raws_product->unit->abbreviation }}
                                        {{-- Количество чего-либо --}}
                                        {{ Form::text('portion_count', $raw->portion_count, ['class'=>'digit-field name-field compact', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', $disabled ? 'disabled' : '']) }}
                                        <div class="sprite-input-right find-status" id="name-check"></div>
                                        <span class="form-error">Введите количество</span>
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>

            <!-- Каталоги -->
            <div class="tabs-panel" id="catalogs">
                <div class="grid-x grid-padding-x">
                    <div class="small-12 medium-6 cell">


                        <fieldset class="fieldset-access">
                            <legend>Каталоги</legend>

                            {{-- Form::select('catalogs[]', $catalogs_list, $raw->catalogs, ['class' => 'chosen-select', 'multiple']) --}}
                            @include('includes.selects.catalogs')

                        </fieldset>
                    </div>
                </div>
            </div>
            {{ Form::close() }}

            {{-- Фотографии --}}
            <div class="tabs-panel" id="photos">
                <div class="grid-x grid-padding-x">


                    <div class="small-12 medium-7 cell">
                        {!!  Form::open(['url' => '/admin/raws/add_photo', 'data-abide', 'novalidate', 'files'=>'true', 'class'=> 'dropzone', 'id' => 'my-dropzone']) !!}
                        {{ Form::hidden('name', $raw->raws_article->name) }}
                        {{ Form::hidden('id', $raw->id) }}
                        {!! Form::close() !!}
                        <ul class="grid-x small-up-4 tabs-margin-top" id="photos-list">

                            @isset($raw->album_id)
                            @include('raws.photos', $raw)
                            @endisset

                        </ul>
                    </div>

                    <div class="small-12 medium-5 cell">

                        {{-- Форма редактированя фотки --}}
                        {{ Form::open(['url' => '/admin/raws/edit_photo', 'data-abide', 'novalidate', 'id' => 'form-photo-edit']) }}

                        {{ Form::hidden('photo_name', $raw->name) }}
                        {{ Form::hidden('id', $raw->id) }}
                        {{ Form::close() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>

    // Основные настройки
    var raw_id = '{{ $raw->id }}';
    var set_status = '{{ $raw->raws_article->raws_product->set_status }}';
    var entity = 'raws';

    var metrics_count = '{{ count($raw->raws_article->metrics) }}';

    if (set_status == 'one') {
        var compositions_count = 0;
    }

    var compositions_count = '{{ count($raw->raws_article->metrics) }}';

    var category_id = '{{ $raw->raws_article->raws_product->raws_category_id }}';

    var unit = '{{ $raw->raws_article->raws_product->unit->abbreviation }}';

    // Мульти Select
    $(".chosen-select").chosen({width: "95%"});

    $(document).on('change', '#select-raws_categories', function(event) {
        event.preventDefault();

        // Меняем группы
        $.post('/admin/raws_products_list', {raws_category_id: $(this).val(), raws_product_id: $('#select-raws_products').val(), set_status: set_status}, function(list){
            // alert(html);
            $('#select-raws_products').replaceWith(list);
        });
    });

    $(document).on('click', '#portion', function() {
        $('#portion-block div').toggle();
        // $('#portion-fieldset').toggleClass('portion-fieldset');
        $('#unit').text( $(this).prop('checked') ? 'порцию' : unit );
    });

    // При клике на удаление состава со страницы
    $(document).on('click', '[data-open="delete-composition"]', function() {

        // Находим описание сущности, id и название удаляемого элемента в родителе
        var parent = $(this).closest('.item');
        // var type = parent.attr('id').split('-')[0];
        $('.title-composition').text(parent.data('name'));
        // $('.delete-button').attr('id', 'del-' + type + '-' + id);
        $('.composition-delete-button').attr('id', 'delete_metric-' + parent.attr('id').split('-')[1]);
    });

    // При клике на подтверждение удаления состава со страницы
    $(document).on('click', '.composition-delete-button', function() {

        // Находим id элемента в родителе
        var id = $(this).attr('id').split('-')[1];
        // alert(id);

        // Удаляем элемент со страницы
        $('#compositions-' + id).remove();

        // Убираем отмеченный чекбокс в списке метрик
        $('#add-composition-' + id).prop('checked', false);

        // Foundation.reInit($('#form-raw'));
    });

    // При клике на удаление состава со страницы
    $(document).on('click', '[data-open="delete-value"]', function() {

        // Удаляем элемент со страницы
        $(this).closest('.item').remove();
    });

    // Проверяем наличие артикула в базе при клике на кнопку добавления артикула
    // $(document).on('click', '#add-cur-raws', function(event) {
    //     event.preventDefault();
    //     // alert($('#form-raw').serialize());
    //     // alert(raw_id);

    //     $.ajax({
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         url: '/admin/raws/' + raw_id,
    //         type: 'PATCH',
    //         data: $('#form-raw').serialize(),
    //         success: function(data) {
    //             var result = $.parseJSON(data);
    //             // alert(result['error_status']);
    //             // alert(data['metric_values']);
    //             if (result['error_status'] == 1) {
    //                 $('#add-cur-raws').prop('disabled', true);
    //                 $('#cur-raws-error').css('display', 'block');
    //             } else {

    //             }
    //         }
    //     })
    // });

    // $(document).on('change', '#form-raw input', function() {
    //     // alert('lol');
    //     $('#add-cur-raws').prop('disabled', false);
    //     $('#cur-raws-error').hide();
    // });

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

            $.post('/admin/ajax_add_property', {id: id, entity: 'raws'}, function(html) {
                // alert(html);
                $('#property-form').html(html);
                $('#properties-dropdown').foundation('close');
            })
        };
    });

    // При клике на кнопку под Select'ом свойств
    $(document).on('click', '#add-metric', function(event) {
        event.preventDefault();
        // alert($('#properties-form').serialize());

        $.post('/admin/metrics', $('#properties-form').serialize(), function(html){
            // alert(html);
            $('#metrics-table').append(html);
            $('#property-form').html('');

            // В случае успеха обновляем список метрик
            $.get('/admin/raws/' + raw_id + '/edit', $('#form-raw').serialize(), function(html) {
                // alert(html);
                $('#properties-dropdown').html(html);
            })
        })
    });

    // При клике на кнопку под Select'ом свойств
    $(document).on('click', '#add-value', function(event) {
        event.preventDefault();

        // alert($('#properties-form input[name=value]').val());
        $.post('/admin/ajax_add_metric_value', {value: $('#properties-form input[name=value]').val()}, function(html){
            // alert(html);
            $('#values-table').append(html);
            $('#properties-form input[name=value]').val('');
        })
    });

    // При клике на чекбокс метрики отображаем ее на странице
    $(document).on('click', '.add-metric', function() {

        // alert($(this).val());
        var id = $(this).val();

        // Если нужно добавить метрику
        if ($(this).prop('checked') == true) {
            $.post('/admin/ajax_add_relation_metric', {id: $(this).val(), entity: 'raws', entity_id: raw_id}, function(html){
                // alert(html);
                $('#metrics-table').append(html);
                $('#property-form').html('');
            })
        } else {
            // Если нужно удалить метрику
            $.post('/admin/ajax_delete_relation_metric', {id: $(this).val(), entity: 'raws', entity_id: raw_id}, function(date){
                var result = $.parseJSON(date);
                // alert(result);

                if (result['error_status'] == 0) {

                    $('#metrics-' + id).remove();
                } else {
                    alert(result['error_message']);
                };
            })
        }
    });

    // При клике на свойство отображаем или скрываем его метрики
    $(document).on('click', '.parent', function() {

        // Скрываем все метрики
        $('.checker-nested').hide();

        // Показываем нужную
        $('#' + $(this).data('open')).show();
    });

    // При клике на чекбокс метрики отображаем ее на странице
    $(document).on('click', '.add-composition', function() {
        // alert($(this).val());
        let id = $(this).val();

        // Если нужно добавить состав
        if ($(this).prop('checked')) {
            $.post('/admin/ajax_add_page_composition', {id: $(this).val(), entity: entity, set_status: set_status}, function(html){
                // alert(html);
                $('#composition-table').append(html);
            })
        } else {
            // Если нужно удалить состав
            $('#compositions-' + id).remove();
        }
    });

    $(document).ready(function($) {
        $('.checkboxer-title .form-error').hide();
    });

    // Валидация группы чекбоксов
    // $(document).on('click', '.checkbox-group input:checkbox', function() {
    //     let id = $(this).closest('.dropdown-pane').attr('id');
    //     if ($(this).closest('.checkbox-group').find("input:checkbox:checked").length == 0) {
    //         $('div[data-toggle=' + id + ']').find('.form-error').show();
    //         $('#add-cur-raws').prop('disabled', true);
    //     } else {
    //         $('div[data-toggle=' + id + ']').find('.form-error').hide();
    //         $('#add-cur-raws').prop('disabled', false);
    //     };
    // });

    // Валидация при клике на кнопку
    $(document).on('click', '#add-cur-raws', function(event) {
        let error = 0;
        $(".checkbox-group").each(function(i) {
            if ($(this).find("input:checkbox:checked").length == 0) {
                let id = $(this).closest('.dropdown-pane').attr('id');
                $('div[data-toggle=' + id + ']').find('.form-error').show();
                error = error + 1;
            };
        });
        $('#form-raw').foundation('validateForm');
        if (error > 0) {
            event.preventDefault();
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
            data: {id: id, entity: 'raws'},
            success: function(html){

                // alert(html);
                $('#form-photo-edit').html(html);
                // $('#modal-create').foundation();
                // $('#modal-create').foundation('open');
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
                // $('#modal-create').foundation();
                // $('#modal-create').foundation('open');
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
        maxFilesize: '{{ $settings['img_max_size'] }}',
        maxFiles: 20,
        acceptedFiles: '{{ $settings['img_formats'] }}',
        addRemoveLinks: true,
        init: function() {
            this.on("success", function(file, responseText) {
                file.previewTemplate.setAttribute('id',responseText[0].id);

                $.post('/admin/raws/photos', {raw_id: raw_id}, function(html){
                    // alert(html);
                    $('#photos-list').html(html);
                    // $('#modal-create').foundation();
                    // $('#modal-create').foundation('open');
                })
            });
            this.on("thumbnail", function(file) {
                if (file.width < '{{ $settings['img_min_width'] }}' || file.height < '{{ $settings['img_min_height'] }}') {
                    file.rejectDimensions();
                } else {
                    file.acceptDimensions();
                }
            });
        },
        accept: function(file, done) {
            file.acceptDimensions = done;
            file.rejectDimensions = function() { done("Размер фото мал, нужно минимум {{ $settings['img_min_width'] }} px в ширину"); };
        }
    };

</script>

@include('includes.scripts.inputs-mask')
@include('includes.scripts.upload-file')
@include('raws.scripts')
{{-- Проверка поля на существование --}}
@include('includes.scripts.check')
@endsection