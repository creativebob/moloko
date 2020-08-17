@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $pageInfo->page_description }}" />

@include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('content-count')
{{-- Количество элементов --}}
@if(!empty($employees))
{{ num_format($employees->count(), 0) }}
{{-- num_format($employees->where('dismissal_date', null)->count(), 0) --}}
@endif
@endsection

@section('title-content')
    @include('system.pages.hr.employees.includes.title_active')
@endsection

@section('content')

{{-- Таблица --}}
<div class="grid-x">
    <div class="small-12 cell">
        <table class="content-table tablesorter content-employees" id="content" data-sticky-container data-entity-alias="employees">

            <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
                    <th class="td-name">Имя сотрудника</th>
                    <th class="td-phone">Телефон</th>
                    <th class="td-position">Название должности</th>

                    @if ((Auth::user()->god == 1) || ($employees->isNotEmpty() && $employees->first()->company->filials->count() > 1))
                    <th class="td-filial">Филиал</th>
                    @endif
                    @if(Auth::user()->god == 1)<th class="td-getauth">Действие</th> @endif
                    <th class="td-employment-date">Дата приема</th>
                    <th class="td-status">Статус</th>
                    <th class="td-access-block">Доступ</th>
                    <th class="td-control"></th>
                    <!-- <th class="td-delete"></th> -->
                </tr>
            </thead>

            <tbody data-tbodyId="1" class="tbody-width">
                @if($employees->isNotEmpty())
                @foreach($employees as $employee)

                {{-- Привязан ли пользователь к сотруднику --}}
                @if($employee->user != null)
                <tr class="item @if($employee->moderation == 1)no-moderation @endif" id="employees-{{ $employee->id }}" data-name="{{ $employee->name }}">
                    <td class="td-drop"><div class="sprite icon-drop"></div></td>
                    <td class="td-checkbox checkbox">
                        <input type="checkbox" class="table-check" name="" id="check-{{ $employee->id }}"

                        {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
                        @if(!empty($filter['booklist']['booklists']['default']))
                        {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
                        @if (in_array($employee->id, $filter['booklist']['booklists']['default'])) checked
                        @endif
                        @endif

                        >
                        <label class="label-check" for="check-{{ $employee->id }}"></label></td>
                        <td class="td-name">

                            @can('update', $employee)
                                <a href="/admin/{{ 'employees/'.$employee->id }}/edit">{{ $employee->user->name_reverse }}</a>
                            @else
                                {{ $employee->user->name_reverse }}
                            @endcan

                        </td>

                        <td class="td-phone">{{ isset($employee->user->main_phone->phone) ? decorPhone($employee->user->main_phone->phone) : 'Телефон не указан' }}
                            <br>
                            <span class="tiny-text">
                                {{ $employee->user->location->city->name ?? '' }} @if(isset($employee->user->location->address)), {{ $employee->user->location->address}}@endif
                            </span>
                        </td>

                        <td class="td-position">
                            {{ $employee->staffer->position->name }}
                            @if (($employee->company->filials->count() > 1)&&($employee->staffer->department->parent_id != null))
                                <br>
                                <span class="tiny-text">{{ $employee->staffer->department->name }}</span>
                            @endif
                        </td>

                        @if (($employee->company->filials->count() > 1) || (Auth::user()->god == 1))
                            <td class="td-filial">{{ $employee->staffer->filial->name }}</td>
                        @endif

                        {{-- Если пользователь бог, то показываем для него переключатель на авторизацию под пользователем --}}
                        @if(Auth::user()->god == 1)

                            @php
                                $count_roles = count($employee->staffer->user->roles);
                                if($count_roles < 1) {
                                    $but_class = "tiny button warning"; $but_text = "Права не назначены";
                                } else {
                                    $but_class = "tiny button"; $but_text = "Авторизоваться";
                                };
                            @endphp
                            <td class="td-getauth">
                                @if((Auth::user()->id != $employee->staffer->user_id) && !empty($employee->staffer->user->company_id))
                                    {{ link_to_route('users.getauthuser', $but_text, ['user_id' => $employee->staffer->user_id], ['class' => $but_class]) }}
                                @endif
                            </td>
                        @endif

                        <td class="td-employment-date">{{ $employee->employment_date->format('d.m.Y') }}</td>
                        <td class="td-status">

                            @if (isset($employee->dismissal_date))
                                @isset($employee->user->gender)
                                    {{ $employee->user->gender == 1 ? 'Уволен' : 'Уволена' }}
                                @endisset
                            @else
                            Работает
                            @endif

                        </td>
                        <td class="td-access-block">
                            {{ decor_access_block($employee->user->access_block) }}
                        </td>

                        {{-- Элементы управления --}}
                        @include('includes.control.table-td', ['item' => $employee])

                    </tr>
                    @endif

                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="grid-x" id="pagination">
      <div class="small-6 cell pagination-head">
        <span class="pagination-title">Кол-во записей: {{ $employees->count() }}</span>
        {{ $employees->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
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
@include('includes.scripts.checkbox-control')

{{-- Скрипт отображения на сайте --}}
@include('includes.scripts.ajax-display')

{{-- Скрипт системной записи --}}
@include('includes.scripts.ajax-system')

@include('includes.scripts.inputs-mask')
@include('includes.scripts.pickmeup-script')

{{-- Скрипт модалки удаления --}}
@include('includes.scripts.modal-delete-script')

@endsection
