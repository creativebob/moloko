@extends('layouts.app')
 
@section('inhead')
{{-- Скрипты таблиц в шапке --}}
  @include('includes.scripts.table-inhead')
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\Product::class, 'type' => 'table'])
@endsection
 
@section('content')

{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="table-content tablesorter" id="content" data-sticky-container data-entity-alias="albums">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"><div class="sprite icon-drop"></div></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-photo">Фото</th>
          <th class="td-name">Название товара</th>
          <th class="td-edit">Редактировать</th>
          <th class="td-category">Категория</th>
          <th class="td-company-id">Компания</th>
          <th class="td-author">Автор</th>
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
      @if(!empty($products))

        @foreach($products as $product)
        <tr class="item @if($product->moderation == 1)no-moderation @endif" id="products-{{ $product->id }}" data-name="{{ $product->name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox">

            <input type="checkbox" class="table-check" name="album_id" id="check-{{ $product->id }}"><label class="label-check" for="check-{{ $product->id }}"></label></td>
          <td>
            <a href="/prodicts/{{ $product->id }}/edit">
              @if (isset($product->avatar))

              @else
              нет фото
              @endif
            </a>
          </td>
          <td class="td-name"><a href="/products/{{ $product->id }}/edit">{{ $product->name }}</a></td>
          <td class="td-edit"><a class="tiny button" href="/products/{{ $product->id }}/edit">Редактировать</a></td>
          <td class="td-category">{{ $product->products_category->name }}</td>
          <td class="td-company-id">@if(!empty($product->company->company_name)) {{ $product->company->company_name }} @else @if($product->system_item == null) Шаблон @else Системная @endif @endif</td>
          <td class="td-author">@if(isset($product->author->first_name)) {{ $product->author->first_name . ' ' . $product->author->second_name }} @endif</td>

          <td class="td-delete">
            @if ($product->system_item != 1)
              @can('delete', $product)
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
    <span class="pagination-title">Кол-во записей: {{ $products->count() }}</span>
    {{ $products->links() }}
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
  @include('includes.scripts.table-scripts')

  {{-- Скрипт модалки удаления --}}
  @include('includes.scripts.modal-delete-script')
  @include('includes.scripts.delete-ajax-script')
  @include('includes.scripts.table-sort')
@endsection
