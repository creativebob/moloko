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
{{ $raws_products->isNotEmpty() ? num_format($raws_products->total(), 0) : 0 }}
@endsection

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\RawsProduct::class, 'type' => 'table'])
@endsection

@section('content')
{{-- Таблица --}}
<div class="grid-x">
    <div class="small-12 cell">

        <table class="content-table tablesorter" id="content" data-sticky-container data-entity-alias="raws_products">

            <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
                    <th class="td-name" data-serversort="name">Название группы сырья</th>
                    <th class="td-raws_category">Категория</th>
                    <th class="td-description">Описание</th>
                    <th class="td-control"></th>
                    <th class="td-delete"></th>
                </tr>
            </thead>

            <tbody data-tbodyId="1" class="tbody-width">

                @if(isset($raws_products) && $raws_products->isNotEmpty())
                @foreach($raws_products as $raws_product)

                <tr class="item @if($raws_product->moderation == 1)no-moderation @endif" id="raws_products-{{ $raws_product->id }}" data-name="{{ $raws_product->name }}">
                    <td class="td-drop">
                        <div class="sprite icon-drop"></div>
                    </td>
                    <td class="td-checkbox checkbox">
                        <input type="checkbox" class="table-check" name="raws_product_id" id="check-{{ $raws_product->id }}"

                        {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
                        @if(!empty($filter['booklist']['booklists']['default']))
                        {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
                        @if (in_array($raws_product->id, $filter['booklist']['booklists']['default'])) checked
                        @endif
                        @endif
                        ><label class="label-check" for="check-{{ $raws_product->id }}"></label>
                    </td>
                    <td class="td-name">

                        @can('update', $raws_product)
                        {{ link_to_route('raws_products.edit', $raws_product->name, $parameters = ['id' => $raws_product->id], $attributes = []) }}
                        @endcan

                        @cannot('update', $raws_product)
                        {{ $raws_product->name }}
                        @endcannot

                        ({{ link_to_route('raws.index', $raws_product->raws_articles_count, $parameters = ['raws_product_id' => $raws_product->id], $attributes = ['class' => 'filter_link light-text', 'title' => 'Перейти на список товаров']) }}) {{ ($raws_product->set_status == 'set') ? '(Набор)' : '' }}

                  </td>
                  <td class="td-raws_category">{{ $raws_product->raws_category->name }}</td>
                  <td class="td-description">{{ $raws_product->description }}</td>

                  {{-- Элементы управления --}}
                    @include('includes.control.table_td', ['item' => $raws_product])

                  <td class="td-delete">

                    @include('includes.control.item_delete_table', ['item' => $raws_product])

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
    <span class="pagination-title">Кол-во записей: {{ $raws_products->count() }}</span>
    {{ $raws_products->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
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
@include('includes.scripts.sortable-table-script')

{{-- Скрипт отображения на сайте --}}
@include('includes.scripts.ajax-display')

{{-- Скрипт системной записи --}}
@include('includes.scripts.ajax-system')

{{-- Скрипт чекбоксов --}}
@include('includes.scripts.checkbox-control')

{{-- Скрипт модалки удаления --}}
@include('includes.scripts.modal-delete-script')
@endsection