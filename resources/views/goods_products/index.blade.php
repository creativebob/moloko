@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $page_info->page_description }}" />
{{-- Скрипты таблиц в шапке --}}
@include('includes.scripts.tablesorter-inhead')
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('content-count')
{{-- Количество элементов --}}
  @if(!empty($goods_products))
    {{ num_format($goods_products->total(), 0) }}
  @endif
@endsection

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\GoodsProduct::class, 'type' => 'table'])
@endsection

@section('content')
{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="table-content tablesorter" id="content" data-sticky-container data-entity-alias="goods_products">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-name" data-serversort="name">Название группы товаров</th>
          <th class="td-goods_catrgory">Категория</th>
          <th class="td-description">Описание</th>
          <th class="td-control"></th>
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
        @if(!empty($goods_products))
        @foreach($goods_products as $goods_product)
        <tr class="item @if($goods_product->moderation == 1)no-moderation @endif" id="goods_products-{{ $goods_product->id }}" data-name="{{ $goods_product->name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox">
            <input type="checkbox" class="table-check" name="goods_product_id" id="check-{{ $goods_product->id }}"

            {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
            @if(!empty($filter['booklist']['booklists']['default']))
            {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
            @if (in_array($goods_product->id, $filter['booklist']['booklists']['default'])) checked 
            @endif
            @endif
            ><label class="label-check" for="check-{{ $goods_product->id }}"></label>
          </td>
          <td class="td-name">
            @php
            $edit = 0;
            @endphp
            @can('update', $goods_product)
            @php
            $edit = 1;
            @endphp
            @endcan
            @if($edit == 1)
            <a href="/admin/goods_products/{{ $goods_product->id }}/edit">
              @endif
              {{ $goods_product->name }} (<a href="/admin/goods?goods_product_id%5B%5D={{ $goods_product->id }}" title="Перейти на список товаров" class="filter_link light-text">{{ $goods_product->goods_articles->count() }}</a>)
              @if($edit == 1)
            </a>
            @endif
          </td>
          <td class="td-goods_catrgory">{{ $goods_product->goods_category->name }}</td>
          <td class="td-description">{{ $goods_product->description }}</td>

          {{-- Элементы управления --}}
          @include('includes.control.table-td', ['item' => $goods_product])

          <td class="td-delete">
            @if ($goods_product->system_item != 1)
            @can('delete', $goods_product)
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
    <span class="pagination-title">Кол-во записей: {{ $goods_products->count() }}</span>
    {{ $goods_products->links() }}
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