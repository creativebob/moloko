@extends('layouts.app')

@section('inhead')
@include('includes.scripts.dropzone-inhead')
@include('includes.scripts.fancybox-inhead')
@include('includes.scripts.sortable-inhead')
@endsection

@section('title', 'Редактирование категории услуг')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $goods_category->name))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">Редактирование категории товаров &laquo{{ $goods_category->name }}&raquo</h2>
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
            <li class="tabs-title"><a data-tabs-target="site" href="#site">Сайт</a></li>
            <li class="tabs-title"><a data-tabs-target="properties" href="#properties">Свойства</a></li>
            <li class="tabs-title"><a data-tabs-target="set-properties" href="#set-properties">Свойства (Набор)</a></li>
            <li class="tabs-title"><a data-tabs-target="compositions" href="#compositions">Состав</a></li>
            {{-- <li class="tabs-title"><a data-tabs-target="price-rules" href="#price-rules">Ценообразование</a></li> --}}
        </ul>
    </div>
</div>

<div class="grid-x tabs-wrap inputs">
    <div class="small-12 cell tabs-margin-top">
        <div class="tabs-content" data-tabs-content="tabs">

            {{ Form::model($goods_category, ['url' => '/admin/goods_categories/'.$goods_category->id, 'data-abide', 'novalidate', 'files'=>'true', 'id' => 'products-category-form']) }}
            {{ method_field('PATCH') }}

            <!-- Общая информация -->
            <div class="tabs-panel is-active" id="options">
                <div class="grid-x grid-padding-x">

                    <div class="small-12 medium-6 cell">

                        <div class="grid-x grid-padding-x">

                            @if(isset($goods_category->parent_id))

                            <div class="small-12 medium-6 cell">
                                <label>Расположение
                                    @include('includes.selects.categories_select', ['entity' => 'goods_categories', 'parent_id' => $goods_category->parent_id, 'id' => $goods_category->id])
                                </label>
                            </div>

                            @else

                            <div class="small-12 medium-6 cell">
                                @include('includes.selects.goods_modes')
                            </div>

                            @endif

                            <div class="small-12 medium-6 cell">
                                <label>Название категории
                                    @include('includes.inputs.name', ['check' => true, 'required' => true])
                                    <div class="item-error">Такая категория уже существует!</div>
                                </label>
                            </div>
                        </div>
                    </div>

                    @include('includes.control.checkboxes', ['item' => $goods_category])

                    {{-- Кнопка --}}
                    <div class="small-12 cell tabs-button tabs-margin-top">
                        {{ Form::submit('Редактировать', ['class'=>'button']) }}
                    </div>
                </div>
            </div>

            <!-- Сайт -->
            <div class="tabs-panel" id="site">
                <div class="grid-x grid-padding-x">
                    <div class="small-12 medium-6 cell">

                        <label>Описание:
                            {{ Form::textarea('description', $goods_category->description, ['id'=>'content-ckeditor', 'autocomplete'=>'off', 'size' => '10x3']) }}
                        </label><br>

                        <label>Description для сайта
                            @include('includes.inputs.textarea', ['value'=>$goods_category->seo_description, 'name'=>'seo_description'])
                        </label>

                    </div>
                    <div class="small-12 medium-6 cell">
                        <label>Выберите аватар
                            {{ Form::file('photo') }}
                        </label>
                        <div class="text-center">
                            <img id="photo" @if (isset($goods_category->photo_id)) src="/storage/{{ $goods_category->company->id }}/media/goods_categories/{{ $goods_category->id }}/img/medium/{{ $goods_category->photo->name }}" @endif>
                        </div>
                    </div>

                    {{-- Кнопка --}}
                    <div class="small-12 cell tabs-button tabs-margin-top">
                        {{ Form::submit('Редактировать категорию товаров', ['class'=>'button']) }}
                    </div>
                </div>
            </div>

            {{ Form::close() }}


            {{-- Подключаем класс дял работы с метриками --}}
            @include('includes.scripts.class.metrics')

            <!-- Свойства -->
            <div class="tabs-panel" id="properties">

                @include('goods_categories.metrics.section', ['goods_category' => $goods_category, 'properties' => $properties, 'set_status' => 'one'])
            </div>

            <!-- Свойства для набора -->
            <div class="tabs-panel" id="set-properties">

                @include('goods_categories.metrics.section', ['goods_category' => $goods_category, 'properties' => $properties, 'set_status' => 'set'])

            </div>

            {{-- Исключаем состав из сырья --}}

            <!-- Состав -->
            <div class="tabs-panel" id="compositions">
                <div class="grid-x grid-padding-x">
                    <div class="small-12 medium-9 cell">
                        <table class="composition-table">
                            <thead>
                                <tr>
                                    <th>Название</th>
                                    <th>Описание</th>
                                    <th>Ед. изм.</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="composition-table">
                                {{-- Таблица метрик товара --}}
                                @if (!empty($goods_category->compositions))

                                @foreach ($goods_category->compositions as $composition)
                                @include ('goods_categories.compositions.composition_tr', $composition)
                                @endforeach

                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="small-12 medium-3 cell">
                        @if (isset($composition_list))
                        {{ Form::model($goods_category, []) }}

                        <ul class="menu vertical">

                            @if (isset($composition_list['composition_categories']))
                            <li>
                                <a class="button" data-toggle="{{ $composition_list['alias'] }}-dropdown">{{ $composition_list['name'] }}</a>
                                <div class="dropdown-pane" id="{{ $composition_list['alias'] }}-dropdown" data-dropdown data-position="bottom" data-alignment="left" data-close-on-click="true">

                                    <ul class="checker" id="products-categories-list">

                                        @foreach ($composition_list['composition_categories'] as $category_name => $composition_articles)
                                        @include('goods_categories.compositions.raws-category', ['composition_articles' => $composition_articles, 'category_name' => $category_name])
                                        @endforeach
                                    </ul>

                                </div>
                            </li>
                            @endif

                        </ul>
                        {{ Form::close() }}
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('modals')
@include('includes.modals.modal-metric-delete')
@include('includes.modals.modal-composition-delete')
@endsection

