@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $page_info->description }}" />
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('portfolio-section-index', $portfolio, $page_info))

@section('content-count')
{{-- Количество элементов --}}
{{ (isset($portfolios_items) && $portfolios_items->isNotEmpty()) ? num_format($portfolios_items->count(), 0) : 0 }}
@endsection

@section('title-content')
{{-- Меню --}}
@include('includes.title-content', [
    'page_info' => $page_info,
    'class' => App\CatalogsGoodsItem::class,
    'type' => 'menu'
]
)
@endsection

@section('content')
{{-- Список --}}
<div class="grid-x">
    <div class="small-12 cell">

        <ul class="vertical menu accordion-menu content-list" id="content" data-accordion-menu data-multi-open="false" data-slide-speed="250" data-entity-alias="portfolios_items">

            @if ($portfolios_items->isNotEmpty())

            {{-- Шаблон вывода и динамического обновления --}}
            @include('system.common.categories.index.categories_list', [
                'items' => $portfolios_items,
                'class' => App\PortfoliosItem::class,
                'entity' => 'portfolios_items',
                'type' => 'page',
                'absolute' => false
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
    @include('includes.scripts.sortable-menu-script')
    {{-- Скрипты --}}
    @include('system.pages.portfolios_items.scripts')
    {{-- Скрипт подсветки многоуровневого меню --}}
    @include('includes.scripts.multilevel-menu-active-scripts')
    {{-- Скрипт отображения на сайте --}}
    @include('includes.scripts.ajax-display')
    {{-- Скрипт системной записи --}}
    @include('includes.scripts.ajax-system')
@endpush
