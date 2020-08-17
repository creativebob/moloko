@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $pageInfo->description }}" />

@endsection

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('content-count')
{{-- Количество элементов --}}
{{ $articles_groups->isNotEmpty() ? num_format($articles_groups->total(), 0) : 0 }}
@endsection

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['pageInfo' => $pageInfo, 'class' => App\ArticlesGroup::class, 'type' => 'table'])
@endsection

@section('content')
{{-- Таблица --}}
<div class="grid-x">
    <div class="small-12 cell">

        <table class="content-table tablesorter" id="content" data-sticky-container data-entity-alias="articles_groups">

            <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
                    <th class="td-name" data-serversort="name">Название группы артикулов</th>
                    <th class="td-description">Описание</th>
                    <th class="td-control"></th>
                    <th class="td-delete"></th>
                </tr>
            </thead>

            <tbody data-tbodyId="1" class="tbody-width">

                @if(isset($articles_groups) && $articles_groups->isNotEmpty())
                @foreach($articles_groups as $articles_group)

                <tr class="item @if($articles_group->moderation == 1)no-moderation @endif" id="articles_groups-{{ $articles_group->id }}" data-name="{{ $articles_group->name }}">
                    <td class="td-drop">
                        <div class="sprite icon-drop"></div>
                    </td>
                    <td class="td-checkbox checkbox">
                        <input type="checkbox" class="table-check" name="articles_group_id" id="check-{{ $articles_group->id }}"

                        {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
                        @if(!empty($filter['booklist']['booklists']['default']))
                        {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
                        @if (in_array($articles_group->id, $filter['booklist']['booklists']['default'])) checked
                        @endif
                        @endif
                        ><label class="label-check" for="check-{{ $articles_group->id }}"></label>
                    </td>
                    <td class="td-name">

                        @can('update', $articles_group)
                        {{ link_to_route('articles_groups.edit', $articles_group->name, $articles_group->id) }}
                        @endcan

                        @cannot('update', $articles_group)
                        {{ $articles_group->name }}
                        @endcannot

                        {{-- %5B%5D --}}
                        ({{ link_to_route('goods.index', $articles_group->articles_count, $parameters = ['articles_group_id%5B%5D' => $articles_group->id], $attributes = ['class' => 'filter_link light-text', 'title' => 'Перейти на список артикулов']) }})

                    </td>
                    <td class="td-description">{{ $articles_group->description }}</td>

                    {{-- Элементы управления --}}
                    @include('includes.control.table_td', ['item' => $articles_group])

                    <td class="td-delete">

                        @include('includes.control.item_delete_table', ['item' => $articles_group])

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
        <span class="pagination-title">Кол-во записей: {{ $articles_groups->count() }}</span>
        {{ $articles_groups->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
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
