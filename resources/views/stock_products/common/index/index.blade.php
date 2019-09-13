@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $page_info->description }}" />

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
                    <th class="td-unit">Ед. измерения</th>
                    <th class="td-stock-count">Кол-во</th>
                    <th class="td-weight">Параметры</th>
                    <th class="td-portion">Порция</th>  
                    <th class="td-category">Категория</th>
                    {{-- <th class="td-description">Описание</th> --}}
                    <th class="td-cost">Себестоимость</th>
                    <th class="td-stock">Склад</th>

                    <th class="mark"></th>
                </tr>
            </thead>
            <tbody data-tbodyId="1" class="tbody-width">
                @if($items->isNotEmpty())

                @foreach($items as $item)
                <tr class="item" id="{{ $entity }}-{{ $item->id }}" data-name="{{ $item->goods->article->name }}">
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
                        <a href="/admin/{{ $entity }}/{{ $item->id }}/edit">
                            <img src="{{ getPhotoPathPlugEntity($item->goods, 'small') }}" alt="{{ isset($item->goods->article->photo_id) ? $item->goods->article->name : 'Нет фото' }}">
                        </a>
                    </td>
                    <td class="td-name">
                        <a href="/admin/{{ $entity }}/{{ $item->id }}/edit">{{ $item->goods->article->name }} @if ($item->goods->article->kit == 1)</a><span class="tiny-text"> - Набор: @endif
                        @if(isset($item->goods->article->goods))
                            @if($item->goods->article->goods->count() != 0)
                                {{ $item->goods->article->goods->count() }}</span>
                            @endif
                        @endif
                        
                        <br><span class="tiny-text">{{ $item->goods->article->manufacturer->name ?? $item->manufacturer->name ?? '' }}</span>
                    </td>
                    <td class="td-unit">
                        {{ $item->goods->article->unit->abbreviation }}
                    </td>
                    <td class="td-stock-count">
                        {{ $item->count ?? 0 }}
                    </td>
                    <td class="td-weight">
                        @if(isset($item->goods->article->weight))
                            {{ $item->goods->article->weight }} {{ $item->goods->article->unit_weight->abbreviation }}
                            <br>
                        @endif

                        @if(isset($item->goods->article->volume))
                            {{ $item->goods->article->volume }} {{ $item->goods->article->unit_volume->abbreviation }}
                        @endif
                    </td>
                    <td class="td-portion">
                        @if($item->goods->article->portion_status == 1)
                            <span>{{ $item->goods->article->portion_abbreviation }}</span><br>
                            <span>{{ $item->goods->article->portion_count * $item->goods->article->unit->ratio }} {{ $item->goods->article->unit->abbreviation }}</span>
                            
                        @endif
                    </td>
                    <td class="td-category">
                        <a href="/admin/{{ $entity }}?category_id%5B%5D={{ $item->goods->category->id }}" class="filter_link" title="Фильтровать">{{ $item->goods->category->name }}</a>

                        <br>
                        @if($item->goods->article->group->name != $item->goods->article->name)
                        <a href="/admin/{{ $entity }}?articles_group_id%5B%5D={{ $item->goods->article->articles_group_id }}" class="filter_link light-text">{{ $item->goods->article->group->name }}</a>
                        @endif
                    </td>

                    {{-- <td class="td-description">{{ $item->goods->article->description }}</td> --}}

                    <td class="td-cost">
                        {{-- Средняя: {{ num_format($item->goods->article->cost_default, 0) }}<br>
                        Последняя: {{ num_format($item->goods->article->cost_default, 0) }} --}}

                        {{ num_format($item->goods->article->cost_default, 0) }}
                    </td>
                    <td class="td-stock">
                        {{ $item->stock ?? 'Не определен' }}
                    </td>
                    <td class="mark">

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

{{-- @include('includes.scripts.inputs-mask') --}}


@endsection
