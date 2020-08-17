@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $pageInfo->page_description }}" />

@endsection

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('content-count')
{{-- Количество элементов --}}
  @if(!empty($extra_requisites))
    {{ num_format($extra_requisites->total(), 0) }}
  @endif
@endsection

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['pageInfo' => $pageInfo, 'class' => App\ExtraRequisite::class, 'type' => 'table'])
@endsection

@section('content')
{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="content-table tablesorter" id="content" data-sticky-container data-entity-alias="extra_requisites">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-name" data-serversort="name">Название реквизита</th>
          <th class="td-description">Описание</th>
          <th class="td-control"></th>
          <th class="td-delete"></th>
      </tr>
  </thead>
  <tbody data-tbodyId="1" class="tbody-width">
    @if(!empty($extra_requisites))
    @foreach($extra_requisites as $extra_requisite)
    <tr class="item @if($user->extra_requisite_id == $extra_requisite->id)active @endif  @if($extra_requisite->moderation == 1)no-moderation @endif" id="extra_requisites-{{ $extra_requisite->id }}" data-name="{{ $extra_requisite->name }}">
      <td class="td-drop"><div class="sprite icon-drop"></div></td>
      <td class="td-checkbox checkbox">
        <input type="checkbox" class="table-check" name="extra_requisite_id" id="check-{{ $extra_requisite->id }}"

        {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
        @if(!empty($filter['booklist']['booklists']['default']))
        {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
        @if (in_array($extra_requisite->id, $filter['booklist']['booklists']['default'])) checked
        @endif
        @endif
        ><label class="label-check" for="check-{{ $extra_requisite->id }}"></label>
    </td>
    <td class="td-name">
        @php
        $edit = 0;
        @endphp
        @can('update', $extra_requisite)
        @php
        $edit = 1;
        @endphp
        @endcan
        @if($edit == 1)
        <a href="/admin/extra_requisites/{{ $extra_requisite->id }}/edit">
          @endif
          {{ $extra_requisite->name }}
          @if($edit == 1)
      </a>
      @endif
  </td>
  <td class="td-description">{{ $extra_requisite->description }} </td>

  {{-- Элементы управления --}}
  @include('includes.control.table-td', ['item' => $extra_requisite])

  <td class="td-delete">
    @if ($extra_requisite->system != 1)
    @can('delete', $extra_requisite)
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
    <span class="pagination-title">Кол-во записей: {{ $extra_requisites->count() }}</span>
    {{ $extra_requisites->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
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
