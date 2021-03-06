@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $pageInfo->description }}" />

@endsection

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('content-count')
{{-- Количество элементов --}}
  @if(!empty($dealers))
    {{ num_format($dealers->total(), 0) }}
  @endif
@endsection

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['pageInfo' => $pageInfo, 'class' => App\Dealer::class, 'type' => 'table'])
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
          <th class="td-name" data-serversort="name">Название дилера</th>
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
        <tr class="item @if($dealer->moderation == 1)no-moderation @endif" id="dealers-{{ $dealer->id }}" data-name="{{ $dealer->client->clientable->name }}">
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
              {{ $dealer->client->clientable->name ?? '' }} ({{ $dealer->client->clientable->legal_form->name ?? '' }})
              @if($edit == 1)
            </a>
            <br>
            <span class="tiny-text">@if(!empty($dealer->client->clientable->location->address)){{ $dealer->client->clientable->location->address }}@endif</span>
            @endif
          </td>
          {{-- Если пользователь бог, то показываем для него переключатель на компанию --}}
          <td class="td-phone">{{ isset($dealer->client->clientable->main_phone->phone) ? decorPhone($dealer->client->clientable->main_phone->phone) : 'Номер не указан' }}
          <br>
          <span class="tiny-text">{{ $dealer->client->clientable->email }}</span>
          </td>

          <td class="td-description">@if(!empty($dealer->description_dealer)){{ $dealer->description_dealer }}@endif </td>
          <td class="td-discount">@if(!empty($dealer->discount)){{ $dealer->discount }} %@endif </td>
          <td class="td-order-counts">@if(!empty($dealer->client->orders)){{ $dealer->client->orders->count() }} @endif </td>

          <td class="td-user_id">
            @if(isset($dealer->client->clientable->director))
              {{ $dealer->client->clientable->director->user->name ?? ' ... ' }}
            @else
              {{ $dealer->client->clientable->name ?? ' ... ' }}
            @endif
          </td>

          {{-- Элементы управления --}}
          @include('includes.control.table-td', ['item' => $dealer])

          <td class="td-delete">
            @if ($dealer->system != 1)
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

@push('scripts')
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

@endpush
