@extends('layouts.app')
 
@section('inhead')
<meta name="description" content="{{ $page_info->page_description }}" />
{{-- Скрипты таблиц в шапке --}}
  @include('includes.scripts.table-inhead')
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
    <table class="table-content tablesorter" id="content" class="content-employees" data-sticky-container>
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"><div class="sprite icon-drop"></div></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-name">Имя сотрудника</th>
          <th class="td-position">Название должности</th>
          @if ($filials > 1)
            <th class="td-filial">Филиал</th>
          @endif
          <th class="td-department">Отдел</th>
          <th class="td-date-employment">Дата приема</th>
          <th class="td-date-dismissal">Дата увольнения</th>
          
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
          <td class="td-checkbox checkbox"><input type="checkbox" class="table-check" name="" id="check-{{ $employee->id }}"><label class="label-check" for="check-{{ $employee->id }}"></label></td>
          <td class="td-name">
          @if ($employee->date_dismissal == null)
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
          <td class="td-date-employment">{{ $employee->date_employment }}</td>
          <td class="td-date-dismissal">{{ $employee->date_dismissal }}</td>
          <td class="td-status">
          @if (!empty($employee->date_dismissal))
            Уволен
          @else
            Работает
          @endif
          </td>
          <td class="td-dismissal-desc">{{ $employee->dismissal_desc }}</td>    
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

@include('includes.scripts.inputs-mask')
@include('includes.scripts.pickmeup-script')

{{-- Скрипт чекбоксов, сортировки и перетаскивания для таблицы --}}
@include('includes.scripts.table-scripts')

{{-- Скрипт модалки удаления --}}
@include('includes.scripts.modal-delete-script')
@endsection