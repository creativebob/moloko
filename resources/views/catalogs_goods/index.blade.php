@extends('layouts.app')

@section('inhead')
	<meta name="description" content="{{ $page_info->page_description }}" />


@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('content-count')

	{{-- Количество элементов --}}
	@if(!empty($catalogs_goods))
		{{ num_format($catalogs_goods->total(), 0) }}
	@endif
@endsection

@section('title-content')

	{{-- Таблица --}}
	@include('includes.title-content', ['page_info' => $page_info, 'class' => App\CatalogsGoods::class, 'type' => 'table'])
@endsection

@section('content')
	{{-- Таблица --}}
	<div class="grid-x">
	    <div class="small-12 cell">

	        <table class="content-table tablesorter" id="content" data-sticky-container data-entity-alias="catalogs_goods">

	            <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
	                <tr id="thead-content">
	                    <th class="td-drop"></th>
	                    <th class="td-checkbox checkbox-th">
	                        <input type="checkbox" class="table-check-all" name="" id="check-all">
	                        <label class="label-check" for="check-all"></label>
	                    </th>
	                    <th class="td-name">Название</th>
	                    <th class="td-alias">Алиас</th>
	                    <th class="td-description">Описание</th>

	                    @can('index', App\CatalogsGoodsItem::class)
	                        <th class="td-tree">Дерево</th>
	                    @endcan

	                    @can('index', App\PricesGoods::class)
	                        <th class="td-goods"></th>
	                    @endcan

	                    <th class="td-author">Автор</th>
	                    <th class="td-control"></th>
	                    <th class="td-delete"></th>
	                </tr>
	            </thead>

	            <tbody data-tbodyId="1" class="tbody-width">

	                @forelse($catalogs_goods as $cur_catalogs_goods)

	                <tr class="item @if($cur_catalogs_goods->moderation == 1)no-moderation @endif" id="catalogs_goods-{{ $cur_catalogs_goods->id }}" data-name="{{ $cur_catalogs_goods->name }}">
	                    <td class="td-drop">
	                        <div class="sprite icon-drop"></div>
	                    </td>
	                    <td class="td-checkbox checkbox">
	                        <input type="checkbox" class="table-check" name="" id="check-{{ $cur_catalogs_goods->id }}">
	                        <label class="label-check" for="check-{{ $cur_catalogs_goods->id }}"></label>
	                    </td>
	                    <td class="td-name">

	                        @can('update', $cur_catalogs_goods)
	                        {{ link_to_route('prices_goods.index', $cur_catalogs_goods->name, $parameters = ['id' => $cur_catalogs_goods->id], $attributes = []) }} <span class="tiny-text">({{ $cur_catalogs_goods->price_goods->where('archive', 0)->where('goods.article.draft', 0)->count() }})</span>

	                            @else
	                            {{ $page->name }}
	                        @endcan


	                    </td>
	                    <td class="td-alias">{{ $cur_catalogs_goods->alias }}</td>
	                    <td class="td-description">{{ $cur_catalogs_goods->description }}</td>
	                    @can('index', App\CatalogsGoodsItem::class)
	                        <td class="td-tree">
	                            {{ link_to_route('catalogs_goods_items.index', '', ['catalog_id' => $cur_catalogs_goods->id], ['class' => 'icon-category sprite']) }}
	                        </td>
	                    @endcan

	                    @can('index', App\PricesGoods::class)
	                        <td class="td-services">

	                            {{ link_to_route($page_info->alias.'.edit', 'Настройка', ['catalog_id' => $cur_catalogs_goods->id], ['class' => 'button tiny']) }}
	                        </td>
	                    @endcan
	                    <td class="td-author">{{ $cur_catalogs_goods->author->name}}</td>

	                    {{-- Элементы управления --}}
	                    @include('includes.control.table-td', ['item' => $cur_catalogs_goods])

	                    <td class="td-delete">
	                        @can('delete', $cur_catalogs_goods)
	                        <a class="icon-delete sprite" data-open="item-delete"></a>
	                        @endcan
	                    </td>
	                </tr>
	                @empty
	                @endforelse

	            </tbody>
	        </table>
	    </div>
	</div>

	{{-- Pagination --}}
	<div class="grid-x" id="pagination">
		<div class="small-6 cell pagination-head">
			<span class="pagination-title">Кол-во записей: {{ $catalogs_goods->count() }}</span>
			{{ $catalogs_goods->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
		</div>
	</div>
@endsection

@section('modals')
	{{-- Модалка удаления с refresh --}}
	@include('includes.modals.modal-delete')
@endsection

@section('scripts')

{{-- Скрипт сортировки --}}
@include('includes.scripts.sortable-table-script')

{{-- Скрипт чекбоксов, сортировки и перетаскивания для таблицы --}}
@include('includes.scripts.tablesorter-script')

{{-- Скрипт отображения на сайте --}}
@include('includes.scripts.ajax-display')

{{-- Скрипт системной записи --}}
@include('includes.scripts.ajax-system')

{{-- Скрипт модалки удаления --}}
@include('includes.scripts.modal-delete-script')

@endsection
