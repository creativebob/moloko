@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $page_info->page_description }}" />
{{-- Скрипты таблиц в шапке --}}
  @include('includes.scripts.tablesorter-inhead')
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('content-count')
{{-- Количество элементов --}}
  @if(!empty($positions))
    {{ num_format($positions->total(), 0) }}
  @endif
@endsection

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\Position::class, 'type' => 'table'])
@endsection

@section('content')

{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="content-table tablesorter" id="content" data-sticky-container data-entity-alias="positions">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-name">Название должности</th>
          <th class="td-page">Alias страницы</th>
          <th class="td-company">Компания</th>
          <th class="td-author">Автор</th>

          <th class="td-control"></th>

          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
      @if(!empty($positions))
        @foreach($positions as $position)
        <tr class="item @if($position->moderation == 1)no-moderation @endif" id="positions-{{ $position->id }}" data-name="{{ $position->name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox">
            <input type="checkbox" class="table-check" name="" id="check-{{ $position->id }}"

              {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
              @if(!empty($filter['booklist']['booklists']['default']))
                {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
                @if (in_array($position->id, $filter['booklist']['booklists']['default'])) checked
              @endif
            @endif

            >
            <label class="label-check" for="check-{{ $position->id }}"></label></td>
          <td class="td-name">
            @can('update', $position)
            <a href="/admin/positions/{{ $position->id }}/edit">
            @endcan
            {{ $position->name }}
            @can('update', $position)
            </a>
            @endcan
          </td>
          <td class="td-page">{{ $position->page->alias }}</td>
          <td class="td-company-id">@if(!empty($position->company->name)) {{ $position->company->name }} @else @if($position->system == null) Шаблон @else Системная @endif @endif</td>
          <td class="td-author">@if(isset($position->author->first_name)) {{ $position->author->first_name . ' ' . $position->author->second_name }} @endif</td>

          {{-- Элементы управления --}}
          @include('includes.control.table-td', ['item' => $position])

          <td class="td-delete">
            @if (($position->system !== 1) && ($position->company_id !== null))
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
    {{ $positions->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
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
@include('includes.scripts.sortable-table-script')

{{-- Скрипт отображения на сайте --}}
@include('includes.scripts.ajax-display')

{{-- Скрипт системной записи --}}
@include('includes.scripts.ajax-system')

{{-- Скрипт чекбоксов --}}
@include('includes.scripts.checkbox-control')

{{-- Скрипт модалки удаления --}}
@include('includes.scripts.modal-delete-script')
@endsection