@extends('layouts.app')

@section('inhead')
@include('includes.scripts.dropzone-inhead')
{{-- @include('includes.scripts.fancybox-inhead')
@include('includes.scripts.sortable-inhead') --}}
@endsection

@section('title', 'Редактирование пункта каталога услуг')

{{-- @section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $catalogs_goods_item->name)) --}}

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">Редактирование пункта каталога услуг &laquo{{ $catalogs_goods_item->name }}&raquo</h2>
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
                <a data-tabs-target="site" href="#site">Настройка для сайта</a>
            </li>
            <li class="tabs-title">
                <a data-tabs-target="tab-filters" href="#tab-filters">Фильтры</a>
            </li>
        </ul>
    </div>
</div>

<div class="grid-x tabs-wrap inputs">
    <div class="small-12 cell tabs-margin-top">
        <div class="tabs-content" data-tabs-content="tabs">

            {{ Form::model($catalogs_goods_item, ['route' => ['catalogs_goods_items.update', 'catalog_id' => $catalog_id, $catalogs_goods_item->id], 'data-abide', 'novalidate', 'files' => 'true']) }}
            {{ method_field('PATCH') }}

            {{-- Общая информация --}}
            <div class="tabs-panel is-active" id="options">
                <div class="grid-x grid-padding-x">

                    <div class="small-12 medium-6 cell">

                        <div class="grid-x grid-padding-x">

                            @if(isset($catalogs_goods_item->parent_id))

                            <div class="small-12 medium-6 cell">
                                <label>Расположение
                                    @include('includes.selects.categories_select', ['entity' => 'catalogs_goods_items', 'parent_id' => $catalogs_goods_item->parent_id, 'id' => $catalogs_goods_item->id])
                                </label>
                            </div>

                            @endif

                            <div class="small-12 medium-6 cell">
                                <label>Название
                                    @include('includes.inputs.name', ['check' => true, 'required' => true])
                                    <div class="item-error">Такая категория уже существует!</div>
                                </label>
                            </div>

                        </div>

                    </div>

                    @include('includes.control.checkboxes', ['item' => $catalogs_goods_item])

                    {{-- Кнопка --}}
                    <div class="small-12 cell tabs-button tabs-margin-top">
                        {{ Form::submit('Редактировать', ['class' => 'button']) }}
                    </div>
                </div>
            </div>

            {{-- Сайт --}}
            <div class="tabs-panel" id="site">
                <div class="grid-x grid-padding-x">
                    <div class="small-12 medium-6 cell">
                        <div class="grid-x grid-padding-x">
                            <div class="small-12 cell">
                                <label>Заголовок страницы
                                    @include('includes.inputs.string', ['name' => 'title'])
                                </label>
                            </div>
                            <div class="small-12 cell">
                                <label>Описание:
                                    {{ Form::textarea('description', $catalogs_goods_item->description, ['id'=>'content-ckeditor', 'autocomplete'=>'off', 'size' => '10x3']) }}
                                </label>
                            </div>
                            <div class="small-12 cell">
                                <label>Description для сайта
                                    @include('includes.inputs.textarea', ['value' => $catalogs_goods_item->seo_description, 'name' => 'seo_description'])
                                </label>
                            </div>
                            <div class="small-12 cell">
                                <label>Режим отображения
                                    @include('includes.selects.display_modes')
                                </label>
                            </div>
                            {!! Form::hidden('is_controllable_mode', 0) !!}
                            <div class="small-12 cell checkbox">
                                {!! Form::checkbox('is_controllable_mode', 1, $catalogs_goods_item->is_controllable_mode, ['id' => 'is_controllable_mode-checkbox']) !!}
                                <label for="is_controllable_mode-checkbox"><span>Разрешить смену отображения</span></label>
                            </div>
                        </div>
                    </div>
                    <div class="small-12 medium-6 cell">
                        <label>Выберите аватар
                            {{ Form::file('photo') }}
                        </label>
                        <div class="text-center">
                            <img id="photo" src="{{ getPhotoPath($catalogs_goods_item) }}">
                        </div>
                        <div class="small-6 medium-6 cell">
                            <label>Цвет для оформления
                                {!! Form::color('color') !!}
                            </label>
                        </div>
                    </div>

                    {{-- Кнопка --}}
                    <div class="small-12 cell tabs-button tabs-margin-top">
                        {{ Form::submit('Редактировать', ['class'=>'button']) }}
                    </div>
                </div>
            </div>

            {{-- Фильтры --}}
            <div class="tabs-panel" id="tab-filters">
                <div class="grid-x grid-padding-x">
                    <div class="small-12 medium-6 cell">
                        <fieldset>
                            <legend>Фильтры</legend>
                            @include('includes.lists.filters')
                        </fieldset>
                    </div>
                    <div class="small-12 medium-6 cell">

                    </div>

                    {{-- Кнопка --}}
                    <div class="small-12 cell tabs-button tabs-margin-top">
                        {{ Form::submit('Редактировать', ['class'=>'button']) }}
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
@include('includes.modals.modal_item_delete')
@endsection

@push('scripts')

@include('includes.scripts.inputs-mask')
@include('includes.scripts.upload-file')
{{-- @include('goods_categories.scripts') --}}

@include('includes.scripts.ckeditor')

{{-- Проверка поля на существование --}}
@include('includes.scripts.check', ['id' => $catalogs_goods_item->id])

@endpush
