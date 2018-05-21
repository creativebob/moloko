@extends('layouts.app')
 
@section('inhead')
<meta name="description" content="{{ $site->site_name }}" />
{{-- Скрипты таблиц в шапке --}}
  @include('includes.scripts.tablesorter-inhead')
@endsection

@section('title', $page_info->name . ' ' . $site->name)

@section('breadcrumbs', Breadcrumbs::render('section', $parent_page_info, $site, $page_info))

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'page_alias' => 'sites/'.$site->alias.'/'.$page_info->alias, 'class' => App\Page::class, 'type' => 'section-table', 'name' => $site->name])
@endsection
 
@section('content')
{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="table-content tablesorter" id="content" data-sticky-container data-entity-alias="pages">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"><div class="sprite icon-drop"></div></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-name">Название страницы</th>
          <th class="td-title">Заголовок</th>
          <th class="td-description">Описание</th>
          <th class="td-alias">Алиас</th>
          <th class="td-site-id">Сайт</th>
          <th class="td-author">Автор</th>
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
      @if(!empty($pages))
        @foreach($pages as $page)
        <tr class="item @if($page->moderation == 1)no-moderation @endif" id="pages-{{ $page->id }}" data-name="{{ $page->name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox"><input type="checkbox" class="table-check" name="" id="check-{{ $page->id }}"><label class="label-check" for="check-{{ $page->id }}"></label></td>
          <td class="td-name">
            @can('update', $page)
              <a href="/sites/{{ $page->site->alias }}/pages/{{ $page->alias }}/edit">
            @endcan
            {{ $page->name }}
            @can('update', $page)
              </a>
            @endcan
          </td>
          <td class="td-title">{{ $page->title }}</td>
          <td class="td-description">{{ $page->description }}</td>
          <td class="td-alias">{{ $page->alias }}</td>
          <td class="td-site-id">{{ $page->site->name or ' ... ' }}</td>
          <td class="td-author">@if(isset($page->author->first_name)) {{ $page->author->first_name . ' ' . $page->author->second_name }} @endif</td>
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
  var alias = '{{ $alias }}';
 // Мягкое удаление с refresh
  $(document).on('click', '[data-open="item-delete"]', function() {
    // находим описание сущности, id и название удаляемого элемента в родителе
    var parent = $(this).closest('.item');
    var type = parent.attr('id').split('-')[0];
    var id = parent.attr('id').split('-')[1];
    var name = parent.data('name');
    $('.title-delete').text(name);
    $('.delete-button').attr('id', 'del-' + type + '-' + id);
    $('#form-item-del').attr('action', '/sites/'+ alias + '/' + type + '/' + id);
  });
});
</script> 
{{-- Скрипт чекбоксов, сортировки и перетаскивания для таблицы --}}
@include('includes.scripts.tablesorter-script')
@endsection