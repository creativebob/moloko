@extends('layouts.app')

@section('inhead')
{{-- Скрипты таблиц в шапке --}}
@include('includes.scripts.tablesorter-inhead')
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\Role::class, 'type' => 'table'])
@endsection

@section('content')

{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="table-content tablesorter" id="content" data-sticky-container data-entity-alias="roles">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-name">Название таблицы</th>
          <th class="td-set">Настройка</th>
          <th class="td-company-id">Компания</th>
          <th class="td-count">Количество правил</th>
          <th class="td-description">Описание</th>
          <th class="td-author">Автор</th>
          <th class="td-system">Статус</th>
          <th class="td-display"></th>
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
        @if(!empty($roles))
        @foreach($roles as $role)
        @php
        $edit = 0;
        @endphp
        @can('update', $role)
        @php
        $edit = 1;
        @endphp
        @endcan
        <tr class="item @if(Auth::user()->role_id == $role->id)active @endif  @if($role->moderation == 1)no-moderation @endif" id="roles-{{ $role->id }}" data-name="{{ $role->name }}">
          <td class="td-drop"></td>
          <td class="td-checkbox checkbox"><input type="checkbox" class="table-check" name="" id="check-{{ $role->id }}"><label class="label-check" for="check-{{ $role->id }}"></label></td>
          <td class="td-name">
            @if($edit == 1)
            <a href="/admin/roles/{{ $role->id }}/edit">
              @endif
              {{ $role->name }}
              @if($edit == 1)
            </a> 
            @endif
          </td>
          <td class="td-set">
            @if($edit == 1)
            @if(!empty($counts_directive_array[$role->id]['disabled_role']))
            <a class="button tiny" disabled>Настройка</a>
            @else 
            {{ link_to_route('roles.setting', 'Настройка', [$role->id], ['class'=>'button tiny']) }}
            @endif
            @endif
          </td>
          <td class="td-company-id">@if($role->company_id != null) {{ $role->company->name }} @else @if($role->system_item == null) Шаблон @else Системная @endif @endif</td>
          <td class="td-count"><span class="allow">{{ $counts_directive_array[$role->id]['count_allow'] }}</span> / <span class="deny"> {{ $counts_directive_array[$role->id]['count_deny'] }}</span></td>
          <td class="td-description">{{ $role->description }}</td>
          <td class="td-author">@if(!empty($role->author->first_name)) {{ $role->author->first_name . ' ' . $role->author->second_name }} @endif</td>

          <td class="td-system">

            @if ($role->system_item == 1)
            @if ($role->company_id != null)
            <a class="icon-system-lock sprite"></a>
            @else               
            <a class="icon-system-programm sprite"></a>
            @endif
            @endif

            @if ($role->system_item == null)
            @if ($role->company_id != null)

            @else               
            <a class="icon-system-template sprite"></a>
            @endif
            @endif
          </td>

          <td class="td-display">

            @if ($role->display == 1)
            <a class="icon-display-show sprite" data-open="item-display"></a>
            @else
            <a class="icon-display-hide sprite" data-open="item-display"></a>
            @endif

          </td>


          <td class="td-delete">
            @if (($role->system_item !== 1) && ($role->company_id !== null))
            @can('delete', $role)
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
    <span class="pagination-title">Кол-во записей: {{ $roles->count() }}</span>
    {{ $roles->links() }}
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

{{-- Скрипт модалки удаления --}}
@include('includes.scripts.modal-delete-script')
@endsection