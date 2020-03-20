@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $page_info->page_description }}" />

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
@include('products.processes.common.index.includes.title_processes', ['page_info' => $page_info, 'class' => $class])
@endsection

@section('content')

<div class="grid-x" id="pagination">
    <div class="small-6 cell pagination-head">
        {{ $items->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
    </div>
</div>
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
                    <th class="td-length">Параметры</th>
                    <th class="td-category">Категория</th>
                    <th class="td-manually">Артикул</th>
                    <th class="td-cost">Себестоимость</th>

                    @if($page_info->alias == 'services')
                        <th class="td-catalog">Прайсы</th>
                    @endif

                    <th class="mark"></th>
                    <th class="td-control"></th>
                    <th class="td-archive"></th>
                </tr>
            </thead>

            <tbody data-tbodyId="1" class="tbody-width">
                @if($items->isNotEmpty())

                @foreach($items as $item)
                <tr class="item @if($item->moderation == 1)no-moderation @endif @if($item->process->draft) draft @endif" id="{{ $entity }}-{{ $item->id }}" data-name="{{ $item->process->name }}"  data-entity="{{ $entity }}" data-id="{{ $item->id }}">

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
                            <img src="{{ getPhotoPathPlugEntity($item, 'small') }}" alt="{{ isset($item->process->photo_id) ? $item->process->name : 'Нет фото' }}">
                        </a>
                    </td>

                    <td class="td-name">
                        @can('update', $item)
                            <a href="/admin/{{ $entity }}/{{ $item->id }}/edit">{{ $item->process->name }} @if ($item->process->set == 1) (Набор) @endif</a>
                        @else
                            {{ $item->process->name }}
                        @endcan
                    </td>

                    <td class="td-unit">
                        {{ $item->process->unit->abbreviation }}
                    </td>

                    <td class="td-length">

                        @if($item->process->length != 0)
{{--                            <span class="tiny-text">Вес: </span><span title="ПРодолжительность указанная вручную">{{ num_format($item->process->weight_trans, 0) ?? '' }} {{ $item->article->unit_weight->abbreviation ?? $item->article->unit->abbreviation ?? '' }}</span>--}}
                            <br>
                        @endif

                        <span class="tiny-text">Состав: </span><span title="Кол-во сырья в составе">{{ $item->process->workflows->count() }}</span>
                    </td>

                    <td class="td-category">
                        <a href="/admin/{{ $entity }}?category_id%5B%5D={{ $item->category->id }}" class="filter_link" title="Фильтровать">{{ $item->category->name_with_parent }}</a>

                        <br>
                        @if($item->process->group->name != $item->process->name)
                            <a href="/admin/{{ $entity }}?processes_group_id%5B%5D={{ $item->process->processes_group_id }}" class="filter_link light-text">{{ $item->process->group->name }}</a>
                        @endif
                    </td>

                    <td class="td-manually" title="Процесс">
                        {{ $item->process->manually ?? '' }}
                    </td>

                    <td class="td-cost" title="Себестоимость">
{{--                        <span>{{ num_format($item->cost_unit, 2) ?? '' }}</span><br>--}}
{{--                        @if($item->portion_status)<span>За порцию: </span><span>{{ num_format($item->cost_portion, 2) ?? '' }}</span>@endif--}}
                    </td>

                    @if($page_info->alias == 'services')
                        <td class="td-catalog">
                            @php // dd($item); @endphp
                            @foreach($item->prices as $price)
                                <span class="catalog-name">{{ $price->catalog->name }}: </span>
                                <span>{{ $price->catalogs_item->name_with_parent }} </span>
                                <span  data-tooltip class="top" tabindex="2" title="Действует с {{ $price->created_at->format('d.m.Y') }}">{{ num_format($price->price, 2) }}

{{--                                    @if($item->process->unit_id == 32)--}}
{{--                                        @if($item->price_unit_id != 32)--}}
{{--                                            <span class='tiny-text'>за {{ $item->price_unit->abbreviation ?? '' }}</span>--}}
{{--                                        @endif--}}
{{--                                    @endif--}}

                                </span>
                                <br>
                            @endforeach
                        </td>
                    @endif

                    {{-- <td class="td-author">@if(isset($item->author->first_name)) {{ $item->author->name }} @endif</td> --}}

                    <td class="mark">

                        @if($item->moderation == 1)
                            <span class="hollow button warning mark-no-moderate tiny">На модерации</span>
                        @endif

                        @if($item->process->draft)
                            <span class="mark-draft">Черновик</span>
                        @endif

                    </td>

                    {{-- Элементы управления --}}
                    @include('includes.control.table_td', ['item' => $item, 'replicate' => true])

                    <td class="td-archive">
                        <a class="button tiny" href="/admin/draft_process/{{ $item->getTable() }}/{{ $item->id }}">Ч</a>
                        @if ($item->system != 1)
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
    @include('includes.modals.modal-archive')
    @include('includes.modals.modal-replicate')
@endsection

@push('scripts')
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

{{--@include('includes.scripts.inputs-mask')--}}
@include('products.common.create.scripts', [
    'entity' => $entity,
    'category_entity' => $category_entity,
    'group_entity' => 'processes_groups'
]
)
@endpush
