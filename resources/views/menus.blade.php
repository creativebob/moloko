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
        <h2 class="header-content">{{ $navigation->site->site_name or 'Список менюшек'}} {{ $navigation->navigation_name}} </h2>
        <a class="icon-add sprite" data-open="section-add"></a>
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
    
    @if($menu_tree)
      <ul class="vertical menu accordion-menu content-list" id="content-list" data-accordion-menu data-allow-all-closed data-multi-open="false" data-slide-speed="250">
        @foreach ($menu_tree as $menu)

        @if($menu['menu_parent_id'] == null)
          {{-- Если Подкатегория --}}
          <li class="first-item parent" id="menus-{{ $menu['id'] }}" data-name="{{ $menu['menu_name'] }}">
            <ul class="icon-list">
              <li><div class="icon-list-add sprite" data-open="menu-add"></div></li>
              <li><div class="icon-list-edit sprite" data-open="section-edit"></div></li>
              <li>
                @if (!isset($menu['children']))
                  <div class="icon-list-delete sprite" data-open="item-delete"></div>
                @endif
              </li>
            </ul>
            <a data-list="" class="first-link">
              <div class="list-title">
                <div class="icon-open sprite"></div>
                <span class="first-item-name">{{ $menu['menu_name'] }}</span>
                <span class="number">
                  @if (isset($menu['children']))
                   {{ count($menu['children']) }}
                  @else
                    0
                  @endif
                </span>
              </div>
            </a>
            @if(isset($menu['children']))
             <ul class="menu vertical medium-list accordion-menu" data-accordion-menu data-allow-all-closed data-multi-open="false">
              @foreach($menu['children'] as $menu)
                @include('menu-list', $menu)
              @endforeach
            </ul>
            @endif
        @endif
          
        @endforeach
      </ul>
    @endif
  </div>
</div>
@endsection

@section('modals')
{{-- Модалка добавления раздела --}}
<div class="reveal" id="section-add" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ раздела меню</h5>
    </div>
  </div>
  {{ Form::open(['url' => '/menus', 'id' => 'form-section-add', 'data-abide', 'novalidate']) }}
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 small-offset-1 cell">
        <label class="input-icon">Введите название раздела
          {{ Form::text('section_name', $value = null, ['class'=>'section-name-field-add', 'autocomplete'=>'off', 'required']) }}
          <span class="form-error">Уж постарайтесь, введите хотя бы 3 символа!</span>
        </label>
        @if($navigation->id == 1)
        <label class="input-icon">Введите имя иконки
          {{ Form::text('section_icon', $value = null, ['class'=>'section-icon-field-add', 'autocomplete'=>'off', 'required']) }}
        </label>
        @endif
        <input type="hidden" name="section_db" id="section-add" value="1">
        <input type="hidden" name="navigation_id" class="navigation-id" value="{{ $navigation->id }}">
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        {{ Form::submit('Сохранить', ['class'=>'button modal-button', 'id'=>'submit-section-add']) }}
      </div>
    </div>
  {{ Form::close() }}
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>
{{-- Конец модалки добавления раздела --}}

{{-- Модалка редактирования раздела --}}
<div class="reveal" id="section-edit" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>Редактирование филиала</h5>
    </div>
  </div>
  {{ Form::open(['id' => 'form-section-edit', 'data-abide', 'novalidate']) }}
  {{ method_field('PATCH') }}
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 small-offset-1 cell">
         <label class="input-icon">Введите название раздела
          {{ Form::text('section_name', $value = null, ['class'=>'section-name-field', 'autocomplete'=>'off', 'required']) }}
          <span class="form-error">Уж постарайтесь, введите хотя бы 3 символа!</span>
        </label>
        @if($navigation->id == 1)
        <label class="input-icon">Введите имя иконки
          {{ Form::text('section_icon', $value = null, ['class'=>'section-icon-field', 'autocomplete'=>'off', 'required']) }}
        </label>
        @endif
        <input type="hidden" name="section_db" id="section-add" value="1">
        <input type="hidden" name="navigation_id" class="navigation-id" value="{{ $navigation->id }}">
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        {{ Form::submit('Сохранить', ['class'=>'button modal-button', 'id'=>'submit-section-edit']) }}
      </div>
    </div>
  {{ Form::close() }}
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>
{{-- Конец модалки редактирования раздела --}}

