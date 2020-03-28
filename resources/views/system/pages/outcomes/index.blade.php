@extends('layouts.app')

@section('inhead')

@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('content-count')
{{-- Количество элементов --}}
{{ $outcomes->isNotEmpty() ? num_format($outcomes->total(), 0) : 0 }}
@endsection

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\outcome::class, 'type' => 'table'])
@endsection

@section('content')

{{-- Таблица --}}
<div class="grid-x">
    <div class="small-12 cell">

        <table class="content-table tablesorter content-outcomes" id="content" data-sticky-container data-entity-alias="outcomes">

            <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th">
                        <input type="checkbox" class="table-check-all" name="" id="check-all">
                        <label class="label-check" for="check-all"></label>
                    </th>
                    <th class="td-photo">Фото</th>
                    <th class="td-name">Название</th>
                    <th class="td-category">Категория</th>
                    <th class="td-description">Описание</th>
                    <th class="td-author">Автор</th>
                    <th class="td-control"></th>
                    <th class="td-delete"></th>
                </tr>
            </thead>

            <tbody data-tbodyId="1" class="tbody-width">

                @foreach($outcomes as $outcome)

                    <tr class="item @if($outcome->moderation == 1)no-moderation @endif" id="outcomes-{{ $outcome->id }}" data-name="{{ $outcome->name }}">
                        <td class="td-drop"><div class="sprite icon-drop"></div></td>
                        <td class="td-checkbox checkbox">

                            <input type="checkbox" class="table-check" name="outcome_id" id="check-{{ $outcome->id }}"
                            @if(!empty($filter['booklist']['booklists']['default']))
                                @if (in_array($outcome->id, $filter['booklist']['booklists']['default'])) checked
                                @endif
                            @endif
                            >
                            <label class="label-check" for="check-{{ $outcome->id }}"></label>
                        </td>
                        <td class="td-photo">
                            <img src="{{ getPhotoPath($outcome, 'small') }}" alt="{{ isset($outcome->photo_id) ? $outcome->name : 'Нет фото' }}">
                        </td>

                        <td class="td-name">

                            @can('update', $outcome)
                                <a href="{{ route('outcomes.edit', $outcome->id) }}">{{ $outcome->name }}</a>
                            @else
                                {{ $outcome->name }}
                            @endcan

                        </td>
                        <td class="td-category">{{ $outcome->category->name }}</td>
                        <td class="td-description">{{ $outcome->description }}</td>
                        <td class="td-author">@if(isset($outcome->author->first_name)) {{ $outcome->author->first_name . ' ' . $outcome->author->second_name }} @endif</td>

                        {{-- Элементы управления --}}
                        @include('includes.control.table-td', ['item' => $outcome])

                        <td class="td-delete">
                            @can('delete', $outcome)
                                <a class="icon-delete sprite" data-open="item-delete"></a>
                            @endcan
                        </td>
                    </tr>

                @endforeach

            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
<div class="grid-x" id="pagination">
    <div class="small-6 cell pagination-head">
      <span class="pagination-title">Кол-во записей: {{ $outcomes->count() }}</span>
      {{ $outcomes->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
  </div>
</div>
@endsection

@section('modals')
{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete')

{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete-ajax')

@endsection

@section('scripts')
{{-- Скрипт чекбоксов, сортировки и перетаскивания для таблицы --}}
@include('includes.scripts.tablesorter-script')
@include('includes.scripts.sortable-table-script')
@include('includes.scripts.checkbox-control')

{{-- Скрипт отображения на сайте --}}
@include('includes.scripts.ajax-display')

{{-- Скрипт системной записи --}}
@include('includes.scripts.ajax-system')

{{-- Скрипт модалки удаления --}}
@include('includes.scripts.modal-delete-script')
@include('includes.scripts.delete-ajax-script')

@endsection
