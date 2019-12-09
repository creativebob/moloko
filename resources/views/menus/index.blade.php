@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $page_info->description }}" />
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('menus-index', $site, $navigation, $page_info))

@section('content-count')
{{-- Количество элементов --}}
{{ (isset($menus) && $menus->isNotEmpty()) ? num_format($menus->count(), 0) : 0 }}
@endsection

@section('title-content')
{{-- Меню --}}
@include('includes.title-content', [
    'page_info' => $page_info,
    'class' => App\Menu::class,
    'type' => 'menu'
]
)
@endsection

@section('content')
{{-- Список --}}
<div class="grid-x">
    <div class="small-12 cell">

        <ul class="vertical menu accordion-menu content-list" id="content" data-accordion-menu data-multi-open="false" data-slide-speed="250" data-entity-alias="menus">

            @if ($menus->isNotEmpty())

            {{-- Шаблон вывода и динамического обновления --}}
            @include('system.common.accordions.categories_list', [
                'items' => $menus,
                'class' => App\Menu::class,
                'entity' => 'menus',
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

@push('scripts')

{{-- Скрипты --}}
@include('menus.scripts')

@include('includes.scripts.sortable-menu-script')

{{-- Скрипт подсветки многоуровневого меню --}}
@include('includes.scripts.multilevel-menu-active-scripts')

{{-- Скрипт отображения на сайте --}}
@include('includes.scripts.ajax-display')

{{-- Скрипт системной записи --}}
@include('includes.scripts.ajax-system')

@endpush
