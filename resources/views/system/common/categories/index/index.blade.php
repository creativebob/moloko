@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $pageInfo->description }}" />
@endsection

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('content-count')
{{-- Количество элементов --}}
{{ (isset($items) && $items->isNotEmpty()) ? num_format($items->count(), 0) : 0 }}
@endsection

@section('title-content')
{{-- Меню --}}
@include('system.common.categories.index.includes.title', [
    'class' => $class,
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
            @include('system.common.categories.index.categories_list', [
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

@push('scripts')

    @include('includes.scripts.sortable-inhead')

{{-- Скрипты --}}
@include('system.common.categories.index.scripts')

{{-- Скрипт модалки удаления ajax --}}
@include('includes.scripts.delete-ajax-script')

{{-- Маска ввода --}}
@include('includes.scripts.inputs-mask')

{{-- Скрипт чекбоксов и перетаскивания для меню --}}
@include('includes.scripts.sortable-menu-script')

{{-- Скрипт подсветки многоуровневого меню --}}
<script type="application/javascript">



</script>

{{-- Скрипт чекбоксов --}}
@include('includes.scripts.checkbox-control')

{{-- Скрипт отображения на сайте --}}
@include('includes.scripts.ajax-display')

{{-- Скрипт системной записи --}}
@include('includes.scripts.ajax-system')

{{-- Скрипт чекбоксов --}}
@include('includes.scripts.checkbox_control_menu')

    <script>
        $.getScript("/js/system/jquery.maskedinput.js");
        $.getScript("/js/system/inputs_mask.js");
    </script>
@endpush
