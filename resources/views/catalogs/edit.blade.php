@extends('layouts.app')

@section('inhead')

@endsection

@section('title', 'Редактирование каталога')

@section('breadcrumbs', Breadcrumbs::render('section-edit', $parent_page_info, $site, $page_info, $catalog))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">Редактирование категории каталога &laquo{{ $catalog->name }}&raquo</h2>
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
        </ul>
    </div>
</div>

<div class="grid-x tabs-wrap inputs">
    <div class="small-12 cell tabs-margin-top">
        <div class="tabs-content" data-tabs-content="tabs">

            {{ Form::model($catalog, ['route' => ['catalogs.update', $site->alias, $catalog->id], 'data-abide', 'novalidate', 'files' => 'true']) }}
            {{ method_field('PATCH') }}

            {!! Form::hidden('id', $catalog->id, ['id' => 'item-id']) !!}

            <!-- Общая информация -->
            <div class="tabs-panel is-active" id="options">
                <div class="grid-x grid-padding-x">

                    <div class="small-12 medium-6 cell">

                        @if(isset($catalog->parent_id))
                        <label>Расположение
                            @include('includes.selects.categories_select', ['entity' => 'catalogs', 'parent_id' => $catalog->parent_id, 'id' => $catalog->id])
                        </label>
                        @endif

                        <label>Название каталога
                            @include('includes.inputs.name', ['value' => $catalog->name, 'required' => true, 'check' => true])
                            <div class="sprite-input-right find-status" id="name-check"></div>
                            <div class="item-error">Такой каталог уже существует!</div>
                        </label>

                        <label>Алиас каталога
                            @include('includes.inputs.varchar', ['name' => 'alias', 'value' => $catalog->alias, 'check' => true])
                            <div class="sprite-input-right find-status" id="alias-check"></div>
                            <div class="item-error">Каталог с таким алиасом уже существует!</div>
                        </label>
                    </div>

                    {{-- Чекбоксы управления --}}
                    @include('includes.control.checkboxes', ['item' => $catalog])

                    {{-- Кнопка --}}
                    <div class="small-12 cell tabs-button tabs-margin-top">
                        {{ Form::submit('Редактировать', ['class'=>'button']) }}
                    </div>
                </div>
            </div>

            <!-- Сайт -->
            <div class="tabs-panel" id="site">
                <div class="grid-x grid-padding-x">
                    <div class="small-12 medium-6 cell">

                        <label>Описание:
                            {{ Form::textarea('description', $catalog->description, ['id'=>'content-ckeditor', 'autocomplete'=>'off', 'size' => '10x3']) }}
                        </label><br>

                        <label>Description для сайта
                            @include('includes.inputs.textarea', ['value'=>$catalog->seo_description, 'name'=>'seo_description'])
                        </label>

                    </div>
                    <div class="small-12 medium-6 cell">
                        <label>Выберите аватар
                            {{ Form::file('photo') }}
                        </label>
                        <div class="text-center">
                            <img id="photo" @if (isset($catalog->photo_id)) src="/storage/{{ $catalog->company_id }}/media/catalogs/{{ $catalog->id }}/img/medium/{{ $catalog->photo->name }}" @endif>
                        </div>
                    </div>

                    {{-- Кнопка --}}
                    <div class="small-12 cell tabs-button tabs-margin-top">
                        {{ Form::submit('Редактировать каталог', ['class'=>'button']) }}
                    </div>
                </div>



                {{ Form::close() }}
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
@include('includes.scripts.inputs-mask')
@include('includes.scripts.upload-file')
@include('catalogs.scripts')
@include('includes.scripts.ckeditor')
@endsection