@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $page_info->description }}" />
{{-- Скрипты таблиц в шапке --}}
@include('includes.scripts.tablesorter-inhead')
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('content-count')
{{-- Количество элементов --}}
{{ $navigations->isNotEmpty() ? num_format($navigations->total(), 0) : 0 }}
@endsection

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\Navigation::class, 'type' => 'table'])
@endsection

@section('content')
{{-- Таблица --}}
<div class="grid-x">
    <div class="small-12 cell">

        <table class="content-table tablesorter" id="content" data-sticky-container data-entity-alias="navigations">

            <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
                    <th class="td-name" data-serversort="name">Название группы товаров</th>
                    <th class="td-navigations_category">Категория</th>
                    <th class="td-tree">Дерево</th>
                    <th class="td-control"></th>
                    <th class="td-delete"></th>
                </tr>
            </thead>

            <tbody data-tbodyId="1" class="tbody-width">

                @if(isset($navigations) && $navigations->isNotEmpty())
                @foreach($navigations as $navigation)

                <tr class="item @if($navigation->moderation == 1)no-moderation @endif" id="navigations-{{ $navigation->id }}" data-name="{{ $navigation->name }}">
                    <td class="td-drop">
                        <div class="sprite icon-drop"></div>
                    </td>
                    <td class="td-checkbox checkbox">
                        <input type="checkbox" class="table-check" name="navigation_id" id="check-{{ $navigation->id }}"

                        {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
                        @if(!empty($filter['booklist']['booklists']['default']))
                        {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
                        @if (in_array($navigation->id, $filter['booklist']['booklists']['default'])) checked
                        @endif
                        @endif
                        ><label class="label-check" for="check-{{ $navigation->id }}"></label>
                    </td>
                    <td class="td-name">

                        @can('update', $navigation)
                        {{ link_to_route('navigations.edit', $navigation->name, $parameters = ['id' => $navigation->id], $attributes = []) }}
                        @endcan

                        @cannot('update', $navigation)
                        {{ $navigation->name }}
                        @endcannot

                    </td>

                    <td class="td-navigations_category">{{ $navigation->category->name }}</td>

                    <td class="td-tree">
                        <a href="/admin/navigations/{{ $navigation->id }}/menus" class="button">Дерево</a>
                    </td>

                    {{-- Элементы управления --}}
                    @include('includes.control.table_td', ['item' => $navigation])

                    <td class="td-delete">

                        @include('includes.control.item_delete_table', ['item' => $navigation])

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
        <span class="pagination-title">Кол-во записей: {{ $navigations->count() }}</span>
        {{ $navigations->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
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