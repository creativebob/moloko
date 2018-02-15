@extends('layouts.app')

@section('inhead')
{{-- Скрипты таблиц в шапке --}}
  @include('includes.scripts.table-inhead')
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

@section('scripts')
<script type="text/javascript">
  // Оставляем ширину у вырванного из потока элемента
  var fixHelper = function(e, ui) {
    ui.children().each(function() {
      $(this).width($(this).width());
    });
    return ui;
  };
  // Включаем перетаскивание
  $("#table-content tbody").sortable({
      helper: fixHelper, // ширина вырванного элемента
      handle: 'td:first' // указываем за какой элемент можно тянуть
  }).disableSelection();

  var parent = $('.parent');
  // Смотрим есть ли выделенные чекбоксы в правах сущности, и если все выделены, то выделяем и ее
  // Разрешение
  function checkedAllow() {
    parent.each(function(index) {
      var checked = $(this).find('input:checkbox:checked:not(.table-check-allow)').length;
      var childs = $(this).find('.checkbox-allow').length;
      if (checked == childs) {
        $(this).find('.table-check-allow').prop('checked', true);
      };
    });
  };
  checkedAllow();
  // Запрет
  function checkedDeny() {
    parent.each(function(index) {
      var checked = $(this).find('input:checkbox:checked:not(.table-check-deny)').length;
      var childs = $(this).find('.checkbox-deny').length;
      if (checked == childs) {
        $(this).find('.table-check-deny').prop('checked', true);
      };
    }); 
  };
  checkedDeny();
  // Скрипт передачи значения на изменение
  // Разрешение
  $(document).on('click', '.checkbox-allow', function() {
    var parent = $(this).closest('.parent');
    if ($(this).prop('checked') == false) {
      parent.find('.table-check-allow').prop('checked', false);
    } else {
      checkedAllow();
    };
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/roles/setright",
      type: "POST",
      data: {right_id: $(this).attr('id'), role_id: $(this).attr('data-role-id')},
      success: function (data) {
        // alert(data);
      }
    });
  });
  // Запрет
  $(document).on('click', '.checkbox-deny', function() {
    var parent = $(this).closest('.parent');
    if ($(this).prop('checked') == false) {
      parent.find('.table-check-deny').prop('checked', false);
    } else {
      checkedDeny();
    };
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/roles/setright",
      type: "POST",
      data: {right_id: $(this).attr('id'), role_id: $(this).attr('data-role-id')},
      success: function (data) {
        // alert(data);
      }
    });
  });

  // Выделяем все чекбоксы сущности
  // Разрешение
  $(document).on('click', '.table-check-allow', function() {
    // При клике на чекбокс сущности получаем id всех прав на сущность
    var parent = $(this).closest('.parent'); 
    var rights = parent.find('.checkbox-allow').map(function(){
      return $(this).attr('id');
    }).get();

    if ($(this).prop('checked') == true) {
      var check = 1;
    } else {
      check = 0;
    };
        // alert(rights);
        // alert(rights);
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/roles/setright",
      type: "POST",
      data: {rights: rights, role_id: $(this).data('role-id'), checkbox: check},
      success: function (data) {
        var result = $.parseJSON(data);
        if (result.status == 1) {
          parent.find('.checkbox-allow').each(function($index) {
            $(this).prop('checked', true);
          });
        } else {
          parent.find('.checkbox-allow').each(function($index) {
            $(this).prop('checked', false);
          });
        };
      }
    });
  });
  // Запрет
  $(document).on('click', '.table-check-deny', function() {
    // При клике на чекбокс сущности получаем id всех прав на сущность
    var parent = $(this).closest('.parent'); 
    var rights = parent.find('.checkbox-deny').map(function(){
      return $(this).attr('id');
    }).get();

    if ($(this).prop('checked') == true) {
      var check = 1;
    } else {
      check = 0;
    };
        // alert(check);
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/roles/setright",
      type: "POST",
      data: {rights: rights, role_id: $(this).data('role-id'), checkbox: check},
      success: function (data) {
        var result = $.parseJSON(data);
        if (result.status == 1) {
          parent.find('.checkbox-deny').each(function($index) {
            $(this).prop('checked', true);
          });
        } else {
          parent.find('.checkbox-deny').each(function($index) {
            $(this).prop('checked', false);
          });
        };
      }
    });
  });

</script>
@endsection



