@extends('layouts.app')
 
@section('inhead')
{{-- Скрипты таблиц в шапке --}}
  @include('includes.scripts.table-inhead')
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
          <th class="td-drop"><div class="sprite icon-drop"></div></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-photo">Фотография</th>
          <th class="td-photo-name">Имя фото</th>
          <th class="td-photo-date">Сведения</th>
          <th class="td-photo-author">Автор</th>
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
          <td class="td-photo"><img src="{{ $photo->path }}" alt="Фотография альбома"></td>
          <td class="td-photo-name">{{ $photo->name }}</td>
          <td class="td-photo-extra-info">
            <span>ID Фото: {{ $photo->id }}</span><br>
            <span>Дата добавления: {{ date('d.m.Y', strtotime($photo->created_at)) }}</span><br>
            <span>Размер, Kb: {{ $photo->size }}</span><br>
          </td>
          <td class="td-photo-author">@if(isset($photo->author->first_name)) {{ $photo->author->first_name . ' ' . $photo->author->second_name }} @endif</td>

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

{{-- Pagination --}}
<div class="grid-x" id="pagination">
  <div class="small-6 cell pagination-head">
    <span class="pagination-title">Кол-во записей: {{ $photos->count() }}</span>
    {{ $photos->links() }}
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
  @include('includes.scripts.table-scripts')

  {{-- Скрипт модалки удаления --}}
  @include('includes.scripts.modal-delete-script')
  @include('includes.scripts.delete-ajax-script')

@endsection
