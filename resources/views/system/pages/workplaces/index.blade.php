@extends('layouts.app')

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('content-count')
    {{ $workplaces->isNotEmpty() ? num_format($workplaces->total(), 0) : 0 }}
@endsection

@section('title-content')
    @include('includes.title-content', ['pageInfo' => $pageInfo, 'class' => App\Workplace::class, 'type' => 'menu'])
@endsection

@section('content')

    {{-- Таблица --}}
    <div class="grid-x">
        <div class="small-12 cell">

            <table class="content-table tablesorter content-workplaces" id="content" data-sticky-container
                   data-entity-alias="workplaces">

                <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2"
                       data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th">
                        <input type="checkbox" class="table-check-all" name="" id="check-all">
                        <label class="label-check" for="check-all"></label>
                    </th>
                    <th class="td-name">Название</th>
                    <th class="td-description">Описание</th>

                    <th class="td-tools">Оборудование</th>
                    <th class="td-staff">Сотрудники</th>

                    <th class="td-outlet">Торговая точка</th>
                    <th class="td-filial">Филиал</th>

                    <th class="td-author">Автор</th>
                    <th class="td-control"></th>
                    <th class="td-delete"></th>
                </tr>
                </thead>

                <tbody data-tbodyId="1" class="tbody-width">

                @foreach($workplaces as $workplace)

                    <tr class="item @if($workplace->moderation == 1)no-moderation @endif"
                        id="workplaces-{{ $workplace->id }}" data-name="{{ $workplace->name }}">
                        <td class="td-drop">
                            <div class="sprite icon-drop"></div>
                        </td>
                        <td class="td-checkbox checkbox">

                            <input type="checkbox" class="table-check" name="workplace_id"
                                   id="check-{{ $workplace->id }}"
                                   @if(!empty($filter['booklist']['booklists']['default']))
                                   @if (in_array($workplace->id, $filter['booklist']['booklists']['default'])) checked
                                @endif
                                @endif
                            >
                            <label class="label-check" for="check-{{ $workplace->id }}"></label>
                        </td>

                        <td class="td-name">

                            @can('update', $workplace)
                                <a href="{{ route('workplaces.edit', $workplace->id) }}">{{ $workplace->name }}</a>
                            @else
                                {{ $workplace->name }}
                            @endcan

                        </td>
                        <td class="td-description">{{ $workplace->description }}</td>

                        <td class="td-tools">{{ $workplace->tools->implode('article.name', ', ') }}</td>
                        <td class="td-staff">{{ $workplace->staff->implode('user.name', ', ') }}</td>

                        <td class="td-outlet">{{ optional($workplace->outlet)->name }}</td>

                        <td class="td-filial">{{ $workplace->filial->name }}</td>

                        <td class="td-author">{{ $workplace->author->name }}</td>

                        {{-- Элементы управления --}}
                        @include('includes.control.table-td', ['item' => $workplace])

                        <td class="td-delete">
                            @can('delete', $workplace)
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
            <span class="pagination-title">Кол-во записей: {{ $workplaces->count() }}</span>
            {{ $workplaces->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
        </div>
    </div>
@endsection

@section('modals')
    @can('create', App\Workplace::class)
        @include('system.pages.workplaces.create')
    @endcan

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
