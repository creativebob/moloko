@extends('layouts.app')

@section('inhead')
    @include('includes.scripts.dropzone-inhead')
@endsection

@section('title', 'Редактирование пункта каталога услуг')

@section('breadcrumbs', Breadcrumbs::render('catalogs_services-section-edit', $catalogServices,  $pageInfo, $catalogsServicesItem))

@section('title-content')
    <div class="top-bar head-content">
        <div class="top-bar-left">
            <h2 class="header-content">Редактирование пункта каталога услуг &laquo{{ $catalogsServicesItem->name }}&raquo</h2>
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
                    <a href="#tab-general" aria-selected="true">Общая информация</a>
                </li>

                @can('index', App\Site::class)
                    <li class="tabs-title">
                        <a data-tabs-target="tab-site" href="#tab-site">Настройка для сайта</a>
                    </li>
                @endcan

                <li class="tabs-title">
                    <a data-tabs-target="tab-seo" href="#tab-seo">SEO</a>
                </li>

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

                {!! Form::model($catalogsServicesItem, ['route' => ['catalogs_services_items.update', 'catalogId' => $catalogServices->id, $catalogsServicesItem->id], 'data-abide', 'novalidate', 'files' => 'true']) !!}
                @method('PATCH')

                {{-- Общая информация --}}
                <div class="tabs-panel is-active" id="tab-general">
                    @include('system.pages.catalogs.services.catalogs_services_items.tabs.general')
                </div>

                @can('index', App\Site::class)
                    {{-- Сайт --}}
                    <div class="tabs-panel" id="tab-site">
                        @include('system.pages.catalogs.services.catalogs_services_items.tabs.site')
                    </div>
                @endcan

                {{-- SEO --}}
                <div class="tabs-panel" id="tab-seo">
                    @include('system.common.tabs.seo', ['seo' => $catalogsServicesItem->seo])
                </div>

                {{-- Фильтры --}}
                <div class="tabs-panel" id="tab-filters">
                    @include('system.pages.catalogs.services.catalogs_services_items.tabs.filters')
                </div>

                @can('index', App\Discount::class)
                    <div class="tabs-panel" id="tab-discounts">
                        @include('system.common.discounts.discounts', ['item' => $catalogsServicesItem, 'entity' => 'catalogs_services_items'])
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
    @include('includes.scripts.check', ['id' => $catalogsServicesItem->id])
@endpush
