@extends('layouts.app')

@section('inhead')
    @include('includes.scripts.dropzone-inhead')
    @include('includes.scripts.fancybox-inhead')
    @include('includes.scripts.sortable-inhead')

    @if ($entity == 'services')
        @include('includes.scripts.chosen-inhead')
        @include('products.processes.services.workflows.class')
    @endif

@endsection

@section('title', $title)

@section('breadcrumbs', Breadcrumbs::render('alias-edit', $pageInfo, $process))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">{{ $title }} &laquo{{ $process->name }}&raquo</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@php
    $disabled = $process->draft == 0 ? true : null;
@endphp

@section('content')
<div class="grid-x tabs-wrap">
    <div class="small-12 cell">
        <ul class="tabs-list" data-tabs id="tabs">

            <li class="tabs-title is-active">
                <a href="#tab-options" aria-selected="true">Общая информация</a>
            </li>

            {{-- Табы для сущности --}}
            @includeIf($pageInfo->entity->view_path . '.tabs')

            <li class="tabs-title">
                <a data-tabs-target="tab-photos" href="#tab-photos">Фотографии</a>
            </li>

            <li class="tabs-title">
                <a data-tabs-target="tab-extra_options" href="#tab-extra_options">Опции</a>
            </li>

            @can('index', App\Site::class)
                <li class="tabs-title">
                    <a data-tabs-target="tab-site" href="#tab-site">Настройка для сайта</a>
                </li>
            @endcan

            <li class="tabs-title">
                <a data-tabs-target="tab-positions" href="#tab-positions">Должности</a>
            </li>

        </ul>
    </div>
</div>

