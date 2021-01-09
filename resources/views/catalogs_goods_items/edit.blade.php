@extends('layouts.app')

@section('inhead')
@include('includes.scripts.dropzone-inhead')
{{-- @include('includes.scripts.fancybox-inhead')
@include('includes.scripts.sortable-inhead') --}}
@endsection

@section('title', 'Редактирование пункта каталога услуг')

@section('breadcrumbs', Breadcrumbs::render('catalogs_goods-section-edit', $catalog_goods,  $pageInfo, $catalogs_goods_item))

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
                <a href="#tab-options" aria-selected="true">Общая информация</a>
            </li>

            @can('index', App\Site::class)
            <li class="tabs-title">
                <a data-tabs-target="tab-site" href="#tab-site">Настройка для сайта</a>
            </li>
            @endcan

            <li class="tabs-title">
                <a data-tabs-target="tab-filters" href="#tab-filters">Фильтры</a>
            </li>

            @can('index', App\Discount::class)
                <li class="tabs-title">
                    <a href="#tab-discounts" data-tabs-target="tab-discounts">Скидки</a>
                </li>
            @endcan
        </ul>
    </div>
</div>

<div class="grid-x tabs-wrap inputs">
    <div class="small-12 cell tabs-margin-top">
        <div class="tabs-content" data-tabs-content="tabs">

            {!! Form::model($catalogs_goods_item, ['route' => ['catalogs_goods_items.update', 'catalog_id' => $catalog_id, $catalogs_goods_item->id], 'data-abide', 'novalidate', 'files' => 'true']) !!}
            @method('PATCH')

            {{-- Общая информация --}}
            <div class="tabs-panel is-active" id="tab-options">
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

            @can('index', App\Site::class)
            {{-- Сайт --}}
            <div class="tabs-panel" id="tab-site">
                <div class="grid-x grid-padding-x">
                    <div class="small-12 medium-6 cell">
                        <div class="grid-x grid-padding-x">
                            <div class="small-12 cell">
                                <label>Название страницы (Title)
                                    {!! Form::text('title', null, ['maxlength'=>'250', 'autocomplete'=>'off', 'autofocus', 'data']) !!}
{{--                                    @include('includes.inputs.string', ['name' => 'title'])--}}
                                </label>
                            </div>
                            <div class="small-12 cell">
                                <label>Заголовок страницы (H1)
                                    {!! Form::text('header', null, ['maxlength'=>'250', 'autocomplete'=>'off', 'autofocus', 'data']) !!}
{{--                                    @include('includes.inputs.string', ['name' => 'header'])--}}
                                </label>
                            </div>
                            <div class="small-12 cell">
                                <label>Описание для вывода на сайт:
                                    {{ Form::textarea('description', $catalogs_goods_item->description, ['id'=>'content-ckeditor', 'autocomplete'=>'off', 'size' => '10x3']) }}
                                </label>
                            </div>
                            <div class="small-12 cell">
                                <label>Описание для поисковых систем (Description)
                                    @include('includes.inputs.textarea', ['value' => $catalogs_goods_item->seo_description, 'name' => 'seo_description'])
                                </label>
                            </div>
                            <div class="small-12 cell">
                                <label>Список ключевых слов (Keywords)
                                    @include('includes.inputs.string', ['name' => 'keywords'])
                                </label>
                            </div>     
                            <div class="small-6 cell">
                                <label>Режим отображения
                                    @include('includes.selects.display_modes')
                                </label>
                            </div>
                            <div class="small-6 cell">
                                <label>Выводить меру в качестве основной:
                                    @include('includes.selects.directive_categories', ['item' => $catalogs_goods_item])
                                </label>
                            </div>

                            {!! Form::hidden('is_controllable_mode', 0) !!}
                            <div class="small-12 cell checkbox">
                                {!! Form::checkbox('is_controllable_mode', 1, $catalogs_goods_item->is_controllable_mode, ['id' => 'checkbox-is_controllable_mode']) !!}
                                <label for="checkbox-is_controllable_mode"><span>Разрешить смену отображения</span></label>
                            </div>

                            {!! Form::hidden('is_show_subcategory', 0) !!}
                            <div class="small-12 cell checkbox">
                                {!! Form::checkbox('is_show_subcategory', 1, $catalogs_goods_item->is_show_subcategory, ['id' => 'checkbox-is_show_subcategory']) !!}
                                <label for="checkbox-is_show_subcategory"><span>Отображать ВСЕ для субкатегорий</span></label>
                            </div>

                            {!! Form::hidden('is_hide_submenu', 0) !!}
                            <div class="small-12 cell checkbox">
                                {!! Form::checkbox('is_hide_submenu', 1, $catalogs_goods_item->is_hide_submenu, ['id' => 'checkbox-is_hide_submenu']) !!}
                                <label for="checkbox-is_hide_submenu"><span>Не отображать субменю</span></label>
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
                                {!! Form::text('color') !!}
                            </label>
                        </div>
                    </div>

                    {{-- Кнопка --}}
                    <div class="small-12 cell tabs-button tabs-margin-top">
                        {{ Form::submit('Редактировать', ['class'=>'button']) }}
                    </div>
                </div>
            </div>
            @endcan

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

            @can('index', App\Discount::class)
                <div class="tabs-panel" id="tab-discounts">
                    @include('system.common.discounts.discounts', ['item' => $catalogs_goods_item, 'entity' => 'catalogs_goods_items'])
                </div>
            @endcan

            {!! Form::close() !!}
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
    @include('includes.scripts.ckeditor')
    {{-- Проверка поля на существование --}}
    @include('includes.scripts.check', ['id' => $catalogs_goods_item->id])
@endpush
