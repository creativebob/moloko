@extends('layouts.app')
 
@section('inhead')
<meta name="description" content="{{ $page_info->page_description }}" />
{{-- Скрипты таблиц в шапке --}}
  @include('includes.scripts.table-inhead')
@endsection

@section('title')
  {{ $page_info->page_name }}
@endsection

@section('title-content')
{{-- Таблица --}}
<div data-sticky-container id="head-content">
  <div class="sticky sticky-topbar" id="head-sticky" data-sticky-on="small" data-sticky data-margin-top="2.4" data-top-anchor="head-content:top">
	  <div class="top-bar head-content">
	    <div class="top-bar-left">
	      <h2 class="header-content">{{ $page_info->page_name }}</h2>
        @can('create', App\Department::class)
	        <a href="/departments" class="icon-add sprite"></a>
        @endcan
	    </div>
	    <div class="top-bar-right">
	      <a class="icon-filter sprite"></a>
	      <input class="search-field" type="search" name="search_field" placeholder="Поиск" />
	      <button type="button" class="icon-search sprite button"></button>
	    </div>
	  </div>
	  {{-- Блок фильтров --}}
    <div class="grid-x">
      <div class="small-12 cell filters" id="filters">
        <fieldset class="fieldset-filters inputs">
          @include('staff.filters')
        </fieldset>
      </div>
    </div>
	</div>
</div>
@endsection
 
@section('content')

{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="table-content tablesorter" id="table-content" data-sticky-container>
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"><div class="sprite icon-drop"></div></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-staffer-position">Название должности</th>
        @if ($filials > 1)
          <th class="td-staffer-filial">Филиал</th>
        @endif
          <th class="td-staffer-department">Отдел</th>
          <th class="td-staffer-phone">Телефон</th>
          <th class="td-staffer-date-employment">Дата приема</th>
          
          <!-- <th class="td-delete"></th> -->
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
      @if(!empty($staff))
        @foreach($staff as $staffer)
        <tr class="parent @if($staffer->moderation == 1)no-moderation @endif" id="staff-{{ $staffer->id }}" data-name="{{ $staffer->position_name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox"><input type="checkbox" class="table-check" name="" id="check-{{ $staffer->id }}"><label class="label-check" for="check-{{ $staffer->id }}"></label></td>
          <td class="td-staffer-position">
            @can('update', $staffer)
            <a href="/staff/{{ $staffer->id }}/edit">
            @endcan
              @if (isset($staffer->user))
                {{ $staffer->user->second_name . ' ' . $staffer->user->first_name }}
              @else
                Вакансия
              @endif
            @can('update', $staffer)
            </a>
            @endcan
            ( {{ $staffer->position->position_name }} )
          </td>
          @if ($filials > 1)
            <td class="td-staffer-filial">{{ $staffer->filial->department_name }}</td>
          @endif
          <td class="td-staffer-department">
          @if ($staffer->filial->department_name !== $staffer->department->department_name)
            {{ $staffer->department->department_name }}
          @endif
          </td>
          <td class="td-staffer-phone">
            @if (isset($staffer->user))
              {{ $staffer->user->phone }}
            @endif</td>
          <td class="td-staffer-date-employment">
            @foreach ($staffer->employees as $employee)
              @if (($employee->user_id == $staffer->user_id) && ($employee->date_dismissal == null))
                {{ $employee->date_employment }}
              @endif
            @endforeach
          </td>
          
         <!--  <td class="td-delete">
            @if (isset($employee->company_id))
              <a class="icon-delete sprite" data-open="item-delete"></a>
            @endif
          </td>  -->      
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
    {{ $staff->links() }}
  </div>
</div>
@endsection

@section('modals')
{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete')
@endsection

@section('scripts')
{{-- Скрипт чекбоксов, сортировки и перетаскивания для таблицы --}}
@include('includes.scripts.table-scripts')

{{-- Скрипт модалки удаления --}}
@include('includes.scripts.modal-delete-script')
@endsection