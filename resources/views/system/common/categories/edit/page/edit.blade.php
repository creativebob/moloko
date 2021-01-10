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

            @can('index', App\Site::class)
            <li class="tabs-title">
                <a data-tabs-target="tab-site" href="#tab-site">Настройка для сайта</a>
            </li>
            @endcan

            {{-- Табы для сущности --}}
            @includeIf($pageInfo->entity->view_path . '.tabs')

            {{-- <li class="tabs-title"><a data-tabs-target="price-rules" href="#price-rules">Ценообразование</a></li> --}}
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

                            @endif

                            <div class="small-12 medium-6 cell">
                                <label>Название категории
                                    @include('includes.inputs.name', ['check' => true, 'required' => true])
                                    <div class="item-error">Такая категория уже существует!</div>
                                </label>
                            </div>

                        </div>

                    </div>

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
                            {{ Form::textarea('description', $category->description, ['id' => 'content-ckeditor', 'autocomplete' => 'off', 'size' => '10x3']) }}
                        </label>

                        <label>Description для сайта
                            @include('includes.inputs.textarea', ['value' => $category->seo_description, 'name' => 'seo_description'])
                        </label>

                        <label>Список ключевых слов (Keywords)
                            {!! Form::text('keywords', null, ['maxlength'=>'250', 'autocomplete'=>'off', 'data']) !!}
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

            {{-- Табы для сущности --}}
            @includeIf($pageInfo->entity->view_path . '.tabs_content')

            {{ Form::close() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('includes.scripts.inputs-mask')
@include('includes.scripts.ckeditor')

{{-- Проверка поля на существование --}}
@include('includes.scripts.check', ['id' => $category->id])
@endpush
