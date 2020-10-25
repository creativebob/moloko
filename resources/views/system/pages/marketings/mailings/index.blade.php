@extends('layouts.app')

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('content-count')
    {{ $mailings->isNotEmpty() ? num_format($mailings->total(), 0) : 0 }}
@endsection

@section('title-content')
    @include('includes.title-content', ['pageInfo' => $pageInfo, 'class' => App\Mailing::class, 'type' => 'table'])
@endsection

@section('content')
    {{-- Таблица --}}
    <div class="grid-x">
        <div class="small-12 cell">

            <table class="content-table tablesorter content-mailings" id="content" data-sticky-container data-entity-alias="mailings">

                <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
                    <tr id="thead-content">
                        <th class="td-drop"></th>
                        <th class="td-checkbox checkbox-th">
                            <input type="checkbox" class="table-check-all" name="" id="check-all">
                            <label class="label-check" for="check-all"></label>
                        </th>
                        <th class="td-name">Название</th>
                        <th class="td-description">Описание</th>
                        <th class="td-list">Список</th>
                        <th class="td-template">Шаблон</th>
                        <th class="td-author">Автор</th>
                        <th class="td-control"></th>
                        <th class="td-delete"></th>
                    </tr>
                </thead>

                <tbody data-tbodyId="1" class="tbody-width">

                    @foreach($mailings as $mailing)

                        <tr class="item @if($mailing->moderation == 1)no-moderation @endif @if($mailing->is_actual == 1)is-actual @endif" id="mailings-{{ $mailing->id }}" data-name="{{ $mailing->name }}">
                            <td class="td-drop"><div class="sprite icon-drop"></div></td>
                            <td class="td-checkbox checkbox">

                                <input type="checkbox" class="table-check" name="mailing_id" id="check-{{ $mailing->id }}"
                                @if(!empty($filter['booklist']['booklists']['default']))
                                    @if (in_array($mailing->id, $filter['booklist']['booklists']['default'])) checked
                                    @endif
                                @endif
                                >
                                <label class="label-check" for="check-{{ $mailing->id }}"></label>
                            </td>
                            <td class="td-name">

                                @can('update', $mailing)
                                    <a href="{{ route('mailings.edit', $mailing->id) }}">{{ $mailing->name }}</a>
                                @else
                                    {{ $mailing->name }}
                                @endcan

                            </td>
                            <td class="td-description">{{ $mailing->description }}</td>

                            <td class="td-list">{{ $mailing->list->name }}</td>
                            <td class="td-template">{{ $mailing->template->name }}</td>

                            <td class="td-author">{{ $mailing->author->name }}</td>

                            {{-- Элементы управления --}}
                            @include('includes.control.table-td', ['item' => $mailing])

                            <td class="td-delete">
                                @can('delete', $mailing)
                                    <a class="icon-delete sprite" data-open="item-archive"></a>
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
          <span class="pagination-title">Кол-во записей: {{ $mailings->count() }}</span>
          {{ $mailings->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
      </div>
    </div>
@endsection

@section('modals')
    {{-- Модалка удаления с refresh --}}
    @include('includes.modals.modal-archive')
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
    {{-- Скрипт модалки удаления --}}
    @include('includes.scripts.modal-archive-script')
@endpush
