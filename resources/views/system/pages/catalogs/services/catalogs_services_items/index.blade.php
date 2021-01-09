@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $pageInfo->description }}" />
@endsection

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('catalogs_services-section-index', $catalogServices,  $pageInfo))

@section('content-count')
{{-- Количество элементов --}}
{{ (isset($catalogsServicesItems) && $catalogsServicesItems->isNotEmpty()) ? num_format($catalogsServicesItems->count(), 0) : 0 }}
@endsection

@section('title-content')
{{-- Меню --}}
@include('includes.title-content', [
    'pageInfo' => $pageInfo,
    'class' => App\CatalogsServicesItem::class,
    'type' => 'menu'
]
)
@endsection

@section('content')
{{-- Список --}}
<div class="grid-x">
    <div class="small-12 cell">

        <ul class="vertical menu accordion-menu content-list" id="content" data-accordion-menu data-multi-open="false" data-slide-speed="250" data-entity-alias="catalogs_services_items">

            @if ($catalogsServicesItems->isNotEmpty())

            {{-- Шаблон вывода и динамического обновления --}}
            @include('system.common.categories.index.categories_list', [
                'items' => $catalogsServicesItems,
                'class' => App\CatalogsServicesItem::class,
                'entity' => 'catalogs_services_items',
                'type' => 'page'
            ]
            )

            @endif

        </ul>

    </div>
</div>
@endsection

@section('modals')
    <section id="modal"></section>
    {{-- Модалка удаления ajax --}}
    @include('includes.modals.modal-delete-ajax')
@endsection

@push('scripts')
    {{-- Скрипты --}}
    @include('system.pages.catalogs.services.catalogs_services_items.scripts')

    @include('includes.scripts.sortable-menu-script')
    {{-- Скрипт подсветки многоуровневого меню --}}
    @include('includes.scripts.multilevel-menu-active-scripts')
    {{-- Скрипт отображения на сайте --}}
    @include('includes.scripts.ajax-display')
    {{-- Скрипт системной записи --}}
    @include('includes.scripts.ajax-system')
@endpush
