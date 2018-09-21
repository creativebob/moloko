@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $page_info->page_description }}" />
{{-- Скрипты таблиц в шапке --}}
@include('includes.scripts.tablesorter-inhead')
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\Company::class, 'type' => 'table'])
@endsection

@section('content')
{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="table-content tablesorter" id="content" data-sticky-container data-entity-alias="companies">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-name" data-serversort="name" >Название компании</th>

          @if($user->god == 1)<th class="td-getauth">Действие</th> @endif

          <th class="td-address">Адрес</th>
          <th class="td-phone">Телефон</th>
          <th class="td-user_id">Руководитель</th>
          <th class="td-control"></th>
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
        @if(!empty($companies))
        @foreach($companies as $company)
        <tr class="item @if($user->company_id == $company->id)active @endif  @if($company->moderation == 1)no-moderation @endif" id="companies-{{ $company->id }}" data-name="{{ $company->name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox">
            <input type="checkbox" class="table-check" name="company_id" id="check-{{ $company->id }}"

            {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
            @if(!empty($filter['booklist']['booklists']['default']))
            {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
            @if (in_array($company->id, $filter['booklist']['booklists']['default'])) checked 
            @endif
            @endif
            ><label class="label-check" for="check-{{ $company->id }}"></label>
          </td>
          <td class="td-name">
            @php
            $edit = 0;
            @endphp
            @can('update', $company)
            @php
            $edit = 1;
            @endphp
            @endcan
            @if($edit == 1)
            <a href="companies/{{ $company->id }}/edit">
              @endif
              {{ $company->name }}
              @if($edit == 1)
            </a> 
            @endif
          </td>
          {{-- Если пользователь бог, то показываем для него переключатель на компанию --}}
          @if($user->god == 1)
          <td class="td-getauth">@if($user->company_id != $company->id) {{ link_to_route('users.getauthcompany', 'Авторизоваться', ['company_id'=>$company->id], ['class' => 'tiny button']) }} @endif</td>
          @endif

          <td class="td-address">@if(!empty($company->location->address)){{ $company->location->address }}@endif </td>
          <td class="td-phone">{{ isset($company->main_phone->phone) ? decorPhone($company->main_phone->phone) : 'Нет телефона' }} </td>
          <td class="td-user_id">{{ $company->director->first_name or ' ... ' }} {{ $company->director->second_name or ' ... ' }} </td>

          {{-- Элементы управления --}}
          @include('includes.control.table-td', ['item' => $company])

          <td class="td-delete">
            @if ($company->system_item != 1)
            @can('delete', $company)
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
    <span class="pagination-title">Кол-во записей: {{ $companies->count() }}</span>
    {{ $companies->links() }}
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
{{-- Скрипт перетаскивания для таблицы --}}
@include('includes.scripts.tablesorter-script')
@include('includes.scripts.sortable-table-script')
@include('includes.scripts.checkbox-control')

{{-- Скрипт отображения на сайте --}}
@include('includes.scripts.ajax-display')

{{-- Скрипт системной записи --}}
@include('includes.scripts.ajax-system')

{{-- Скрипт модалки удаления --}}
@include('includes.scripts.modal-delete-script')
@include('includes.scripts.delete-ajax-script')

@endsection