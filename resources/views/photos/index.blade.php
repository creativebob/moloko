@extends('layouts.app')

@section('inhead')
{{-- Скрипты таблиц в шапке --}}
@include('includes.scripts.tablesorter-inhead')
@include('includes.scripts.fancybox-inhead')
@endsection

@section('title', $page_info->title . ' ' . $album->name)

@section('breadcrumbs', Breadcrumbs::render('section', $parent_page_info, $album, $page_info))

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'page_alias' => $parent_page_info->alias.'/'.$album->alias.'/'.$page_info->alias, 'class' => App\Photo::class, 'type' => 'section-table', 'name' => $album->name])
@endsection

@section('content')
{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="table-content tablesorter" id="content" data-sticky-container data-entity-alias="photos">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td">Фотография</th>
          <th class="td-name">Имя фото</th>
          <th class="td-date">Сведения</th>
          <th class="td-author">Автор</th>
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
        @if(!empty($photos))

        @foreach($photos as $photo)
        <tr class="item @if($photo->moderation == 1)no-moderation @endif" id="photos-{{ $photo->id }}" data-name="{{ $photo->name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox">
            <input type="checkbox" class="table-check" name="photo_id" id="check-{{ $photo->id }}"
            @if(!empty($filter['booklist']['booklists']['default']))
            @if (in_array($photo->id, $filter['booklist']['booklists']['default'])) checked 
            @endif
            @endif 
            ><label class="label-check" for="check-{{ $photo->id }}"></label></td>
            <td class="td">
              <a data-fancybox="photos" href="/storage/{{ $photo->company_id }}/media/albums/{{ $album->id }}/img/large/{{ $photo->name }}">
                <img src="/storage/{{ $photo->company_id }}/media/albums/{{ $album->id }}/img/small/{{ $photo->name }}" alt="Фотография альбома">
              </a>
            </td>
            <td class="td-name">
              <a href="/albums/{{ $album->alias }}/photos/{{ $photo->id }}/edit">{{ $photo->name }}</a>
            </td>
            <td class="td-extra-info">
              <ul>
                <li>Дата добавления: {{ date('d.m.Y', strtotime($photo->created_at)) }}</li>
                <li>Размер, Kb: {{ $photo->size }}</li>
              </ul>
            </td>
            <td class="td-author">@if(isset($photo->author->first_name)) {{ $photo->author->first_name . ' ' . $photo->author->second_name }} @endif
            </td>
            <td class="td-delete">
              @if ($photo->system_item != 1)
              @can('delete', $photo)
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

  @endsection

  @section('modals')
  {{-- Модалка удаления с refresh --}}
  @include('includes.modals.modal-delete')

  {{-- Модалка удаления с refresh --}}
  @include('includes.modals.modal-delete-ajax')

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
      $('#form-item-del').attr('action', '/albums/'+ alias + '/' + type + '/' + id);
    });
  });
</script> 
{{-- Скрипт чекбоксов, сортировки и перетаскивания для таблицы --}}
@include('includes.scripts.tablesorter-script')

{{-- Скрипт модалки удаления --}}
@include('includes.scripts.modal-delete-script')
@include('includes.scripts.delete-ajax-script')
@include('includes.scripts.sortable-table-script')
@endsection
