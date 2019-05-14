@extends('layouts.app')

@section('inhead')
@include('includes.scripts.dropzone-inhead')
@include('includes.scripts.fancybox-inhead')
@include('includes.scripts.sortable-inhead')

@if ($entity == 'services')
@include('includes.scripts.chosen-inhead')
@endif

@endsection

@section('title', $title)

@section('breadcrumbs', Breadcrumbs::render('alias-edit', $page_info, $process))

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
                <a href="#options" aria-selected="true">Общая информация</a>
            </li>

            <li class="tabs-title">
                <a data-tabs-target="price-rules" href="#price-rules">Ценообразование</a>
            </li>

            {{-- Табы для сущности --}}
            @includeIf($entity . '.tabs')

            <li class="tabs-title">
                <a data-tabs-target="photos" href="#photos">Фотографии</a>
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

                                <label>Название
                                    {{ Form::text('name', $process->name, ['required']) }}
                                </label>

                                <label>Группа
                                    @include('includes.selects.processes_groups', [
                                        'entity' => $category_entity,
                                        'category_id' => $item->category->id,
                                        'processes_group_id' => $process->processes_group_id
                                    ]
                                    )
                                </label>

                                <label>Категория
                                    @include('includes.selects.categories', [
                                        'category_entity' => $category_entity,
                                        'category_id' => $item->category->id
                                    ]
                                    )
                                </label>

                                <label>Производитель

                                    @if ($item->category->manufacturers->isNotEmpty())

                                    {!! Form::select('manufacturer_id', $item->category->manufacturers->pluck('company.name', 'id'), $process->manufacturer_id, [$disabled ? 'disabled' : '']) !!}

                                    @else

                                    @include('includes.selects.manufacturers', ['manufacturer_id' => $process->manufacturer_id, 'item' => $item])

                                    @endif

                                </label>

                                <label>Тип процесса
                                    @include('includes.selects.processes_types')
                                </label>

                                @if ($process->group->unit->units_category_id == 3)

                                <label>Еденица измерения
                                    @include('processes.edit.select_units', [
                                        'units_category_id' => 3,
                                        'disabled' => true,
                                    ]
                                    )
                                </label>

                                @else

                                <label>Еденица измерения
                                    @include('processes.edit.select_units', [
                                        'units_category_id' => $process->group->unit->units_category_id,
                                        'disabled' => ($process->draft == 1) ? null : true,
                                    ]
                                    )
                                </label>
                                <label>Продолжительность единицы ({{ $process->group->unit->abbreviation }})
                                    {!! Form::number('length', null, []) !!}
                                </label>

                                @endif

                                {!! Form::hidden('id', null, ['id' => 'item-id']) !!}

                            </div>

                            <div class="small-12 medium-6 cell">

                                <div class="small-12 cell">
                                    <label>Фотография
                                        {{ Form::file('photo') }}
                                    </label>
                                    <div class="text-center">
                                        <img id="photo" src="{{ getPhotoPath($process) }}">
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
                                    {{ Form::text('internal', null, ['disabled']) }}
                                </div>
                            </div>
                        </fieldset>

                        <div class="grid-x">
                            <div class="small-12 cell">
                                <label>Описание
                                    @include('includes.inputs.textarea', ['name' => 'description', 'value' => $process->description])
                                </label>
                            </div>
                        </div>

                        {{-- Метрики --}}
                        {{-- @includeIf($item->getTable().'.metrics.metrics') --}}


                        <div id="item-inputs"></div>
                        <div class="small-12 cell tabs-margin-top text-center">
                            <div class="item-error" id="item-error">Такой артикул уже существует!<br>Измените значения!</div>
                        </div>
                        {{ Form::hidden('item_id', $item->id) }}
                    </div>
                    {{-- Конец правого блока на первой вкладке --}}

                    {{-- Чекбокс черновика --}}
                    @if ($process->draft == 1)
                    <div class="small-12 cell checkbox">
                        {{ Form::checkbox('draft', 1, $process->draft, ['id' => 'draft']) }}
                        <label for="draft"><span>Черновик</span></label>
                    </div>
                    @endif

                    {{-- Чекбоксы управления --}}
                    @include('includes.control.checkboxes', ['item' => $item])
                    <div class="small-12 cell ">
                        <span id="composition-error" class="form-error"></span>
                    </div>

                    {{-- Кнопка --}}
                    <div class="small-12 cell tabs-button tabs-margin-top">
                        {{ Form::submit('Редактировать', ['class' => 'button', 'id' => 'add-item']) }}
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
                                    <label>Цена за (<span id="unit">{{ $process->group->unit->abbreviation }}</span>)
                                        {{ Form::number('price_default', null) }}
                                    </label>
                                </div>
                            </div>
                        </fieldset>

                    </div>
                </div>
            </div>

            @includeIf($entity . '.tabs_content')

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

                        {!! Form::hidden('name', $process->name) !!}
                        {!! Form::hidden('id', $process->id) !!}
                        {!! Form::hidden('entity', 'processes') !!}
                        {{-- {!! Form::hidden('album_id', $cur_goods->album_id) !!} --}}

                        {!! Form::close() !!}

                        <ul class="grid-x small-up-4 tabs-margin-top" id="photos-list">

                            @isset($process->album_id)
                            @include('photos.photos', ['item' => $process])
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
@endsection

@section('scripts')
<script>

    // Основные настройки
    var category_entity = '{{ $category_entity }}';

</script>

@include('processes.edit.change_processes_groups_script')

@include('includes.scripts.inputs-mask')
@include('includes.scripts.upload-file')

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

@includeIf($entity . '.scripts')
@endsection
