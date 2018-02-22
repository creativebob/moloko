@extends('layouts.app')
 
@section('inhead')
{{-- Скрипты таблиц в шапке --}}
  @include('includes.scripts.table-inhead')
@endsection

@section('title')
  {{ $page_info->page_name }}
@endsection

@section('title-content')
{{-- Таблица --}}
<div data-sticky-container id="head-content">
  <div class="sticky sticky-topbar" id="head-sticky" data-sticky-on="small" data-sticky data-margin-top="2.4" data-top-anchor="head-content:top">
	  <div class="top-bar head-content">
	    <div class="top-bar-left">
        <h2 class="header-content">{{ $page_info->page_name }}</h2>
        @can('create', App\User::class)
          <a href="/users/create" class="icon-add sprite"></a>
        @endcan
	    </div>
	    <div class="top-bar-right">
	      <a class="icon-filter sprite"></a>
	      <input class="search-field" type="search" name="search_field" placeholder="Поиск" />
	      <button type="button" class="icon-search sprite button"></button>
	    </div>
	  </div>
	  {{-- Блок фильтров --}}
	  <div class="grid-x">
      <div class="small-12 cell filters" id="filters">
        <fieldset class="fieldset-filters inputs">
          @include('users.filters')
        </fieldset>
      </div>
    </div>
	</div>
</div>
@endsection
 
@section('content')

{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="table-content tablesorter" id="table-content" data-sticky-container>
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"><div class="sprite icon-drop"></div></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-second-name">Пользователь</th>
          <th class="td-login">Логин</th>
          @if(Auth::user()->god == 1)<th class="td-getauth">Действие</th> @endif
<!--           <th class="td-first-name">Имя</th> -->
          <th class="td-phone">Телефон</th>
          <th class="td-email">Почта</th>
          <th class="td-contragent-status">Статус</th>
          <th class="td-access-block">Доступ</th>
{{--           <th class="td-group-users-id">Уровень доступа</th>
          <th class="td-group-users-id">Локализация</th> --}}
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
      @if(!empty($users))

        @foreach($users as $user)
        <tr class="parent @if($user->moderation == 1)no-moderation @endif" id="users-{{ $user->id }}" data-name="{{ $user->nickname }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox"><input type="checkbox" class="table-check" name="check-{{ $user->id }}" id="check-{{ $user->id }}"><label class="label-check" for="check-{{ $user->id }}"></label></td>
          <td class="td-second-name">
            @php
              $edit = 0;
            @endphp
            @can('update', $user)
              @php
                $edit = 1;
              @endphp
            @endcan
            @if($edit == 1)
            <a href="/users/{{ $user->id }}/edit">
            @endif
            {{ $user->second_name . " " . $user->first_name . "  (". $user->nickname . ")" }}
            @if($edit == 1)
            </a> 
            @endif
          </td>
          <td class="td-login">{{ $user->login }}</td>


          {{-- Если пользователь бог, то показываем для него переключатель на авторизацию под пользователем --}}
          @if(Auth::user()->god == 1)

          @php
            $count_roles = count($user->roles);
            if($count_roles < 1){$but_class = "tiny button warning"; $but_text = "Права не назначены";} else {$but_class = "tiny button"; $but_text = "Авторизоваться";};
          @endphp
            <td class="td-getauth">@if((Auth::user()->id != $user->id)&&!empty($user->company_id)) {{ link_to_route('users.getauthuser', $but_text, ['user_id'=>$user->id], ['class' => $but_class]) }} @endif</td>
          @endif
          <td class="td-phone">{{ $user->phone }}</td>
          <td class="td-email">{{ $user->email }}</td>
          <td class="td-contragent-status">{{ decor_user_type($user->user_type) }}</td>
          <td class="td-access-block">{{ decor_access_block($user->access_block) }}</td>
{{--           <td class="td-group_action_id">{{ $user->group_action->access_group_name }}</td>
          <td class="td-group_locality_id">{{ $user->group_locality->access_group_name }}</td> --}}
          <td class="td-delete">
            @if (($user->system_item != 1) && ($user->god != 1))
              @can('delete', $user)
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
    <span class="pagination-title">Кол-во записей: {{ $users->count() }}</span>
    {{ $users->links() }}
  </div>
</div>
@endsection

@section('modals')


{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete')
@endsection

@section('scripts')
{{-- Скрипт чекбоксов, сортировки и перетаскивания для таблицы --}}
@include('includes.scripts.table-scripts')

{{-- Скрипт модалки удаления --}}
@include('includes.scripts.modal-delete-script')
@endsection
