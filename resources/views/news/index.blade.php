@extends('layouts.app')
 
@section('inhead')
<meta name="description" content="{{ $site->site_name }}" />
{{-- Скрипты таблиц в шапке --}}
  @include('includes.scripts.table-inhead')
@endsection

@section('title', $page_info->name . ' ' . $site->name)

@section('breadcrumbs', Breadcrumbs::render('section', $parent_page_info, $site, $page_info))

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'page_alias' => 'sites/'.$site->alias.'/'.$page_info->alias, 'class' => App\News::class, 'type' => 'section-table', 'name' => $site->name])
@endsection
 
@section('content')
{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="table-content tablesorter" id="content" data-sticky-container>
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"><div class="sprite icon-drop"></div></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-news-name">Название новости</th>
          <th class="td-news-title">Заголовок</th>
          <th class="td-news-preview">Превью</th>
          <th class="td-news-photo">Превью</th>
          <th class="td-news-alias">Алиас</th>
          <th class="td-site-name">Сайт</th>
          <th class="td-view">Просмотр</th>
          <th class="td-news-author">Автор</th>
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
      @if(!empty($news))
        @foreach($news as $cur_news)
        <tr class="item @if($cur_news->moderation == 1)no-moderation @endif" id="news-{{ $cur_news->id }}" data-name="{{ $cur_news->name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox"><input type="checkbox" class="table-check" name="" id="check-{{ $cur_news->id }}"><label class="label-check" for="check-{{ $cur_news->id }}"></label></td>
          <td class="td-news-name">
            @can('update', $cur_news)
              <a href="/sites/{{ $cur_news->site->alias }}/news/{{ $cur_news->alias }}/edit">
            @endcan
            {{ $cur_news->name }}
            @can('update', $cur_news)
              </a>
            @endcan
          </td>
          <td class="td-news-title">{{ $cur_news->title }}</td>
          <td class="td-news-preview">{{ str_limit($cur_news->preview, 50) }}</td>
          <td class="td-news-photo">
          @if (isset($cur_news->photo_id))
            <img src="/storage/{{ $cur_news->company->id }}/media/news/{{ $cur_news->id }}/small/{{ $cur_news->photo->name }}">
          @else
            Нет превью
          @endif</td>
          <td class="td-news-alias">{{ $cur_news->alias }}</td>
          <td class="td-site-name">{{ $cur_news->site->name or ' ... ' }}</td>
          <td class="td-view">
            <a class="button" href="http://{{ $cur_news->site->alias }}/{{ $cur_news->cities[0]->alias }}/news/{{ $cur_news->alias }}" target="_blank">Чек</a>
          </td>
          <td class="td-news-author">@if(isset($cur_news->author->first_name)) {{ $cur_news->author->first_name . ' ' . $cur_news->author->second_name }} @endif</td>
          <td class="td-delete">
            @if ($cur_news->system_item != 1)
              @can('delete', $cur_news)
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
    <span class="pagination-title">Кол-во записей: {{ $news->count() }}</span>
    {{ $news->links() }}
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
@include('includes.scripts.table-scripts')
@include('includes.scripts.table-sort')
@endsection