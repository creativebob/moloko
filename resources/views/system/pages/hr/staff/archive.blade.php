@extends('layouts.app')

@section('inhead')
    <meta name="description" content="{{ $pageInfo->description }}"/>

@endsection

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('content-count')
    {{-- Количество элементов --}}
    {{ num_format($staff->total(), 0) }}
@endsection

@section('title-content')
    {{-- Таблица --}}
    @include('system.pages.hr.staff.includes.title')
@endsection

@section('content')

    {{-- Таблица --}}
    <div class="grid-x">
        <div class="small-12 cell">
            <table class="content-table tablesorter" id="content" data-sticky-container data-entity-alias="staff">

                <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2"
                       data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th">
                        <input type="checkbox" class="table-check-all" name="" id="check-all">
                        <label class="label-check" for="check-all"></label>
                    </th>
                    <th class="td-position">Название должности</th>
                    @if ($staff->isNotEmpty() && $staff->first()->company->filials->count() > 1)
                        <th class="td-filial">Филиал</th>
                    @endif
                    <th class="td-department">Отдел</th>
                    <th class="td-unarchive"></th>
                </tr>
                </thead>

                <tbody data-tbodyId="1" class="tbody-width">

                    @foreach($staff as $staffer)

                        <tr class="item @if($staffer->moderation == 1)no-moderation @endif"
                            id="staff-{{ $staffer->id }}"
                            data-name="{{ isset($staffer->user) ? $staffer->user->name : 'Вакансия' }} ( {{ $staffer->position->name }} )"
                            data-entity="staff" data-id="{{ $staffer->id }}">
                            <td class="td-drop">
                                <div class="sprite icon-drop"></div>
                            </td>
                            <td class="td-checkbox checkbox">
                                <input type="checkbox" class="table-check" name="" id="check-{{ $staffer->id }}"

                                       {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
                                       @if(!empty($filter['booklist']['booklists']['default']))
                                       {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
                                       @if (in_array($staffer->id, $filter['booklist']['booklists']['default'])) checked
                                    @endif
                                    @endif
                                >

                                <label class="label-check" for="check-{{ $staffer->id }}"></label>
                            </td>
                            <td class="td-position">
                                    {{ $staffer->position->name }}
                                    ( {{ isset($staffer->user) ? $staffer->user->name : 'Вакансия' }} )
                            </td>

                            @if ($staffer->company->filials->count() > 1)
                                <td class="td-filial">{{ $staffer->filial->name }}</td>
                            @endif

                            <td class="td-department">
                                @if ($staffer->filial->name !== $staffer->department->name)
                                    {{ $staffer->department->name }}
                                @endif
                            </td>

                            <td class="td-unarchive">
                                <a class="button tiny" href="{{ route('staff.unarchive', $staffer->id) }}">Восстановить</a>
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
            <span class="pagination-title">Кол-во записей: {{ $staff->count() }}</span>
            {{ $staff->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
        </div>
    </div>
@endsection


@push('scripts')
    {{-- Скрипт чекбоксов, сортировки и перетаскивания для таблицы --}}
    @include('includes.scripts.tablesorter-script')
    @include('includes.scripts.sortable-table-script')
@endpush
