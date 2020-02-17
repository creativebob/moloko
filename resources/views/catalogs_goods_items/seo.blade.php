@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $page_info->page_description }}" />

@endsection

@section('title', $page_info->name)

@section('content-count')
{{-- Количество элементов --}}
  @if(!empty($catalogs_goods_items))
    {{ num_format($catalogs_goods_items->count(), 0) }}
  @endif
@endsection

@section('title-content')
{{-- Таблица --}}
@php $page_info->title = 'SEO Description: ' . $catalog_goods->name; @endphp
@include('includes.title-content', ['page_info' => $page_info, 'class' => null, 'type' => 'table'])
@endsection

@section('content')
{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="content-table tablesorter" id="content" data-sticky-container data-entity-alias="catalogs_goods_items">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-name" data-serversort="name">Название пункта</th>
          <th class="td-seo-description">Description</th>
          <th class="td-keyword">Ключевые слова</th>
      </tr>
  </thead>
  <tbody data-tbodyId="1" class="tbody-width">
    @if(!empty($catalogs_goods_items))
    @foreach($catalogs_goods_items as $catalogs_goods_item)
    <tr class="item @if($user->catalogs_goods_item_id == $catalogs_goods_item->id)active @endif  @if($catalogs_goods_item->moderation == 1)no-moderation @endif" id="catalogs_goods_items-{{ $catalogs_goods_item->id }}" data-name="{{ $catalogs_goods_item->name }}">
      <td class="td-drop"><div class="sprite icon-drop"></div></td>
      <td class="td-checkbox checkbox">
        <input type="checkbox" class="table-check" name="catalogs_goods_item_id" id="check-{{ $catalogs_goods_item->id }}"

        {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
        @if(!empty($filter['booklist']['booklists']['default']))
        {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
        @if (in_array($catalogs_goods_item->id, $filter['booklist']['booklists']['default'])) checked
        @endif
        @endif
        ><label class="label-check" for="check-{{ $catalogs_goods_item->id }}"></label>
    </td>
    <td class="td-name">
        <a href="/admin/catalogs_goods/{{$catalog_goods->id}}/catalogs_goods_items/{{ $catalogs_goods_item->id }}/edit">
          {{ $catalogs_goods_item->name }}
      </a>
    </td>
  <td class="td-seo-description">{{ $catalogs_goods_item->seo_description }} </td>
  <td></td>

  </tr>
  @endforeach
@endif
</tbody>
</table>
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