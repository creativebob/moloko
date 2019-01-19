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
  @if(!empty($claims))
    {{ num_format($claims->total(), 0) }}
  @endif
@endsection

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\Claim::class, 'type' => 'table'])
@endsection

@section('content')
{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="content-table tablesorter claims" id="content" data-sticky-container data-entity-alias="claims">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-date">Дата</th>
          <th class="td-lead-case-number">Обращение</th>
          <th class="td-serial-number">Номер</th>
          <th class="td-body">Описание проблемы</th>
          <th class="td-status">Статус</th>
          <th class="td-lead-number-case">№ заказа</th>
          <th class="td-manager">Менеджер</th>
          <th class="td-control"></th>
          <th class="td-delete"></th>
      </tr>
  </thead>
  <tbody data-tbodyId="1" class="tbody-width">
    @if(!empty($claims))
    @foreach($claims as $claim)
    <tr class="item @if($user->claim_id == $claim->id)active @endif  @if($claim->moderation == 1)no-moderation @endif" id="claims-{{ $claim->id }}" data-name="{{ $claim->name }}">
      <td class="td-drop"><div class="sprite icon-drop"></div></td>
      <td class="td-checkbox checkbox">
        <input type="checkbox" class="table-check" name="claim_id" id="check-{{ $claim->id }}"

        {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
        @if(!empty($filter['booklist']['booklists']['default']))
        {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
        @if (in_array($claim->id, $filter['booklist']['booklists']['default'])) checked
        @endif
        @endif
        ><label class="label-check" for="check-{{ $claim->id }}"></label>
    </td>
    <td class="td-date">
      <span>{{ $claim->created_at->format('d.m.Y') }}</span><br>
      <span class="tiny-text">{{ $claim->created_at->format('H:i') }}</span>
    </td>
    <td class="td-lead-case-number">{{ $claim->source_lead->case_number or '' }}</td>
    <td class="td-serial-number">

    @if(empty($claim->serial_number))
      {{ $claim->old_claim_id or '' }}
    @else
      {{ $claim->serial_number or 'Нет номера!' }}
    @endif
    </td>
    <td class="td-body">{{ $claim->body }}</td>
    <td class="td-status">
      @if($claim->status == 1)
        В работе
      @else
        Отработана
      @endif
    </td>
    <td class="td-case-number">
        <a href="/admin/leads/{{ $claim->lead_id }}/edit">
            {{ $claim->lead->case_number or ' ' }}
        </a>
    </td>

    <td class="td-manager">
      @if(!empty($claim->manager->first_name))
      {{ $claim->manager->first_name . ' ' . $claim->manager->second_name }}
      @else
      Не назначен
      @endif
    </td>

  {{-- Элементы управления --}}
  @include('includes.control.table-td', ['item' => $claim])

  <td class="td-delete">
    @if ($claim->system_item != 1)
    @can('delete', $claim)
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
    <span class="pagination-title">Кол-во записей: {{ $claims->count() }}</span>
    {{ $claims->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
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