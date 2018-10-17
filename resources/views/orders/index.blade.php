@extends('layouts.app')
 
@section('inhead')
{{-- Скрипты таблиц в шапке --}}
  @include('includes.scripts.tablesorter-inhead')
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('content-count')
{{-- Количество элементов --}}
  @if(!empty($orders))
    {{ num_format($orders->total(), 0) }}
  @endif
@endsection

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\Order::class, 'type' => 'table'])
@endsection
 
@section('content')

{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="table-content tablesorter" id="content" data-sticky-container data-order-alias="orders">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-name">Название таблицы</th>
          <th class="td-model">Имя модели</th>
          <th class="td-alias">Название в DB</th>
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
      @if(!empty($orders))
        @foreach($orders as $order)
        <tr class="item @if(Auth::user()->entity_id == $order->id)active @endif  @if($order->moderation == 1)no-moderation @endif" id="orders-{{ $order->id }}" data-name="{{ $order->name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox"><input type="checkbox" class="table-check" name="" id="check-{{ $order->id }}"><label class="label-check" for="check-{{ $order->id }}"></label></td>
          <td class="td-name">
            @can('update', $order)
            <a href="/admin/orders/{{ $order->id }}/edit">
            @endcan
            {{ $order->name }}
            @can('update', $order)
            </a> 
            @endcan
          <td class="td-model">{{ $order->model }}</td>
          <td class="td-alias">{{ $order->alias }}</td>
          <td class="td-delete">
          @if ($order->system_item !== 1)
            @can('delete', $order)
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
    <span class="pagination-title">Кол-во записей: {{ $orders->count() }}</span>
    {{ $orders->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
  </div>
</div>
@endsection

@section('modals')
{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete')
@endsection

@section('scripts')
{{-- Скрипт чекбоксов, сортировки и перетаскивания для таблицы --}}
@include('includes.scripts.tablesorter-script')

  {{-- Скрипт сортировки --}}
  @include('includes.scripts.sortable-table-script')

{{-- Скрипт модалки удаления --}}
@include('includes.scripts.modal-delete-script')
@endsection