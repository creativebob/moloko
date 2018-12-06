@extends('layouts.app')

@section('inhead')
@include('includes.scripts.dropzone-inhead')
@include('includes.scripts.fancybox-inhead')
@include('includes.scripts.sortable-inhead')
@endsection

@section('title', 'Редактировать артикул')

@section('breadcrumbs', Breadcrumbs::render('alias-edit', $page_info, $article))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕДАКТИРОВАТЬ артикул &laquo{{ $article->name }}&raquo</h2>
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

            @if ($type != 'raws')
            <li class="tabs-title"><a data-tabs-target="compositions" href="#compositions">Состав</a></li>
            @endif
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

            {{ Form::model($article, ['url' => ['/admin/articles/'.$article->id], 'data-abide', 'novalidate', 'files'=>'true', 'id' => 'article-form']) }}
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

                                <label>Категория товара
                                    <select name="products_category_id" disabled>
                                        @php
                                        echo $products_categories_list;
                                        @endphp
                                    </select>
                                </label>
                                <label>Название группы товаров
                                    {{ Form::select('product_id', $products_list, $article->product_id) }}
                                </label>

                                <fieldset class="fieldset">
                                    <legend>
                                        <div class="small-12 cell checkbox">
                                            {{ Form::checkbox('portion', 1, $article->display, ['id' => 'portion']) }}
                                            <label for="portion"><span>Принимать порциями</span></label>
                                        </div>
                                    </legend>

                                    <div class="grid-x grid-margin-x">
                                        <div class="small-12 medium-6 cell">
                                            <label>Имя порции
                                                {{ Form::text('pr', "", ['class'=>'text-field name-field compact', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}']) }}
                                            </label>
                                        </div>
                                        <div class="small-6 medium-3 cell">
                                            <label>Сокр. имя
                                                {{ Form::text('pr1',  "", ['class'=>'text-field name-field compact', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}']) }}
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
                                        {{ Form::select('manufacturer_id', $manufacturers_list, $article->manufacturer_id, ['placeholder' => 'Выберите производителя'])}}
                                    </label>
                                </div>
                            </div>

                            <div class="small-12 medium-6 cell">

                                <div class="small-12 cell">
                                    <label>Фотография продукта
                                        {{ Form::file('photo') }}
                                    </label>
                                    <div class="text-center">
                                        <img id="photo" @if (isset($article->photo_id)) src="/storage/{{ $article->company->id }}/media/articles/{{ $article->id }}/img/medium/{{ $article->photo->name }}" @endif>
                                    </div>
                                </div>
                            </div>

                            <div class="small-12 cell">
                                <label>Описание товара
                                    @include('includes.inputs.textarea', ['name'=>'description', 'value'=>$article->description])
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
                                        {{ Form::text('name', null, ['required']) }}
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
                        @if (($article->product->products_category->metrics_count > 0) || ($article->metrics_values_count > 0))
                        <fieldset class="fieldset-access">
                            <legend>Метрики</legend>

                            @if ($article->template == 1)

                            @foreach ($article->product->products_category->metrics as $metric)
                            @include('articles.metrics.metric-input', $metric)
                            @endforeach

                            @else

                            @foreach ($article->metrics_values as $metric)
                            @include('articles.metrics.metric-value', $metric)
                            @endforeach

                            @endif

                            {{-- @if ($article->metrics_values_count > 0)
                             @each('articles.metrics.metric-input', $article->product->products_category->metrics, 'metric')
                             @each('articles.metrics.metric-value', $article->metrics_values, 'metric')
                             @endif --}}

                         </fieldset>
                         @endif
                         <div id="article-inputs"></div>
                         <div class="small-12 cell tabs-margin-top text-center">
                            <div class="item-error" id="article-error">Такой артикул уже существует!<br>Измените значения!</div>
                        </div>
                        {{ Form::hidden('article_id', $article->id) }}


                    </div>
                    {{-- Конец правого блока на первой вкладке --}}


                    {{-- Чекбокс черновика --}}
                    <div class="small-12 cell checkbox">
                        {{ Form::checkbox('template', 1, $article->template, ['id' => 'template']) }}
                        <label for="template"><span>Черновик</span></label>
                    </div>

                    {{-- Чекбокс отображения на сайте --}}
                    @can ('publisher', $article)
                    <div class="small-12 cell checkbox">
                        {{ Form::checkbox('display', 1, $article->display, ['id' => 'display']) }}
                        <label for="display"><span>Отображать на сайте</span></label>
                    </div>
                    @endcan

                    {{-- Чекбокс модерации --}}
                    @can ('moderator', $article)
                    @if ($article->moderation == 1)
                    <div class="small-12 cell checkbox">
                        @include('includes.inputs.moderation', ['value'=>$article->moderation, 'name'=>'moderation'])
                    </div>
                    @endif
                    @endcan

                    {{-- Чекбокс системной записи --}}
                    @can ('god', $article)
                    <div class="small-12 cell checkbox">
                        @include('includes.inputs.system', ['value'=>$article->system_item, 'name'=>'system_item'])
                    </div>
                    @endcan

                    {{-- Кнопка --}}
                    <div class="small-12 cell tabs-button tabs-margin-top">
                        {{ Form::submit('Создать артикул', ['class'=>'button', 'id' => 'add-article']) }}
                    </div>

                </div>{{-- Закрытие разделителя на блоки --}}
            </div>{{-- Закрытите таба --}}





            <!-- Состав -->
            <div class="tabs-panel" id="compositions">
                <div class="grid-x grid-padding-x">
                    <div class="small-12 medium-9 cell">
                        <table class="composition-table">
                            <thead>
                                <tr>
                                    @if ($article->template == 1)
                                    <th>Категория:</th>
                                    <th>Продукт:</th>
                                    <th>Кол-во:</th>
                                    <th>Использование:</th>
                                    <th>Отход:</th>
                                    <th>Остаток:</th>
                                    <th>Операция над остатком:</th>
                                    @else
                                    <th>Продукт:</th>
                                    <th>Кол-во:</th>
                                    @endif

                                    <!-- <th></th> -->
                                </tr>
                            </thead>
                            <tbody id="composition-table">
                                {{-- Таблица состава --}}
                                @if (!empty($article->product->products_category->compositions))
                                @if ($article->template == 1)
                                @foreach ($article->product->products_category->compositions as $composition)
                                @include('articles.compositions.composition', $composition)
                                @endforeach
                                @else
                                @foreach ($article->compositions_values as $composition)
                                @include('articles.compositions.composition-value', $composition)
                                @endforeach
                                @endif
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="small-12 medium-3 cell">

                        @if ($article->template == 1)
                        <ul class="menu vertical">

                            @foreach ($products_modes_list as $products_mode)
                            <li>
                              <a class="button" data-toggle="{{ $products_mode['alias'] }}-dropdown">{{ $products_mode['name'] }}</a>
                              <div class="dropdown-pane" id="{{ $products_mode['alias'] }}-dropdown" data-dropdown data-position="bottom" data-alignment="left" data-close-on-click="true">

                                <ul class="checker" id="products-categories-list">
                                    @foreach ($products_mode['products_categories'] as $products_cat)
                                    @include('articles.compositions.products-category', $products_cat)
                                    @endforeach
                                </ul>

                            </div>
                        </li>
                        @endforeach
                    </ul>
                    @endif
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
                                    {{ Form::number('cost', $article->cost) }}
                                </label>
                            </div>
                            <div class="small-12 medium-6 cell">
                                <label>Цена
                                    {{ Form::number('price', $article->price) }}
                                </label>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
        {{ Form::close() }}

        <!-- Фотографии -->
        <div class="tabs-panel" id="photos">
            <div class="grid-x grid-padding-x">

                <div class="small-12 medium-7 cell">
                    {{ Form::open(['url' => '/article/add_photo', 'data-abide', 'novalidate', 'files'=>'true', 'class'=> 'dropzone', 'id' => 'my-dropzone']) }}
                    {{ Form::hidden('name', $article->name) }}
                    {{ Form::hidden('id', $article->id) }}
                    {{ Form::close() }}
                    <ul class="grid-x small-up-4 tabs-margin-top" id="photos-list">
                        @if (isset($article->album_id))

                        @include('articles.photos', $article)

                        @endif
                    </ul>
                </div>

                <div class="small-12 medium-5 cell">

                    {{-- Форма редактированя фотки --}}
                    {{ Form::open(['url' => '/article/edit_photo', 'data-abide', 'novalidate', 'id' => 'form-photo-edit']) }}


                    {{ Form::hidden('name', $article->name) }}
                    {{ Form::hidden('id', $article->id) }}
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
@include('articles.scripts')
@php
$settings = config()->get('settings');
@endphp

<script>

        // Основные ностойки
        var article_id = '{{ $article->id }}';

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
                data: {id: id, entity: 'articles', entity_id: article_id},
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
        //   url: '/articles/' + article_id + '/edit',
        //   type: 'GET',
        //   data: $('#article-form').serialize(),
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
                data: {id: id, article_id: article_id},
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
            if ($('#articles:visible').length) {

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/ajax_get_article_inputs',
                    type: 'POST',
                    data: {article_id: article_id},
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
        // $(document).on('click', '#add-article', function(event) {
        //     event.preventDefault();
        //     // alert($('#article-form').serialize());

        //     $.ajax({
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         },
        //         url: '/articles/' + article_id,
        //         type: 'PATCH',
        //         data: $('#article-form').serialize(),
        //         success: function(data) {
        //             var result = $.parseJSON(data);
        //             alert(result['error_status']);
        //             // alert(data['metric_values']);
        //             if (result['error_status'] == 1) {
        //                 $('#add-article').prop('disabled', true);
        //                 $('#article-error').css('display', 'block');
        //             } else {

        //             }
        //         }
        //     })
        // });

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
            data: {id: id, entity: 'articles'},
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
            url: '/articles/' + article_id + '/edit',
            type: 'GET',
            data: $('#article-form').serialize(),
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
                data: {id: $(this).val(), entity: 'articles', entity_id: article_id},
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
            data: {id: $(this).val(), entity: 'articles', entity_id: article_id},
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
                url: '/ajax_add_page_composition',
                type: 'POST',
                data: {id: $(this).val(), entity: 'articles', article_id: article_id},
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
            url: '/ajax_get_photo',
            type: 'POST',
            data: {id: id, entity: 'articles'},
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
            url: '/ajax_update_photo/' + id,
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
                    url: '/article/photos',
                    type: 'post',
                    data: {article_id: article_id},
                    success: function(html){
        // alert(html);
        $('#photos-list').html(html);

        // $('#modal-create').foundation();
        // $('#modal-create').foundation('open');
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