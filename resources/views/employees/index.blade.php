@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $page_info->page_description }}" />
{{-- Скрипты таблиц в шапке --}}
@include('includes.scripts.tablesorter-inhead')
@include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => null, 'type' => 'table'])
@endsection

@section('content')

{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="table-content tablesorter content-employees" id="content" data-sticky-container data-entity-alias="employees">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-name">Имя сотрудника</th>
          <th class="td-position">Название должности</th>
          @if ($filials > 1)
          <th class="td-filial">Филиал</th>
          @endif
          <th class="td-department">Отдел</th>
          <th class="td-employment-date">Дата приема</th>
          <th class="td-dismissal-date">Дата увольнения</th>
          
          <th class="td-status">Статус</th>
          <th class="td-dismissal-desc">Причина увольнения</th>
          <!-- <th class="td-delete"></th> -->
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
        @if(!empty($employees))
        @foreach($employees as $employee)
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
            @if ($employee->dismissal_date == null)
              @can('update', $employee)
              <a href="/staff/{{ $employee->staffer->id }}/edit">
              @endcan
            @else
              @can('update', $employee)
              <a href="/employees/{{ $employee->id }}/edit">
              @endcan
            @endif
            {{ $employee->user->name }}
            @can('update', $employee)
              </a>
            @endcan
            </td>
            <td class="td-position">
              {{ $employee->staffer->position->name }}
            </td>
            @if ($filials > 1)
            <td class="td-filial">{{ $employee->staffer->filial->name }}</td>
            @endif
            <td class="td-department">
              @if ($employee->staffer->filial->name !== $employee->staffer->department->name)
              {{ $employee->staffer->department->name }}
              @endif
            </td>
            <td class="td-employment-date">{{ $employee->employment_date }}</td>
            <td class="td-dismissal-date">{{ $employee->dismissal_date }}</td>
            <td class="td-status">
              @if (!empty($employee->dismissal_date))
              Уволен
              @else
              Работает
              @endif
            </td>
            <td class="td-dismissal-description">{{ $employee->dismissal_description }}</td>    
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
      <span class="pagination-title">Кол-во записей: {{ $employees->count() }}</span>
      {{ $employees->links() }}
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

  {{-- Скрипт чекбоксов --}}
  @include('includes.scripts.checkbox-control')

  {{-- Скрипт перетаскивания для меню --}}
  @include('includes.scripts.sortable-table-script')

  @include('includes.scripts.inputs-mask')
  @include('includes.scripts.pickmeup-script')
  
  {{-- Скрипт модалки удаления --}}
  @include('includes.scripts.modal-delete-script')

  @endsection