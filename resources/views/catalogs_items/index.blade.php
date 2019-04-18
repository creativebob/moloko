@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $page_info->description }}" />
{{-- Скрипты меню в шапке --}}
@include('includes.scripts.sortable-inhead')
@endsection

@section('title', $page_info->name)

{{-- @section('breadcrumbs', Breadcrumbs::render('index', $page_info)) --}}

@section('content-count')
{{-- Количество элементов --}}
{{ (isset($catalogs_items) && $catalogs_items->isNotEmpty()) ? num_format($catalogs_items->count(), 0) : 0 }}
@endsection

@section('title-content')
{{-- Меню --}}
@include('includes.title-content', [
    'page_info' => $page_info,
    'class' => App\CatalogsItem::class,
    'type' => 'menu'
]
)
@endsection

@section('content')
{{-- Список --}}
<div class="grid-x">
    <div class="small-12 cell">

        <ul class="vertical menu accordion-menu content-list" id="content" data-accordion-menu data-multi-open="false" data-slide-speed="250" data-entity-alias="catalogs_items">

            @if ($catalogs_items->isNotEmpty())

            {{-- Шаблон вывода и динамического обновления --}}
            @include('includes.menu_views.category_list', [
                'items' => $catalogs_items,
                'class' => App\CatalogsItem::class,
                'entity' => 'catalogs_items',
                'type' => 'modal'
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

@section('scripts')

{{-- Скрипты --}}
@include('catalogs_items.scripts')

{{-- Скрипт подсветки многоуровневого меню --}}
@include('includes.scripts.multilevel-menu-active-scripts')

{{-- Скрипт отображения на сайте --}}
@include('includes.scripts.ajax-display')

{{-- Скрипт системной записи --}}
@include('includes.scripts.ajax-system')

@endsection