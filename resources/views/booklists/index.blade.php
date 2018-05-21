@extends('layouts.app')
 
@section('inhead')
{{-- Скрипты таблиц в шапке --}}
  @include('includes.scripts.tablesorter-inhead')
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\Booklists::class, 'type' => 'table'])
@endsection
 
@section('content')

{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="table-content tablesorter" id="content" data-sticky-container data-entity-alias="booklists">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"><div class="sprite icon-drop"></div></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-name">Имя списка</th>
          <th class="td-description">Описание списка</th>
          <th class="td-entity-id">Имя сущности</th>
          <th class="td-company-name">Компания</th>
          <th class="td-author">Автор</th>
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
      @if(!empty($booklists))
        @foreach($booklists as $booklist)
        <tr class="item @if($booklist->moderation == 1)no-moderation @endif" id="booklists-{{ $booklist->id }}" data-name="{{ $booklist->booklist_name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox"><input type="checkbox" class="table-check" name="" id="check-{{ $booklist->id }}"><label class="label-check" for="check-{{ $booklist->id }}"></label></td>
          <td class="td-name">{{ $booklist->booklist_name }} </td>
          <td class="td-description">{{ $booklist->booklist_description }}</td>
          <td class="td-entity-name">{{ $booklist->entity->entity_name }}</td>
          <td class="td-booklist-company-id">@if(!empty($booklist->company->name)) {{ $booklist->company->name }} @else @if($booklist->system_item == null) Шаблон @else Системная @endif @endif</td>
          <td class="td-author">@if(isset($booklist->author->first_name)) {{ $booklist->author->first_name . ' ' . $booklist->author->second_name }} @endif</td>
          <td class="td-delete"><a class="icon-delete sprite" data-open="item-delete"></a></td>       
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
    <span class="pagination-title">Кол-во записей: {{ $booklists->count() }}</span>
    {{ $booklists->links() }}
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
