@extends('layouts.app')

@section('inhead')
    <meta name="description" content="{{ $pageInfo->page_description }}" />

@endsection

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('content-count')

    {{-- Количество элементов --}}
    @if(!empty($indicators))
        {{ num_format($indicators->count(), 0) }}
    @endif
@endsection

@section('title-content')

    {{-- Таблица --}}
    @include('includes.title-content', ['pageInfo' => $pageInfo, 'class' => App\Plan::class, 'type' => 'table'])
@endsection

@section('content')

{{-- Таблица --}}
<div class="grid-x">

    <div class="small-12 cell">

        {{-- Подключаем таблицу с отображением: месяц --}}
        @include('plans.table-month')

    </div>
</div>

@endsection

@section('modals')

    {{-- Модалка удаления с refresh --}}
    @include('includes.modals.modal-delete')

    {{-- Модалка удаления с refresh --}}
    @include('includes.modals.modal-delete-ajax')
@endsection

@push('scripts')

    {{-- Скрипт чекбоксов, сортировки и перетаскивания для таблицы --}}
    @include('includes.scripts.tablesorter-script')
    @include('includes.scripts.sortable-table-script')

    {{-- Скрипт отображения на сайте --}}
    @include('includes.scripts.ajax-display')

    {{-- Скрипт системной записи --}}
    @include('includes.scripts.ajax-system')

    {{-- Скрипт чекбоксов --}}
    @include('includes.scripts.checkbox-control')

    {{-- Скрипт модалки удаления --}}
    @include('includes.scripts.modal-delete-script')
    @include('includes.scripts.delete-ajax-script')
    @endpush
