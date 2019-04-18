@extends('layouts.app')

@section('inhead')
@include('includes.scripts.dropzone-inhead')
@include('includes.scripts.fancybox-inhead')
@include('includes.scripts.sortable-inhead')
@include('includes.scripts.chosen-inhead')
@endsection

@section('title', 'Редактировать товар')

@section('breadcrumbs', Breadcrumbs::render('alias-edit', $page_info, $cur_goods->article))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕДАКТИРОВАТЬ товар &laquo{{ $cur_goods->article->name }}&raquo</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@php
$disabled = $cur_goods->article->draft == null;
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
                <a data-tabs-target="compositions" href="#compositions">Состав</a>
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

            {{ Form::model($article, [
                'route' => ['goods.update', $cur_goods->id],
                'data-abide',
                'novalidate',
                'files' => 'true',
                'id' => 'form-cur_goods'
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

                                <label>Название товара
                                    {{ Form::text('name', $cur_goods->article->name, ['required']) }}
                                </label>

                                <label>Группа
                                    @include('includes.selects.articles_groups', [
                                        'entity' => 'goods_categories',
                                        'category_id' => $cur_goods->goods_category_id,
                                        'articles_group_id' => $article->articles_group_id
                                    ]
                                    )
                                </label>

                                <label>Категория
                                    @include('includes.selects.goods_categories', ['goods_category_id' => $cur_goods->goods_category_id])
                                </label>

                                <label>Производитель

                                    @if ($cur_goods->category->manufacturers->isNotEmpty())

                                    {!! Form::select('manufacturer_id', $cur_goods->category->manufacturers->pluck('company.name', 'id'), $cur_goods->article->manufacturer_id, []) !!}

                                    @else

                                    @include('includes.selects.manufacturers', ['manufacturer_id' => $cur_goods->article->manufacturer_id, 'item' => $cur_goods, 'draft' => $cur_goods->article->draft])

                                    @endif

                                </label>

                                {{-- @include('includes.selects.manufacturers', ['manufacturer_id' => $cur_goods->article->manufacturer_id, 'draft' => $cur_goods->article->draft]) --}}

                                {!! Form::hidden('id', null, ['id' => 'item-id']) !!}

                            </div>

                            <div class="small-12 medium-6 cell">

                                <div class="small-12 cell">
                                    <label>Фотография
                                        {{ Form::file('photo') }}
                                    </label>
                                    <div class="text-center">
                                        <img id="photo" src="{{ getPhotoPath($article) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    {{-- Конец левого блока на первой вкладке --}}


                    {{-- Правый блок на первой вкладке --}}
                    <div class="small-12 large-6 cell">

                        <fieldset class="fieldset-access">
                            <legend>Артикул</legend>

                            <div class="grid-x grid-margin-x">
                                <div class="small-12 medium-4 cell">
                                    <label id="loading">Удобный (вручную)
                                        {{ Form::text('manually', null, ['class' => 'check-field']) }}
                                        <div class="sprite-input-right find-status"></div>
                                        <div class="item-error">Такой артикул уже существует!</div>
                                    </label>
                                </div>

                                <div class="small-12 medium-4 cell">
                                    <label>Внешний
                                        {{ Form::text('external') }}
                                    </label>
                                </div>

                                <div class="small-12 medium-4 cell">
                                    <label>Программный</label>
                                    {{ $cur_goods->article->internal }}
                                </div>
                            </div>
                        </fieldset>

                        <div class="grid-x">
                            <div class="small-12 cell">
                                <label>Описание товара
                                    @include('includes.inputs.textarea', ['name' => 'description', 'value' => $cur_goods->article->description])
                                </label>
                            </div>
                        </div>

                        {{-- @php
                        $metric_relation = ($cur_goods->article->group->set_status == 'one') ? 'one_metrics' : 'set_metrics';
                        @endphp --}}

                        {{-- @if ($cur_goods->article->metrics->isNotEmpty() || $cur_goods->category->$metric_relation->isNotEmpty())

                        @include('includes.scripts.class.metric_validation')

                        <fieldset class="fieldset-access">
                            <legend>Метрики</legend>

                            <div id="metrics-list">

                                Если уже сохранили метрики товара, то тянем их с собой
                                @if ($cur_goods->article->metrics->isNotEmpty())
                                @foreach ($cur_goods->article->metrics->unique() as $metric)
                                @include('includes.metrics.metric_input', $metric)
                                @endforeach

                                @else

                                @if ($cur_goods->article->product->category->$metric_relation->isNotEmpty())
                                @foreach ($cur_goods->article->product->category->$metric_relation as $metric)
                                @include('includes.metrics.metric_input', $metric)
                                @endforeach
                                @endif

                                @endif

                            </div>
                        </fieldset>

                        @endif --}}

                        <div id="cur-goods-inputs"></div>
                        <div class="small-12 cell tabs-margin-top text-center">
                            <div class="item-error" id="cur-goods-error">Такой артикул уже существует!<br>Измените значения!</div>
                        </div>
                        {{ Form::hidden('cur_goods_id', $cur_goods->id) }}
                    </div>
                    {{-- Конец правого блока на первой вкладке --}}

                    {{-- Чекбокс черновика --}}
                    @if ($article->draft == 1)
                    <div class="small-12 cell checkbox">
                        {{ Form::checkbox('draft', 1, $article->draft, ['id' => 'draft']) }}
                        <label for="draft"><span>Черновик</span></label>
                    </div>
                    @endif

                    {{-- Чекбоксы управления --}}
                    @include('includes.control.checkboxes', ['item' => $cur_goods])

                    {{-- Кнопка --}}
                    <div class="small-12 cell tabs-button tabs-margin-top">
                        {{ Form::submit('Редактировать товар', ['class'=>'button', 'id' => 'add-cur-goods']) }}
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
                                        {{ Form::number('cost_default', $article->cost_default) }}
                                    </label>
                                </div>
                                <div class="small-12 medium-6 cell">
                                    <label>Цена за (<span id="unit">{{ ($article->portion_status == null) ? $article->group->unit->abbreviation : 'порцию' }}</span>)
                                        {{ Form::number('price_default', $article->price_default) }}
                                    </label>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="fieldset portion-fieldset" id="portion-fieldset">
                            <legend class="checkbox">
                                {{ Form::checkbox('portion_status', 1, $article->portion_status, ['id' => 'portion', $disabled ? 'disabled' : '']) }}
                                <label for="portion">
                                    <span id="portion-change">Принимать порциями</span>
                                </label>

                            </legend>

                            <div class="grid-x grid-margin-x" id="portion-block">
                                <div class="small-12 cell @if ($article->portion_status == null) portion-hide @endif">
                                    <label>Имя&nbsp;порции
                                        {{ Form::text('portion_name', $article->portion_name, ['class'=>'text-field name-field compact', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', $disabled ? 'disabled' : '']) }}
                                    </label>
                                </div>
                                <div class="small-6 cell @if ($article->portion_status == null) portion-hide @endif">
                                    <label>Сокр.&nbsp;имя
                                        {{ Form::text('portion_abbreviation',  $article->portion_abbreviation, ['class'=>'text-field name-field compact', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', $disabled ? 'disabled' : '']) }}
                                    </label>
                                </div>
                                <div class="small-6 cell @if ($article->portion_status == null) portion-hide @endif">
                                    <label>Кол-во,&nbsp;{{ $article->group->unit->abbreviation }}
                                        Количество чего-либо
                                        {{ Form::text('portion_count', $article->portion_count, ['class'=>'digit-field name-field compact', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', $disabled ? 'disabled' : '']) }}
                                        <div class="sprite-input-right find-status" id="name-check"></div>
                                        <span class="form-error">Введите количество</span>
                                    </label>
                                </div>
                            </div>
                        </fieldset>

                    </div>
                </div>
            </div>

            {{-- Каталоги --}}
            <div class="tabs-panel" id="catalogs">
                <div class="grid-x grid-padding-x">
                    <div class="small-12 medium-6 cell">


                        <fieldset class="fieldset-access">
                            <legend>Каталоги</legend>

                            @include('includes.catalogs_with_items')


                            {{-- Form::select('catalogs[]', $catalogs_list, $cur_goods->catalogs, ['class' => 'chosen-select', 'multiple']) --}}
                            {{-- @include('includes.selects.catalogs_chosen', ['parent_id' => $cur_goods->catalogs->keyBy('id')->toArray()]) --}}

                        </fieldset>
                    </div>
                </div>
            </div>

            {{-- Состав --}}
            <div class="tabs-panel" id="compositions">
                <div class="grid-x grid-padding-x">
                    <div class="small-12 medium-9 cell">
                        {{-- Состав --}}
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

                               {{--  @php
                                $composition_relation = ($cur_goods->article->product->set_status == 'one') ? 'compositions' : 'set_compositions';
                                @endphp

                                У товара есть значения состава, берем их
                                @if ($cur_goods->article->$composition_relation->isNotEmpty())

                                @foreach ($cur_goods->article->$composition_relation as $composition)
                                @include ('goods.compositions.composition_input', $composition)
                                @endforeach

                                @else

                                В статусе набора у категории не может быть пресетов, берем только значения состава товара, если они имеются
                                @if (($composition_relation != 'set_compositions') && ($cur_goods->article->product->category->compositions->isNotEmpty())
                                && ($cur_goods->article->draft == 1))
                                @foreach ($cur_goods->article->product->category->compositions as $composition)
                                @include ('goods.compositions.composition_input', $composition)
                                @endforeach
                                @endif

                                @endif --}}


                            </tbody>
                        </table>
                    </div>



                    <div class="small-12 medium-3 cell">

                        {{-- Если статус у товара статус черновика, то показываем сырье/товары для добавления, в зависимости от статуса набора --}}

                        {{-- @if ($cur_goods->article->draft == 1) --}}
                        {{-- @isset ($composition_list)

                        @if ($cur_goods->article->$composition_relation->isNotEmpty())
                        {{ Form::model($cur_goods->article, []) }}
                        @else
                        @if ($cur_goods->article->product->set_status == 'one')
                        {{ Form::model($cur_goods->article->product->category, []) }}
                        @endisset
                        @endif --}}

                        {{-- <ul class="menu vertical">

                            @isset ($composition_list['composition_categories'])
                            <li>
                                <a class="button" data-toggle="{{ $composition_list['alias'] }}-dropdown">{{ $composition_list['name'] }}</a>
                                <div class="dropdown-pane" id="{{ $composition_list['alias'] }}-dropdown" data-dropdown data-position="bottom" data-alignment="left" data-close-on-click="true">

                                    <ul class="checker" id="products-categories-list">

                                        @foreach ($composition_list['composition_categories'] as $category_name => $composition_articles)
                                        @include('goods.compositions.category', ['composition_articles' => $composition_articles, 'category_name' => $category_name])
                                        @endforeach

                                    </ul>

                                </div>
                            </li>
                            @endisset
                        </ul>

                        {{ Form::close() }}

                        @endif
                        @endisset --}}

                    </div>
                </div>
            </div>

            {{ Form::close() }}

            {{-- Фотографии --}}
            <div class="tabs-panel" id="photos">
                <div class="grid-x grid-padding-x">


                    <div class="small-12 medium-7 cell">

                        {!!  Form::open([
                            'route' => 'photos.ajax_store',
                            'data-abide',
                            'novalidate',
                            'files' => 'true',
                            'class' => 'dropzone',
                            'id' => 'my-dropzone'
                        ]
                        ) !!}

                        {!! Form::hidden('name', $article->name) !!}
                        {!! Form::hidden('id', $article->id) !!}
                        {!! Form::hidden('entity', 'articles') !!}
                        {{-- {!! Form::hidden('album_id', $cur_goods->album_id) !!} --}}

                        {!! Form::close() !!}

                        <ul class="grid-x small-up-4 tabs-margin-top" id="photos-list">

                            @isset($cur_goods->album_id)
                            @include('photos.photos', ['item' => $cur_goods])
                            @endisset

                        </ul>
                    </div>

                    <div class="small-12 medium-5 cell" id="photo-edit-partail">

                        {{-- Форма редактированя фотки --}}

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('modals')
@include('includes.modals.modal-composition-delete')
@endsection

@section('scripts')
<script>

    // Основные настройки
    var item_id = '{{ $cur_goods->id }}';
    var entity = 'goods';
    var category_entity = 'goods_categories';
    var metrics_count = 0;
    var set_status = '{{ $cur_goods->set_status }}';
    var category_id = '{{ $cur_goods->goods_category_id }}';
    var unit = 'шт';

    // Мульти Select
    $(".chosen-select").chosen({width: "95%"});

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

        // Foundation.reInit($('#form-cur_goods'));
    });

    // При клике на удаление состава со страницы
    $(document).on('click', '[data-open="delete-value"]', function() {

        // Удаляем элемент со страницы
        $(this).closest('.item').remove();
    });

    // Проверяем наличие артикула в базе при клике на кнопку добавления артикула
    // $(document).on('click', '#add-cur-goods', function(event) {
    //     event.preventDefault();
    //     // alert($('#form-cur_goods').serialize());
    //     // alert(cur_goods_id);

    //     $.ajax({
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         url: '/admin/goods/' + cur_goods_id,
    //         type: 'PATCH',
    //         data: $('#form-cur_goods').serialize(),
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

    // $(document).on('change', '#form-cur_goods input', function() {
    //     // alert('lol');
    //     $('#add-cur-goods').prop('disabled', false);
    //     $('#cur-goods-error').hide();
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

            $.post('/admin/ajax_add_property', {id: id, entity: 'goods'}, function(html) {
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
            $.get('/admin/goods/' + item_id + '/edit', $('#form-cur_goods').serialize(), function(html) {
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
            $.post('/admin/ajax_add_relation_metric', {id: $(this).val(), entity: 'goods', entity_id: item_id}, function(html){
                // alert(html);
                $('#metrics-table').append(html);
                $('#property-form').html('');
            })
        } else {
            // Если нужно удалить метрику
            $.post('/admin/ajax_delete_relation_metric', {id: $(this).val(), entity: 'goods', entity_id: item_id}, function(date){
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

    // При клике на чекбокс состава отображаем ее на странице
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

    $(function() {
        $('.checkboxer-title .form-error').hide();
    });

    // Валидация группы чекбоксов
    // $(document).on('click', '.checkbox-group input:checkbox', function() {
    //     let id = $(this).closest('.dropdown-pane').attr('id');
    //     if ($(this).closest('.checkbox-group').find("input:checkbox:checked").length == 0) {
    //         $('div[data-toggle=' + id + ']').find('.form-error').show();
    //         $('#add-cur-goods').prop('disabled', true);
    //     } else {
    //         $('div[data-toggle=' + id + ']').find('.form-error').hide();
    //         $('#add-cur-goods').prop('disabled', false);
    //     };
    // });

    // Валидация при клике на кнопку
    $(document).on('click', '#add-cur-goods', function(event) {
        let error = 0;
        $(".checkbox-group").each(function(i) {
            if ($(this).find("input:checkbox:checked").length == 0) {
                let id = $(this).closest('.dropdown-pane').attr('id');
                $('div[data-toggle=' + id + ']').find('.form-error').show();
                error = error + 1;
            };
        });
        $('#form-cur_goods').foundation('validateForm');
        if (error > 0) {
            event.preventDefault();
        }
    });
</script>

@include('includes.edit_operations.change_articles_groups_script')
@include('includes.edit_operations.change_portions_script')

@include('includes.scripts.inputs-mask')
@include('includes.scripts.upload-file')
{{-- @include('goods.scripts') --}}
@include('includes.scripts.dropzone', [
    'settings' => $settings,
    'item_id' => $article->id
]
)
{{-- Проверка поля на существование --}}
@include('includes.scripts.check', [
    'entity' => 'articles',
    'id' => $article->id
]
)
@endsection