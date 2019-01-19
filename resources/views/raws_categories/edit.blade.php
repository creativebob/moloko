@extends('layouts.app')

@section('inhead')
@include('includes.scripts.dropzone-inhead')
@endsection

@section('title', 'Редактирование категории сырья')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $raws_category->name))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">Редактирование категории сырья &laquo{{ $raws_category->name }}&raquo</h2>
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
            <li class="tabs-title">
                <a data-tabs-target="properties" href="#properties">Свойства</a>
            </li>
        </ul>
    </div>
</div>

<div class="grid-x tabs-wrap inputs">
    <div class="small-12 cell tabs-margin-top">
        <div class="tabs-content" data-tabs-content="tabs">

            {{ Form::model($raws_category, ['url' => '/admin/raws_categories/'.$raws_category->id, 'data-abide', 'novalidate', 'files'=>'true', 'id' => 'products-category-form']) }}
            {{ method_field('PATCH') }}

            {{-- Общая информация --}}
            <div class="tabs-panel is-active" id="options">
                <div class="grid-x grid-padding-x">

                    <div class="small-12 medium-6 cell">

                        <div class="grid-x grid-padding-x">

                            @if(isset($raws_category->parent_id))

                            <div class="small-12 medium-6 cell">
                                <label>Расположение
                                    @include('includes.selects.categories_select', ['entity' => 'raws_categories', 'parent_id' => $raws_category->parent_id, 'id' => $raws_category->id])
                                </label>
                            </div>

                            @else

                            {{-- <div class="small-12 medium-6 cell"> --}}
                                @include('includes.selects.raws_modes')
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

                                @include('includes.inputs.checker_contragents', [
                                    'entity' => $raws_category,
                                    'title' => 'Производители',
                                    'name' => 'manufacturers'
                                ]
                                )

                            </div>
                        </div>

                    </div>

                    @include('includes.control.checkboxes', ['item' => $raws_category])

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
                            {{ Form::textarea('description', $raws_category->description, ['id'=>'content-ckeditor', 'autocomplete'=>'off', 'size' => '10x3']) }}
                        </label>

                        <label>Description для сайта
                            @include('includes.inputs.textarea', ['value' => $raws_category->seo_description, 'name' => 'seo_description'])
                        </label>

                    </div>
                    <div class="small-12 medium-6 cell">
                        <label>Выберите аватар
                            {{ Form::file('photo') }}
                        </label>
                        <div class="text-center">
                            <img id="photo" @if (isset($raws_category->photo_id)) src="/storage/{{ $raws_category->company->id }}/media/raws_categories/{{ $raws_category->id }}/img/medium/{{ $raws_category->photo->name }}" @endif>
                        </div>
                    </div>

                    {{-- Кнопка --}}
                    <div class="small-12 cell tabs-button tabs-margin-top">
                        {{ Form::submit('Редактировать категорию услуг', ['class'=>'button']) }}
                    </div>
                </div>
            </div>

            {{ Form::close() }}

            {{-- Подключаем класс для работы с метриками --}}
            @include('includes.scripts.class.metrics')

            {{-- Свойства --}}
            <div class="tabs-panel" id="properties">
                @include('includes.metrics_category.section', ['category' => $raws_category])
            </div>

        </div>
    </div>
</div>

@endsection

@section('scripts')

@include('includes.scripts.inputs-mask')
@include('includes.scripts.upload-file')
@include('raws_categories.scripts')
@include('includes.scripts.ckeditor')

{{-- Проверка поля на существование --}}
@include('includes.scripts.check', ['entity' => $raws_category->getTable()])
@endsection