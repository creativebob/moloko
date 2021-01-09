@extends('layouts.app')

@section('inhead')
    <meta name="description" content="{{ $pageInfo->description }}"/>
@endsection

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('content-count')
    {{-- Количество элементов --}}
    {{ num_format($catalogsServices->total(), 0) }}
@endsection

@section('title-content')

    {{-- Таблица --}}
    @include('includes.title-content', ['pageInfo' => $pageInfo, 'class' => App\CatalogsService::class, 'type' => 'table'])
@endsection

@section('content')
    {{-- Таблица --}}
    <div class="grid-x">
        <div class="small-12 cell">

            <table class="content-table tablesorter" id="content" data-sticky-container
                   data-entity-alias="catalogs_services">

                <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2"
                       data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th">
                        <input type="checkbox" class="table-check-all" name="" id="check-all">
                        <label class="label-check" for="check-all"></label>
                    </th>
                    <th class="td-name">Название</th>
                    <th class="td-alias">Алиас</th>
                    <th class="td-description">Описание</th>

                    @can('index', App\CatalogsServicesItem::class)
                        <th class="td-tree">Дерево</th>
                    @endcan

                    @can('index', App\PricesService::class)
                        <th class="td-services">Услуги</th>
                    @endcan

                    <th class="td-author">Автор</th>
                    <th class="td-control"></th>
                    <th class="td-delete"></th>
                </tr>
                </thead>

                <tbody data-tbodyId="1" class="tbody-width">

                @forelse($catalogsServices as $catalogsService)

                    <tr class="item @if($catalogsService->moderation == 1)no-moderation @endif"
                        id="catalogs_services-{{ $catalogsService->id }}" data-name="{{ $catalogsService->name }}">
                        <td class="td-drop">
                            <div class="sprite icon-drop"></div>
                        </td>
                        <td class="td-checkbox checkbox">
                            <input type="checkbox" class="table-check" name="" id="check-{{ $catalogsService->id }}">
                            <label class="label-check" for="check-{{ $catalogsService->id }}"></label>
                        </td>
                        <td class="td-name">

                            @can('update', $catalogsService)
                                {{ link_to_route('prices_services.index', $catalogsService->name, $catalogsService->id, $attributes = []) }}
                                <span class="tiny-text">({{ $catalogsService->price_services->where('archive', 0)->where('service.article.draft', 0)->count() }})</span>

                            @else
                                {{ $page->name }}
                            @endcan


                        </td>
                        <td class="td-alias">{{ $catalogsService->alias }}</td>
                        <td class="td-description">{{ $catalogsService->description }}</td>
                        @can('index', App\CatalogsServicesItem::class)
                            <td class="td-tree">
                                <a href="{{ route('catalogs_services_items.index', $catalogsService->id) }}"
                                   class="icon-category sprite"></a>
                            </td>
                        @endcan

                        @can('index', App\PricesService::class)
                            <td class="td-services">
                                <a href="{{ route($pageInfo->alias.'.edit', $catalogsService->id) }}"
                                   class="button tiny">Настройка</a>
                            </td>
                        @endcan
                        <td class="td-author">{{ $catalogsService->author->name}}</td>

                        {{-- Элементы управления --}}
                        @include('includes.control.table-td', ['item' => $catalogsService])

                        <td class="td-delete">
                            @can('delete', $catalogsService)
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
            <span class="pagination-title">Кол-во записей: {{ $catalogsServices->count() }}</span>
            {{ $catalogsServices->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
        </div>
    </div>
@endsection

@section('modals')
    {{-- Модалка удаления с refresh --}}
    @include('includes.modals.modal-delete')
@endsection

@push('scripts')
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
@endpush
