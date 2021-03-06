@extends('layouts.app')

@section('inhead')

@endsection

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('content-count')
{{-- Количество элементов --}}
{{ $stocks->isNotEmpty() ? num_format($stocks->total(), 0) : 0 }}
@endsection

@section('title-content')
    {{-- Таблица --}}
    @include('system.common.stocks.includes.title')
@endsection

@section('content')

{{-- Таблица --}}

<div class="grid-x" id="pagination">
    <div class="small-6 cell pagination-head">
        {{ $stocks->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
    </div>
</div>

<div class="grid-x">
    <div class="small-12 cell">

        <table class="content-table tablesorter" id="content" class="content-stocks" data-sticky-container data-entity-alias="stocks">

            <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">

                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th">
                        <input type="checkbox" class="table-check-all" name="" id="check-all">
                        <label class="label-check" for="check-all"></label>
                    </th>
                    <th class="td-name">Название</th>
                    <th class="td-manufacturer">Производитель</th>
                    <th class="td-count">Количество</th>
                    <th class="td-reserve">Резерв</th>
                    <th class="td-free">Доступно</th>

                    <th class="td-weight">Вес</th>
                    <th class="td-volume">Объем</th>
                    <th class="td-cost">Стоимость</th>
                    <th class="td-stock">Склад</th>
                    <th class="td-status">Статус</th>
                    <th class="td-company">Компания</th>
                </tr>

            </thead>

            <tbody data-tbodyId="1" class="tbody-width">

                @if($stocks->isNotEmpty())
                @foreach($stocks as $stock)

                <tr class="item @if($stock->moderation == 1)no-moderation @endif @if($stock->cmv->archive == 1) archive-cmv @endif" id="stocks-{{ $stock->id }}" data-name="{{ $stock->name }}">
                    <td class="td-drop">
                        <div class="sprite icon-drop"></div>
                    </td>

                    <td class="td-checkbox checkbox">
                        <input type="checkbox" class="table-check" name="stock_id" id="check-{{ $stock->id }}"
                        @if(!empty($filter['booklist']['booklists']['default']))
                        @if (in_array($stock->id, $filter['booklist']['booklists']['default'])) checked
                        @endif
                        @endif
                        >
                        <label class="label-check" for="check-{{ $stock->id }}"></label>
                    </td>


                    <td class="td-name" title="ID ТМЦ: {{ $stock->cmv->id }}">
                        @can('update', $stock)
                            <a href="{{ route($stock->getTable() . '.edit', $stock->id) }}">{{ $stock->cmv->article->name }}</a>
                        @else
                            {{ $stock->cmv->article->name }}
                            @endcan
                        <br><span class="tiny-text">{{ $stock->cmv->category->name }}</span>
                    </td>
                    <td>
                        {{ $stock->manufacturer->company->name ?? '' }}
                    </td>

                    <td class="td-count">{{ num_format($stock->count, 2) }} {{ $stock->cmv->article->unit->abbreviation }}</td>
                    <td class="td-reserve">{{ num_format($stock->reserve, 2) }} {{ $stock->cmv->article->unit->abbreviation }}</td>
                    <td class="td-free">{{ num_format($stock->free, 2) }} {{ $stock->cmv->article->unit->abbreviation }}</td>

                    <td class="td-weight">{{ num_format($stock->weight, 2) }} кг.</td>
                    <td class="td-volume">{{ num_format($stock->volume / 0.001, 2) }} л.</td>
                    <td class="td-cost">
                        {{ num_format($stock->cmv->cost_unit * $stock->count, 2) }}<br>
                        <span class="tiny-text">{{ num_format($stock->cmv->cost_unit, 2) }}</span>
                    </td>

                    <td class="td-stock">{{ $stock->stock->name }}
                        {{-- @if($stock->stock->is_production)<br><span class="tiny-text">(Производственный)</span>@endif
                        @if($stock->stock->is_goods)<br><span class="tiny-text">(Готовой продукции)</span>@endif --}}
                    </td>

                    <td class="td-status">
                        @if($stock->stock->is_production)<span>Производственный</span><br>@endif
                        @if($stock->stock->is_goods)<span>Готовой продукции</span><br>@endif
                    </td>

                    <td class="td-company">
                        @if(!empty($stock->company->name))
                            {{ $stock->company->designation ?? $stock->company->name }}
                            <br><span class="tiny-text">{{ $stock->stock->filial->name }}</span>
                        @else

                            @if($stock->system == null)
                                Шаблон
                            @else
                                Системная
                            @endif

                        @endif
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
      <span class="pagination-title">Кол-во записей: {{ $stocks->count() }}</span>
      {{ $stocks->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
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
@include('includes.scripts.checkbox-control')

{{-- Скрипт отображения на сайте --}}
@include('includes.scripts.ajax-display')

{{-- Скрипт системной записи --}}
@include('includes.scripts.ajax-system')

{{-- Скрипт модалки удаления --}}
@include('includes.scripts.modal-delete-script')
@include('includes.scripts.delete-ajax-script')

@endpush