<div class="grid-x tabs-wrap inputs">
    <div class="small-12 cell tabs-margin-top">
        <div class="tabs-content" data-tabs-content="tabs">

            {{ Form::model($process, [
                'route' => [$entity.'.update', $item->id],
                'data-abide',
                'novalidate',
                'files' => 'true',
                'id' => 'form-edit'
            ]
            ) }}
            @method('PATCH')

            {!! Form::hidden('previous_url', $previous_url ?? null) !!}

            {{-- Общая информация --}}
            <div class="tabs-panel is-active" id="tab-options">

                {{-- Разделитель на первой вкладке --}}
                <div class="grid-x grid-padding-x">

                    {{-- Левый блок на первой вкладке --}}
                    <div class="small-12 large-6 cell">

                        {{-- Основная инфа --}}
                        <div class="grid-x grid-margin-x">
                            <div class="small-12 medium-6 cell">

                                <label>Название
                                    {{ Form::text('name', $process->name, ['required']) }}
                                </label>

                                <processes-categories-with-groups-component :item="{{ $item }}" :process="{{ $process }}" :categories='@json($categories_tree)' :groups='@json($groups)'></processes-categories-with-groups-component>

                                <label>Производитель

                                    @if ($item->category->manufacturers->isNotEmpty())

                                        {!! Form::select('manufacturer_id', $item->category->manufacturers->pluck('company.name', 'id'), $process->manufacturer_id, [$disabled ? 'disabled' : '']) !!}

                                    @else

                                        @include('includes.selects.manufacturers', ['manufacturer_id' => $process->manufacturer_id, 'item' => $item])

                                    @endif

                                </label>

                                <div class="grid-x grid-margin-x">
                                    <div class="small-12 medium-6 cell">
                                        <label>Единица измерения
                                            @include('products.processes.common.edit.select_units', [
                                                'units_category_id' => $process->unit->category_id,
                                                'value' => $process->unit_id,
                                            ])
                                        </label>
                                    </div>
                                    {{-- <div class="small-12 medium-6 cell">
                                        @isset ($process->unit_id)
                                            @if($process->group->units_category_id != 2)
                                                <label>Вес единицы, {{ $process->weight_unit->abbreviation }}
                                                    {!! Form::number('weight', null, ['disabled' => ($process->draft == 1) ? null : true]) !!}
                                                </label>
                                            @else
                                                {{ Form::hidden('weight', $process->weight) }}
                                            @endif
                                        @endisset
                                    </div> --}}
                                </div>

                                <label>Тип процесса
                                    @include('includes.selects.processes_types', ['processes_type_id' => $process->processes_type_id])
                                </label>

                                {{-- Если указана ед. измерения - ШТ. --}}
                                {{-- @if($item->getTable() == 'goods') --}}
{{--                                @if($article->group->units_category_id == 6)--}}
{{--                                    <div class="cell small-12 block-price-unit">--}}
{{--                                        <fieldset class="minimal-fieldset">--}}
{{--                                            <legend>Единица для определения цены</legend>--}}
{{--                                            <div class="grid-x grid-margin-x">--}}
{{--                                                <div class="small-12 medium-6 cell">--}}
{{--                                                    @include('includes.selects.units_categories', [--}}
{{--                                                        'default' => 6,--}}
{{--                                                        'data' => $item->price_unit_category_id,--}}
{{--                                                        'type' => 'article',--}}
{{--                                                        'name' => 'price_unit_category_id',--}}
{{--                                                        'id' => 'select-price-units_categories',--}}
{{--                                                    ])--}}
{{--                                                </div>--}}
{{--                                                <div class="small-12 medium-6 cell">--}}
{{--                                                    @include('includes.selects.units', [--}}
{{--                                                        'default' => 32,--}}
{{--                                                        'data' => $item->price_unit_id,--}}
{{--                                                        'units_category_id' => $item->price_unit_category_id,--}}
{{--                                                        'name' => 'price_unit_id',--}}
{{--                                                        'id' => 'select-price-units',--}}
{{--                                                    ])--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </fieldset>--}}
{{--                                    </div>--}}
{{--                                @endif--}}
{{--                                --}}{{-- @endif --}}
                                {!! Form::hidden('id', null, ['id' => 'item-id']) !!}

                            </div>

                            <div class="small-12 medium-6 cell">
                                <div class="small-12 cell">
                                    <label>Фотография
                                        {{ Form::file('photo') }}
                                    </label>
                                    <div class="text-center wrap-article-photo">
                                        <img id="photo" src="{{ getPhotoPathPlugEntity($item) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    {{-- Конец левого блока на первой вкладке --}}

                    {{-- Правый блок на первой вкладке --}}
                    <div class="small-12 large-6 cell">

                        <div class="grid-x">
                            <div class="small-12 cell">
                                <label>Описание
                                    @include('includes.inputs.textarea', ['name' => 'description', 'value' => $process->description])
                                </label>
                            </div>
                            @if($process->unit->category_id != 3)
                                <div class="cell small-12">
                                    <div class="grid-x grid-margin-x">
                                        <div class="small-12 medium-3 cell">
                                            <label>Продолжительность
                                                {!! Form::number('length', $process->lengthTrans) !!}
                                            </label>
                                        </div>
                                        <div class="small-12 medium-3 cell">
                                            <label>Единица измерения
                                                 @include('products.processes.common.edit.select_units', [
                                                    'name' => 'unit_length_id',
                                                    'units_category_id' => 3,
                                                    'value' => $process->unit_length_id,
                                                    'disabled' => null,
                                                ])
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Метрики --}}
                        @includeIf('products.processes.'.$item->getTable().'.metrics.metrics')
                        @include('products.common.edit.metrics.metrics')


                        <div id="item-inputs"></div>
                        <div class="small-12 cell tabs-margin-top text-center">
                            <div class="item-error" id="item-error">Такой артикул уже существует!<br>Измените значения!</div>
                        </div>
                        {{ Form::hidden('item_id', $item->id) }}
                    </div>
                    {{-- Конец правого блока на первой вкладке --}}

                    {{-- Кнопка --}}
                    <div class="small-12 cell tabs-button tabs-margin-top">
                        {{ Form::submit('Редактировать', ['class' => 'button', 'id' => 'add-item']) }}
                    </div>

                </div>
            </div>

            {{-- Дополнительные опции --}}
            <div class="tabs-panel" id="tab-extra_options">
                <div class="grid-x grid-padding-x">
                    <div class="small-12 medium-6 cell">

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
                                    {{ Form::text('internal', null, ['disabled']) }}
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="fieldset-access">
                            <legend>Умолчания для стоимости</legend>

                            <div class="grid-x grid-margin-x">
                                <div class="small-12 medium-6 cell">
                                    <label>Себестоимость
                                        {{ Form::number('cost_default', null) }}
                                    </label>
                                </div>
{{--                                <div class="small-12 medium-6 cell">--}}
{{--                                    <label>Цена за (<span id="unit">{{ ($article->package_status == false) ? $article->group->unit->abbreviation : 'порцию' }}</span>)--}}
{{--                                        {{ Form::number('price_default', null) }}--}}
{{--                                    </label>--}}
{{--                                </div>--}}
                            </div>
                        </fieldset>

