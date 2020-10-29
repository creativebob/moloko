@extends('layouts.app')

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('exсel')
    <button class="button tiny" data-open="modal-add_to_mailing_list">Добавление в список</button>
    <div class="reveal" id="modal-add_to_mailing_list" data-reveal>
        <div class="grid-x">
            <div class="small-12 cell modal-title">
                <h5>Добавиление в список рассылки</h5>
            </div>
        </div>

        {!! Form::open(['route' => ['subscribers.addToMailingList', Request::all()]]) !!}
        <div class="grid-x align-center grid-padding-x tabs-margin-top">
            <div class="cell small-6">
                <label>Выберите список
                    {!! Form::select('mailing_list_id', $mailingLists->pluck('name', 'id')) !!}
                </label>
            </div>
        </div>

        <div class="grid-x align-center grid-padding-x">
            <div class="cell small-6">
                <input type="submit" class="button modal-button" value="Добавить">
            </div>
        </div>
        {!! Form::close() !!}

        <div data-close class="icon-close-modal sprite close-modal remove-modal"></div>
    </div>

    <button class="button tiny" data-open="modal-import">Импорт</button>
    <div class="reveal" id="modal-import" data-reveal>
        <div class="grid-x">
            <div class="small-12 cell modal-title">
                <h5>Импорт</h5>
            </div>
        </div>

        {!! Form::open(['route' => 'subscribers.excelImport', 'files' => true]) !!}
        @isset($sites)
            <div class="grid-x align-center grid-padding-x">
                <div class="cell small-6">
                    @if($sites->count() > 1)
                        <label>Сайт
                            {!! Form::select('site_id', $sites->pluck('name', 'id'), null, ['id' => 'select-sites']) !!}
                        </label>
                    @else
                        {!! Form::hidden('site_id', $sites->first()->id) !!}
                    @endif
                </div>
            </div>
        @endisset
        <div class="grid-x align-center grid-padding-x tabs-margin-top">
            <div class="cell small-6">
                <input name="subscribers" type="file">
            </div>
        </div>
        <div class="grid-x align-center grid-padding-x">
            <div class="cell small-6">
                <input type="submit" class="button modal-button" value="Загрузить">
            </div>
        </div>
        {!! Form::close() !!}

        <div data-close class="icon-close-modal sprite close-modal remove-modal"></div>
    </div>
@endsection

@section('content-count')
    {{ $subscribers->isNotEmpty() ? num_format($subscribers->total(), 0) : 0 }}
@endsection

@section('title-content')
    @include('system.pages.marketings.subscribers.includes.title')
@endsection

@section('content')
    {{-- Таблица --}}
    <div class="grid-x">
        <div class="small-12 cell">

            <table class="content-table tablesorter content-subscribers" id="content" data-sticky-container
                   data-entity-alias="subscribers">

                <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2"
                       data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th">
                        <input type="checkbox" class="table-check-all" name="" id="check-all">
                        <label class="label-check" for="check-all"></label>
                    </th>
                    <th class="td-name">Имя</th>
                    <th class="td-email">Email</th>
                    <th class="td-active">Статус</th>
                    <th class="td-deny">Запрещен</th>
                    <th class="td-dispatches">Письма</th>
                    <th class="td-author">Автор</th>
                    <th class="td-control"></th>
                    <th class="td-delete"></th>
                </tr>
                </thead>

                <tbody data-tbodyId="1" class="tbody-width">

                @foreach($subscribers as $subscriber)

                    <tr
                        class="item
                        @if($subscriber->moderation == 1)no-moderation @endif
                        @if($subscriber->is_active == 0)is-not-active @endif
                        @if($subscriber->denied_at)is-denied @endif
                        @if($subscriber->is_valid == 0)is-not-valid @endif
                            "
                        id="subscribers-{{ $subscriber->id }}"
                        data-name="{{ $subscriber->email }}"
                    >
                        <td class="td-drop">
                            <div class="sprite icon-drop"></div>
                        </td>
                        <td class="td-checkbox checkbox">

                            <input type="checkbox" class="table-check" name="subscriber_id"
                                   id="check-{{ $subscriber->id }}"
                                   @if(!empty($filter['booklist']['booklists']['default']))
                                   @if (in_array($subscriber->id, $filter['booklist']['booklists']['default'])) checked
                                @endif
                                @endif
                            >
                            <label class="label-check" for="check-{{ $subscriber->id }}"></label>
                        </td>
                        <td class="td-name">{{ $subscriber->getName }}</td>
                        <td class="td-email">
                            @can('update', $subscriber)
                                <a href="{{ route('subscribers.edit', $subscriber->id) }}">{{ $subscriber->email }}</a>
                            @else
                                {{ $subscriber->email }}
                            @endcan
                        </td>
                        <td class="td-active">{{ $subscriber->is_active == 0 ? 'Не действителен' : '' }}</td>
                        <td class="td-deny">{{ optional($subscriber->denied_at)->format('d.m.y H:i') }}</td>
                        <td class="td-dispatches">{{ $subscriber->sended_dispatches_count }}</td>

                        <td class="td-author">{{ $subscriber->author->name }}</td>

                        {{-- Элементы управления --}}
                        @include('includes.control.table-td', ['item' => $subscriber])

                        <td class="td-delete">
                            @can('delete', $subscriber)
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
            <span class="pagination-title">Кол-во записей: {{ $subscribers->count() }}</span>
            {{ $subscribers->appends(Request::all())->links() }}
        </div>
    </div>
@endsection

@section('modals')
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
@endpush