@section('scripts')

@include('includes.scripts.inputs-mask')
@include('includes.scripts.upload-file')
@include('goods_categories.scripts')


@include('includes.scripts.modal-metric-delete-script')
@include('includes.scripts.modal-composition-delete-script')


@php
$settings = config()->get('settings');
@endphp

<script src="/vendor/unisharp/laravel-ckeditor/ckeditor.js"></script>
<script>

    CKEDITOR.replace('content-ckeditor');

    // Конфигурация
    CKEDITOR.config.toolbar = [
    ['Bold', 'Italic', 'NumberedList', 'BulletedList', 'Maximize', 'Source']
    ];

    // Основные настройки
    var goods_category_id = '{{ $goods_category->id }}';
    var entity ='goods_categories';

    // При клике на удаление состава со страницы
    $(document).on('click', '[data-open="delete-value"]', function() {

        // Удаляем элемент со страницы
        $(this).closest('.item').remove();
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
        var id = $(this).val();
        // alert(goods_category_id + ' ' + id);

        if ($(this).prop('checked') == true) {
            // Если нужно добавить состав
            $.post('/admin/ajax_add_relation_composition', {id: id, goods_category_id: goods_category_id, entity: 'goods_categories'}, function(html){
                // alert(html);
                $('#composition-table').append(html);
            })
        } else {
            // Если нужно удалить состав
            $.post('/admin/ajax_delete_relation_composition', {id: id, goods_category_id: goods_category_id, entity: 'goods_categories'}, function(data){
                // alert(result);
                if (data == true) {
                    $('#compositions-' + id).remove();
                } else {
                    alert(data);
                };
            })
        };
    });

    // При клике на фотку подствляем ее значения в блок редактирования
    $(document).on('click', '#photos-list img', function(event) {
        event.preventDefault();

        // Удаляем всем фоткам активный класс
        $('#photos-list img').removeClass('active');

        // Наваливаем его текущей
        $(this).addClass('active');
        let id = $(this).data('id');

        // Получаем инфу фотки
        $.post('/admin/ajax_get_photo', {id: id, entity: 'products'}, function(html){
            // alert(html);
            $('#form-photo-edit').html(html);
            // $('#modal-create').foundation();
            // $('#modal-create').foundation('open');
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
                    url: '/product/photos',
                    type: 'post',
                    data: {goods_category_id: goods_category_id},
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

{{-- Проверка поля на существование --}}
@include('includes.scripts.check')
@endsection