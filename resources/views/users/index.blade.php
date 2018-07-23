@extends('layouts.app')
 
@section('inhead')
{{-- Скрипты таблиц в шапке --}}
  @include('includes.scripts.tablesorter-inhead')
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\User::class, 'type' => 'table'])
@endsection
 
@section('content')

{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="table-content tablesorter" id="content" data-sticky-container data-entity-alias="users">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-second-name">Пользователь</th>
          <th class="td-login">Логин</th>
          @if(Auth::user()->god == 1)<th class="td-getauth">Действие</th> @endif
<!--           <th class="td-first-name">Имя</th> -->
          <th class="td-phone">Телефон</th>
          <th class="td-email">Почта</th>
          <th class="td-contragent-status">Статус</th>
          <th class="td-staffer">Должность</th>

          <th class="td-access-block">Доступ</th>
{{--           <th class="td-group-users-id">Уровень доступа</th>
          <th class="td-group-users-id">Локализация</th> --}}
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
      @if(!empty($users))

        @foreach($users as $user)
        <tr class="item @if($user->moderation == 1)no-moderation @endif" id="users-{{ $user->id }}" data-name="{{ $user->first_name.' '.$user->second_name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox">

            <input type="checkbox" class="table-check" name="user_id" id="check-{{ $user->id }}"
            @if(!empty($filter['booklist']['booklists']['default']))
              @if (in_array($user->id, $filter['booklist']['booklists']['default'])) checked 
              @endif
            @endif 
            ><label class="label-check" for="check-{{ $user->id }}"></label>
          </td>
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
                <a href="/admin/users/{{ $user->id }}/edit">
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
          <td class="td-staffer">@if(!empty($user->staff->first()->position->name)) {{ $user->staff->first()->position->name }} @endif</td>
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
