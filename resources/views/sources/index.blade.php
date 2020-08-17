@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $pageInfo->page_description }}" />

@endsection

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('content-count')
{{-- Количество элементов --}}
  @if(!empty($sources))
    {{ num_format($sources->total(), 0) }}
  @endif
@endsection

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['pageInfo' => $pageInfo, 'class' => App\Source::class, 'type' => 'table'])
@endsection

@section('content')

{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="content-table tablesorter" id="content" data-sticky-container data-entity-alias="sources">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-name">Название этапа</th>
          <th class="td-description">Описание этапа</th>
          <th class="td-company">Компания</th>
          <th class="td-author">Автор</th>

          <th class="td-control"></th>

          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
      @if(!empty($sources))
        @foreach($sources as $source)
        <tr class="item @if($source->moderation == 1)no-moderation @endif" id="sources-{{ $source->id }}" data-name="{{ $source->name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox">
            <input type="checkbox" class="table-check" name="" id="check-{{ $source->id }}"

              {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
              @if(!empty($filter['booklist']['booklists']['default']))
                {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
                @if (in_array($source->id, $filter['booklist']['booklists']['default'])) checked
              @endif
            @endif

            >
            <label class="label-check" for="check-{{ $source->id }}"></label></td>
          <td class="td-name">
            @can('update', $source)
            <a href="/admin/sources/{{ $source->id }}/edit">
            @endcan
            {{ $source->name }}
            @can('update', $source)
            </a>
            @endcan
          </td>
          <td class="td-description">{{ $source->description }}</td>
          <td class="td-company-id">@if(!empty($source->company->name)) {{ $source->company->name }} @else @if($source->system == null) Шаблон @else Системная @endif @endif</td>
          <td class="td-author">@if(isset($source->author->first_name)) {{ $source->author->first_name . ' ' . $source->author->second_name }} @endif</td>

          {{-- Элементы управления --}}
          @include('includes.control.table-td', ['item' => $source])

          <td class="td-delete">
            @if (($source->system !== 1) && ($source->company_id !== null))
              @can('delete', $source)
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
    <span class="pagination-title">Кол-во записей: {{ $sources->count() }}</span>
    {{ $sources->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
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