{{-- Модалка добавления отдела --}}
<div class="reveal" id="department-add" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ отдела / должности</h5>
    </div>
  </div>
  <div class="grid-x tabs-wrap tabs-margin-top">
    <div class="small-8 small-offset-2 cell">
      <ul class="tabs-list" data-tabs id="tabs">
        <li class="tabs-title is-active"><a href="#add-department" aria-selected="true">Добавить отдел</a></li>
        <li class="tabs-title"><a data-tabs-target="add-position" href="#add-position">Добавить должность</a></li>
      </ul>
    </div>
  </div>
  <div class="tabs-wrap inputs">
    <div class="tabs-content" data-tabs-content="tabs">
      <!-- Добавляем отдел -->
      <div class="tabs-panel is-active" id="add-department">
        {{ Form::open(['url' => '/departments', 'id' => 'form-department-add']) }}
          <div class="grid-x grid-padding-x modal-content inputs">
            <div class="small-10 small-offset-1 cell">
            
              <label>Название отдела
                {{ Form::text('department_name', $value = null, ['id'=>'department-name-field', 'autocomplete'=>'off', 'required']) }}
                <span class="form-error">Уж постарайтесь, введите хотя бы 2 символа!</span>
              </label>
              <label class="input-icon">Город
                {{ Form::text('city_name', $value = null, ['class'=>'city-name-field', 'autocomplete'=>'off']) }}
                <div class="sprite-input-right icon-success load">лол</div>
                <span class="form-error">Уж постарайтесь, введите хотя бы 3 символа!</span>
              </label>
              <label>Адресс отдела
                {{ Form::text('department_address', $value = null, ['class'=>'department-address-field', 'autocomplete'=>'off']) }}
              </label>
              <label>Телефон отдела
                {{ Form::text('department_phone', $value = null, ['class'=>'department-phone-field phone-field', 'autocomplete'=>'off']) }}
              </label>
              <input type="hidden" name="department_database" id="department-database" value="0">
              <input type="hidden" name="section_id" id="dep-filial-id-field">
              <input type="hidden" name="parent_id" id="dep-parent-id-field">
            </div>
          </div>
          <div class="grid-x align-center">
            <div class="small-6 medium-4 cell">
              {{ Form::submit('Сохранить', ['data-close', 'class'=>'button modal-button', 'id'=>'submit-department-add']) }}
            </div>
          </div>
        {{ Form::close() }}
      </div>
      <!-- Добавляем должность -->
      <div class="tabs-panel" id="add-position">
        {{ Form::open(['url' => '/staff', 'id' => 'form-positions-add']) }}
          <div class="grid-x grid-padding-x modal-content inputs">
            <div class="small-10 small-offset-1 cell">
              {{-- <label>Добавляем должность в:
                {{ Form::select('tree', $tree, null, ['id'=>'pos-tree-select']) }}
              </label> --}}

              <input type="hidden" name="section_id" id="pos-filial-id-field">
              <input type="hidden" name="parent_id" id="pos-parent-id-field">
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
{{-- Конец модалки добавления отдела --}}

