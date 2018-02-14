@extends('layouts.app')
 
@section('inhead')
<meta name="description" content="Меню {{ $site->site_name }}" />
@endsection

@section('title')
   Меню {{ $site->site_name }}
@endsection

@section('title-content')
<div data-sticky-container id="head-content">
  <div class="sticky sticky-topbar" id="head-sticky" data-sticky data-margin-top="2.4" data-options="stickyOn: small;" data-top-anchor="head-content:top">
    <div class="top-bar head-content">
      <div class="top-bar-left">
        <h2 class="header-content">{{ $site->site_name }}</h2>
        @can('create', App\Navigation::class)
        <a class="icon-add sprite" data-open="navigation-add"></a>
        @endcan
      </div>
      <div class="top-bar-right">
        <a class="icon-filter sprite"></a>
        <input class="search-field" type="search" name="search-field" placeholder="Поиск" />
        <button type="button" class="icon-search sprite button"></button>
      </div>
    </div>
    {{-- Блок фильтров --}}
    <div class="grid-x">
      <div class="small-12 cell filters" id="filters">
        <fieldset class="fieldset-filters inputs">
          {{ Form::open(['route' => 'users.index', 'data-abide', 'novalidate', 'name'=>'filter', 'method'=>'GET']) }}
          <legend>Фильтрация</legend>
          <div class="grid-x grid-padding-x"> 
            <div class="small-6 cell">
              <label>Статус пользователя
                {{ Form::select('user_type', [ 'all' => 'Все пользователи','1' => 'Сотрудник', '2' => 'Клиент'], 'all') }}
              </label>
            </div>
            <div class="small-6 cell">
              <label>Блокировка доступа
                {{ Form::select('access_block', [ 'all' => 'Все пользователи', '1' => 'Доступ блокирован', '' => 'Доступ открыт'], 'all') }}
              </label>
            </div>

            <div class="small-12 medium-12 align-center cell tabs-button">
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
{{-- Список --}}
<div class="grid-x">
  <div class="small-12 cell">
    
    @if($navigation_tree)
      <ul class="vertical menu accordion-menu content-list" id="content-list" data-accordion-menu data-allow-all-closed data-multi-open="false" data-slide-speed="250">
        @foreach ($navigation_tree as $navigation)
          @if (isset($navigation['menus']))
            {{-- Если Подкатегория --}}
            <li class="first-item parent
            @if (isset($navigation['menus']))
            parent-item
            @endif" id="navigations-{{ $navigation['id'] }}" data-name="{{ $navigation['navigation_name'] }}">
              <ul class="icon-list">
                <li>
                  @can('create', App\Menu::class)
                  <div class="icon-list-add sprite" data-open="menu-add"></div>
                  @endcan
                </li>
                <li>
                  @if($navigation['edit'] == 1)
                  <div class="icon-list-edit sprite" data-open="navigation-edit"></div>
                  @endif
                </li>
                <li>
                  @if(($navigation['system_item'] != 1) && (count($navigation['menus']) == 0) && ($navigation['delete'] == 1))
                    <div class="icon-list-delete sprite" data-open="item-delete"></div>
                  @endif
                </li>
              </ul>
              <a data-list="" class="first-link">
                <div class="list-title">
                  <div class="icon-open sprite"></div>
                  <span class="first-item-name">{{ $navigation['navigation_name'] }}</span>
                  <span class="number">
                    @if (isset($navigation['menus']))
                      {{ count($navigation['menus']) }}
                    @else
                      0
                    @endif
                  </span>
                </div>
              </a>
              @if (isset($navigation['menus']))
                <ul class="menu vertical medium-list accordion-menu" data-accordion-menu data-allow-all-closed data-multi-open="false">
                  @foreach($navigation['menus'] as $menu)
                    @include('menus-list', $menu)
                  @endforeach
                </ul>
              @endif
            </li>
          @endif
        @endforeach
      </ul>
    @endif
  </div>
</div>
@endsection