{{--                        @if(isset($raw))--}}
{{--                            <fieldset class="fieldset-access">--}}
{{--                                <legend>Умолчания для сырья</legend>--}}

{{--                                <div class="grid-x grid-margin-x">--}}
{{--                                    <div class="small-12 medium-6 cell">--}}
{{--                                        <label>Еденица измерения--}}
{{--                                            @include('products.articles.common.edit.select_units', [--}}
{{--                                                'field_name' => 'unit_for_composition_id',--}}
{{--                                                'units_category_id' => $article->unit->category_id,--}}
{{--                                                'disabled' => null,--}}
{{--                                                'data' => $raw->unit_for_composition_id ?? $raw->article->unit_id,--}}
{{--                                            ])--}}
{{--                                        </label>--}}
{{--                                    </div>--}}
{{--                                    <div class="small-12 medium-6 cell">--}}

{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </fieldset>--}}
{{--                        @endif--}}

{{--                        <fieldset class="fieldset package-fieldset" id="package-fieldset">--}}

{{--                            <legend class="checkbox">--}}
{{--                                {!! Form::checkbox('package_status', 1, $article->package_status, ['id' => 'package', $disabled ? 'disabled' : '']) !!}--}}
{{--                                <label for="package">--}}
{{--                                    <span id="package-change">Сформировать порцию для приема на склад</span>--}}
{{--                                </label>--}}
{{--                            </legend>--}}

