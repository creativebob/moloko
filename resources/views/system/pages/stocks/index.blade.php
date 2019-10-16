@extends('layouts.app')

@section('inhead')

@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('content-count')
{{-- Количество элементов --}}
{{ $stocks->isNotEmpty() ? num_format($stocks->total(), 0) : 0 }}
@endsection

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\Stock::class, 'type' => 'table'])
@endsection

@section('content')

{{-- Таблица --}}
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
                    <th class="td-name">Название склада</th>
                    <th class="td-description">Описание</th>
                    <th class="td-company">Компания</th>
                    <th class="td-author">Автор</th>
                    <th class="td-control"></th>
                    <th class="td-delete"></th>
                </tr>

            </thead>

            <tbody data-tbodyId="1" class="tbody-width">

                @if($stocks->isNotEmpty())
                @foreach($stocks as $stock)

                <tr class="item @if($stock->moderation == 1)no-moderation @endif" id="stocks-{{ $stock->id }}" data-name="{{ $stock->name }}">
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


                    <td class="td-name">

                        @can('update', $stock)
                            <a href="{{ route('stocks.edit', $stock->id) }}">{{ $stock->name }}</a>
                        @else
                            {{ $stock->name }}
                        @endcan

                    </td>
                    <td class="td-description">{{ $stock->description }}</td>

                    <td class="td-company">
                        @if(!empty($stock->company->name))
                        {{ $stock->company->name }}
                        @else

                        @if($stock->system == null)
                        Шаблон
                        @else
                        Системная
                        @endif

                        @endif
                    </td>

                    <td class="td-author">@if(isset($stock->author->first_name)) {{ $stock->author->first_name . ' ' . $stock->author->second_name }} @endif</td>

                    {{-- Элементы управления --}}
                    @include('includes.control.table-td', ['item' => $stock])

                    <td class="td-delete">
                        @if (($stock->system != 1) && ($stock->photos_count == 0))
                        @can('delete', $stock)
                        <a class="icon-delete sprite" data-open="item-delete"></a>
                        @endcan
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
