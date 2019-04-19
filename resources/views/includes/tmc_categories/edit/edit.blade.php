@extends('layouts.app')

@section('inhead')
@include('includes.scripts.dropzone-inhead')
{{-- @include('includes.scripts.fancybox-inhead')
@include('includes.scripts.sortable-inhead') --}}
@endsection

@section('title', $title)

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $category->name))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">{{ $title }} &laquo{{ $category->name }}&raquo</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')
<div class="grid-x tabs-wrap">
    <div class="small-12 cell">
        <ul class="tabs-list" data-tabs id="tabs">
            <li class="tabs-title is-active">
                <a href="#options" aria-selected="true">Общая информация</a>
            </li>
            <li class="tabs-title">
                <a data-tabs-target="site" href="#site">Сайт</a>
            </li>

            @if ($entity == 'goods_categories')
            <li class="tabs-title">
                <a data-tabs-target="properties" href="#properties">Свойства</a>
            </li>
            @endif
{{--             <li class="tabs-title">
                <a data-tabs-target="set-properties" href="#set-properties">Свойства (Набор)</a>
            </li> --}}
            <li class="tabs-title">
                <a data-tabs-target="compositions" href="#compositions">Состав</a>
            </li>
            {{-- <li class="tabs-title"><a data-tabs-target="price-rules" href="#price-rules">Ценообразование</a></li> --}}
        </ul>
    </div>
</div>

