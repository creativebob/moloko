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

@section('content')
<div class="grid-x tabs-wrap">
    <div class="small-12 cell">
        <ul class="tabs-list" data-tabs id="tabs">
            <li class="tabs-title is-active"><a href="#options" aria-selected="true">Общая информация</a></li>
            <li class="tabs-title"><a data-tabs-target="price-rules" href="#price-rules">Ценообразование</a></li>

            <li class="tabs-title"><a data-tabs-target="catalogs" href="#catalogs">Каталоги</a></li> 

            <li class="tabs-title"><a data-tabs-target="photos" href="#photos">Фотографии</a></li> 
        </ul>
    </div>
</div>

<div class="grid-x tabs-wrap inputs">
    <div class="small-12 cell tabs-margin-top">
        <div class="tabs-content" data-tabs-content="tabs">

            {{ Form::model($raw, ['url' => ['/admin/raws/'.$raw->id], 'data-abide', 'novalidate', 'files'=>'true', 'id' => 'raws-form']) }}
            {{ method_field('PATCH') }}

            @php
            if ($raw->draft == 1) {
                $disabled = '';
            } else {
                $disabled = 'disabled';
            }
            @endphp

            <!-- Общая информация -->
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
                                    {{ Form::select('raws_product_id', $raws_products_list, $raw->raws_article->raws_product_id, [$disabled]) }}
                                </label>

                                 <label>Категория
                                    <select name="raws_category_id" {{ $disabled }}>
                                        @php
                                        echo $raws_categories_list;
                                        @endphp
                                    </select>
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
                                    {{ Form::select('manufacturer_id', $manufacturers_list, $raw->manufacturer_id, ['placeholder' => 'Выберите производителя', $disabled])}}
                                </label>

                            </div>



                            <div class="small-12 medium-6 cell">

                                <div class="small-12 cell">
                                    <label>Фотография
                                        {{ Form::file('photo') }}
                                    </label>
                                    <div class="text-center">
                                        <img id="photo" @if (isset($raw->photo_id)) src="/storage/{{ $raw->company->id }}/media/raws/{{ $raw->id }}/img/medium/{{ $raw->photo->name }}" @endif>
                                    </div>
                                </div>
                            </div>


                        </div>

                    </div>
                    {{-- Конец левого блока на первой вкладке --}}


                    {{-- Правый блок на первой вкладке --}}
                    <div class="small-12 large-6 cell">
                        {{ Form::open(['url' => 'raws', 'data-abide', 'novalidate', 'id' => 'raws-form']) }}

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
                                    @include('includes.inputs.textarea', ['name'=>'description', 'value'=>$raw->description, 'required'=>''])
                                </label>
                            </div>
                        </div>
                        @if (($raw->raws_article->raws_product->raws_category->metrics_count > 0) || ($raw->metrics_values_count > 0))
                        <fieldset class="fieldset-access">
                            <legend>Метрики</legend>

                            @if ($raw->draft == 1)

                            @foreach ($raw->raws_article->raws_product->raws_category->metrics as $metric)
                            @include('raws.metrics.metric-input', $metric)
                            @endforeach

                            @else

                            @foreach ($raw->metrics_values as $metric)
                            @include('raws.metrics.metric-value', $metric)
                            @endforeach

                            @endif

                            {{-- @if ($raw->metrics_values_count > 0)
                               @each('raws.metrics.metric-input', $raw->raws_article->raws_product->raws_category->metrics, 'metric')
                               @each('raws.metrics.metric-value', $raw->metrics_values, 'metric')
                               @endif --}}

                           </fieldset>
                           @endif
                           <div id="raws-inputs"></div>
                           <div class="small-12 cell tabs-margin-top text-center">
                            <div class="item-error" id="raws-error">Такой артикул уже существует!<br>Измените значения!</div>
                        </div>
                        {{ Form::hidden('raw_id', $raw->id) }}

                    </div>
                    {{-- Конец правого блока на первой вкладке --}}

                    @if ($raw->draft == 1)
                    {{-- Чекбокс черновика --}}
                    <div class="small-12 cell checkbox">
                        {{ Form::checkbox('draft', 1, $raw->draft, ['id' => 'draft']) }}
                        <label for="draft"><span>Черновик</span></label>
                    </div>
                    @endif

                    {{-- Чекбоксы управления --}}
                    @include('includes.control.checkboxes', ['item' => $raw])  

                    {{-- Кнопка --}}
                    <div class="small-12 cell tabs-button tabs-margin-top">
                        {{ Form::submit('Редактировать сырьё', ['class'=>'button', 'id' => 'add-raws']) }}
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
                                        {{ Form::number('cost', $raw->cost, [$disabled]) }}
                                    </label>
                                </div>
                                <div class="small-12 medium-6 cell">
                                    <label>Цена
                                        {{ Form::number('price', $raw->price, [$disabled]) }}
                                    </label>
                                </div>

                                <div class="small-12 cell checkbox">
                                    {{ Form::checkbox('sail_status', 1, $raw->sail_status, ['id' => 'sail-status']) }}
                                    <label for="sail-status"><span>Для продажи</span></label>
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
                            <select name="catalogs[]" data-placeholder="Выберите каталоги..." multiple class="chosen-select">
                                @php
                                echo $catalogs_list;
                                @endphp
                            </select>

                        </fieldset>
                    </div>
                </div>
            </div>
            {{ Form::close() }}

            <!-- Фотографии -->
            <div class="tabs-panel" id="photos">
                <div class="grid-x grid-padding-x">

                    <div class="small-12 medium-7 cell">
                        {{ Form::open(['url' => '/admin/raws/add_photo', 'data-abide', 'novalidate', 'files'=>'true', 'class'=> 'dropzone', 'id' => 'my-dropzone']) }}
                        {{ Form::hidden('name', $raw->name) }}
                        {{ Form::hidden('id', $raw->id) }}
                        {{ Form::close() }}
                        <ul class="grid-x small-up-4 tabs-margin-top" id="photos-list">
                            @if (isset($raw->album_id))

                            @include('raws.photos', $raw)

                            @endif
                        </ul>
                    </div>

                    <div class="small-12 medium-5 cell">

                        {{-- Форма редактированя фотки --}}
                        {{ Form::open(['url' => '/admin/raws/edit_photo', 'data-abide', 'novalidate', 'id' => 'form-photo-edit']) }}

                        {{ Form::hidden('name', $raw->name) }}
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

