@extends('layouts.app')

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('content-count')
    {{ $labels->isNotEmpty() ? num_format($labels->total(), 0) : 0 }}
@endsection

@section('title-content')
    @include('includes.title-content', ['pageInfo' => $pageInfo, 'class' => App\Label::class, 'type' => 'table'])
@endsection

@section('content')

    {{-- Таблица --}}
    <div class="grid-x">
        <div class="small-12 cell">

            <table class="content-table tablesorter content-labels" id="content" data-sticky-container
                   data-entity-alias="labels">

                <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2"
                       data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th">
                        <input type="checkbox" class="table-check-all" name="" id="check-all">
                        <label class="label-check" for="check-all"></label>
                    </th>
                    <th class="td-name">Название</th>
                    <th class="td-author">Автор</th>
                    <th class="td-control"></th>
                    <th class="td-delete"></th>
                </tr>
                </thead>

                <tbody data-tbodyId="1" class="tbody-width">

                @foreach($labels as $label)

                    <tr class="item @if($label->moderation == 1)no-moderation @endif" id="labels-{{ $label->id }}"
                        data-name="{{ $label->name }}">
                        <td class="td-drop">
                            <div class="sprite icon-drop"></div>
                        </td>
                        <td class="td-checkbox checkbox">

                            <input type="checkbox" class="table-check" name="label_id" id="check-{{ $label->id }}"
                                   @if(!empty($filter['booklist']['booklists']['default']))
                                   @if (in_array($label->id, $filter['booklist']['booklists']['default'])) checked
                                @endif
                                @endif
                            >
                            <label class="label-check" for="check-{{ $label->id }}"></label>
                        </td>

                        <td class="td-name">

                            @can('update', $label)
                                <a href="{{ route('labels.edit', $label->id) }}">{{ $label->name }}</a>
                            @else
                                {{ $label->name }}
                            @endcan

                        </td>

                        <td class="td-author">{{ $label->author->name }}</td>

                        {{-- Элементы управления --}}
                        @include('includes.control.table-td', ['item' => $label])

                        <td class="td-delete">
                            @can('delete', $label)
                                <a class="icon-delete sprite" data-open="item-delete"></a>
                            @endcan
                        </td>
                    </tr>

                @endforeach

                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="grid-x" id="pagination">
        <div class="small-6 cell pagination-head">
            <span class="pagination-title">Кол-во записей: {{ $labels->count() }}</span>
            {{ $labels->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
        </div>
    </div>
@endsection

@section('modals')
    @include('includes.modals.modal-delete')
@endsection

@push('scripts')
    {{-- Скрипт чекбоксов, сортировки и перетаскивания для таблицы --}}
    @include('includes.scripts.tablesorter-script')
    @include('includes.scripts.sortable-table-script')
    @include('includes.scripts.checkbox-control')

    {{-- Скрипт отображения на сайте --}}
    @include('includes.scripts.ajax-display')

    {{-- Скрипт системной записи --}}
    @include('includes.scripts.ajax-system')

    @include('includes.scripts.modal-delete-script')

@endpush
