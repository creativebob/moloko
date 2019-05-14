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
{{ $rooms->isNotEmpty() ? num_format($rooms->total(), 0) : 0 }}
@endsection

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\Room::class, 'type' => 'menu'])
@endsection

@section('content')

{{-- Таблица --}}
<div class="grid-x">

    <div class="small-12 cell">
        <table class="content-table tablesorter" id="content" data-sticky-container data-entity-alias="rooms">
            <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
                    <th class="td-photo">Фото</th>
                    <th class="td-name">Название сырья</th>
                    <th class="td-category">Категория</th>
                    <th class="td-description">Описание</th>
                    <th class="td-cost">Себестоимость</th>
                    <th class="td-price">Цена</th>
                    <th class="td-catalog">Разделы на сайте:</th>
                    <th class="td-author">Автор</th>
                    <th class="td-control"></th>
                    <th class="td-archive"></th>
                </tr>
            </thead>
            <tbody data-tbodyId="1" class="tbody-width">
                @if($rooms->isNotEmpty())

                @foreach($rooms as $room)
                <tr class="item @if($room->moderation == 1)no-moderation @endif @if($room->article->draft) draft @endif" id="rooms-{{ $room->id }}" data-name="{{ $room->article->name }}">
                    <td class="td-drop">
                        <div class="sprite icon-drop"></div>
                    </td>
                    <td class="td-checkbox checkbox">
                        <input type="checkbox" class="table-check" name="room_id" id="check-{{ $room->id }}"
                        {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
                        @if(!empty($filter['booklist']['booklists']['default']))
                        {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
                        @if (in_array($room->id, $filter['booklist']['booklists']['default'])) checked
                        @endif
                        @endif
                        >
                        <label class="label-check" for="check-{{ $room->id }}"></label>
                    </td>
                    <td class="td-photo">
                        <a href="/admin/rooms/{{ $room->id }}/edit">
                            <img src="{{ getPhotoPath($room->article, 'small') }}" alt="{{ isset($room->article->photo_id) ? $room->article->name : 'Нет фото' }}">
                        </a>
                    </td>
                    <td class="td-name">
                        <a href="/admin/rooms/{{ $room->id }}/edit">{{ $room->article->name }} @if ($room->set_status == 1) (Набор) @endif</a>
                    </td>
                    <td class="td-category">
                        <a href="/admin/rooms?category_id%5B%5D={{ $room->category->id }}" class="filter_link" title="Фильтровать">{{ $room->category->name }}</a>

                        <br>
                        @if($room->article->group->name != $room->article->name)
                        <a href="/admin/rooms?rooms_product_id%5B%5D={{ $room->article->id }}" class="filter_link light-text">{{ $room->article->group->name }}</a>
                        @endif
                    </td>
                    <td class="td-description">{{ $room->article->description }}</td>
                    <td class="td-cost">{{ num_format($room->article->cost_default, 0) }}</td>
                    <td class="td-price">{{ num_format($room->article->price_default, 0) }}</td>
                    <td class="td-catalog"></td>

                    <td class="td-author">@if(isset($room->author->first_name)) {{ $room->author->name }} @endif</td>

                    {{-- Элементы управления --}}
                    @include('includes.control.table_td', ['item' => $room])

                    <td class="td-archive">
                        @if ($room->system_item != 1)
                        @can('delete', $room)
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
        <span class="pagination-title">Кол-во записей: {{ $rooms->count() }}</span>
        {{ $rooms->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
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
@include('tmc.create.scripts', ['entity' => 'rooms', 'category_entity' => 'rooms_categories'])

<script type="text/javascript">


</script>
@endsection
