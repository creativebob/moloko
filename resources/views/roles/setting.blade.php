@extends('layouts.app')
@section('inhead')
  @include('includes.table-inhead')
@endsection


@section('title', 'Настройка прав доступа пользователей')

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">НАСТРОЙКА ПРАВ ДОСТУПА</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')
  <div class="grid-x tabs-wrap">
    <div class="small-12 cell">
      <ul class="tabs-list" data-tabs id="tabs">
        <li class="tabs-title is-active"><a href="#content-panel-1" aria-selected="true">Разрешения</a></li>
        <li class="tabs-title"><a data-tabs-target="content-panel-2" href="#content-panel-2">Запреты</a></li>
        <li class="tabs-title"><a data-tabs-target="content-panel-3" href="#content-panel-3">Авторские права</a></li>
      </ul>
    </div>
  </div>
  <div class="grid-x tabs-wrap inputs">
    <div class="small-12 cell tabs-margin-top">
      <div class="tabs-content" data-tabs-content="tabs">
        <div class="tabs-panel is-active" id="content-panel-1">
          <div class="grid-x grid-padding-x">
            <div class="small-12 cell">
            {{-- Таблица --}}
              <div class="grid-x">
                <div class="small-12 cell">
                  <table class="table-content tablesorter" id="table-content">
                    <thead class="thead-width">
                      <tr id="thead-content">
                        <th class="td-drop"><div class="sprite icon-drop"></div></th>
                        <th class="td-checkbox checkbox-th">
                          {{-- <input type="checkbox" class="table-check-all" name="" id="check-all-allow"><label class="label-check" for="check-all-allow"></label> --}}
                        </th>
                        <th class="td-entity-name">Название таблицы</th>
                          @foreach($actions as $action)
                            <th class="td-action-{{ $action->action_method }}">{{ $action->action_name }}</th>
                          @endforeach
                      </tr>
                    </thead>
                    <tbody class="tbody-width">
                    @foreach($main_mass as $one_string)
                      <tr class="parent" id="entities-{{ $one_string['entity_name'] }}" data-name="{{ $one_string['entity_name'] }}">
                        <td class="td-drop"><div class="sprite icon-drop"></div></td>
                        <td class="td-checkbox checkbox"><input type="checkbox" class="table-check-allow"  data-role-id="{{ $role_id }}" name="" id="check-{{ $one_string['entity_name'] }}-allow"><label class="label-check" for="check-{{ $one_string['entity_name'] }}-allow"></label></td>
                        <td class="td-entity-name">{{ $one_string['entity_name'] }} </td>
                          @foreach($one_string['boxes'] as $boxes)
                            <td class="td-action-{{ $action->action_method }}">
                                <div class="checkbox">
                                  <input type="checkbox" class="checkbox-allow" {{ $boxes['checked'] }} {{ $boxes['disabled'] }} id="{{ $boxes['right_id'] }}" data-role-id="{{ $role_id }}" data-entity-id="{{ $one_string['entity_id'] }}" data-action-id="{{ $boxes['action_id'] }}" data-deny-status="0">
                                  <label for="{{ $boxes['right_id'] }}" class="{{ $boxes['disabled'] }}"></label>
                                </div>
                            </td>
                          @endforeach
                      </tr>
                    @endforeach
                  </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="tabs-panel" id="content-panel-2">
          <div class="grid-x grid-padding-x">
            <div class="small-12 cell">
            {{-- Таблица --}}
            <div class="grid-x">
              <div class="small-12 cell">
                <table class="table-content tablesorter" id="table-content">
                  <thead class="thead-width">
                    <tr id="thead-content">
                      <th class="td-drop"><div class="sprite icon-drop"></div></th>
                      <th class="td-checkbox checkbox-th">
                        {{-- <input type="checkbox" class="table-check-all" name="" id="check-all-deny"><label class="label-check" for="check-all-deny"></label> --}}
                      </th>
                      <th class="td-entity-name">Название таблицы</th>
                        @foreach($actions as $action)
                          <th class="td-action-{{ $action->action_method }}">{{ $action->action_name }}</th>
                        @endforeach
                    </tr>
                  </thead>
                  <tbody class="tbody-width">
                    @foreach($main_mass_deny as $one_string)
                      <tr class="parent" id="entities-{{ $one_string['entity_name'] }}" data-name="{{ $one_string['entity_name'] }}">
                        <td class="td-drop"><div class="sprite icon-drop"></div></td>
                        <td class="td-checkbox checkbox"><input type="checkbox" class="table-check-deny"  data-role-id="{{ $role_id }}" name="" id="check-{{ $one_string['entity_name'] }}-deny"><label class="label-check" for="check-{{ $one_string['entity_name'] }}-deny"></label></td>
                        <td class="td-entity-name">{{ $one_string['entity_name'] }} </td>
                          @foreach($one_string['boxes'] as $boxes)
                            <td class="td-action-{{ $action->action_method }}">
                                <div class="checkbox">
                                  <input type="checkbox" class="checkbox-deny" {{ $boxes['checked'] }} {{ $boxes['disabled'] }} id="{{ $boxes['right_id'] }}" data-role-id="{{ $role_id }}" data-entity-id="{{ $one_string['entity_id'] }}" data-action-id="{{ $boxes['action_id'] }}" data-deny-status="1">
                                  <label for="{{ $boxes['right_id'] }}" class="{{ $boxes['disabled'] }}"></label>
                                </div>
                            </td>
                          @endforeach
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
            </div>
          </div>
        </div>
        <div class="tabs-panel" id="content-panel-3">
          <div class="grid-x grid-padding-x">
            <div class="small-12 medium-6 cell">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection




