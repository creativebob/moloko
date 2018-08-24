@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $page_info->page_description }}" />
{{-- Скрипты таблиц в шапке --}}
@include('includes.scripts.tablesorter-inhead')
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\ServicesProduct::class, 'type' => 'table'])
@endsection

@section('content')
{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="table-content tablesorter" id="content" data-sticky-container data-entity-alias="services_products">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-name" data-serversort="name">Название группы услуг</th>
          <th class="td-services_catrgory">Категория</th>
          <th class="td-description">Описание</th>
          <th class="td-control"></th>
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
        @if(!empty($services_products))
        @foreach($services_products as $services_product)
        <tr class="item @if($services_product->moderation == 1)no-moderation @endif" id="services_products-{{ $services_product->id }}" data-name="{{ $services_product->name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox">
            <input type="checkbox" class="table-check" name="services_product_id" id="check-{{ $services_product->id }}"

            {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
            @if(!empty($filter['booklist']['booklists']['default']))
            {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
            @if (in_array($services_product->id, $filter['booklist']['booklists']['default'])) checked 
            @endif
            @endif
            ><label class="label-check" for="check-{{ $services_product->id }}"></label>
          </td>
          <td class="td-name">
            @php
            $edit = 0;
            @endphp
            @can('update', $services_product)
            @php
            $edit = 1;
            @endphp
            @endcan
            @if($edit == 1)
            <a href="/admin/services_products/{{ $services_product->id }}/edit">
              @endif
              {{ $services_product->name }} (<a href="/admin/services?services_product_id%5B%5D={{ $services_product->id }}" title="Перейти на список услуг" class="filter_link light-text">{{ $services_product->services_articles->count() }}</a>)
              @if($edit == 1)
            </a>
            @endif
          </td>
          <td class="td-services_catrgory">{{ $services_product->services_category->name }}</td>
          <td class="td-description">{{ $services_product->description }}</td>

          {{-- Элементы управления --}}
          @include('includes.control.table-td', ['item' => $services_product])

          <td class="td-delete">
            @if ($services_product->system_item != 1)
            @can('delete', $services_product)
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
    <span class="pagination-title">Кол-во записей: {{ $services_products->count() }}</span>
    {{ $services_products->links() }}
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