@extends('layouts.app')

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('content-count')
    {{ $mailingLists->isNotEmpty() ? num_format($mailingLists->total(), 0) : 0 }}
@endsection

@section('title-content')
    @include('includes.title-content', ['pageInfo' => $pageInfo, 'class' => App\MailingList::class, 'type' => 'table'])
@endsection

@section('content')
    {{-- Таблица --}}
    <div class="grid-x">
        <div class="small-12 cell">

            <table class="content-table tablesorter content-mailing_lists" id="content" data-sticky-container data-entity-alias="mailing_lists">

                <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
                    <tr id="thead-content">
                        <th class="td-drop"></th>
                        <th class="td-checkbox checkbox-th">
                            <input type="checkbox" class="table-check-all" name="" id="check-all">
                            <label class="label-check" for="check-all"></label>
                        </th>
                        <th class="td-name">Название</th>
                        <th class="td-description">Описание</th>
                        <th class="td-author">Автор</th>
                        <th class="td-control"></th>
                        <th class="td-delete"></th>
                    </tr>
                </thead>

                <tbody data-tbodyId="1" class="tbody-width">

                    @foreach($mailingLists as $mailingList)

                        <tr class="item @if($mailingList->moderation == 1)no-moderation @endif" id="mailing_lists-{{ $mailingList->id }}" data-name="{{ $mailingList->name }}">
                            <td class="td-drop"><div class="sprite icon-drop"></div></td>
                            <td class="td-checkbox checkbox">

                                <input type="checkbox" class="table-check" name="mailing_list_id" id="check-{{ $mailingList->id }}"
                                @if(!empty($filter['booklist']['booklists']['default']))
                                    @if (in_array($mailingList->id, $filter['booklist']['booklists']['default'])) checked
                                    @endif
                                @endif
                                >
                                <label class="label-check" for="check-{{ $mailingList->id }}"></label>
                            </td>
                            <td class="td-name">

                                @can('update', $mailingList)
                                    <a href="{{ route('mailing_lists.edit', $mailingList->id) }}">{{ $mailingList->name }}</a>
                                @else
                                    {{ $mailingList->name }}
                                @endcan

                                <span>({{ $mailingList->subscribers_count }})</span>

                            </td>
                            <td class="td-description">{{ $mailingList->description }}</td>

                            <td class="td-author">{{ $mailingList->author->name }}</td>

                            {{-- Элементы управления --}}
                            @include('includes.control.table-td', ['item' => $mailingList])

                            <td class="td-delete">
                                @can('delete', $mailingList)
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
          <span class="pagination-title">Кол-во записей: {{ $mailingLists->count() }}</span>
          {{ $mailingLists->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
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
