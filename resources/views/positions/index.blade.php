@extends('layouts.app')
 
@section('inhead')
<meta name="description" content="{{ $page_info->page_description }}" />
{{-- Скрипты таблиц в шапке --}}
  @include('includes.scripts.table-inhead')
@endsection

@section('title', $page_info->page_name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\Position::class, 'type' => 'table'])
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
          <th class="td-position-name">Название должности</th>
          <th class="td-position-page">Alias страницы</th>
          <th class="td-position-company">Компания</th>
          <th class="td-position-author">Автор</th>
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
      @if(!empty($positions))
        @foreach($positions as $position)
        <tr class="item @if($position->moderation == 1)no-moderation @endif" id="positions-{{ $position->id }}" data-name="{{ $position->position_name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox"><input type="checkbox" class="table-check" name="" id="check-{{ $position->id }}"><label class="label-check" for="check-{{ $position->id }}"></label></td>
          <td class="td-position-name">
            @can('update', $position)
            <a href="/positions/{{ $position->id }}/edit">
            @endcan
            {{ $position->position_name }}
            @can('update', $position)
            </a> 
            @endcan
          </td>
          <td class="td-position-page">{{ $position->page->page_alias }}</td>
          <td class="td-position-company-id">@if(!empty($position->company->company_name)) {{ $position->company->company_name }} @else @if($position->system_item == null) Шаблон @else Системная @endif @endif</td>
          <td class="td-position-author">@if(isset($position->author->first_name)) {{ $position->author->first_name . ' ' . $position->author->second_name }} @endif</td>
          <td class="td-delete">
            @if (($position->system_item !== 1) && ($position->company_id !== null))
              @can('delete', $position)
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
    <span class="pagination-title">Кол-во записей: {{ $positions->count() }}</span>
    {{ $positions->links() }}
  </div>
</div>
@endsection

@section('modals')
{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete')
@endsection

@section('scripts')
{{-- Скрипт чекбоксов, сортировки и перетаскивания для таблицы --}}
@include('includes.scripts.table-scripts')

{{-- Скрипт модалки удаления --}}
@include('includes.scripts.modal-delete-script')
@endsection