{{-- Модалка редактирования отдела --}}
<div class="reveal" id="department-edit" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>Редактирование отдела</h5>
    </div>
  </div>
  <!-- Редактируем отдел -->
  {{ Form::open(['id' => 'form-department-edit']) }}
  {{ method_field('PATCH') }}
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 small-offset-1 cell">

        <label>Название отдела
          {{ Form::text('department_name', $value = null, ['class'=>'department-name-field', 'autocomplete'=>'off', 'required']) }}
          <span class="form-error">Уж постарайтесь, введите хотя бы 2 символа!</span>
        </label>
        <label class="input-icon">Город
          {{ Form::text('city_name', $value = null, ['id'=>'dep-city-name-field-edit', 'autocomplete'=>'off']) }}
          <div class="sprite-input-right icon-success load">лол</div>
          <span class="form-error">Уж постарайтесь, введите хотя бы 3 символа!</span>
        </label>
        <label>Адресс отдела
          {{ Form::text('department_address', $value = null, ['class'=>'department-address-field', 'autocomplete'=>'off']) }}
        </label>
        <label>Телефон отдела
          {{ Form::text('department_phone', $value = null, ['class'=>'department-phone-field phone-field', 'autocomplete'=>'off']) }}
        </label>
        <input type="hidden" name="department_database" id="department-db-edit" value="0">
        <input type="hidden" name="section_id" id="dep-filial-id-field-edit">
        <input type="hidden" name="city_id" id="dep-city-id-field-edit">
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        {{ Form::submit('Сохранить', ['data-close', 'class'=>'button modal-button', 'id'=>'submit-department-edit']) }}
      </div>
    </div>
  {{ Form::close() }}
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>
{{-- Конец модалки отдела --}}

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
  // Редактируем раздел меню
  $(document).on('click', '[data-open="section-edit"]', function() {
      // Получаем данные о разделе
      var id = $(this).closest('.parent').attr('id').split('-')[1];
      $('#form-section-edit').attr('action', '/menus/' + id);
      // Сам ajax запрос
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "/menus/" + id + "/edit",
        type: "GET",
        success: function(date){
          var result = $.parseJSON(date);
          $('.section-name-field').val(result.section_name);
          $('.section-icon-field').val(result.section_icon);
          $('.navigation-edit').val(result.navigation_id);
        }
      });
  });

  // При закрытии модалки очищаем поля
  $(document).on('click', '.close-modal', function() {
    $('.section-name-field').val('');
    $('.section-icon-field').val('');
    $('.navigation-edit').val('');
  });


  // Добавление отдела или должности
  // Переносим id родителя и филиала в модалку
  $(document).on('click', '[data-open="menu-add"]', function() {
    var parent = $(this).closest('.parent').attr('id').split('-')[1];
    var filial = $(this).closest('.first-item').attr('id').split('-')[1];
    // Заполняем скрытые инпуты филиала и родителя
    $('#dep-filial-id-field').val(filial);
    $('#dep-parent-id-field').val(parent);
    $('#pos-filial-id-field').val(filial);
    $('#pos-parent-id-field').val(parent);
    // Отмечам в какой пункт будем добавлять
    $('#dep-tree-select>[value="' + parent + '"]').prop('selected', true);
    $('#pos-tree-select>[value="' + parent + '"]').prop('selected', true);
  });
  // Редактируем отдел
  $(document).on('click', '[data-open="menu-edit"]', function() {
    var id = $(this).closest('.parent').attr('id').split('-')[1];
    // Отмечам в какой пункт будем добавлять
    // $('#dep-select-edit>[value="' + id + '"]').prop('selected', true);
    // Блокируем кнопку
    $('#submit-menu-edit').prop('disabled', false);
      // Получаем данные о филиале
      $('#form-menu-edit').attr('action', '/menu/' + id);
      // Сам ajax запрос
      // alert(id);
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "/menu/" + id + "/edit",
        type: "GET",
        success: function(date){
          var result = $.parseJSON(date);
          // alert(result);
          $('#dep-city-name-field-edit').val(result.city_name);
          $('.menu-name-field').val(result.menu_name);
          $('.menu-address-field').val(result.filial_address);
          $('.menu-phone-field').val(result.filial_phone);
          $('#dep-city-id-field-edit').val(result.city_id);
          $('#menu-db-edit').val(1);
          $('#dep-filial-id-field-edit').val(result.section_id);
          $('#depaprment-parent-id>[value="' + result.menu_parent_id + '"]').prop('selected', true);
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
    // Подсвечиваем область
    $('#menus-' + {{ $data['section_id'] }}).addClass('first-active').find('.icon-list:first-child').attr('aria-hidden', 'false').css('display', 'block');
    // Открываем область
    var firstItem = $('#menus-' + {{ $data['section_id'] }}).find('.medium-list');
    // Открываем аккордионы
    $('#content-list').foundation('down', firstItem);

    // Отображаем отдел и филиал, без должностей
    if (({{ $data['page_id'] }} == 0) && ({{ $data['menu_id'] }} !== 0)) {
      // Подсвечиваем ссылку
      $('#menus-{{ $data['menu_id'] }}').find('.medium-link').addClass('medium-active');
      // Открываем меню удаления в середине
       $('#menus-{{ $data['menu_id'] }}').find('.icon-list').attr('aria-hidden', 'false').css('display', 'block');
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