{{--                            <div class="grid-x grid-margin-x" id="package-block">--}}
{{--                                --}}{{-- <div class="small-12 cell @if ($article->package_status == null) package-hide @endif">--}}
{{--                                    <label>Имя&nbsp;порции--}}
{{--                                        {{ Form::text('package_name', $article->package_name, ['class'=>'text-field name-field compact', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', $disabled ? 'disabled' : ''], ['required']) }}--}}
{{--                                    </label>--}}
{{--                                </div> --}}
{{--                                <div class="small-6 cell @if (!$article->package_status) package-hide @endif">--}}
{{--                                    <label>Сокр.&nbsp;имя--}}
{{--                                        {{ Form::text('package_abbreviation',  $article->package_abbreviation, ['class'=>'text-field name-field compact', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', $disabled ? 'disabled' : ''], ['required']) }}--}}
{{--                                    </label>--}}
{{--                                </div>--}}
{{--                                <div class="small-6 cell @if (!$article->package_status) package-hide @endif">--}}
{{--                                    <label>Кол-во,&nbsp;{{ $article->unit->abbreviation }}--}}
{{--                                        {{ Form::text('package_count', $article->package_count, ['class'=>'digit-field name-field compact', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', $disabled ? 'disabled' : ''], ['required']) }}--}}
{{--                                        <div class="sprite-input-right find-status" id="name-check"></div>--}}
{{--                                        <span class="form-error">Введите количество</span>--}}
{{--                                    </label>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </fieldset>--}}

                        @includeIf('products.processes.'.$item->getTable().'.fieldsets')

                        <fieldset class="fieldset-access">
                            <legend>Доступность</legend>
                            {{-- Чекбокс черновика --}}
                            {!! Form::hidden('draft', 0) !!}
                            {{-- @if ($process->draft) --}}
                            <div class="small-12 cell checkbox">
                                {!! Form::checkbox('draft', 1, $process->draft, ['id' => 'checkbox-draft']) !!}
                                <label for="checkbox-draft"><span>Черновик</span></label>
                            </div>
                            {{-- @endif --}}


                            <div class="small-12 cell checkbox">
                                {!! Form::hidden('serial', 0) !!}
                                {!! Form::checkbox('serial', 1, $item->serial, ['id' => 'checkbox-serial']) !!}
                                <label for="checkbox-serial"><span>Серийный номер</span></label>
                            </div>

                            {{-- Чекбоксы управления --}}
                            @include('includes.control.checkboxes', ['item' => $item])
                            <div class="small-12 cell ">
                                <span id="composition-error" class="form-error"></span>
                            </div>
                        </fieldset>

                        <fieldset class="fieldset-access">
                            <legend>Дополнительное медиа</legend>
                            <label>Видео
                                {{ Form::text('video_url', $process->video_url, []) }}
                            </label>
                        </fieldset>

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
                                        {{ Form::number('cost_default', null) }}
                                    </label>
                                </div>
                                <div class="small-12 medium-6 cell">
                                    <label>Цена за (<span id="unit">{{ $process->unit->abbreviation }}</span>)
                                        {{ Form::number('price_default', null) }}
                                    </label>
                                </div>
                            </div>
                        </fieldset>

                    </div>
                </div>
            </div>

            @includeIf($pageInfo->entity->view_path . '.tabs_content')

            {{-- Сайт --}}
            @can('index', App\Site::class)
                <div class="tabs-panel" id="tab-site">
                    <div class="grid-x grid-padding-x">
                        <div class="small-12 medium-6 cell">
                            <label>Описание:
                                {{ Form::textarea('content', $process->content, ['id' => 'content-ckeditor', 'autocomplete' => 'off', 'size' => '10x3']) }}
                            </label>

                            <label>Description
                                @include('includes.inputs.textarea', ['value' => $process->seo_description, 'name' => 'seo_description'])
                            </label>

                            <label>Keywords
                                @include('includes.inputs.textarea', ['value' => $process->keywords, 'name' => 'keywords'])
                            </label>

                        </div>
                    </div>
                </div>
            @endcan

            {{-- Должности --}}
            @can('index', App\Position::class)
                <div class="tabs-panel" id="tab-positions">
                    <div class="grid-x grid-padding-x">
                        <div class="small-12 medium-3 cell">
                            <fieldset>
                                <legend>Должности</legend>
                                @include('includes.lists.positions')
                            </fieldset>
                        </div>
                    </div>
                </div>
            @endcan

            {{ Form::close() }}

            {{-- Фотографии --}}
            <div class="tabs-panel" id="tab-photos">
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

                        {!! Form::hidden('name', $process->name) !!}
                        {!! Form::hidden('id', $process->id) !!}
                        {!! Form::hidden('entity', 'processes') !!}
                        {!! Form::hidden('album_id', $item->album_id) !!}

                        {!! Form::close() !!}

                        <ul class="grid-x small-up-4 tabs-margin-top" id="photos-list">

                            @isset($process->album_id)
                            @include('photos.photos', ['album' => $process->album])
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
@include('includes.modals.modal_item_delete')

@includeIf($pageInfo->entity->view_path . '.modals')
@endsection

@push('scripts')
<script>

    // Основные настройки
    var category_entity = '{{ $category_entity }}';

</script>

@include('products.processes.common.edit.change_processes_groups_script')

@include('includes.scripts.inputs-mask')
@include('includes.scripts.upload-file')
@include('includes.scripts.ckeditor')

@include('includes.scripts.dropzone', [
    'settings' => $settings,
    'item_id' => $process->id,
    'item_entity' => 'processes'
]
)

{{-- Проверка поля на существование --}}
@include('includes.scripts.check', [
    'entity' => 'processes',
    'id' => $process->id
]
)

@includeIf($pageInfo->entity->view_path . '.scripts')
@endpush
