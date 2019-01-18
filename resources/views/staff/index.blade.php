@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $page_info->page_description }}" />
{{-- Скрипты таблиц в шапке --}}
@include('includes.scripts.tablesorter-inhead')
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('content-count')
{{-- Количество элементов --}}
@if(!empty($staff))
{{ num_format($staff->total(), 0) }}
@endif
@endsection

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\Department::class, 'type' => 'table'])
@endsection

@section('content')

{{-- Таблица --}}
<div class="grid-x">
    <div class="small-12 cell">
        <table class="content-table tablesorter" id="content" data-sticky-container data-entity-alias="staff">

            <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
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
                    <th class="td-phone">Телефон</th>
                    <th class="td-employment-date">Дата приема</th>
                    <th class="td-control"></th>
                    <!-- <th class="td-delete"></th> -->
                </tr>
            </thead>

            <tbody data-tbodyId="1" class="tbody-width">

                @if($staff->isNotEmpty())
                @foreach($staff as $staffer)

                <tr class="item @if($staffer->moderation == 1)no-moderation @endif" id="staff-{{ $staffer->id }}" data-name="{{ $staffer->name }}">
                    <td class="td-drop"><div class="sprite icon-drop"></div></td>
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

                        @can('update', $staffer)
                        <a href="/admin/staff/{{ $staffer->id }}/edit">{{ isset($staffer->user) ? $staffer->user->name : 'Вакансия' }} ( {{ $staffer->position->name }} )</a>
                        @endcan

                        @cannot('update', $staffer)
                            {{ isset($staffer->user) ? $staffer->user->name : 'Вакансия' }} ( {{ $staffer->position->name }} )
                        @endcannot

                    </td>

                    @if ($staffer->company->filials->count() > 1)
                    <td class="td-filial">{{ $staffer->filial->name }}</td>
                    @endif

                    <td class="td-department">

                        @if ($staffer->filial->name !== $staffer->department->name)
                        {{ $staffer->department->name }}
                        @endif

                    </td>
                    <td class="td-phone">

                        @isset($staffer->user)
                        {{ isset($staffer->user->main_phone->phone) ? decorPhone($staffer->user->main_phone->phone) : 'Телефон не указан' }}
                        @endisset

                    </td>
                    <td class="td-employment-date">

                        {{ isset($staffer->employee) ? $staffer->employee->employment_date->format('d.m.Y') : '' }}

                    </td>

                    {{-- Элементы управления --}}
                    @include('includes.control.table-td', ['item' => $staffer])

                        {{--  <td class="td-delete">
                        @if (isset($employee->company_id))
                            <a class="icon-delete sprite" data-open="item-delete"></a>
                        @endif
                    </td>  --}}
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
        <span class="pagination-title">Кол-во записей: {{ $staff->count() }}</span>
        {{ $staff->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
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