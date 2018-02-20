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
        @can('create', App\Site::class)
	      <a href="/sites/create" class="icon-add sprite"></a>
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
          @include('sites.filters')
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
          <th class="td-site-name">Название сайта</th>
          <th class="td-site-domen">Домен сайта</th>
          <th class="td-company-name">Компания</th>
          <th class="td-site-edit">Изменить</th>
          <th class="td-site-author">Автор</th>
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
      @if(!empty($sites))
        @foreach($sites as $site)
        @php
          $edit = 0;
        @endphp
        @can('update', $site)
          @php
            $edit = 1;
          @endphp
        @endcan
        <tr class="parent @if($site->moderated == 1)no-moderation @endif" id="sites-{{ $site->id }}" data-name="{{ $site->site_name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox"><input type="checkbox" class="table-check" name="" id="check-{{ $site->id }}"><label class="label-check" for="check-{{ $site->id }}"></label></td>
          <td class="td-site-name">
            @if($edit == 1)
              <a href="/sites/{{ $site->site_alias }}">
            @endif
            {{ $site->site_name }}
            @if($edit == 1)
              </a> 
            @endif
          </td>
          <td class="td-site-domen"><a href="http://{{ $site->site_domen }}" target="_blank">{{ $site->site_domen }}</a></td>
          <td class="td-site-company-id">@if(!empty($site->company->company_name)) {{ $site->company->company_name }} @else @if($site->system_item == null) Шаблон @else Системная @endif @endif</td>
          <td class="td-site-edit">
            @if($edit == 1)
            <a class="tiny button" href="/sites/{{ $site->site_alias }}/edit">Редактировать</a>
            @endif
          </td>
          <td class="td-site-author">@if(isset($site->author->first_name)) {{ $site->author->first_name . ' ' . $site->author->second_name }} @endif</td>
          <td class="td-delete">
            @if ($site->system_item != 1)
              @can('delete', $site)
              <a class="icon-delete sprite" data-open="item-delete"></a>
              @endcan
            @endif
          </td>   
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
    <span class="pagination-title">Кол-во записей: {{ $sites->count() }}</span>
    {{ $sites->links() }}
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