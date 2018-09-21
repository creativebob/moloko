@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $site->site_name }}" />
{{-- Скрипты таблиц в шапке --}}
@include('includes.scripts.tablesorter-inhead')
@endsection

@section('title', $page_info->name . ' ' . $site->name)

@section('breadcrumbs', Breadcrumbs::render('section', $parent_page_info, $site, $page_info))

@section('content-count')
{{-- Количество элементов --}}
  @if(!empty($news))
    {{ num_format($news->total(), 0) }}
  @endif
@endsection

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'page_alias' => 'sites/'.$site->alias.'/'.$page_info->alias, 'class' => App\News::class, 'type' => 'section-table', 'name' => $site->name])
@endsection

@section('content')
{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="table-content tablesorter content-news" id="content" data-sticky-container data-entity-alias="news">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-photo">Фото</th>
          <th class="td-name">Название новости</th>
          <!--           <th class="td-news-title">Заголовок</th> -->
          <th class="td-preview">Короткая новость</th>
          <th class="td-info">Инфо</th>
          <th class="td-date-publish">Срок публикации</th>
          <th class="td-author">Автор</th>
          <th class="td-control"></th>
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
        @if(!empty($news))
        @foreach($news as $cur_news)
        <tr class="item @if($cur_news->moderation == 1)no-moderation @endif" id="news-{{ $cur_news->id }}" data-name="{{ $cur_news->name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox">
            <input type="checkbox" class="table-check" name="cur_news_id" id="check-{{ $cur_news->id }}"
            {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
            @if(!empty($filter['booklist']['booklists']['default']))
            {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
            @if (in_array($cur_news->id, $filter['booklist']['booklists']['default'])) checked 
            @endif
            @endif
            >

            <label class="label-check" for="check-{{ $cur_news->id }}">

            </label></td>
            <td class="td-photo">
              <img src="{{ isset($cur_news->photo_id) ? '/storage/'.$cur_news->company_id.'/media/news/'.$cur_news->id.'/img/small/'.$cur_news->photo->name : '/img/plug/news_small_default_color.jpg' }}" alt="{{ isset($cur_news->photo_id) ? $cur_news->name : 'Нет фото' }}">
            </td>
            <td class="td-name">
              @can('update', $cur_news)
              <a href="/admin/sites/{{ $cur_news->site->alias }}/news/{{ $cur_news->alias }}/edit">
                @endcan
                {{ $cur_news->name }}
                @can('update', $cur_news)
              </a>
              @endcan
            </td>
            {{-- <td class="td-title">{{ $cur_news->title }}</td> --}}
            <td class="td-preview">{{ str_limit($cur_news->preview, 150) }}</td>

            <td class="td-info">
<!--             <span>Сайт:&nbsp;{{ $cur_news->site->name or ' ... ' }}</span>
  <br><br> -->
  {{-- <span>Домен:&nbsp;{{ $cur_news->site->domen or ' ... ' }}</span><br> --}}
  @if ($cur_news->display == 1)
  <span>Алиас:&nbsp;<a href="http://{{ $cur_news->site->alias }}/{{ $cur_news->company->location->city->alias }}/news/{{ $cur_news->alias }}" target="_blank">{{ $cur_news->alias }}</a></span>
  @else
  не отображается
  @endif
  <br>
  {{-- 
  <span title="{{ $cur_news->cities->implode('name', ', ') }}">Города:&nbsp;
    @if (count($cur_news->cities) > 0)
    @if (count($cur_news->cities) == 1)
    {{$cur_news->cities->first()->name or ' ' }}
    @else 
    {{$cur_news->cities->first()->name or ' ' }}&nbsp;и&nbsp;др.
    @endif
    @else
    Нет
    @endif
  </span>
  --}}
  <td class="td-date-publish">
    <span>{{ $cur_news->publish_begin_date }} {{ getWeekDay($cur_news->publish_begin_date, 1) }}</span><br>
    <span>{{ $cur_news->publish_end_date }} {{ getWeekDay($cur_news->publish_end_date, 1) }}</span>
  </td>
  <td class="td-author">@if(isset($cur_news->author->first_name)) {{ $cur_news->author->first_name . ' ' . $cur_news->author->second_name }} @endif</td>

  {{-- Элементы управления --}}
            @include('includes.control.table-td', ['item' => $cur_news])
            
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

{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete-ajax')

@endsection

@section('scripts')
{{-- Скрипт чекбоксов, сортировки и перетаскивания для таблицы --}}
@include('includes.scripts.tablesorter-script')
@include('includes.scripts.sortable-table-script')

{{-- Скрипт отображения на сайте --}}
@include('includes.scripts.ajax-display')

{{-- Скрипт системной записи --}}
@include('includes.scripts.ajax-system')

{{-- Скрипт чекбоксов --}}
@include('includes.scripts.checkbox-control')

{{-- Скрипт модалки удаления --}}
@include('includes.scripts.modal-delete-script')
@include('includes.scripts.delete-ajax-script')

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
    $('#form-item-del').attr('action', '/admin/sites/'+ alias + '/' + type + '/' + id);
  });
});
</script>
@endsection



