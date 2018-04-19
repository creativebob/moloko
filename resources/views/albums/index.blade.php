@extends('layouts.app')
 
@section('inhead')
{{-- Скрипты таблиц в шапке --}}
  @include('includes.scripts.table-inhead')
@endsection

@section('title', $page_info->page_name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\User::class, 'type' => 'table'])
@endsection
 
@section('content')

{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="table-content tablesorter" id="content" data-sticky-container data-entity-alias="albums">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"><div class="sprite icon-drop"></div></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th>Обложка</th>
          <th class="td-album-name">Название альбомa</th>
          <th class="td-album-description">Описание</th>
          <th class="td-album-date">Сведения</th>
          <th class="td-album-company-id">Компания</th>
          <th class="td-album-author">Автор</th>

          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
      @if(!empty($albums))

        @foreach($albums as $album)
        <tr class="item @if($album->moderation == 1)no-moderation @endif" id="albums-{{ $album->id }}" data-name="{{ $album->album_name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox">

            <input type="checkbox" class="table-check" name="album_id" id="check-{{ $album->id }}"
            @if(!empty($filter['booklist']['booklists']['default']))
              @if (in_array($album->id, $filter['booklist']['booklists']['default'])) checked 
              @endif
            @endif 
            ><label class="label-check" for="check-{{ $album->id }}"></label></td>
          <td><img src="{{ $album->album_avatar }}" alt="Фотография альбома"></td>
          <td class="td-album-name">{{ $album->album_name }}</td>
          <td class="td-album-description">{{ $album->album_description }}</td>
          <td class="td-album-extra-info">
            <span>ID альбома: {{ $album->id }}</span><br>
            <span>Доступ: {{ $album->album_access }}</span><br>
            <span>Кол-во фотографий: {{ 3  }}</span><br>
            <span>Дата создания: {{ date('d.m.Y', strtotime($album->created_at)) }}</span><br>
            <span>Размер, Mb: {{ 56.23 }}</span><br>
          </td>
          <td class="td-album-company-id">@if(!empty($album->company->company_name)) {{ $album->company->company_name }} @else @if($album->system_item == null) Шаблон @else Системная @endif @endif</td>
          <td class="td-album-author">@if(isset($album->author->first_name)) {{ $album->author->first_name . ' ' . $album->author->second_name }} @endif</td>

          <td class="td-delete">
            @if (($album->system_item != 1) && ($user->god != 1))
              @can('delete', $album)
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
    <span class="pagination-title">Кол-во записей: {{ $albums->count() }}</span>
    {{ $albums->links() }}
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
