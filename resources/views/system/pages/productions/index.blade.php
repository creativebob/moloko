@extends('layouts.app')

@section('inhead')

@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('content-count')
{{-- Количество элементов --}}
{{ $productions->isNotEmpty() ? num_format($productions->total(), 0) : 0 }}
@endsection

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\Consignment::class, 'type' => 'table'])
@endsection

@section('content')

{{-- Таблица --}}
<div class="grid-x">
    <div class="small-12 cell">

        <table class="content-table tablesorter consignments" id="content" data-sticky-container data-entity-alias="consignments">

            <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
                    <th class="td-receipt_date">Дата</th>
                    {{-- <th class="td-number">Номер</th> --}}
                    {{-- <th class="td-supplier-name">Поставщик</th> --}}
                    <th class="td-phone">Телефон</th>
                    <th class="td-amount">Сумма</th>
                    <th class="td-description">Коментарий</th>
                    <th class="td-payment">Оплачено</th>
                    <th class="td-status">Статус</th>
                    <th class="td-created_at">Создана</th>
                    <th class="td-author">Автор</th>
                    <th class="td-delete"></th>
                </tr>
            </thead>

            <tbody data-tbodyId="1" class="tbody-width">

                @if($productions->isNotEmpty())
                @foreach($productions as $production)

                <tr class="item @if($production->moderation == 1)no-moderation @endif" id="consignments-{{ $production->id }}" data-name="{{ $production->name }}">
                    <td class="td-drop">
                        <div class="sprite icon-drop"></div>
                    </td>
                    <td class="td-checkbox checkbox">

                        <input type="checkbox" class="table-check" name="consignment" id="check-{{ $production->id }}"
                        @if(!empty($filter['booklist']['booklists']['default']))
                        @if (in_array($production->id, $filter['booklist']['booklists']['default'])) checked
                        @endif
                        @endif
                        >
                        <label class="label-check" for="check-{{ $production->id }}"></label>

                    </td>

                    <td class="td-receipt_date">
                        <a href="/admin/{{ $production->getTable() }}/{{ $production->id }}/edit">
                        <span>{{ isset($production->receipt_date) ? $production->receipt_date->format('d.m.Y') : null }}</span></a>
                    </td>


                    <td class="td-number">
                        {{ $production->number ?? '' }}
                        <br><span class="tiny-text"></span>
                    </td>

                      {{-- <td class="td-supplier-name">

                          <a href="/admin/consignments?supplier_id%5B%5D={{ $production->supplier->id ?? '' }}" class="filter_link" title="Фильтровать">
                            {{ $production->supplier->company->name ?? '' }}
                        </a>
                        <br>
                        <span class="tiny-text">
                            {{ $production->supplier->company->location->city->name ?? '' }}, {{ $production->supplier->company->location->address ?? '' }}
                        </span>
                      </td> --}}

                        {{-- <td class="td-phone">
                            {{ isset($production->supplier->company->main_phone->phone) ? decorPhone($production->supplier->company->main_phone->phone) : 'Номер не указан' }}
                            @if($production->supplier->email ?? '' )<br><span class="tiny-text">{{ $production->supplier->company->email ?? '' }}</span>@endif
                        </td> --}}

                        <td class="td-amount">{{ num_format($production->amount, 0) }}</td>

                    <td class="td-description">

                        @can('view', $production)
                        
                            <span data-toggle="dropdown-{{ $production->id }}">{{ $production->name ?? '' }}</span>
                            <div class="dropdown-pane bottom right" id="dropdown-{{ $production->id }}" data-dropdown data-hover="true" data-hover-pane="true">
                              {!! $production->description ?? '' !!}
                          </div>
                          @else
                          {{ $production->name ?? '' }}
                          @endcan

                    </td>

                        <td class="td-payment">{{ num_format($production->payment, 0) }}
                          <br><span class="tiny-text">{{ num_format($production->amount - $production->payment, 0) }}</span>
                      </td>
                      <td class="td-status">@if($production->is_produced)Произведен @endif</td>
                      <td class="td-created_at">
                        <span>{{ $production->created_at->format('d.m.Y') }}</span><br>
                        <span class="tiny-text">{{ $production->created_at->format('H:i') }}</span>
                    </td>
                    <td class="td-author">{{ $production->author->name ?? '' }}</td>
                    {{-- Элементы управления --}}
                    @include('includes.control.table_td', ['item' => $production])
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
    <span class="pagination-title">Кол-во записей: {{ $productions->count() }}</span>
    {{ $productions->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
</div>
</div>

@endsection

@section('modals')
<section id="modal"></section>

{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete')

@endsection

@section('scripts')

{{-- Скрипт сортировки и перетаскивания для таблицы --}}
@include('includes.scripts.tablesorter-script')
@include('includes.scripts.sortable-table-script')
@include('includes.scripts.pickmeup-script')

{{-- Скрипт отображения на сайте --}}
@include('includes.scripts.ajax-display')

{{-- Скрипт системной записи --}}
@include('includes.scripts.ajax-system')

{{-- Скрипт чекбоксов --}}
@include('includes.scripts.checkbox-control')

{{-- Скрипт модалки удаления --}}
@include('includes.scripts.modal-delete-script')
@include('includes.scripts.delete-ajax-script')

@endsection

