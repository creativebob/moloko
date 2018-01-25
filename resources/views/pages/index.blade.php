@extends('layouts.app')
 
@section('inhead')
<meta name="description" content="{{ $site->site_name }}" />
{{-- Скрипты таблиц в шапке --}}
  @include('includes.table-inhead')
@endsection

@section('title')
  {{ $site->site_name }}
@endsection

@section('title-content')
{{-- Таблица --}}
<div data-sticky-container id="head-content">
  <div class="sticky sticky-topbar" id="head-sticky" data-sticky-on="small" data-sticky data-margin-top="2.4" data-top-anchor="head-content:top">
	  <div class="top-bar head-content">
	    <div class="top-bar-left">
	      <h2 class="header-content">{{ $site->site_name }}</h2>
	      <a href="/pages/create" class="icon-add sprite"></a>
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
        <fieldset class="fieldset-filters">

          {{ Form::open(['route' => 'companies.index', 'data-abide', 'novalidate', 'name'=>'filter', 'method'=>'GET']) }}

          <legend>Фильтрация</legend>
            <div class="grid-x grid-padding-x"> 
              <div class="small-6 cell">
                <label>Статус пользователя
                  {{ Form::select('contragent_status', [ 'all' => 'Все пользователи','1' => 'Сотрудник', '2' => 'Клиент'], 'all') }}
                </label>
              </div>
              <div class="small-6 cell">
                <label>Блокировка доступа
                  {{ Form::select('access_block', [ 'all' => 'Все пользователи', '1' => 'Доступ блокирован', '' => 'Доступ открыт'], 'all') }}
                </label>
              </div>
              <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
               {{ Form::submit('Фильтрация', ['class'=>'button']) }}
              </div>
            </div>

          {{ Form::close() }}

        </fieldset>
      </div>
    </div>
	</div>
</div>
@endsection

@section('breadcrumbs')
<div class="grid-x breadcrumbs">
  <div class="small-12 cell"> 
    <ul>
      <li><a href="/sites">Сайты</a></li>
      <li>{{ $site->site_name }}</li>
    </ul>
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
          <th class="td-page-name">Название страницы</th>
          <th class="td-page-title">Заголовок</th>
          <th class="td-page-description">Описание</th>
          <th class="td-page-alias">Алиас</th>
          <th class="td-site-id">Сайт</th>
          <th class="td-page-author">Автор</th>
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
      @if(!empty($pages))
        @foreach($pages as $page)
        <tr class="parent" id="pages-{{ $page->id }}" data-name="{{ $page->page_name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox"><input type="checkbox" class="table-check" name="" id="check-{{ $page->id }}"><label class="label-check" for="check-{{ $page->id }}"></label></td>
          <td class="td-page-name">{{ link_to_route('pages.edit', $page->page_name, [$page->id]) }} </td>
          <td class="td-page-title">{{ $page->page_title }}</td>
          <td class="td-page-description">{{ $page->page_description }}</td>
          <td class="td-page-alias">{{ $page->page_alias }}</td>
          <td class="td-site-id">{{ $page->site->site_name or ' ... ' }}</td>
          <td class="td-page-author">@if(isset($page->author->first_name)) {{ $page->author->first_name . ' ' . $page->author->second_name }} @endif</td>
          <td class="td-delete">
            @if (isset($page->site->company_id))
              <a class="icon-delete sprite" data-open="item-delete"></a>
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
    <span class="pagination-title">Кол-во записей: {{ $pages->count() }}</span>
    {{ $pages->links() }}
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