@section('modals')
{{-- Модалка добавления навигации --}}
<div class="reveal" id="navigation-add" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ навигации</h5>
    </div>
  </div>
  {{ Form::open(['url' => '/sites/'.$site_alias.'/navigations', 'id' => 'form-navigation-add', 'data-abide', 'novalidate']) }}
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 small-offset-1 cell">
        <label class="input-icon">Введите название навигации
          {{ Form::text('navigation_name', $value = null, ['autocomplete'=>'off', 'required']) }}
          <span class="form-error">Уж постарайтесь, введите хотя бы 3 символа!</span>
        </label>
        <input type="hidden" name="site_id" value="{{ $site->id }}">
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        {{ Form::submit('Сохранить', ['class'=>'button modal-button', 'id'=>'submit-navigation-add']) }}
      </div>
    </div>
  {{ Form::close() }}
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>
{{-- Конец модалки добавления навигации --}}

{{-- Модалка редактирования навигации --}}
<div class="reveal" id="navigation-edit" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>Редактирование навигации</h5>
    </div>
  </div>
  {{ Form::open(['id' => 'form-navigation-edit', 'data-abide', 'novalidate']) }}
  {{ method_field('PATCH') }}
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 small-offset-1 cell">
         <label class="input-icon">Введите название навигации
          {{ Form::text('navigation_name', $value = null, ['id'=>'navigation-name-field', 'autocomplete'=>'off', 'required']) }}
          <span class="form-error">Уж постарайтесь, введите хотя бы 3 символа!</span>
        </label>
        <input type="hidden" name="site_id" value="{{ $site->id }}">
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        {{ Form::submit('Сохранить', ['class'=>'button modal-button', 'id'=>'submit-navigation-edit']) }}
      </div>
    </div>
  {{ Form::close() }}
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>
{{-- Конец модалки редактирования навигации --}}

{{-- Модалка добавления пункта меню --}}
<div class="reveal" id="menu-add" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ пункта меню</h5>
    </div>
  </div>
  <div class="grid-x tabs-wrap tabs-margin-top align-center">
    <div class="small-10 medium-4 cell">
      <ul class="tabs-list" data-tabs id="tabs">
        <li class="tabs-title is-active"><a href="#add-menu" aria-selected="true">Меню</a></li>
        <li class="tabs-title"><a data-tabs-target="add-options" href="#add-options">Настройки</a></li>
      </ul>
    </div>
  </div>
  <div class="tabs-wrap inputs">
    <div class="tabs-content" data-tabs-content="tabs">
      {{ Form::open(['url' => '/sites/'.$site_alias.'/menus', 'id' => 'form-menu-add']) }}
        <!-- Добавляем пункт меню -->
        <div class="tabs-panel is-active" id="add-menu">
          <div class="grid-x grid-padding-x modal-content inputs">
            <div class="small-10 small-offset-1 cell">
              <label>Название пункта меню
                {{ Form::text('menu_name', $value = null, ['autocomplete'=>'off', 'required']) }}
                <span class="form-error">Уж постарайтесь, введите хотя бы 2 символа!</span>
              </label>
              <label>Введите ссылку
                {{ Form::text('menu_alias', $value = null, ['autocomplete'=>'off']) }}
              </label>
              <label>Страница:
                {{ Form::select('page_id', $pages_list, null, ['class'=>'pages-tree-select', 'placeholder'=>'Не выбрано']) }}
              </label>
              <input type="hidden" name="site_id" value="{{ $site->id }}">
            </div>
          </div>
        </div>
        <!-- Добавляем опции -->
        <div class="tabs-panel" id="add-options">
          <div class="grid-x grid-padding-x modal-content inputs">
            <div class="small-10 small-offset-1 cell">
              <label>Меню:
                {{ Form::select('navigation_id', $navigations, null, ['class'=>'navigations-tree-select']) }}
              </label>
              <label>Добавляем пункт в:
                <select class="menus-tree-select" name="menu_parent_id">
                  <option value="null">Не выбрано</option>
                </select>
              </label>
              <label>Введите имя иконки
                {{ Form::text('menu_icon', $value = null, ['autocomplete'=>'off']) }}
              </label>
            </div>
          </div>
        </div>
        <div class="grid-x align-center">
          <div class="small-6 medium-4 cell">
            {{ Form::submit('Сохранить', ['data-close', 'class'=>'button modal-button', 'id'=>'submit-menu-add']) }}
          </div>
        </div>
      {{ Form::close() }}
    </div>
  </div>
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>
{{-- Конец модалки добавления пункта меню --}}

