@extends('layouts.app')

@section('inhead')
@include('includes.scripts.dropzone-inhead')
@endsection

@section('title', $title)

@section('breadcrumbs', Breadcrumbs::render('category', $category))

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
                <a href="#tab-options" aria-selected="true">Общая информация</a>
            </li>

            @can('index', App\Metric::class)
                @if($pageInfo->entity->metric)
                    <li class="tabs-title">
                        <a data-tabs-target="tab-metrics" href="#tab-metrics">Свойства</a>
                    </li>
                @endif
            @endcan

            @can('index', App\Site::class)
                <li class="tabs-title">
                    <a data-tabs-target="tab-site" href="#tab-site">Настройка для сайта</a>
                </li>
            @endcan

            {{-- Табы для сущности --}}
            @includeIf($pageInfo->entity->view_path . '.tabs')

        </ul>
    </div>
</div>

<div class="grid-x tabs-wrap inputs">
    <div class="small-12 cell tabs-margin-top">
        <div class="tabs-content" data-tabs-content="tabs">

            {{ Form::model($category, ['route' => [$entity . '.update', $category->id], 'data-abide', 'novalidate', 'files' => 'true']) }}
            {{ method_field('PATCH') }}

            {{-- Общая информация --}}
            <div class="tabs-panel is-active" id="tab-options">
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

                            <div class="small-12 medium-6 cell">
                                <label>Тип процесса
                                    @include('includes.selects.processes_types', ['processes_type_id' => $category->processes_type_id])
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

                    @if (is_null($category->parent_id) && ($category->getTable() == 'services_categories'))
                    <div class="small-12 cell checkbox">
                        {{ Form::hidden('is_direction', 0) }}
                        {{ Form::checkbox('is_direction', 1, null, ['id' => 'direction-checkbox']) }}
                        <label for="direction-checkbox"><span>Направление</span></label>
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
            @can('index', App\Site::class)
            <div class="tabs-panel" id="tab-site">
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
                        <div class="grid-x">
                            <photo-upload-component :photo='@json($category->photo)'></photo-upload-component>
                        </div>
{{--                        <label>Выберите аватар--}}
{{--                            {{ Form::file('photo') }}--}}
{{--                        </label>--}}
{{--                        <div class="text-center">--}}
{{--                            <img id="photo" src="{{ getPhotoPath($category) }}">--}}
{{--                        </div>--}}
                    </div>

                    {{-- Кнопка --}}
                    <div class="small-12 cell tabs-button tabs-margin-top">
                        {{ Form::submit('Редактировать', ['class'=>'button']) }}
                    </div>
                </div>
            </div>
            @endcan

            {{-- Метрики --}}
            @can('index', App\Metric::class)
                @if($pageInfo->entity->metric)

                    <div class="tabs-panel" id="tab-metrics">
                        @include('products.common.metrics.page')
                    </div>
                @endif
            @endcan

            {{-- Табы для сущности --}}
            @includeIf($pageInfo->entity->view_path . '.tabs_content')

            {{ Form::close() }}
        </div>
    </div>
</div>
@endsection

@section('modals')
@include('includes.modals.modal_item_delete')
@endsection

@push('scripts')
@include('includes.scripts.inputs-mask')
@include('includes.scripts.ckeditor')

{{-- Проверка поля на существование --}}
@include('includes.scripts.check', ['id' => $category->id])
@endpush
