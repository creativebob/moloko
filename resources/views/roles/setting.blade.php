@extends('layouts.app')
@include('roles.inhead')

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
        <li class="tabs-title is-active"><a href="#content-panel-1" aria-selected="true">Функциональные права</a></li>
        <li class="tabs-title"><a data-tabs-target="content-panel-2" href="#content-panel-2">Права в рамках отделов</a></li>
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
                      <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
                      <th class="td-entity-name">Название таблицы</th>

                         @foreach($actions as $action)
                            <th class="td-action-{{ $action->action_method }}">{{ $action->action_name }}</th>
                         @endforeach

                    </tr>
                  </thead>
                  <tbody data-tbodyId="1" class="tbody-width">
                  @if(!empty($entities))
                    @foreach($entities as $entity)

                      <tr class="parent" id="entities-{{ $entity->id }}" data-name="{{ $entity->entity_name }}">
                        <td class="td-drop"><div class="sprite icon-drop"></div></td>
                        <td class="td-checkbox checkbox"><input type="checkbox" class="table-check" name="" id="check-{{ $entity->id }}"><label class="label-check" for="check-{{ $entity->id }}"></label></td>
                        <td class="td-entity-name">{{ $entity->entity_name }} </td>

                         @foreach($actions as $action)
                         <td class="td-action-{{ $action->action_method }}">


                            @php $a = ""; @endphp
                            @foreach($role->rights as $right)

                              @if(!empty($right->actionentity->alias_action_entity))
                                @if($right->actionentity->alias_action_entity == ($action->action_method . '-' . $entity->entity_alias)) 
                                  @php $a = "checked"; @endphp

                                  <div class="checkbox">
                                    <input type="checkbox" id="{{ $entity->entity_name }}-{{ $action->id }}" {{ $a }}>
                                    <label for="{{ $entity->entity_name }}-{{ $action->id }}"></label>
                                  </div>

                                @endif
                              @endif
                              
                            @endforeach


                             </td>


                           @endforeach

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
                <span class="pagination-title">Кол-во записей: {{ $entities->count() }}</span>
                {{ $entities->links() }}
              </div>
            </div>
            </div>
          </div>
        </div>


        <div class="tabs-panel" id="content-panel-2">
          <div class="grid-x grid-padding-x">
            <div class="small-12 medium-6 cell">

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
@include('roles.scripts')



