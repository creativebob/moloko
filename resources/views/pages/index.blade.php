@extends('layouts.app')
 
@section('inhead')
<meta name="description" content="{{ $site->site_name }}" />
{{-- Скрипты таблиц в шапке --}}
  @include('includes.table-inhead')
@endsection

@section('title')
  {{ $page_info->page_name . ' ' . $site->site_name }}
@endsection

@section('title-content')
{{-- Таблица --}}
<div data-sticky-container id="head-content">
  <div class="sticky sticky-topbar" id="head-sticky" data-sticky-on="small" data-sticky data-margin-top="2.4" data-top-anchor="head-content:top">
	  <div class="top-bar head-content">
	    <div class="top-bar-left">
	      <h2 class="header-content">{{ $page_info->page_name . ' ' . $site->site_name }}</h2>
        @can('create', App\Page::class)
	      <a href="/sites/{{ $site_alias }}/pages/create" class="icon-add sprite"></a>
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
          @include('pages.filters')
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
          <td class="td-page-name">
            @can('update', $page)
              <a href="/sites/{{ $page->site->site_alias }}/pages/{{ $page->page_alias }}/edit">
            @endcan
            {{ $page->page_name }}
            @can('update', $page)
              </a>
            @endcan
          </td>
          <td class="td-page-title">{{ $page->page_title }}</td>
          <td class="td-page-description">{{ $page->page_description }}</td>
          <td class="td-page-alias">{{ $page->page_alias }}</td>
          <td class="td-site-id">{{ $page->site->site_name or ' ... ' }}</td>
          <td class="td-page-author">@if(isset($page->author->first_name)) {{ $page->author->first_name . ' ' . $page->author->second_name }} @endif</td>
          <td class="td-delete">
            @if ($page->system_item != 1)
              @can('delete', $page)
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
<script type="text/javascript">
$(function() {
  // Берем алиас сайта
  var siteAlias = '{{ $site_alias }}';
 // Мягкое удаление с refresh
  $(document).on('click', '[data-open="item-delete"]', function() {
    // находим описание сущности, id и название удаляемого элемента в родителе
    var parent = $(this).closest('.parent');
    var type = parent.attr('id').split('-')[0];
    var id = parent.attr('id').split('-')[1];
    var name = parent.data('name');
    $('.title-delete').text(name);
    $('.delete-button').attr('id', 'del-' + type + '-' + id);
    $('#form-item-del').attr('action', '/sites/'+ siteAlias + '/' + type + '/' + id);
  });
});
</script> 
{{-- Скрипт чекбоксов, сортировки и перетаскивания для таблицы --}}
@include('includes.table-scripts')
@endsection