{{-- Модалка редактирования пункта меню --}}
<div class="reveal" id="menu-edit" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>Редактирование пункта меню</h5>
    </div>
  </div>
  <div class="grid-x tabs-wrap tabs-margin-top align-center">
    <div class="small-10 medium-4 cell">
      <ul class="tabs-list" data-tabs id="tabs">
        <li class="tabs-title is-active"><a href="#edit-menu" aria-selected="true">Меню</a></li>
        <li class="tabs-title"><a data-tabs-target="edit-options" href="#edit-options">Настройки</a></li>
      </ul>
    </div>
  </div>
  <div class="tabs-wrap inputs">
    <div class="tabs-content" data-tabs-content="tabs">
      {{ Form::open(['id' => 'form-menu-edit']) }}
      {{ method_field('PATCH') }}
        <!-- Добавляем пункт меню -->
        <div class="tabs-panel is-active" id="edit-menu">
          <div class="grid-x grid-padding-x modal-content inputs">
            <div class="small-10 small-offset-1 cell">
              <label>Название пункта меню
                {{ Form::text('menu_name', $value = null, ['id'=>'menu-name', 'autocomplete'=>'off', 'required']) }}
                <span class="form-error">Уж постарайтесь, введите хотя бы 2 символа!</span>
              </label>
              <label>Введите ссылку
                {{ Form::text('menu_alias', $value = null, ['id'=>'menu-alias', 'autocomplete'=>'off']) }}
              </label>
              <label>Страница:
                {{ Form::select('page_id', $pages_list, null, ['class'=>'pages-tree-select', 'class'=>'pages-tree-select', 'placeholder'=>'Не выбрано']) }}
              </label>
              <input type="hidden" name="site_id" value="{{ $site->id }}">
            </div>
          </div>
        </div>
        <!-- Добавляем опции -->
        <div class="tabs-panel" id="edit-options">
          <div class="grid-x grid-padding-x modal-content inputs">
            <div class="small-10 small-offset-1 cell">
              <label>Меню:
                {{ Form::select('navigation_id', $navigations, null, ['class'=>'navigations-tree-select']) }}
              </label>
              <label>Добавляем пункт в:
                <select class="menus-tree-select" name="menu_parent_id">
                  <option value="null">Не выбрано</option>
                </select>
              </label>
              <label>Введите имя иконки
                {{ Form::text('menu_icon', $value = null, ['id'=>'menu-icon', 'autocomplete'=>'off']) }}
              </label>
            </div>
          </div>
        </div>
        <div class="grid-x align-center">
          <div class="small-6 medium-4 cell">
            {{ Form::submit('Сохранить', ['data-close', 'class'=>'button modal-button', 'id'=>'submit-menu-add']) }}
          </div>
        </div>
      {{ Form::close() }}
    </div>
  </div>
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>
{{-- Конец модалки пункта меню --}}

