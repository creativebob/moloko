@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $pageInfo->page_description }}" />
{{-- Скрипты меню в шапке --}}
@include('includes.scripts.sortable-inhead')
@endsection

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('content-count')
{{-- Количество элементов --}}
{{ (isset($items) && $items->isNotEmpty()) ? num_format($items->count(), 0) : 0 }}
@endsection

@section('title-content')
{{-- Меню --}}
@include('includes.title-content', [
    'pageInfo' => $pageInfo,
    'class' => $class,
    'type' => 'menu'
]
)
@endsection

@section('content')
{{-- Список --}}
<div class="grid-x">
    <div class="small-12 cell">
        <ul class="vertical menu accordion-menu content-list" id="content" data-accordion-menu data-multi-open="false" data-slide-speed="250" data-entity-alias="{{ $entity }}">

            @if (isset($items) && $items->isNotEmpty())

            {{-- Шаблон вывода и динамического обновления --}}
            @include('includes.menu_views.category_list', [
                'items' => $items,
                'class' => $class,
                'entity' => $entity,
                'type' => $type
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
@include('includes.menu_views.scripts')

{{-- Скрипт модалки удаления ajax --}}
@include('includes.scripts.delete-ajax-script')

{{-- Маска ввода --}}
@include('includes.scripts.inputs-mask')

{{-- Скрипт подсветки многоуровневого меню --}}
@include('includes.scripts.multilevel-menu-active-scripts')

{{-- Скрипт чекбоксов --}}
@include('includes.scripts.checkbox-control')

{{-- Скрипт отображения на сайте --}}
@include('includes.scripts.ajax-display')

{{-- Скрипт системной записи --}}
@include('includes.scripts.ajax-system')

{{-- Скрипт чекбоксов --}}
@include('includes.scripts.checkbox_control_menu')
@endsection
