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
  @if(!empty($dealers))
    {{ num_format($dealers->total(), 0) }}
  @endif
@endsection

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\Dealer::class, 'type' => 'table'])
@endsection

@section('content')
{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="content-table tablesorter" id="content" data-sticky-container data-entity-alias="dealers">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-name" data-serversort="name">Название поставщика</th>
          <th class="td-address">Адрес</th>
          <th class="td-phone">Телефон</th>
          <th class="td-description">Описание</th>
          <th class="td-discount">Скидка</th>
          <th class="td-order-count">Кол-во заказов</th>
          <th class="td-user_id">Руководитель</th>
          <th class="td-control"></th>
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
        @if(!empty($dealers))
        @foreach($dealers as $dealer)
        <tr class="item @if($dealer->moderation == 1)no-moderation @endif" id="dealers-{{ $dealer->id }}" data-name="{{ $dealer->client->client->name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox">
            <input type="checkbox" class="table-check" name="dealer_id" id="check-{{ $dealer->id }}"

            {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
            @if(!empty($filter['booklist']['booklists']['default']))
            {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
            @if (in_array($dealer->id, $filter['booklist']['booklists']['default'])) checked
            @endif
            @endif
            ><label class="label-check" for="check-{{ $dealer->id }}"></label>
          </td>
          <td class="td-name">
            @php
            $edit = 0;
            @endphp
            @can('update', $dealer)
            @php
            $edit = 1;
            @endphp
            @endcan
            @if($edit == 1)
            <a href="dealers/{{ $dealer->id }}/edit">
              @endif
              {{ $dealer->client->client->name or '' }} ({{ $dealer->client->client->legal_form->name or '' }})
              @if($edit == 1)
            </a>
            @endif
          </td>
          {{-- Если пользователь бог, то показываем для него переключатель на компанию --}}
          <td class="td-address">@if(!empty($dealer->client->client->location->address)){{ $dealer->client->client->location->address }}@endif </td>
          <td class="td-phone">{{ isset($dealer->client->client->main_phone->phone) ? decorPhone($dealer->client->client->main_phone->phone) : 'Номер не указан' }}</td>

          <td class="td-description">@if(!empty($dealer->description)){{ $dealer->description }}@endif </td>
          <td class="td-discount">@if(!empty($dealer->discount)){{ $dealer->discount }} %@endif </td>
          <td class="td-order-counts">@if(!empty($dealer->client->orders)){{ $dealer->client->orders->count() }} @endif </td>

          <td class="td-user_id">{{ $dealer->client->client->director->user->name or ' ... ' }} </td>

          {{-- Элементы управления --}}
          @include('includes.control.table-td', ['item' => $dealer])

          <td class="td-delete">
            @if ($dealer->system_item != 1)
            @can('delete', $dealer)
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
    <span class="pagination-title">Кол-во записей: {{ $dealers->count() }}</span>
    {{ $dealers->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
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
{{-- Скрипт сортировки и перетаскивания для таблицы --}}
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