{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete')

{{-- Модалка удаления ajax --}}
@include('includes.modals.modal-delete-ajax')
@endsection

@section('scripts')
<script type="text/javascript" src="/js/jquery.inputmask.min.js"></script>
<script type="text/javascript">
$(function() {
  // Берем алиас сайта
  var siteAlias = '{{ $site_alias }}';
  $('.phone-field').mask('8 (000) 000-00-00');
  // Функция появления окна с ошибкой
  function showError (msg) {
    var error = "<div class=\"callout item-error\" data-closable><p>" + msg + "</p><button class=\"close-button error-close\" aria-label=\"Dismiss alert\" type=\"button\" data-close><span aria-hidden=\"true\">&times;</span></button></div>";
    return error;
  };
  // Редактируем навигацию
  $(document).on('click', '[data-open="navigation-edit"]', function() {
    // Получаем данные о разделе
    var id = $(this).closest('.parent').attr('id').split('-')[1];
    $('#form-navigation-edit').attr('action', '/sites/' + siteAlias + '/navigations/' + id);
    // Ajax запрос
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/sites/" + siteAlias + "/navigations/" + id + "/edit",
      type: "GET",
      success: function(date){
        var result = $.parseJSON(date);
        $('#navigation-name-field').val(result.navigation_name);
      }
    });
  });
  // При закрытии модалки очищаем поля
  $(document).on('click', '.close-modal', function() {
    $('#navigation-name-field').val('');
  });

  // Добавление пункта меню
  // Переносим id родителя и навигации в модалку
  $(document).on('click', '[data-open="menu-add"]', function() {
    var parent = $(this).closest('.parent').attr('id').split('-')[1];
    var navigation = $(this).closest('.first-item').attr('id').split('-')[1];
    // alert(navigation + parent);
    if (parent == navigation) {
      // Если id родителя совпадает с id навигации, значит навигация и отправляем на контроллер навигаций
      var url = "/sites/" + siteAlias + "/navigations/" + navigation + "/edit";
    } else {
      // Иначе отправляем на контроллер пунктов меню
      var url = "/sites/" + siteAlias + "/menus/" + parent + "/edit";
    };
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: url,
      type: "GET",
      success: function(date){
        var result = $.parseJSON(date);
        $('.menus-tree-select>option').remove();
        var data = "<option value>Не выбрано</option>";
        $.each(result.menus, function(id, name) {
          data = data + "<option value=" + id + ">" + name + "</option>";
        });
        $('.menus-tree-select').append(data);
        if (parent == navigation) {
          $('.navigations-tree-select>[value="' + navigation + '"]').prop('selected', true);
        } else {
          $('.navigations-tree-select>[value="' + navigation + '"]').prop('selected', true);
          $('.menus-tree-select>[value="' + parent + '"]').prop('selected', true);
        };
        
      }
    }); 
  });
  // Редактируем меню
  $(document).on('click', '[data-open="menu-edit"]', function() {
    var id = $(this).closest('.medium-item').attr('id').split('-')[1];
    // alert(id);
      // Получаем данные о филиале
      $('#form-menu-edit').attr('action', '/sites/' + siteAlias + '/menus/' + id);
      // Аjax запрос
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "/sites/" + siteAlias + "/menus/" + id + "/edit",
        type: "GET",
        success: function(date){
          var result = $.parseJSON(date);

          // alert(result.menus);
          $('#menu-name').val(result.menu_name);
          $('#menu-icon').val(result.menu_icon);
          $('#menu-alias').val(result.menu_alias);
          $('.menus-tree-select>option').remove();
          var data = "<option value  >Не выбрано</option>";
          $.each(result.menus, function(id, name) {
            data = data + "<option value=" + id + ">" + name + "</option>";
          });
          $('.menus-tree-select').append(data);
          $('.navigations-tree-select>[value="' + result.navigation_id + '"]').prop('selected', true);
          $('.menus-tree-select>[value="' + result.menu_parent_id + '"]').prop('selected', true);
          $('.pages-tree-select>[value="' + result.page_id + '"]').prop('selected', true);
          // alert(result.page_id);
        }
      });
  });

  // При смене навигации меняем список менюшек
  $(document).on('change', '.navigations-tree-select', function() {
    var id = $(this).val();
    // alert(id);
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/sites/" + siteAlias + "/navigations/" + id + "/edit",
      type: "GET",
      success: function(date){
        var result = $.parseJSON(date);
        $('.menus-tree-select>option').remove();
        var data = "<option>Не выбрано</option>";
        $.each(result.menus, function(id, name) {
          data = data + "<option value=" + id + ">" + name + "</option>";
        });

        $('.menus-tree-select').append(data);
      }
    });
  });
  $(document).on('click', '.icon-close-modal', function() {
    $('#menu-name').val('');
    $('#menu-icon').val('');
    $('#menu-alias').val('');
    $('.pages-tree-select>option:first-child').prop('selected', true);
    $('.navigations-tree-select>option:first-child').prop('selected', true);
    $('.menus-tree-select>option').remove();
  });
  // При смнене пункта меняем id родителя
  $(document).on('change', '#dep-tree-select', function() {
    var parent = $('#dep-tree-select>option:selected').val();
    $('#dep-parent-id-field').val(parent);
  });
  $(document).on('change', '#pos-tree-select', function() {
    var parent = $('#pos-tree-select>option:selected').val();
    $('#pos-parent-id-field').val(parent);
  });

  // Чекаем отдел в нашей бд
  $('#menu-name-field').keyup(function() {
    // Блокируем кнопку
    $('#submit-menu-add').prop('disabled', true);
    $('#menu-database').val(0);
    // Получаем фрагмент текста
    var menu = $('#menu-name-field').val();
    // Первая буква отдела заглавная
    menu = menu.charAt(0).toUpperCase() + menu.substr(1);
    // alert(menu);
    // Смотрим сколько символов
    var lenmenu = menu.length;
    // Если символов больше 3 - делаем запрос
    if (lenmenu > 2) {
      // Сам ajax запрос
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "/menu",
        type: "POST",
        data: {menu_name: menu, section_id: $('#filial-id-field').val(), menu_database: $('#menu-database').val()},
        beforeSend: function () {
          $('.icon-load').removeClass('load');
        },
        success: function(date){
          $('.icon-load').addClass('load');
          // Удаляем все значения чтобы вписать новые
          $('#tbody-menu-add>tr').remove();
          var result = $.parseJSON(date);
          var data = '';
          // alert(result.error_status);
          if (result.error_status == 0) {
            data = "<tr><td>Данный отдел уже сущестует в этой компании!</td></tr>";
            // Выводим пришедшие данные на страницу
            $('#tbody-menu-add').append(data);
          };
          if (result.error_status == 1) {
            $('#menu-database').val(1);
            $('#submit-menu-add').prop('disabled', false);
          };
        }
      });
    };
    if (lenmenu <= 2) {
      // Удаляем все значения, если символов меньше 3х
      $('#tbody-menu-add>tr').remove();
      $('.item-error').remove();
      // $('#city-name-field').val('');
    };
  });
  // При закрытии окна с ошибкой очищаем модалку
  $(document).on('click', '.error-close', function() {
    $('.item-error').remove();
    $('#tbody-city-add>tr').remove();
    $('#tbody-region-add>tr').remove();
    $('#city-name-field').val('');
    $('#region-name-field').val('');
    $('#area-name').val('');
    $('#region-name').val('');
  });
  // Открываем меню и подменю, если только что добавили населенный пункт
  @if(!empty($data))
    function backlightItems ($data) {
      // Подсвечиваем навигацию
      $('#navigations-{{ $data['section_id'] }}').addClass('first-active').find('.icon-list:first').attr('aria-hidden', 'false').css('display', 'block');
      
      // Отображаем подпункт меню без страницы
      if ({{ $data['item_id'] }} == 0) {
        // Открываем только навигацию
        var firstItem = $('#navigations-{{ $data['section_id'] }}').find('.medium-list:first');
        // Открываем аккордион
        $('#content-list').foundation('down', firstItem);
      } else {
        // Перебираем родителей и подсвечиваем их
        $.each($('#menus-{{ $data['item_id'] }}').parents('.parent-item').get().reverse(), function (index) {
          $(this).children('.medium-link:first').addClass('medium-active');
          $(this).children('.icon-list:first').attr('aria-hidden', 'false').css('display', 'block');
          $('#content-list').foundation('down', $(this).closest('.medium-list'));
        });
        // Если родитель содержит не пустой элемент
        if ($('#menus-{{ $data['item_id'] }}').parent('.parent-item').has('.parent')) {
          $('#content-list').foundation('down', $('#menus-{{ $data['item_id'] }}').closest('.medium-list'));
        };
        // Если элемент содержит вложенность, открываем его
        if ($('#menus-{{ $data['item_id'] }}').hasClass('parent-item')) {
          $('#menus-{{ $data['item_id'] }}').children('.medium-link:first').addClass('medium-active');
          $('#menus-{{ $data['item_id'] }}').children('.icon-list:first').attr('aria-hidden', 'false').css('display', 'block');
          $('#content-list').foundation('down', $('#menus-{{ $data['item_id'] }}').children('.medium-list:first'));
        }
      };
    };
    backlightItems ();
  @endif

  // Мягкое удаление с refresh
  $(document).on('click', '[data-open="item-delete"]', function() {
    // находим описание сущности, id и название удаляемого элемента в родителе
    var parent = $(this).closest('.parent');
    var type = parent.attr('id').split('-')[0];
    var id = parent.attr('id').split('-')[1];
    var name = parent.data('name');
    $('.title-delete').text(name);
    $('.delete-button').attr('id', 'del-' + type + '-' + id);
    $('#form-item-del').attr('action', '/sites/'+ siteAlias + '/' + type + '/' + id);
  });
});
</script>
{{-- Скрипт подсветки многоуровневого меню --}}
@include('includes.scripts.multilevel-menu-active-scripts')
@endsection