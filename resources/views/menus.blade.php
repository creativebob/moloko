@extends('layouts.app')
 
@section('inhead')
<meta name="description" content="{{ $page_info->page_description }}" />
@endsection

@section('title')
  {{ $page_info->page_name }}
@endsection

@section('title-content')
<div data-sticky-container id="head-content">
  <div class="sticky sticky-topbar" id="head-sticky" data-sticky data-margin-top="2.4" data-options="stickyOn: small;" data-top-anchor="head-content:top">
    <div class="top-bar head-content">
      <div class="top-bar-left">
        <h2 class="header-content">{{ $site->site_name }}</h2>
        <a class="icon-add sprite" data-open="navigation-add"></a>
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
        <fieldset class="fieldset-filters">
          <legend>Фильтрация</legend>
          <div>lol</div>
          <div>lol</div>
          <div>lol</div>
          <div>lol</div>
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
            <li class="first-item parent" id="navigations-{{ $navigation['id'] }}" data-name="{{ $navigation['navigation_name'] }}">
              <ul class="icon-list">
                <li><div class="icon-list-add sprite" data-open="menu-add"></div></li>
                <li><div class="icon-list-edit sprite" data-open="navigation-edit"></div></li>
                <li>
                  @if(count($navigation['menus']) == 0)
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
  {{ Form::open(['url' => '/navigations', 'id' => 'form-navigation-add', 'data-abide', 'novalidate']) }}
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
      <h5>ДОБАВЛЕНИЕ меню / странички</h5>
    </div>
  </div>
  <div class="grid-x tabs-wrap tabs-margin-top">
    <div class="small-8 small-offset-2 cell">
      <ul class="tabs-list" data-tabs id="tabs">
        <li class="tabs-title is-active"><a href="#add-menu" aria-selected="true">Добавить пункт меню</a></li>
        <li class="tabs-title"><a data-tabs-target="add-page" href="#add-page">Добавить страничку</a></li>
      </ul>
    </div>
  </div>
  <div class="tabs-wrap inputs">
    <div class="tabs-content" data-tabs-content="tabs">
      <!-- Добавляем пункт меню -->
      <div class="tabs-panel is-active" id="add-menu">
        {{ Form::open(['url' => '/menus', 'id' => 'form-menu-add']) }}
          <div class="grid-x grid-padding-x modal-content inputs">
            <div class="small-10 small-offset-1 cell">
              {{-- <label>Добавляем пункт в:
                <select >
                  @foreach ($navigation_tree as $navigation)
                    @foreach ($navigation['menus'] as $menu)

                      <option>{{ $menu['menu_name'] }}</option>
                    @endforeach
                  @endforeach
                </select>
              </label> --}}
              <label>Название пункта меню
                {{ Form::text('menu_name', $value = null, ['autocomplete'=>'off', 'required']) }}
                <span class="form-error">Уж постарайтесь, введите хотя бы 2 символа!</span>
              </label>
              <label>Введите имя иконки
                {{ Form::text('menu_icon', $value = null, ['autocomplete'=>'off']) }}
              </label>
              <input type="hidden" name="section" value="1">
              <input type="hidden" name="site_id" value="{{ $site->id }}">
              <input type="hidden" name="navigation_id" class="navigation-id">
              <input type="hidden" name="menu_parent_id" class="menu-parent-id">
            </div>
          </div>
          <div class="grid-x align-center">
            <div class="small-6 medium-4 cell">
              {{ Form::submit('Сохранить', ['data-close', 'class'=>'button modal-button', 'id'=>'submit-menu-add']) }}
            </div>
          </div>
        {{ Form::close() }}
      </div>
      <!-- Добавляем страничку -->
      <div class="tabs-panel" id="add-page">
        {{ Form::open(['url' => '/menus', 'id' => 'form-page-add']) }}
          <div class="grid-x grid-padding-x modal-content inputs">
            <div class="small-10 small-offset-1 cell">
              <label>Страничка:
                {{ Form::select('page_id', $pages, null, ['id'=>'pages-tree-select']) }}
              </label>
              <input type="hidden" name="page" value="1">
              <input type="hidden" name="site_id" value="{{ $site->id }}">
              <input type="hidden" name="navigation_id" class="navigation-id">
              <input type="hidden" name="menu_parent_id" class="menu-parent-id">
            </div>
          </div>
          <div class="grid-x align-center">
            <div class="small-6 medium-4 cell">
              {{ Form::submit('Сохранить', ['data-close', 'class'=>'button modal-button', 'id'=>'submit-department-add']) }}
            </div>
          </div>
        {{ Form::close() }}
      </div>
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
  <!-- Редактируем отдел -->
  {{ Form::open(['id' => 'form-menu-edit']) }}
  {{ method_field('PATCH') }}
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 small-offset-1 cell">
        <label>Название пункта меню
          {{ Form::text('menu_name', $value = null, ['id'=>'menu-name', 'autocomplete'=>'off', 'required']) }}
          <span class="form-error">Уж постарайтесь, введите хотя бы 2 символа!</span>
        </label>
        <label>Введите имя иконки
          {{ Form::text('menu_icon', $value = null, ['id'=>'menu-icon', 'autocomplete'=>'off']) }}
        </label>
        <input type="hidden" name="site_id" value="{{ $site->id }}">
        <input type="hidden" name="navigation_id" class="navigation-id">
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        {{ Form::submit('Сохранить', ['data-close', 'class'=>'button modal-button', 'id'=>'submit-menu-edit']) }}
      </div>
    </div>
  {{ Form::close() }}
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
      $('#form-navigation-edit').attr('action', '/navigations/' + id);
      // Сам ajax запрос
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "/navigations/" + id + "/edit",
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
  // Добавление отдела или должности
  // Переносим id родителя и филиала в модалку
  $(document).on('click', '[data-open="menu-add"]', function() {
    var parent = $(this).closest('.parent').attr('id').split('-')[1];
    var navigation = $(this).closest('.first-item').attr('id').split('-')[1];
    if (parent == navigation) {
      $('.navigation-id').val(navigation);
    } else {
      $('.menu-parent-id').val(parent);
      $('.navigation-id').val(navigation);
    }
    // alert(parent);
    // Заполняем скрытые инпуты филиала и родителя
    
    // $('#dep-parent-id-field').val(parent);
    // $('#pos-filial-id-field').val(filial);
    // $('#pos-parent-id-field').val(parent);
    // Отмечам в какой пункт будем добавлять
    // $('#dep-tree-select>[value="' + parent + '"]').prop('selected', true);
    // $('#pos-tree-select>[value="' + parent + '"]').prop('selected', true);
  });
  // Редактируем меню
  $(document).on('click', '[data-open="menu-edit"]', function() {
    var id = $(this).closest('.parent').attr('id').split('-')[1];
    // Отмечам в какой пункт будем добавлять
    // $('#dep-select-edit>[value="' + id + '"]').prop('selected', true);
    // // Блокируем кнопку
    // $('#submit-menu-edit').prop('disabled', false);
      // Получаем данные о филиале
      $('#form-menu-edit').attr('action', '/menus/' + id);
      // Сам ajax запрос
      // alert(id);
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "/menus/" + id + "/edit",
        type: "GET",
        success: function(date){
          var result = $.parseJSON(date);
          // alert(result);
          $('#menu-name').val(result.menu_name);
          $('#menu-icon').val(result.menu_icon);
          $('.navigation-id').val(result.navigation_id);
          // $('#dep-city-id-field-edit').val(result.city_id);
          // $('#menu-db-edit').val(1);
          // $('#dep-filial-id-field-edit').val(result.section_id);
          // $('#depaprment-parent-id>[value="' + result.menu_parent_id + '"]').prop('selected', true);
        }
      });
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
  if ({{ $data != null }})  {
    // Общие правила
    // Подсвечиваем навигацию
    $('#navigations-' + {{ $data['navigation_id'] }}).addClass('first-active').find('.icon-list:first').attr('aria-hidden', 'false').css('display', 'block');
    // Открываем навигацию
    var firstItem = $('#navigations-' + {{ $data['navigation_id'] }}).find('.medium-list:first');
    // Открываем аккордион
    $('#content-list').foundation('down', firstItem);
    // Отображаем подпункт меню без страницы
    if ({{ $data['menu_id'] }} !== 0) {
      // Подсвечиваем ссылку
      $('#menus-{{ $data['menu_id'] }}').find('.medium-link').addClass('medium-active');
      // Открываем меню удаления в середине
      $('#menus-{{ $data['menu_id'] }}').find('.icon-list').attr('aria-hidden', 'false').css('display', 'block');
      if ($('#menus-' + {{ $data['menu_id'] }}).is('.medium-list')) {
       // Открываем навигацию
       var mediumItem = $('#menus-' + {{ $data['menu_id'] }}).find('.medium-list');
      // Открываем аккордион
      $('#content-list').foundation('down', mediumItem);
      };
      
    };

    // 

        // Перебираем родителей и посвечиваем их
    // var parents = $('#menu-{{ $data['menu_id'] }}').parents('.parent');
    // for (var i = 0; i < parents.length; i++) {
    //   $(parents[i]).find('.medium-link').addClass('medium-active');
    //   $(parents[i]).find('.icon-list').css('display', 'block').attr('aria-hiden', 'false');
    // };
        
  }
  @endif
});
</script>

{{-- Скрипт подсветки многоуровневого меню --}}
@include('includes.multilevel-menu-active-scripts')

{{-- Скрипт модалки удаления ajax --}}
@include('includes.modals.modal-delete-ajax-script')

{{-- Скрипт модалки удаления ajax --}}
@include('includes.modals.modal-delete-script')

@endsection