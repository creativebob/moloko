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
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\Place::class, 'type' => 'table'])
@endsection

@section('content')
{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="table-content tablesorter" id="content" data-sticky-container data-entity-alias="places">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-name" data-serversort="name" >Название компании</th>

          @if($user->god == 1)<th class="td-getauth">Действие</th> @endif

          <th class="td-address">Адрес</th>
          <th class="td-square">Площадь</th>
          <th class="td-stockroom-status">Склад</th>
          <th class="td-rent-status">Аренда</th>
          
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
        @if(!empty($places))
        @foreach($places as $place)
        <tr class="item @if($user->place_id == $place->id)active @endif  @if($place->moderation == 1)no-moderation @endif" id="places-{{ $place->id }}" data-name="{{ $place->name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox">
            <input type="checkbox" class="table-check" name="place_id" id="check-{{ $place->id }}"

              {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
              @if(!empty($filter['booklist']['booklists']['default']))
                {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
                @if (in_array($place->id, $filter['booklist']['booklists']['default'])) checked 
              @endif
            @endif
            ><label class="label-check" for="check-{{ $place->id }}"></label>
          </td>
          <td class="td-name">
            @php
            $edit = 0;
            @endphp
            @can('update', $place)
            @php
            $edit = 1;
            @endphp
            @endcan
            @if($edit == 1)
            <a href="/places/{{ $place->id }}/edit">
              @endif
              {{ $place->name }}
              @if($edit == 1)
            </a> 
            @endif
          </td>
          {{-- Если пользователь бог, то показываем для него переключатель на компанию --}}
          @if($user->god == 1)
          <td class="td-getauth">@if($user->place_id != $place->id) {{ link_to_route('users.getauthplace', 'Авторизоваться', ['place_id'=>$place->id], ['class' => 'tiny button']) }} @endif</td>
          @endif

          <td class="td-address">@if(!empty($place->location->address)){{ $place->location->address }}@endif </td>
          <td class="td-square">{{ $place->square }} </td>
          <td class="td-stockroom-status">{{ $place->stockroom_status }} </tdh>
          <td class="td-rent-status">{{ $place->rent_status }} </td>

          <td class="td-delete">
            @if ($place->system_item != 1)
            @can('delete', $place)
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
    <span class="pagination-title">Кол-во записей: {{ $places->count() }}</span>
    {{ $places->links() }}
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

  {{-- Скрипт чекбоксов --}}
  @include('includes.scripts.checkbox-control')

  @include('includes.scripts.sortable-table-script')

  {{-- Скрипт модалки удаления --}}
  @include('includes.scripts.modal-delete-script')
  @include('includes.scripts.delete-ajax-script')

@endsection