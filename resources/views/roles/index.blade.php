@extends('layouts.app')
 
@section('inhead')
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
        @can('create', App\Role::class)
	      <a href="/roles/create" class="icon-add sprite"></a>
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
          @include('roles.filters')
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
          <th class="td-role-name">Название таблицы</th>
          <th class="td-role-set">Настройка</th>
          <th class="td-role-company-id">Компания</th>
          <th class="td-role-count">Количество правил</th>
          <th class="td-role-description">Описание</th>
          <th class="td-role-author">Автор</th>
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
      @if(!empty($roles))
        @foreach($roles as $role)
        @php
          $edit = 0;
        @endphp
        @can('update', $role)
          @php
            $edit = 1;
          @endphp
        @endcan
        <tr class="parent @if(Auth::user()->role_id == $role->id)active @endif" id="roles-{{ $role->id }}" data-name="{{ $role->role_name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox"><input type="checkbox" class="table-check" name="" id="check-{{ $role->id }}"><label class="label-check" for="check-{{ $role->id }}"></label></td>
          <td class="td-role-name">
            @if($edit == 1)
              <a href="/roles/{{ $role->id }}/edit">
            @endif
            {{ $role->role_name }}
            @if($edit == 1)
              </a> 
            @endif
          </td>
          <td class="td-role-set">
            @if($edit == 1)
            @if(!empty($counts_directive_array[$role->id]['disabled_role']))
              <a class="button tiny" disabled>Настройка</a>
            @else 
              {{ link_to_route('roles.setting', 'Настройка', [$role->id], ['class'=>'button tiny']) }}
            @endif
            @endif
          </td>
          <td class="td-role-company-id">@if(!empty($role->company->company_name)) {{ $role->company->company_name }} @else @if($role->system_item == null) Шаблон @else Системная @endif @endif</td>
          <td class="td-role-count"><span class="allow">{{ $counts_directive_array[$role->id]['count_allow'] }}</span> / <span class="deny"> {{ $counts_directive_array[$role->id]['count_deny'] }}</span></td>
          <td class="td-role-description">{{ $role->role_description }} </td>
          <td class="td-role-author">@if(!empty($role->author->first_name)) {{ $role->author->first_name . ' ' . $role->author->second_name }} @endif</td>
          <td class="td-delete">
          @if ($role->system_item !== 1)
            @can('delete', $role)
            <a class="icon-delete sprite" data-open="item-delete"></a></td> 
            @endcan
          @endif       
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
    <span class="pagination-title">Кол-во записей: {{ $roles->count() }}</span>
    {{ $roles->links() }}
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