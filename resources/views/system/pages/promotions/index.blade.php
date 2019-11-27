@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $page_info->page_description }}" />

@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('content-count')
{{-- Количество элементов --}}
{{ $promotions->isNotEmpty() ? num_format($promotions->total(), 0) : 0 }}
@endsection

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\Promotion::class, 'type' => 'table'])
@endsection

@section('content')

{{-- Таблица --}}
<div class="grid-x">
    <div class="small-12 cell">

        <table class="content-table tablesorter" id="content" data-sticky-container data-entity-alias="promotions">

            <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
                    <th class="td-photo">Фото</th>
                    <th class="td-name">Название</th>
                    <th class="td-description">Описание</th>
                    <th class="td-begin-date">Дата начала</th>
                    <th class="td-end-date">Дата окончания</th>
                    <th class="td-link">Ссылка</th>
                    <th class="td-author">Автор</th>
                    <th class="td-control"></th>
                    <th class="td-delete"></th>
                </tr>
            </thead>

            <tbody data-tbodyId="1" class="tbody-width">

                @if(isset($promotions) && $promotions->isNotEmpty())
                @foreach($promotions as $promotion)

                <tr class="item @if($promotion->moderation == 1)no-moderation @endif" id="promotions-{{ $promotion->id }}" data-name="{{ $promotion->name }}">

                    <td class="td-drop">
                        <div class="sprite icon-drop"></div>
                    </td>
                    <td class="td-checkbox checkbox">
                        <input type="checkbox" class="table-check" name="" id="check-{{ $promotion->id }}"

                        {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
                        @if(!empty($filter['booklist']['booklists']['default']))
                        {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
                        @if (in_array($promotion->id, $filter['booklist']['booklists']['default'])) checked
                        @endif
                        @endif

                        >
                        <label class="label-check" for="check-{{ $promotion->id }}"></label>
                    </td>
                    <td class="td-photo tiny">
                        <img src="{{ $promotion->tiny->path }}" alt="">
                    </td>
                    <td class="td-name">
                        @can('update', $promotion)
                            <a href="{{ route('promotions.edit', $promotion->id) }}">{{ $promotion->name }}</a>
                            @else
                            {{ $promotion->name }}
                        @endcan
                    </td>
                    <td class="td-description">
                        {{ $promotion->description }}
                    </td>
                    <td class="td-begin-date">
                        {{ $promotion->begin_date->format('d.m.Y') }}
                    </td>
                    <td class="td-begin-date">
                        {{ $promotion->end_date->format('d.m.Y') }}
                    </td>
                    <td class="td-link">
                        <a href="{{ $promotion->link }}">{{ $promotion->link }}</a>
                    </td>
                    <td class="td-author">

                        @if(isset($promotion->author->first_name))
                        {{ $promotion->author->first_name . ' ' . $promotion->author->second_name }}
                        @endif

                    </td>

                    {{-- Элементы управления --}}
                    @include('includes.control.table_td', ['item' => $promotion])

                    <td class="td-delete">

                       @include('includes.control.item_delete_table', ['item' => $promotion])

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
        <span class="pagination-title">Кол-во записей: {{ $promotions->count() }}</span>
        {{ $promotions->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
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