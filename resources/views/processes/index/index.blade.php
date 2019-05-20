@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $page_info->page_description }}" />
{{-- Скрипты таблиц в шапке --}}
@include('includes.scripts.tablesorter-inhead')
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

{{-- @section('exel')
@include('includes.title-exel', ['entity' => $page_info->alias])
@endsection --}}

@section('content-count')
{{-- Количество элементов --}}
{{ $items->isNotEmpty() ? num_format($items->total(), 0) : 0 }}
@endsection

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => $class, 'type' => 'menu'])
@endsection

@section('content')

{{-- Таблица --}}
<div class="grid-x">

    <div class="small-12 cell">
        <table class="content-table tablesorter" id="content" data-sticky-container data-entity-alias="{{ $entity }}">
            <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
                    <th class="td-photo">Фото</th>
                    <th class="td-name">Название</th>
                    <th class="td-category">Категория</th>
                    <th class="td-description">Описание</th>
                    <th class="td-cost">Себестоимость</th>
                    <th class="td-price">Цена</th>
                    <th class="td-author">Автор</th>
                    <th class="td-control"></th>
                    <th class="td-archive"></th>
                </tr>
            </thead>
            <tbody data-tbodyId="1" class="tbody-width">
                @if($items->isNotEmpty())

                @foreach($items as $item)
                <tr class="item @if($item->moderation == 1)no-moderation @endif @if($item->process->draft) draft @endif" id="{{ $entity }}-{{ $item->id }}" data-name="{{ $item->process->name }}">
                    <td class="td-drop">
                        <div class="sprite icon-drop"></div>
                    </td>
                    <td class="td-checkbox checkbox">
                        <input type="checkbox" class="table-check" name="item_id" id="check-{{ $item->id }}"
                        {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
                        @if(!empty($filter['booklist']['booklists']['default']))
                        {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
                        @if (in_array($item->id, $filter['booklist']['booklists']['default'])) checked
                        @endif
                        @endif
                        >
                        <label class="label-check" for="check-{{ $item->id }}"></label>
                    </td>
                    <td class="td-photo">
                        <a href="/admin/raws/{{ $raw->id }}/edit">
                            <img src="{{ getPhotoPathPlugEntity($item, 'small') }}" alt="{{ isset($item->process->photo_id) ? $item->process->name : 'Нет фото' }}">
                        </a>
                    </td>
                    <td class="td-name">
                        <a href="/admin/{{ $entity }}/{{ $item->id }}/edit">{{ $item->process->name }} @if ($item->set_status == 1) (Набор) @endif</a>
                    </td>
                    <td class="td-category">
                        <a href="/admin/{{ $entity }}?category_id%5B%5D={{ $item->category->id }}" class="filter_link" title="Фильтровать">{{ $item->category->name }}</a>

                        <br>
                        @if($item->process->group->name != $item->process->name)
                        <a href="/admin/{{ $entity }}?product_id%5B%5D={{ $item->process->id }}" class="filter_link light-text">{{ $item->process->group->name }}</a>
                        @endif
                    </td>
                    <td class="td-description">{{ $item->process->description }}</td>
                    <td class="td-cost">{{ num_format($item->process->cost_default, 0) }}</td>
                    <td class="td-price">{{ num_format($item->process->price_default, 0) }}</td>
                    <td class="td-catalog"></td>

                    <td class="td-author">@if(isset($item->author->first_name)) {{ $item->author->name }} @endif</td>

                    {{-- Элементы управления --}}
                    @include('includes.control.table_td', ['item' => $item])

                    <td class="td-archive">
                        @if ($item->system_item != 1)
                        @can('delete', $item)
                        <a class="icon-delete sprite" data-open="item-archive"></a>
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
        <span class="pagination-title">Кол-во записей: {{ $items->count() }}</span>
        {{ $items->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
    </div>
</div>
@endsection


@section('modals')
<section id="modal"></section>

{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-archive')

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
@include('includes.scripts.modal-archive-script')

@include('includes.scripts.inputs-mask')
@include('processes.create.scripts', ['entity' => $entity, 'category_entity' => $category_entity])

@endsection