<div class="grid-x tabs-wrap inputs">
    <div class="small-12 cell tabs-margin-top">
        <div class="tabs-content" data-tabs-content="tabs">

            {{ Form::model($category, ['route' => [$entity.'.update', $category->id], 'data-abide', 'novalidate', 'files' => 'true']) }}
            {{ method_field('PATCH') }}

            {{-- Общая информация --}}
            <div class="tabs-panel is-active" id="options">
                <div class="grid-x grid-padding-x">

                    <div class="small-12 medium-6 cell">

                        <div class="grid-x grid-padding-x">

                            @if(isset($category->parent_id))

                            <div class="small-12 medium-6 cell">
                                <label>Расположение
                                    @include('includes.selects.categories_select', ['entity' => $entity, 'parent_id' => $category->parent_id, 'id' => $category->id])
                                </label>
                            </div>

                            @else

                            {{-- <div class="small-12 medium-6 cell"> --}}
                                {{-- @include('includes.selects.goods_modes') --}}
                            {{-- </div> --}}

                            @endif

                            <div class="small-12 medium-6 cell">
                                <label>Название категории
                                    @include('includes.inputs.name', ['check' => true, 'required' => true])
                                    <div class="item-error">Такая категория уже существует!</div>
                                </label>
                            </div>
                        </div>

                        <div class="grid-x grid-padding-x">
                            <div class="small-12 medium-6 cell checkbox checkboxer">

                                {{-- Подключаем класс Checkboxer --}}
                                @include('includes.scripts.class.checkboxer')

                                @include('includes.lists.manufacturers', [
                                    'entity' => $category,
                                    'title' => 'Производители',
                                    'name' => 'manufacturers'
                                ]
                                )

                            </div>
                        </div>

                    </div>

                    @if ($category->parent_id == null)
                    <div class="small-12 cell checkbox">
                        @if ($category->direction != null)
                        {{ Form::checkbox('direction', 1, ($category->direction->archive == false) ? 1 : 0, ['id' => 'direction-checkbox']) }}
                        @else
                        {{ Form::checkbox('direction', 1, null, ['id' => 'direction-checkbox']) }}
                        @endif

                        <label for="direction-checkbox"><span>Направление</span></label>
                        {{-- @include('includes.control.direction', ['direction' => isset($goods_category->direction) ]) --}}
                    </div>
                    @endif

                    @include('includes.control.checkboxes', ['item' => $category])

                    {{-- Кнопка --}}
                    <div class="small-12 cell tabs-button tabs-margin-top">
                        {{ Form::submit('Редактировать', ['class'=>'button']) }}
                    </div>
                </div>
            </div>

            {{-- Сайт --}}
            <div class="tabs-panel" id="site">
                <div class="grid-x grid-padding-x">
                    <div class="small-12 medium-6 cell">

                        <label>Описание:
                            {{ Form::textarea('description', $category->description, ['id'=>'content-ckeditor', 'autocomplete'=>'off', 'size' => '10x3']) }}
                        </label>

                        <label>Description для сайта
                            @include('includes.inputs.textarea', ['value' => $category->seo_description, 'name' => 'seo_description'])
                        </label>

                    </div>
                    <div class="small-12 medium-6 cell">
                        <label>Выберите аватар
                            {{ Form::file('photo') }}
                        </label>
                        <div class="text-center">
                            <img id="photo" src="{{ getPhotoPath($category) }}">
                        </div>
                    </div>

                    {{-- Кнопка --}}
                    <div class="small-12 cell tabs-button tabs-margin-top">
                        {{ Form::submit('Редактировать', ['class'=>'button']) }}
                    </div>
                </div>
            </div>


            @if ($entity == 'goods_categories')

            {{-- Свойства --}}
            <div class="tabs-panel" id="properties">
                @include('includes.category_metrics.section', ['category' => $category])
            </div>

            @endif

            <!-- Свойства для набора -->
            {{-- <div class="tabs-panel" id="set-properties">

                @include('includes.metrics_category.section', ['category' => $goods_category, 'set_status' => 'set'])

            </div> --}}

            {{-- Исключаем состав из сырья --}}

            {{-- Состав --}}

            {{-- Подключаем класс для работы с составами --}}
            @include('includes.category_compositions.class')

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
                            <tbody id="compositions-table">

                                {{-- Состав --}}
                                @if ($category->compositions->isNotEmpty())



                                @foreach ($category->compositions as $composition)
                                @include ('includes.tmc_categories.edit.compositions.composition_tr', $composition)
                                @endforeach

                                @endif

                            </tbody>
                        </table>
                    </div>

                    <div class="small-12 medium-3 cell">

                        <ul class="menu vertical">

                            <li>
                                <a class="button" data-toggle="compositions-dropdown">Состав</a>
                                <div class="dropdown-pane" id="compositions-dropdown" data-dropdown data-position="bottom" data-alignment="center" data-close-on-click="true">

                                    <ul class="checker" id="categories-list">

                                        @include('includes.tmc_categories.edit.compositions.category_compositions', ['alias' => $entity])
                                    </ul>

                                </div>
                            </li>

                        </ul>
                        {{-- @if (isset($composition_list))
                        {{ Form::model($goods_category, []) }}

                        <ul class="menu vertical">

                            @if (isset($composition_list['composition_categories']))
                            <li>
                                <a class="button" data-toggle="{{ $composition_list['alias'] }}-dropdown">{{ $composition_list['name'] }}</a>
                                <div class="dropdown-pane" id="{{ $composition_list['alias'] }}-dropdown" data-dropdown data-position="bottom" data-alignment="left" data-close-on-click="true">

                                    <ul class="checker" id="products-categories-list">

                                        @foreach ($composition_list['composition_categories'] as $category_name => $composition_articles)
                                        @include('goods_categories.compositions.categories', ['composition_articles' => $composition_articles, 'category_name' => $category_name])
                                        @endforeach
                                    </ul>

                                </div>
                            </li>
                            @endif

                        </ul>
                        {{ Form::close() }}
                        @endif --}}

                    </div>
                </div>
            </div>


            {{ Form::close() }}
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
{{-- @include('goods_categories.scripts') --}}

@include('includes.scripts.ckeditor')

{{-- Проверка поля на существование --}}
@include('includes.scripts.check', ['id' => $category->id])
<script>

    // Основные настройки
    var category_id = '{{ $category->id }}';
    var entity = '{{ $entity }}';

    // При клике на фотку подствляем ее значения в блок редактирования
    $(document).on('click', '#photos-list img', function(event) {
        event.preventDefault();

        // Удаляем всем фоткам активный класс
        $('#photos-list img').removeClass('active');

        // Наваливаем его текущей
        $(this).addClass('active');
        let id = $(this).data('id');

        // Получаем инфу фотки
        $.post('/admin/ajax_get_photo', {
            id: id,
            entity: 'products'
        }, function(html){
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

@endsection