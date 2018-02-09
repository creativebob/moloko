@extends('layouts.app')
 
@section('inhead')
{{-- Скрипты таблиц в шапке --}}
  @include('includes.table-inhead')
@endsection

@section('title', 'Пользователи')

@section('title-content')
{{-- Таблица --}}
<div data-sticky-container id="head-content">
  <div class="sticky sticky-topbar" id="head-sticky" data-sticky-on="small" data-sticky data-margin-top="2.4" data-top-anchor="head-content:top">
	  <div class="top-bar head-content">
	    <div class="top-bar-left">
	      <h2 class="header-content">Список компаний</h2>
	      <a href="/companies/create" class="icon-add sprite"></a>
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
          @include('companies.filters')
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
          <th class="td-company-name">Название компании</th>

          @if(Auth::user()->god == 1)<th class="td-getauth">Действие</th> @endif

          <th class="td-company-address">Адрес</th>
          <th class="td-company-phone">Телефон</th>
          <th class="td-user_id">Руководитель</th>
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
      @if(!empty($companies))
        @foreach($companies as $company)
        <tr class="parent @if(Auth::user()->company_id == $company->id)active @endif" id="companies-{{ $company->id }}" data-name="{{ $company->company_name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox"><input type="checkbox" class="table-check" name="" id="check-{{ $company->id }}"><label class="label-check" for="check-{{ $company->id }}"></label></td>
          <td class="td-company-name">{{ link_to_route('companies.edit', $company->company_name, [$company->id]) }} </td>

          {{-- Если пользователь бог, то показываем для него переключатель на компанию --}}
          @if(Auth::user()->god == 1)
            <td class="td-getauth">@if(Auth::user()->company_id != $company->id) {{ link_to_route('users.getauthcompany', 'Авторизоваться', ['company_id'=>$company->id], ['class' => 'tiny button']) }} @endif</td>
          @endif

          <td class="td-company-address">{{ $company->company_address }} </td>
          <td class="td-company-phone">{{ decorPhone($company->company_phone) }} </td>
          <td class="td-user_id">{{ $company->director->first_name or ' ... ' }} {{ $company->director->second_name or ' ... ' }} </td>

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
    <span class="pagination-title">Кол-во записей: {{ $companies->count() }}</span>
    {{ $companies->links() }}
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