@include('includes.scripts.inputs-mask')
@include('includes.scripts.upload-file')
@include('raws.scripts')

<script>

    // Основные ностойки
    var raw_id = '{{ $raw->id }}';

    // Мульти Select
    $(".chosen-select").chosen({width: "95%"});

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
            data: {id: id, entity: 'raws', entity_id: raw_id},
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
                    //   url: '/raws/' + raw_id + '/edit',
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
            data: {id: id, raw_id: raw_id},
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
        if ($('#raws:visible').length) {

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/admin/ajax_get_raw_inputs',
                type: 'POST',
                data: {raw_id: raw_id},
                success: function(html){
                    // alert(html);
                    $('#raws-inputs').html(html);
                    $('#raws-inputs').foundation();
                    // Foundation.reInit($('#service-inputs'));
                }
            })
        }
    });

    // Проверяем наличие артикула в базе при клике на кнопку добавления артикула
    // $(document).on('click', '#add-raws', function(event) {
    //     event.preventDefault();
    //     // alert($('#raws-form').serialize());
    //     // alert(raw_id);

    //     $.ajax({
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         url: '/admin/raws/' + raw_id,
    //         type: 'PATCH',
    //         data: $('#raws-form').serialize(),
    //         success: function(data) {
    //             var result = $.parseJSON(data);
    //             // alert(result['error_status']);
    //             // alert(data['metric_values']);
    //             if (result['error_status'] == 1) {
    //                 $('#add-raws').prop('disabled', true);
    //                 $('#raws-error').css('display', 'block');
    //             } else {

    //             }
    //         }
    //     })
    // });

    $(document).on('change', '#raws-form input', function() {
        // alert('lol');
        $('#add-raws').prop('disabled', false);
        $('#raws-error').css('display', 'none');
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
                data: {id: id, entity: 'raws'},
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
                    url: '/admin/raws/' + raw_id + '/edit',
                    type: 'GET',
                    data: $('#raws-form').serialize(),
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
                data: {id: $(this).val(), entity: 'raws', entity_id: raw_id},
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
                data: {id: $(this).val(), entity: 'raws', entity_id: raw_id},
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
                data: {id: $(this).val(), entity: 'raws', raw_id: raw_id},
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
            data: {id: id, entity: 'raws'},
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
                    url: '/admin/raws/photos',
                    type: 'POST',
                    data: {raw_id: raw_id},
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