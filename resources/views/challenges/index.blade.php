@extends('layouts.app')

@section('inhead')
{{-- Скрипты таблиц в шапке --}}
@include('includes.scripts.tablesorter-inhead')
@include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('content-count')
{{-- Количество элементов --}}
  @if(!empty($challenges_page))
    {{ num_format($challenges_page->total(), 0) }}
  @endif
@endsection

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\Challenge::class, 'type' => 'table'])
@endsection

@section('content')

{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="table-content tablesorter" id="content" data-sticky-container data-entity-alias="challenges">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-type">Тип задачи</th>
          <th class="td-description">Описание задачи</th>
          <th class="td-appointed">Ответственный</th>
          <th class="td-deadline-date">Дата дедлайна</th>
          <th class="td-author">Автор</th>
          <th class="td-status"></th>
          <th class="td-control"></th>
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
        @if(!empty($challenges_page))
        @foreach($challenges_page as $challenge)
        <tr class="item @if($challenge->moderation == 1)no-moderation @endif" id="challenges-{{ $challenge->id }}" data-name="{{ $challenge->body }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox">
            <input type="checkbox" class="table-check" name="" id="check-{{ $challenge->id }}"><label class="label-check" for="check-{{ $challenge->id }}"></label>
          </td>
          <td class="td-type">{{ $challenge->challenge_type->name }} по обращению: <a href="/admin/leads/{{ $challenge->challenges->id }}/edit">{{ $challenge->challenges->case_number }}</a></td>
          <td class="td-description">{{ $challenge->description }}</td>
          <td class="td-appointed">{{ $challenge->appointed->first_name . ' ' . $challenge->appointed->second_name }}</td>
          <td class="td-deadline-date">{{ isset($challenge->deadline_date) ? $challenge->deadline_date->format('d.m.Y H:i') : 'Не указана' }}</td>
          <td class="td-author">{{ isset($challenge->author->first_name) ? $challenge->author->first_name . ' ' . $challenge->author->second_name : ' ' }}</td>

          <td class="td-status">
            {{ $challenge->status_result }}
          </td>

          {{-- Элементы управления --}}
          @include('includes.control.table-td', ['item' => $challenge])

          <td class="td-delete">
            @if (($challenge->system_item !== 1) && ($challenge->company_id !== null))
            @can('delete', $challenge)
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
    <span class="pagination-title">Кол-во записей: {{ $challenges_page->count() }}</span>
    {{ $challenges_page->links() }}
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
@include('includes.scripts.pickmeup-script')

{{-- Скрипт отображения на сайте --}}
@include('includes.scripts.ajax-display')

{{-- Скрипт системной записи --}}
@include('includes.scripts.ajax-system')

{{-- Скрипт модалки удаления --}}
@include('includes.scripts.modal-delete-script')
@endsection