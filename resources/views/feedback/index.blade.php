@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $pageInfo->page_description }}" />

@endsection

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('content-count')
{{-- Количество элементов --}}
  @if(!empty($feedback))
    {{ num_format($feedback->total(), 0) }}
  @endif
@endsection

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['pageInfo' => $pageInfo, 'class' => App\Feedback::class, 'type' => 'table'])
@endsection

@section('content')
{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="content-table tablesorter" id="content" data-sticky-container data-entity-alias="feedback">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-call-date">Дата</th>
          <th class="td-body">Текст отзыва</th>
          <th class="td-person">Автор отзыва</th>
          <th class="td-job">Деятельность</th>
          <th class="td-site">Сайт</th>
          <th class="td-control"></th>
          <th class="td-delete"></th>
      </tr>
  </thead>
  <tbody data-tbodyId="1" class="tbody-width">
    @if(!empty($feedback))
    @foreach($feedback as $cur_feedback)
    <tr class="item @if($cur_feedback->moderation == 1)no-moderation @endif" id="feedback-{{ $cur_feedback->id }}" data-name="{{ $cur_feedback->name }}">
      <td class="td-drop"><div class="sprite icon-drop"></div></td>
      <td class="td-checkbox checkbox">
        <input type="checkbox" class="table-check" name="feedback_id" id="check-{{ $cur_feedback->id }}"

        {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
        @if(!empty($filter['booklist']['booklists']['default']))
        {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
        @if (in_array($cur_feedback->id, $filter['booklist']['booklists']['default'])) checked
        @endif
        @endif
        ><label class="label-check" for="check-{{ $cur_feedback->id }}"></label>
    </td>
    <td class="td-call-date">
        @php
        $edit = 0;
        @endphp
        @can('update', $cur_feedback)
        @php
        $edit = 1;
        @endphp
        @endcan
        @if($edit == 1)
        <a href="/admin/feedback/{{ $cur_feedback->id }}/edit">
          @endif
          {{ $cur_feedback->call_date->format('d.m.Y') }}
          @if($edit == 1)
        </a>
      @endif
    </td>
  <td class="td-body">{!! $cur_feedback->body !!} </td>
  <td class="td-person">{{ $cur_feedback->person }} </td>
  <td class="td-job">{{ $cur_feedback->job }} </td>
  <td class="td-site">{{ $cur_feedback->site->name or '' }} </td>

  {{-- Элементы управления --}}
  @include('includes.control.table-td', ['item' => $cur_feedback])

  <td class="td-delete">
    @if ($cur_feedback->system != 1)
    @can('delete', $cur_feedback)
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
    <span class="pagination-title">Кол-во записей: {{ $feedback->count() }}</span>
    {{ $feedback->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
</div>
</div>
@endsection

@section('modals')
{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete')

{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete-ajax')

@endsection

@push('scripts')

{{-- Скрипт сортировки и перетаскивания для таблицы --}}
@include('includes.scripts.tablesorter-script')
@include('includes.scripts.sortable-table-script')
@include('includes.scripts.pickmeup-script')

{{-- Скрипт отображения на сайте --}}
@include('includes.scripts.ajax-display')

{{-- Скрипт системной записи --}}
@include('includes.scripts.ajax-system')

{{-- Скрипт чекбоксов --}}
@include('includes.scripts.checkbox-control')

{{-- Скрипт модалки удаления --}}
@include('includes.scripts.modal-delete-script')
@include('includes.scripts.delete-ajax-script')

@endpush
