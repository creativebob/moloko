@extends('layouts.app')

@section('inhead')

@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('content-count')
{{-- Количество элементов --}}
{{ $indicators->isNotEmpty() ? num_format($indicators->total(), 0) : 0 }}
@endsection

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\Indicator::class, 'type' => 'table'])
@endsection

@section('content')

{{-- Таблица --}}
<div class="grid-x">
    <div class="small-12 cell">

        <table class="content-table tablesorter" id="content" class="content-indicators" data-sticky-container data-entity-alias="indicators">

            <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">

                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th">
                        <input type="checkbox" class="table-check-all" name="" id="check-all">
                        <label class="label-check" for="check-all"></label>
                    </th>
                    <th class="td-name">Название</th>
                    <th class="td-category">Категория показателя</th>
                    <th class="td-description">Описание</th>
                    <th class="td-entity">Сущность</th>
                    <th class="td-author">Автор</th>
                    <th class="td-control"></th>
                    <th class="td-delete"></th>
                </tr>

            </thead>

            <tbody data-tbodyId="1" class="tbody-width">

                @if($indicators->isNotEmpty())
                @foreach($indicators as $indicator)

                <tr class="item @if($indicator->moderation == 1)no-moderation @endif" id="indicators-{{ $indicator->id }}" data-name="{{ $indicator->name }}">

                    <td class="td-drop">
                        <div class="sprite icon-drop"></div>
                    </td>
                    <td class="td-checkbox checkbox">

                        <input type="checkbox" class="table-check" name="indicator_id" id="check-{{ $indicator->id }}"
                        >
                        <label class="label-check" for="check-{{ $indicator->id }}"></label>
                    </td>

                    <td class="td-name">

                        @can('update', $indicator)
                            <a href="{{ route('indicators.edit', $indicator->id) }}">{{ $indicator->name }}</a>
                        @else
                            {{ $indicator->name }}
                        @endcan


                    </td>

                    <td class="td-category">{{ isset($indicator->indicators_category) ? $indicator->indicators_category->name : 'Нет' }}</td>
                    <td class="td-description">{{ $indicator->description }}</td>

                    <td class="td-entity">{{ isset($indicator->entity) ? $indicator->entity->name : '' }}</td>

                    <td class="td-author">
                        {{ isset($indicator->author) ? $indicator->author->name : 'Не указан' }}
                    </td>

                    {{-- Элементы управления --}}
                    @include('includes.control.table-td', ['item' => $indicator])

                    <td class="td-delete">

                        @can('delete', $indicator)
                        <a class="icon-delete sprite" data-open="item-delete"></a>
                        @endcan

                    </td>
                </tr>

                @endforeach
                @endif

            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
<div class="grid-x" id="pagination">
    <div class="small-6 cell pagination-head">
      <span class="pagination-title">Кол-во записей: {{ $indicators->count() }}</span>
      {{ $indicators->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
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
