@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $pageInfo->page_description }}" />

@endsection

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('content-count')
{{-- Количество элементов --}}
  @if(!empty($posts))
    {{ num_format($posts->total(), 0) }}
  @endif
@endsection

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['pageInfo' => $pageInfo, 'class' => App\Place::class, 'type' => 'table'])
@endsection

@section('content')
{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="content-table tablesorter content-posts" id="content" data-sticky-container data-entity-alias="posts">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-photo">Фото</th>
          <th class="td-name">Заголовок поста</th>
          <!--           <th class="td-posts-title">Заголовок</th> -->
          <th class="td-preview">Коротко</th>
          <th class="td-info">Инфо</th>
          <th class="td-date-publish">Срок публикации</th>
          <th class="td-author">Автор</th>
          <th class="td-control"></th>
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
        @if(!empty($posts))
        @foreach($posts as $post)
        <tr class="item @if($post->moderation == 1)no-moderation @endif" id="posts-{{ $post->id }}" data-name="{{ $post->name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox">
            <input type="checkbox" class="table-check" name="post_id" id="check-{{ $post->id }}"
            {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
            @if(!empty($filter['booklist']['booklists']['default']))
            {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
            @if (in_array($post->id, $filter['booklist']['booklists']['default'])) checked
            @endif
            @endif
            >

            <label class="label-check" for="check-{{ $post->id }}">

            </label></td>
            <td class="td-photo">
              <img src="{{ isset($post->photo_id) ? '/storage/'.$post->company_id.'/media/posts/'.$post->id.'/img/small/'.$post->photo->name : '/crm/img/plug/news_small_default_color.jpg' }}" alt="{{ isset($post->photo_id) ? $post->name : 'Нет фото' }}">
            </td>
            <td class="td-name">
              @can('update', $post)
              <a href="/admin/posts/{{ $post->id }}/edit">
                @endcan
                {{ $post->name }}
                @can('update', $post)
              </a>
              @endcan
            </td>
            {{-- <td class="td-title">{{ $post->title }}</td> --}}
            <td class="td-preview">{{ str_limit($post->preview, 150) }}</td>

            <td class="td-info">

  <td class="td-date-publish">
    <span>{{ $post->publish_begin_date }} {{ getWeekDay($post->publish_begin_date, 1) }}</span><br>
    <span>
      @if(!empty($post->publish_end_date))
      {{ $post->publish_end_date }} {{ getWeekDay($post->publish_end_date, 1) }}
      @else
      <!--       Без срока -->
      @endif
    </span>
  </td>
  <td class="td-author">@if(isset($post->author->first_name)) {{ $post->author->first_name . ' ' . $post->author->second_name }} @endif</td>

  {{-- Элементы управления --}}
            @include('includes.control.table-td', ['item' => $post])

  <td class="td-delete">
    @if ($post->system != 1)
    @can('delete', $post)
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
    <span class="pagination-title">Кол-во записей: {{ $posts->count() }}</span>
    {{ $posts->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
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
@endsection



