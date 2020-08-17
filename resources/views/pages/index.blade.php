@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $pageInfo->description }}" />

@endsection

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('site-section-index', $site, $pageInfo))

@section('title-content')
{{-- Таблица --}}
{{-- Заголовок и фильтры --}}
<div data-sticky-container id="head-content">
    <div class="sticky sticky-topbar" id="head-sticky" data-sticky data-margin-top="2.4" data-sticky-on="small" data-top-anchor="head-content:top">
        <div class="top-bar head-content">
            <div class="top-bar-left">
                <h2 class="header-content">{{ $pageInfo->title }}
                    <span class="content-count" title="Общее количество">
                        {{ $pages->isNotEmpty() ? num_format($pages->total(), 0) : 0 }}
                    </span>
                </h2>

                @can('create', App\Page::class)

                {{ link_to_route($pageInfo->alias.'.create', '', $parameters = ['site_id' => $site_id], $attributes = ['class' => 'icon-add sprite']) }}

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

                        @include($pageInfo->alias.'.filters')

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

        <table class="content-table tablesorter" id="content" data-sticky-container data-entity-alias="pages">

            <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th">
                        <input type="checkbox" class="table-check-all" name="" id="check-all">
                        <label class="label-check" for="check-all"></label>
                    </th>
                    <th class="td-name">Название страницы</th>
                    <th class="td-title">Заголовок</th>
                    <th class="td-alias">Алиас</th>
                    <th class="td-description">Описание</th>
                    <th class="td-author">Автор</th>
                    <th class="td-control"></th>
                    <th class="td-delete"></th>
                </tr>
            </thead>

            <tbody data-tbodyId="1" class="tbody-width">

                @if($pages->isNotEmpty())
                @foreach($pages as $page)

                <tr class="item @if($page->moderation == 1)no-moderation @endif" id="pages-{{ $page->id }}" data-name="{{ $page->name }}">
                    <td class="td-drop">
                        <div class="sprite icon-drop"></div>
                    </td>
                    <td class="td-checkbox checkbox">
                        <input type="checkbox" class="table-check" name="" id="check-{{ $page->id }}">
                        <label class="label-check" for="check-{{ $page->id }}"></label>
                    </td>
                    <td class="td-name">

                        @can('update', $page)
                        <a href="/admin/sites/{{ $site_id }}/pages/{{ $page->id }}/edit">{{ $page->name }}</a>
                        @endcan

                        @cannot('update', $page)
                        {{ $page->name }}
                        @endcannot

                    </td>
                    <td class="td-title">{{ $page->title }}</td>
                    <td class="td-alias">{{ $page->alias }}</td>
                    <td class="td-description">{{ $page->description }}</td>
                    <td class="td-author">{{ $page->author->name}}</td>

                    {{-- Элементы управления --}}
                    @include('includes.control.table-td', ['item' => $page])

                    <td class="td-delete">
                        @if ($page->system != 1)
                        @can('delete', $page)
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
    <span class="pagination-title">Кол-во записей: {{ $pages->count() }}</span>
    {{ $pages->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
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

<script type="application/javascript">

    $(function() {

        // Берем алиас сайта
        var site_id = '{{ $site_id }}';

        // Мягкое удаление с refresh
        $(document).on('click', '[data-open="item-delete"]', function() {

            // находим описание сущности, id и название удаляемого элемента в родителе
            var parent = $(this).closest('.item');
            var type = parent.attr('id').split('-')[0];
            var id = parent.attr('id').split('-')[1];
            var name = parent.data('name');
            $('.title-delete').text(name);
            $('.delete-button').attr('id', 'del-' + type + '-' + id);
            $('#form-item-del').attr('action', '/admin/sites/'+ site_id + '/' + type + '/' + id);
        });
    });

</script>
@endsection
