@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $pageInfo->description }}" />

@endsection

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('content-count')
{{-- Количество элементов --}}
  @if(!empty($accounts))
    {{ num_format($accounts->total(), 0) }}
  @endif
@endsection

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['pageInfo' => $pageInfo, 'class' => App\Account::class, 'type' => 'table'])
@endsection

@section('content')
{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="content-table tablesorter" id="content" data-sticky-container data-entity-alias="accounts">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-name" data-serversort="name">Рабочее имя аккаунта</th>
          <th class="td-description">Описание</th>
          <th class="td-source">Сервис</th>
          <th class="td-login">Логин</th>
          <th class="td-alias">Алиас</th>
          <th class="td-control"></th>
          <th class="td-delete"></th>
      </tr>
  </thead>
  <tbody data-tbodyId="1" class="tbody-width">
    @if(!empty($accounts))
    @foreach($accounts as $account)
    <tr class="item @if($user->account_id == $account->id)active @endif  @if($account->moderation == 1)no-moderation @endif" id="accounts-{{ $account->id }}" data-name="{{ $account->name }}">
      <td class="td-drop"><div class="sprite icon-drop"></div></td>
      <td class="td-checkbox checkbox">
        <input type="checkbox" class="table-check" name="account_id" id="check-{{ $account->id }}"

        {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
        @if(!empty($filter['booklist']['booklists']['default']))
        {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
        @if (in_array($account->id, $filter['booklist']['booklists']['default'])) checked
        @endif
        @endif
        ><label class="label-check" for="check-{{ $account->id }}"></label>
    </td>
    <td class="td-name">
        @php
        $edit = 0;
        @endphp
        @can('update', $account)
        @php
        $edit = 1;
        @endphp
        @endcan
        @if($edit == 1)
        <a href="/admin/accounts/{{ $account->id }}/edit">
          @endif
          {{ $account->fullName }}
          @if($edit == 1)
      </a>
      @endif
  </td>
  <td class="td-description">{{ $account->description }} </td>
  <td class="td-source">{{ $account->source_service->name }}</td>
  <td class="td-login">{{ $account->login }}</td>
  <td class="td-alias">{{ $account->alias }}</td>

  {{-- Элементы управления --}}
  @include('includes.control.table-td', ['item' => $account])

  <td class="td-delete">
    @if ($account->system != 1)
    @can('delete', $account)
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
    <span class="pagination-title">Кол-во записей: {{ $accounts->count() }}</span>
    {{ $accounts->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
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


@endpush
