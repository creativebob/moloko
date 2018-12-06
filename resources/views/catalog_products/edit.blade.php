@extends('layouts.app')

@section('inhead')
@include('includes.scripts.dropzone-inhead')
@include('includes.scripts.fancybox-inhead')
@include('includes.scripts.sortable-inhead')

<script src="/crm/js/plugins/chosen/chosen.jquery.js"></script>
<link rel="stylesheet" href="/crm/js/plugins/chosen/chosen.css">
@endsection

@section('title', 'Редактировать услугу')

@section('breadcrumbs', Breadcrumbs::render('alias-edit', $page_info, $service->services_article))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕДАКТИРОВАТЬ услугу &laquo{{ $service->services_article->name }}&raquo</h2>
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

            {{ Form::model($service, ['url' => ['/admin/services/'.$service->id], 'data-abide', 'novalidate', 'files'=>'true', 'id' => 'service-form']) }}
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

                                <label>Название услуги
                                    {{ Form::text('name', $service->services_article->name, ['required']) }}
                                </label>

                                <label>Группа
                                    {{ Form::select('services_product_id', $services_products_list, $service->services_product_id) }}
                                </label>

                                <label>Категория
                                    <select name="services_category_id">
                                        @php
                                        echo $services_categories_list;
                                        @endphp
                                    </select>
                                </label>

                                <div class="small-12 cell">
                                    <label>Описание услуги
                                        @include('includes.inputs.textarea', ['name'=>'description', 'value'=>$service->description])
                                    </label>
                                </div>
                                <div class="grid-x grid-margin-x">
                                    <div class="small-12 medium-6 cell">
                                        <label>Цена
                                            {{ Form::number('price', $service->price) }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="small-12 medium-6 cell">

                                <div class="small-12 cell">
                                    <label>Фотография
                                        {{ Form::file('photo') }}
                                    </label>
                                    <div class="text-center">
                                        <img id="photo" @if (isset($service->photo_id)) src="/storage/{{ $service->company_id }}/media/services/{{ $service->id }}/img/medium/{{ $service->photo->name }}" @endif>
                                    </div>
                                </div>
                            </div>


                        </div>

                    </div>
                    {{-- Конец левого блока на первой вкладке --}}


                    {{-- Правый блок на первой вкладке --}}
                    <div class="small-12 large-6 cell">
                        {{ Form::open(['url' => 'services', 'data-abide', 'novalidate', 'id' => 'service-form']) }}

                        <fieldset class="fieldset-access">
                            <legend>Артикул</legend>

                            <div class="grid-x grid-margin-x">
                                <div class="small-12 medium-4 cell">
                                    <label>Удобный (вручную)
                                        {{ Form::text('manually', $service->manually) }}
                                    </label>
                                </div>
                                <div class="small-12 medium-4 cell">
                                    <label>Программный
                                        {{ Form::text('internal', $service->services_article->internal, ['required', 'disabled']) }}
                                    </label>
                                </div>
                                <div class="small-12 medium-4 cell">
                                    <label>Внешний
                                        {{ Form::text('external', $service->external) }}
                                    </label>
                                </div>
                            </div>
                        </fieldset>


                        @if (($service->services_article->services_product->services_category->metrics_count > 0) || ($service->metrics_values_count > 0))
                        <fieldset class="fieldset-access">
                            <legend>Метрики</legend>

                            @if ($service->draft == 1)

                            @foreach ($service->services_article->services_product->services_category->metrics as $metric)
                            @include('services.metrics.metric-input', $metric)
                            @endforeach

                            @else

                            @foreach ($service->metrics_values as $metric)
                            @include('services.metrics.metric-value', $metric)
                            @endforeach

                            @endif

                            {{-- @if ($service->metrics_values_count > 0)
                               @each('services.metrics.metric-input', $service->services_product->services_category->metrics, 'metric')
                               @each('services.metrics.metric-value', $service->metrics_values, 'metric')
                               @endif --}}

                           </fieldset>
                           @endif
                           <div id="service-inputs"></div>
                           <div class="small-12 cell tabs-margin-top text-center">
                            <div class="item-error" id="service-error">Такой артикул уже существует!<br>Измените значения!</div>
                        </div>
                        {{ Form::hidden('service_id', $service->id) }}


                    </div>
                    {{-- Конец правого блока на первой вкладке --}}


                    {{-- Чекбокс черновика --}}
                    <div class="small-12 cell checkbox">
                        {{ Form::checkbox('draft', 1, $service->draft, ['id' => 'draft']) }}
                        <label for="draft"><span>Черновик</span></label>
                    </div>

                    {{-- Чекбоксы управления --}}
                    @include('includes.control.checkboxes', ['item' => $service])

                    {{-- Кнопка --}}
                    <div class="small-12 cell tabs-button tabs-margin-top">
                        {{ Form::submit('Редактировать услугу', ['class'=>'button', 'id' => 'add-service']) }}
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
                                        {{ Form::number('cost', $service->cost) }}
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

                             {{-- Form::select('catalogs[]', $catalogs_list, $service->catalogs, ['class' => 'chosen-select', 'multiple']) --}}
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
                        {{ Form::open(['url' => '/admin/service/add_photo', 'data-abide', 'novalidate', 'files'=>'true', 'class'=> 'dropzone', 'id' => 'my-dropzone']) }}
                        {{ Form::hidden('name', $service->name) }}
                        {{ Form::hidden('id', $service->id) }}
                        {{ Form::close() }}
                        <ul class="grid-x small-up-4 tabs-margin-top" id="photos-list">
                            @if (isset($service->album_id))

                            @include('services.photos', $service)

                            @endif
                        </ul>
                    </div>

                    <div class="small-12 medium-5 cell">

                        {{-- Форма редактированя фотки --}}
                        {{ Form::open(['url' => '/admin/service/edit_photo', 'data-abide', 'novalidate', 'id' => 'form-photo-edit']) }}


                        {{ Form::hidden('name', $service->name) }}
                        {{ Form::hidden('id', $service->id) }}
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
@include('services.scripts')
@php
$settings = config()->get('settings');
@endphp

<script>


        // Основные ностойки
        var service_id = '{{ $service->id }}';

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
                data: {id: id, entity: 'services', entity_id: service_id},
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
        //   url: '/services/' + service_id + '/edit',
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
                data: {id: id, service_id: service_id},
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
            if ($('#services:visible').length) {

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/admin/ajax_get_service_inputs',
                    type: 'POST',
                    data: {service_id: service_id},
                    success: function(html){
                        // alert(html);
                        $('#service-inputs').html(html);
                        $('#service-inputs').foundation();
                        // Foundation.reInit($('#service-inputs'));
                    }
                })
            }
        });

        // Проверяем наличие артикула в базе при клике на кнопку добавления артикула
        // $(document).on('click', '#add-service', function(event) {
        //     event.preventDefault();
        //     // alert($('#service-form').serialize());

        //     $.ajax({
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         },
        //         url: '/services/' + service_id,
        //         type: 'PATCH',
        //         data: $('#service-form').serialize(),
        //         success: function(data) {
        //             var result = $.parseJSON(data);
        //             alert(result['error_status']);
        //             // alert(data['metric_values']);
        //             if (result['error_status'] == 1) {
        //                 $('#add-service').prop('disabled', true);
        //                 $('#service-error').css('display', 'block');
        //             } else {

        //             }
        //         }
        //     })
        // });

        $(document).on('change', '#service-form input', function() {
            $('#add-service').prop('disabled', false);
            $('#service-error').css('display', 'none');
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
            data: {id: id, entity: 'services'},
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
            url: '/admin/services/' + service_id + '/edit',
            type: 'GET',
            data: $('#service-form').serialize(),
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
                data: {id: $(this).val(), entity: 'services', entity_id: service_id},
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
            data: {id: $(this).val(), entity: 'services', entity_id: service_id},
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
                data: {id: $(this).val(), entity: 'services', service_id: service_id},
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
            data: {id: id, entity: 'services'},
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
        var minImageHeight = 795;
        Dropzone.options.myDropzone = {
            paramName: 'photo',
        maxFilesize: {{ $settings['img_max_size'] }}, // MB
        maxFiles: 20,
        acceptedFiles: '{{ $settings['img_formats'] }}',
        addRemoveLinks: true,
        init: function() {
            this.on("success", function(file, responseText) {
                file.previewTemplate.setAttribute('id',responseText[0].id);

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/admin/service/photos',
                    type: 'post',
                    data: {service_id: service_id},
                    success: function(html){
        // alert(html);
        $('#photos-list').html(html);

        // $('#modal-create').foundation();
        // $('#modal-create').foundation('open');
    }
})
            });
            this.on("thumbnail", function(file) {
                if (file.width < {{ $settings['img_min_width'] }} || file.height < minImageHeight) {
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
@endsection