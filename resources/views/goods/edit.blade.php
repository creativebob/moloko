@extends('layouts.app')

@section('inhead')
@include('includes.scripts.dropzone-inhead')
@include('includes.scripts.fancybox-inhead')
@include('includes.scripts.sortable-inhead')
@include('includes.scripts.chosen-inhead')
@endsection

@section('title', 'Редактировать товар')

@section('breadcrumbs', Breadcrumbs::render('alias-edit', $page_info, $cur_goods->goods_article))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕДАКТИРОВАТЬ товар &laquo{{ $cur_goods->goods_article->name }}&raquo</h2>
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

            <li class="tabs-title"><a data-tabs-target="compositions" href="#compositions">Состав</a></li>
            <li class="tabs-title"><a data-tabs-target="photos" href="#photos">Фотографии</a></li>
        </ul>
    </div>
</div>

<div class="grid-x tabs-wrap inputs">
    <div class="small-12 cell tabs-margin-top">
        <div class="tabs-content" data-tabs-content="tabs">

            {{ Form::model($cur_goods, ['url' => ['/admin/goods/'.$cur_goods->id], 'data-abide', 'novalidate', 'files'=>'true', 'id' => 'cur-goods-form']) }}
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
                                    {{ Form::text('name', $cur_goods->goods_article->name, ['required']) }}
                                </label>

                                <label>Группа
                                    <div id="goods-products-select">
                                        {{ Form::select('goods_product_id', $goods_products_list, $cur_goods->goods_article->goods_product_id, ['id' => 'goods-products-list']) }}
                                    </div>
                                </label>

                                <label>Категория
                                    <select name="goods_category_id" id="goods-categories-list">
                                        {!! $goods_categories_list !!}
                                    </select>
                                </label>

                                <label>Производитель</label>

                                @if ($cur_goods->goods_article->draft == 1)
                                {{ Form::select('manufacturer_id', $manufacturers_list, $cur_goods->goods_article->manufacturer_id, ['placeholder' => 'Выберите производителя'])}}
                                @else
                                {{ $cur_goods->goods_article->manufacturer->name ?? 'Не указан' }}
                                @endif

                            </div>

                            <div class="small-12 medium-6 cell">

                                <div class="small-12 cell">
                                    <label>Фотография
                                        {{ Form::file('photo') }}
                                    </label>
                                    <div class="text-center">
                                        <img id="photo" @isset($cur_goods->photo_id)) src="/storage/{{ $cur_goods->company->id }}/media/goods/{{ $cur_goods->id }}/img/medium/{{ $cur_goods->photo->name }}" @endisset>
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
                                    <label id="loading">Удобный (вручную)
                                        {{ Form::text('manually') }}
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
                                    {{ $cur_goods->goods_article->internal }}
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

                        @php
                        $metric_relation = ($cur_goods->goods_article->goods_product->set_status == 'one') ? 'one_metrics' : 'set_metrics';
                        @endphp

                        @if (count($cur_goods->goods_article->metrics) || count($cur_goods->goods_article->goods_product->goods_category->$metric_relation))

                        @include('includes.scripts.class.metric_validation')

                        <fieldset class="fieldset-access">
                            <legend>Метрики</legend>

                            {{-- Если черновик --}}
                            @if ($cur_goods->goods_article->draft == 1)

                            <div id="metrics-list">
                                @if (count($cur_goods->goods_article->metric))

                                {{-- Если уже сохранили метрики товара, то тянем их с собой --}}
                                @isset ($cur_goods->goods_article->metrics)
                                {{-- @each ('goods.metrics.metric_input', $cur_goods->goods_article->metrics->unique(), 'metric') --}}
                                @foreach ($cur_goods->goods_article->metrics->unique() as $metric)
                                @include('goods.metrics.metric_input', $metric)
                                @endforeach
                                @endisset

                                @else

                                @isset ($cur_goods->goods_article->goods_product->goods_category->$metric_relation)
                                @foreach ($cur_goods->goods_article->goods_product->goods_category->$metric_relation as $metric)
                                @include('goods.metrics.metric_input', $metric)
                                @endforeach
                                @endisset

                                @endif


                                {!! Form::open() !!}
                            </div>

                            @else

                            {{-- Если товар --}}
                            @isset ($cur_goods->goods_article->metrics)

                            <table>
                                <tbody>
                                    @each ('goods.metrics.metric_value', $cur_goods->goods_article->metrics, 'metric')
                                </tbody>
                            </table>

                            @endisset

                            @endif
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
                    @if ($cur_goods->goods_article->draft == 1)
                    <div class="small-12 cell checkbox">
                        {{ Form::checkbox('draft', 1, $cur_goods->goods_article->draft, ['id' => 'draft']) }}
                        <label for="draft"><span>Черновик</span></label>
                    </div>
                    @endif

                    {{-- Чекбоксы управления --}}
                    @include('includes.control.checkboxes', ['item' => $cur_goods])

                    {{-- Кнопка --}}
                    <div class="small-12 cell tabs-button tabs-margin-top">
                        {{ Form::submit('Редактировать товар', ['class'=>'button', 'id' => 'add-cur-goods']) }}
                    </div>

                </div>{{-- Закрытие разделителя на блоки --}}
            </div>{{-- Закрытите таба --}}

            {{-- Ценообразование --}}
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
                                    <label>Цена за (<span id="unit">{{ ($cur_goods->portion_status == null) ?$cur_goods->goods_article->goods_product->unit->abbreviation : 'порцию' }}</span>)
                                        {{ Form::number('price', $cur_goods->price) }}
                                    </label>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="fieldset portion-fieldset" id="portion-fieldset">
                            <legend class="checkbox">
                                {{ Form::checkbox('portion_status', 1, $cur_goods->portion_status, ['id' => 'portion']) }}
                                <label for="portion">
                                    <span id="portion-change">Принимать порциями</span>
                                </label>

                            </legend>

                            <div class="grid-x grid-margin-x" id="portion-block">
                                <div class="small-12 cell @if ($cur_goods->portion_status == null) portion-hide @endif">
                                    <label>Имя&nbsp;порции
                                        {{ Form::text('portion_name', $cur_goods->portion_name, ['class'=>'text-field name-field compact', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}']) }}
                                    </label>
                                </div>
                                <div class="small-6 cell @if ($cur_goods->portion_status == null) portion-hide @endif">
                                    <label>Сокр.&nbsp;имя
                                        {{ Form::text('portion_abbreviation',  $cur_goods->portion_abbreviation, ['class'=>'text-field name-field compact', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}']) }}
                                    </label>
                                </div>
                                <div class="small-6 cell @if ($cur_goods->portion_status == null) portion-hide @endif">
                                    <label>Кол-во,&nbsp;{{ $cur_goods->goods_article->goods_product->unit->abbreviation }}
                                        {{-- Количество чего-либо --}}
                                        {{ Form::text('portion_count', $cur_goods->portion_count, ['class'=>'digit-field name-field compact', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}']) }}
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

                            {{-- Form::select('catalogs[]', $catalogs_list, $cur_goods->catalogs, ['class' => 'chosen-select', 'multiple']) --}}
                            <select name="catalogs[]" data-placeholder="Выберите каталоги..." multiple class="chosen-select">
                                {!! $catalogs_list !!}
                            </select>

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

                                @php
                                $composition_relation = ($cur_goods->goods_article->goods_product->set_status == 'one') ? 'compositions' : 'set_compositions';
                                @endphp

                                {{-- Если черновик --}}
                                @if ($cur_goods->goods_article->draft == 1)

                                {{-- У товара есть значения состава, берем их --}}
                                @if (count($cur_goods->goods_article->$composition_relation))

                                @foreach ($cur_goods->goods_article->$composition_relation as $composition)
                                @include ('goods.compositions.composition_input', $composition)
                                @endforeach

                                @else

                                {{-- В статусе набора у категории не может быть пресетов, берем только значения состава товара, если они имеются --}}
                                @if (($composition_relation != 'set_compositions') && count($cur_goods->goods_article->goods_product->goods_category->compositions))
                                @foreach ($cur_goods->goods_article->goods_product->goods_category->compositions as $composition)
                                @include ('goods.compositions.composition_input', $composition)
                                @endforeach
                                @endif

                                @endif

                                @else

                                {{-- У товара есть значения состава, берем их --}}
                                @isset ($cur_goods->goods_article->$composition_relation)
                                @foreach ($cur_goods->goods_article->$composition_relation as $composition)
                                @include ('goods.compositions.composition_value', $composition)
                                @endforeach
                                @endisset

                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="small-12 medium-3 cell">

                        {{-- Если статус у товара статус черновика, то показываем сырье/товары для добавления, в зависимости от статуса набора --}}
                        @isset ($composition_list)
                        @if ($cur_goods->goods_article->draft == 1)

                        @if ($cur_goods->goods_article->goods_product->set_status == 'one')

                        @if (count($cur_goods->goods_article->$composition_relation))
                        {{ Form::model($cur_goods->goods_article, []) }}
                        @else
                        {{ Form::model($cur_goods->goods_article->goods_product->goods_category, []) }}
                        @endif

                        @else

                        {{ Form::model($cur_goods->goods_article, []) }}

                        @endif

                        <ul class="menu vertical">

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
                        @endisset

                    </div>
                </div>
            </div>

            {{-- Фотографии --}}
            <div class="tabs-panel" id="photos">
                <div class="grid-x grid-padding-x">

                    <div class="small-12 medium-7 cell">
                        {{ Form::open(['url' => '/admin/goods/add_photo', 'data-abide', 'novalidate', 'files'=>'true', 'class'=> 'dropzone', 'id' => 'my-dropzone']) }}
                        {{ Form::hidden('photo_name', $cur_goods->name) }}
                        {{ Form::hidden('id', $cur_goods->id) }}
                        {{ Form::close() }}
                        <ul class="grid-x small-up-4 tabs-margin-top" id="photos-list">

                            @isset($cur_goods->album_id)
                            @include('goods.photos', $cur_goods)
                            @endisset

                        </ul>
                    </div>

                    <div class="small-12 medium-5 cell">

                        {{-- Форма редактированя фотки --}}
                        {{ Form::open(['url' => '/admin/goods/edit_photo', 'data-abide', 'novalidate', 'id' => 'form-photo-edit']) }}

                        {{ Form::hidden('photo_name', $cur_goods->name) }}
                        {{ Form::hidden('id', $cur_goods->id) }}
                        {{ Form::close() }}
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

@include('includes.scripts.inputs-mask')
@include('includes.scripts.upload-file')
@include('goods.scripts')

<script>

    // Основные настройки
    var cur_goods_id = '{{ $cur_goods->id }}';
    var set_status = '{{ $cur_goods->goods_article->goods_product->set_status }}';

    var metrics_count = '{{ count($cur_goods->goods_article->metrics) }}';

    if (set_status == 'one') {
        var compositions_count = '{{ count($cur_goods->goods_article->compositions) }}';
    } else {
        var compositions_count = 0;
    }

    var compositions_count = '{{ count($cur_goods->goods_article->metrics) }}';

    var category_id = '{{ $cur_goods->goods_article->goods_product->goods_category_id }}';

    var unit = '{{ $cur_goods->goods_article->goods_product->unit->abbreviation }}';

    // Обозначаем таймер для проверки
    var timerId;
    var time = 300;

    // Мульти Select
    $(".chosen-select").chosen({width: "95%"});

    // Првоерка на совпадение
    function manuallyArticleCheck (value) {

        // Если символов больше 3 - делаем запрос
        if (value.length > 3) {

            // Сам ajax запрос
            $.ajax({
                url: '/admin/goods_check',
                type: "POST",
                data: {value: value, id: cur_goods_id},
                beforeSend: function () {
                    $('#loading .find-status').addClass('icon-load');
                },
                success: function(error) {
                    $('#loading .find-status').removeClass('icon-load');
                    // Если существует
                    if (error > 0) {
                        $('#add-cur-goods').prop('disabled', true);
                        $('#loading .item-error').show();
                    } else {
                        $('#add-cur-goods').prop('disabled', false);
                        $('#loading .item-error').hide();
                    };
                }
            });
        } else {
            // Удаляем все значения, если символов меньше 3х
            $('#add-cur-goods').prop('disabled', false);
            $('#loading .item-error').hide();
        };
    };
    $(document).on('keyup', '#loading input', function() {

        // Получаем фрагмент текста
        let value = $(this).val();

        // Выполняем запрос
        clearTimeout(timerId);
        timerId = setTimeout(function() {
            manuallyArticleCheck(value);
        }, time);
    });

    $(document).on('change', '#goods-categories-list', function(event) {
        event.preventDefault();

        // Меняем группы
        $.post('/admin/goods_products_list', {goods_category_id: $(this).val(), goods_product_id: $('#goods-products-list').val(), set_status: set_status}, function(list){
            // alert(html);
            $('#goods-products-select').html(list);
        });
    });

    $(document).on('click', '#portion', function() {
        $('#portion-block div').toggle();
        // $('#portion-fieldset').toggleClass('portion-fieldset');

        if ($(this).prop('checked') == true) {
            $('#unit').text('порцию');
        } else {
            $('#unit').text(unit);
        }
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

        Foundation.reInit($('#cur-goods-form'));
    });

    // При клике на удаление состава со страницы
    $(document).on('click', '[data-open="delete-value"]', function() {

        // Удаляем элемент со страницы
        $(this).closest('.item').remove();
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
        $('#cur-goods-error').hide();
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
            $.get('/admin/goods/' + cur_goods_id + '/edit', $('#cur-goods-form').serialize(), function(html) {
                // alert(html);
                $('#properties-dropdown').html(html);
            })
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

        // alert(set_status);
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
                data: {id: $(this).val(), entity: 'goods', cur_goods_id: cur_goods_id, set_status: set_status},
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