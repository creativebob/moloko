@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $page_info->description }}" />
{{-- Скрипты таблиц в шапке --}}
@include('includes.scripts.tablesorter-inhead')
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('title-content')
{{-- Таблица --}}
{{-- Заголовок и фильтры --}}
<div data-sticky-container id="head-content">
    <div class="sticky sticky-topbar" id="head-sticky" data-sticky data-margin-top="2.4" data-sticky-on="small" data-top-anchor="head-content:top">
        <div class="top-bar head-content">
            <div class="top-bar-left">
                <h2 class="header-content">{{ $page_info->title }}
                    <span class="content-count" title="Общее количество">
                        {{ $catalogs_goods->isNotEmpty() ? num_format($catalogs_goods->total(), 0) : 0 }}
                    </span>
                </h2>

                @can('create', App\CatalogsGoods::class)

                {{ link_to_route($page_info->alias.'.create', '', $parameters = [], $attributes = ['class' => 'icon-add sprite']) }}

                @endcan
            </div>
            <div class="top-bar-right">
                @if (isset($filter))
                <a class="icon-filter sprite @if ($filter['status'] == 'active') filtration-active @endif"></a>
                @endif

                <input class="search-field" type="search" id="search_field" name="search_field" placeholder="Поиск" />

                <button type="button" class="icon-search sprite button"></button>
            </div>

        </div>

        <div id="port-result-search">
        </div>
        {{-- Подключаем стандартный ПОИСК --}}
        @include('includes.scripts.search-script')

        {{-- Блок фильтров --}}
        @if (isset($filter))

        {{-- Подключаем класс Checkboxer --}}
        @include('includes.scripts.class.checkboxer')

        <div class="grid-x">
            <div class="small-12 cell filters fieldset-filters" id="filters">
                <div class="grid-padding-x">
                    <div class="small-12 cell text-right">
                        {{ link_to(Request::url() . '?filter=disable', 'Сбросить', ['class' => 'small-link']) }}
                    </div>
                </div>
                <div class="grid-padding-x">
                    <div class="small-12 cell">
                        {{ Form::open(['url' => Request::url(), 'data-abide', 'novalidate', 'name'=>'filter', 'method'=>'GET', 'id' => 'filter-form', 'class' => 'grid-x grid-padding-x inputs']) }}

                        @include($page_info->alias.'.filters')

                        <div class="small-12 cell text-center">
                            {{ Form::submit('Фильтрация', ['class'=>'button']) }}
                            <input hidden name="filter" value="active">
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
                <div class="grid-x">
                    <a class="small-12 cell text-center filter-close">
                        <button type="button" class="icon-moveup sprite"></button>
                    </a>
                </div>
            </div>
        </div>

        @endif
    </div>
</div>
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
                        <th class="td-goods">Товары</th>
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
                        {{ link_to_route($page_info->alias.'.edit', $cur_catalogs_goods->name, $parameters = ['id' => $cur_catalogs_goods->id], $attributes = []) }}
                            @else
                            {{ $page->name }}
                        @endcan


                    </td>
                    <td class="td-alias">{{ $cur_catalogs_goods->alias }}</td>
                    <td class="td-description">{{ $cur_catalogs_goods->description }}</td>
                    @can('index', App\CatalogsGoodsItem::class)
                        <td class="td-tree">
                            {{ link_to_route('catalogs_goods_items.index', 'Дерево', ['catalog_id' => $cur_catalogs_goods->id], ['class' => 'button']) }}
                        </td>
                    @endcan

                    @can('index', App\PricesGoods::class)
                        <td class="td-services">
                            {{ link_to_route('prices_goods.index', 'Товары', ['catalog_id' => $cur_catalogs_goods->id], ['class' => 'button']) }}
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
