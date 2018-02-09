@extends('layouts.app')
 
@section('inhead')
{{-- Скрипты таблиц в шапке --}}
  @include('includes.table-inhead')
@endsection

@section('title', 'Пользователи')

@section('title-content')
{{-- Таблица --}}
<div data-sticky-container id="head-content">
  <div class="sticky sticky-topbar" id="head-sticky" data-sticky-on="small" data-sticky data-margin-top="2.4" data-top-anchor="head-content:top">
	  <div class="top-bar head-content">
	    <div class="top-bar-left">
        <h2 class="header-content">{{ $page_info->page_name }}</h2>
	      <a href="/rights/create" class="icon-add sprite"></a>
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
        <fieldset class="fieldset-filters">

          {{ Form::open(['route' => 'rights.index', 'data-abide', 'novalidate', 'name'=>'filter', 'method'=>'GET']) }}

          <legend>Фильтрация</legend>
            <div class="grid-x grid-padding-x"> 
              <div class="small-6 cell">
                <label>Статус пользователя
                  {{ Form::select('category_right_id', [ 'all' => 'Все пользователи','1' => 'Сотрудник', '2' => 'Клиент'], 'all') }}
                </label>
              </div>
              <div class="small-6 cell">
                <label>Блокировка доступа

                </label>
              </div>
              <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
               {{ Form::submit('Фильтрация', ['class'=>'button']) }}
              </div>
            </div>

          {{ Form::close() }}

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
          <th class="td-right_name">Правило</th>
          <th class="td-right_action">Действие</th>
          <th class="category_right_id">Категория правила</th>
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
      @if(!empty($rights))
        @foreach($rights as $right)
        <tr class="parent" id="right-{{ $right->id }}" data-name="{{ $right->right_name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox"><input type="checkbox" class="table-check" name="" id="check-{{ $right->id }}"><label class="label-check" for="check-{{ $right->id }}"></label></td>
          <td class="td-right_name">{{ $right->right_name }}</td>
          <td class="td-right_action">@if($right->category_right_id == 1) {{ $right->actionentity->alias_action_entity }} @endif</td>
          <td class="category_right_id">{{ $right->category_right_id }}</td>

          <td class="td-delete"><a class="icon-delete sprite" data-open="item-delete"></a></td>       
          <!-- <td class="td-delete">{{ link_to_route('rights.destroy', " " , [$right->id], ['class'=>'icon-delete sprite']) }}</td> -->
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
    <span class="pagination-title">Кол-во записей: {{ $rights->count() }}</span>
    {{ $rights->links() }}
  </div>
</div>
@endsection

@section('modals')
{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete')
@endsection

@section('scripts')
{{-- Скрипт чекбоксов, сортировки и перетаскивания для таблицы --}}
@include('includes.table-scripts')

{{-- Скрипт модалки удаления --}}
@include('includes.modals.modal-delete-script')
@endsection
