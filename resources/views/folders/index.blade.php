@extends('layouts.app')

@section('inhead')

@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\Folder::class, 'type' => 'table'])
@endsection

@section('content')

{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="content-table tablesorter" id="content" data-sticky-container data-entity-alias="folders">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-name">Имя папки</th>
          <th class="td-alias">Алиас</th>
          <th class="td-url">Путь</th>
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
      @if(!empty($folders))
        @foreach($folders as $folder)
        <tr class="item @if(Auth::user()->folder_id == $folder->id)active @endif  @if($folder->moderation == 1)no-moderation @endif" id="folders-{{ $folder->id }}" data-name="{{ $folder->folder_name }}">
          <td class="td-drop"></td>
          <td class="td-checkbox checkbox"><input type="checkbox" class="table-check" name="" id="check-{{ $folder->id }}"><label class="label-check" for="check-{{ $folder->id }}"></label></td>
          <td class="td-name">
            @can('update', $folder)
            <a href="/admin/folders/{{ $folder->id }}/edit">
            @endcan
            {{ $folder->folder_name }}
            @can('update', $folder)
            </a>
            @endcan
          <td class="td-alias">{{ $folder->folder_alias }} </td>
          <td class="td-url">{{ $folder->folder_url }} </td>
          <td class="td-delete">
          @if ($folder->system !== 1)
            @can('delete', $folder)
            <a class="icon-delete sprite" data-open="item-delete"></a>
            @endcan
          @endif
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
    <span class="pagination-title">Кол-во записей: {{ $folders->count() }}</span>
    {{ $folders->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
  </div>
</div>
@endsection

@section('modals')
{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete')
@endsection

@section('scripts')
{{-- Скрипт чекбоксов, сортировки и перетаскивания для таблицы --}}
@include('includes.scripts.tablesorter-script')

{{-- Скрипт модалки удаления --}}
@include('includes.scripts.modal-delete-script')
@endsection