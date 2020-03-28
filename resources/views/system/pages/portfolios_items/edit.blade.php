@extends('layouts.app')

@section('inhead')
    @include('includes.scripts.dropzone-inhead')
@endsection

@section('title', 'Редактирование пунктапортфолио')

@section('breadcrumbs', Breadcrumbs::render('portfolio-section-edit', $portfolio, $page_info, $portfolios_item))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">Редактирование пункта каталога услуг &laquo{{ $portfolios_item->name }}&raquo</h2>
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
            <li class="tabs-title">
                <a data-tabs-target="tab-site" href="#tab-site">Настройка для сайта</a>
            </li>

        </ul>
    </div>
</div>

<div class="grid-x tabs-wrap inputs">
    <div class="small-12 cell tabs-margin-top">
        <div class="tabs-content" data-tabs-content="tabs">

            {!! Form::model($portfolios_item, ['route' => ['portfolios_items.update', 'portfolio_id' => $portfolio->id, $portfolios_item->id], 'data-abide', 'novalidate', 'files' => 'true']) !!}
            @method('PATCH')

            {{-- Общая информация --}}
            <div class="tabs-panel is-active" id="tab-options">
                <div class="grid-x grid-padding-x">

                    <div class="small-12 medium-6 cell">

                        <div class="grid-x grid-padding-x">

                            @if(isset($portfolios_item->parent_id))

                            <div class="small-12 medium-6 cell">
                                <label>Расположение
                                    @include('includes.selects.categories_select', ['entity' => 'portfolios_items', 'parent_id' => $portfolios_item->parent_id, 'id' => $portfolios_item->id])
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

                    @include('includes.control.checkboxes', ['item' => $portfolios_item])

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
                                <label>Заголовок страницы
                                    @include('includes.inputs.string', ['name' => 'title'])
                                </label>
                            </div>
                            <div class="small-12 cell">
                                <label>Описание:
                                    {{ Form::textarea('description', $portfolios_item->description, ['id'=>'content-ckeditor', 'autocomplete'=>'off', 'size' => '10x3']) }}
                                </label>
                                <br>
                            </div>
                            <div class="small-12 cell">
                                <label>Description для сайта
                                    @include('includes.inputs.textarea', ['value' => $portfolios_item->seo_description, 'name' => 'seo_description'])
                                </label>
                            </div>
                            <div class="small-6 cell">
                                <label>Режим отображения
                                    @include('includes.selects.display_modes')
                                </label>
                            </div>
                            <div class="small-6 cell">
                                <label>Выводить меру в качестве основной:
                                    @include('includes.selects.directive_categories', ['item' => $portfolios_item])
                                </label>
                            </div>

                            {!! Form::hidden('is_controllable_mode', 0) !!}
                            <div class="small-12 cell checkbox">
                                {!! Form::checkbox('is_controllable_mode', 1, $portfolios_item->is_controllable_mode, ['id' => 'checkbox-is_controllable_mode']) !!}
                                <label for="checkbox-is_controllable_mode"><span>Разрешить смену отображения</span></label>
                            </div>

                            {!! Form::hidden('is_show_subcategory', 0) !!}
                            <div class="small-12 cell checkbox">
                                {!! Form::checkbox('is_show_subcategory', 1, $portfolios_item->is_show_subcategory, ['id' => 'checkbox-is_show_subcategory']) !!}
                                <label for="checkbox-is_show_subcategory"><span>Отображать ВСЕ для субкатегорий</span></label>
                            </div>
                        </div>
                    </div>
                    <div class="small-12 medium-6 cell">
                        <label>Выберите аватар
                            {{ Form::file('photo') }}
                        </label>
                        <div class="text-center">
                            <img id="photo" src="{{ getPhotoPath($portfolios_item) }}">
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
    @include('includes.scripts.check', ['id' => $portfolios_item->id])
@endpush
