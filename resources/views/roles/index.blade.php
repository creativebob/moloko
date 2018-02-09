@extends('layouts.app')
 
@section('inhead')
{{-- Скрипты таблиц в шапке --}}
  @include('includes.table-inhead')
@endsection

@section('title', 'Группы доступа')

@section('title-content')
{{-- Таблица --}}
<div data-sticky-container id="head-content">
  <div class="sticky sticky-topbar" id="head-sticky" data-sticky-on="small" data-sticky data-margin-top="2.4" data-top-anchor="head-content:top">
	  <div class="top-bar head-content">
	    <div class="top-bar-left">
	      <h2 class="header-content">Список групп доступа</h2>
	      <a href="/roles/create" class="icon-add sprite"></a>
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
        <tr class="parent @if(Auth::user()->role_id == $role->id)active @endif" id="roles-{{ $role->id }}" data-name="{{ $role->role_name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox"><input type="checkbox" class="table-check" name="" id="check-{{ $role->id }}"><label class="label-check" for="check-{{ $role->id }}"></label></td>
          <td class="td-role-name">{{ link_to_route('roles.edit', $role->role_name, [$role->id]) }}</td>
          <td class="td-role-set">
            @if(!empty($counts_directive_array[$role->id]['disabled_role']))
              <a class="button tiny" disabled>Настройка</a>
            @else 
              {{ link_to_route('roles.setting', 'Настройка', [$role->id], ['class'=>'button tiny']) }}
            @endif</td>
          <td class="td-role-company-id">@if(!empty($role->company->company_name)) {{ $role->company->company_name }} @else Системная  @endif</td>
          <td class="td-role-count"><span class="allow">{{ $counts_directive_array[$role->id]['count_allow'] }}</span> / <span class="deny"> {{ $counts_directive_array[$role->id]['count_deny'] }}</span></td>
          <td class="td-role-description">{{ $role->role_description }} </td>
          <td class="td-role-author">@if(!empty($role->author->first_name)) {{ $role->author->first_name . ' ' . $role->author->second_name }} @endif</td>
          <td class="td-delete"><a class="icon-delete sprite" data-open="item-delete"></a></td>       
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
@include('includes.table-scripts')

{{-- Скрипт модалки удаления --}}
@include('includes.modals.modal-delete-script')
@endsection