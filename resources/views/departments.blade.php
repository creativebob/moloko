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
        <h2 class="header-content">{{ $page_info->page_name }}</h2>
        @can('create', App\Department::class)
        <a class="icon-add sprite" data-open="filial-add"></a>
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

    @if($departments_tree)
      <ul class="vertical menu accordion-menu content-list" id="content-list" data-accordion-menu data-allow-all-closed data-multi-open="false" data-slide-speed="250">
        @foreach ($departments_tree as $department)
         
          @if($department['filial_status'] == 1)
            {{-- Если филиал --}}
            <li class="first-item parent 
            @if (isset($department['children']) && isset($department['staff']))
            parent-item
            @endif" id="departments-{{ $department['id'] }}" data-name="{{ $department['department_name'] }}">
              <ul class="icon-list">
                <li>
                  @can('create', App\Department::class)
                  <div class="icon-list-add sprite" data-open="department-add"></div>
                  @endcan
                </li>
                <li>
                  @if($department['edit'] == 1)
                  <div class="icon-list-edit sprite" data-open="filial-edit"></div>
                  @endif
                </li>
                <li>
                  @if ((count($department['staff']) == 0) && !isset($department['children']) && ($department['system_item'] != 1) && $department['delete'] == 1)
                    <div class="icon-list-delete sprite" data-open="item-delete"></div>
                  @endif
                </li>
              </ul>
              <a data-list="" class="first-link">
                <div class="list-title">
                  <div class="icon-open sprite"></div>
                  <span class="first-item-name">{{ $department['department_name'] }}</span>
                  <span class="number">{{ $department['count'] }}</span>
                </div>
              </a>
            @if (isset($department['staff']) || isset($department['children']))
              <ul class="menu vertical medium-list accordion-menu" data-accordion-menu data-allow-all-closed data-multi-open="false">
                @if (isset($department['staff']))
                  @foreach($department['staff'] as $staffer)
                    <li class="medium-item parent" id="staff-{{ $staffer['id'] }}" data-name="{{ $staffer['position']['position_name'] }}">
                      <div class="medium-as-last">{{ $staffer['position']['position_name'] }} ( <a href="/staff/{{ $staffer['id'] }}/edit" class="link-recursion">
                      @if (isset($staffer['user_id']))
                        {{ $staffer['user']['first_name'] }} {{ $staffer['user']['second_name'] }}
                      @else
                        Вакансия
                      @endif
                      </a> ) 
                        <ul class="icon-list">
                          <li>
                            @if(($staffer['system_item'] != 1) && ($staffer['delete'] == 1) && !isset($staffer['user']))
                              <div class="icon-list-delete sprite" data-open="item-delete"></div>
                            @endif
                          </li>
                        </ul>
                      </div>
                    </li>
                  @endforeach
                @endif
                @if (isset($department['children']))
                  @foreach($department['children'] as $department)
                    @include('departments-list', $department)
                  @endforeach
                @endif
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
{{-- Модалка добавления филиала --}}
<div class="reveal" id="filial-add" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ филиала</h5>
    </div>
  </div>
  {{ Form::open(['url'=>'/departments', 'id' => 'form-filial-add', 'data-abide', 'novalidate']) }}
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 small-offset-1 cell">
        <label class="input-icon">Введите город
          {{ Form::text('city_name', $value = null, ['id'=>'city-name-field-add', 'autocomplete'=>'off', 'required']) }}
          <div class="sprite-input-right icon-success load">лол</div>
          <span class="form-error">Уж постарайтесь, введите хотя бы 3 символа!</span>
        </label>
        </label>
        <label>Название филиала
           {{ Form::text('filial_name', $value = null, ['class'=>'filial-name-field', 'autocomplete'=>'off', 'required']) }}
        </label>
        <label>Адресс филиала
           {{ Form::text('filial_address', $value = null, ['class'=>'filial-address-field', 'autocomplete'=>'off', 'required']) }}
        </label>
        <label>Телефон филиала
           {{ Form::text('filial_phone', $value = null, ['class'=>'filial-phone-field phone-field', 'autocomplete'=>'off', 'required']) }}
        </label>
        <input type="hidden" name="city_id" id="city-id-field-add">
        <input type="hidden" name="filial_database" id="filial-database-add" value="0">
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        {{ Form::submit('Сохранить', ['class'=>'button modal-button', 'id'=>'submit-filial-add']) }}
      </div>
    </div>
  {{ Form::close() }}
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>
{{-- Конец модалки добавления филиала --}}

{{-- Модалка редактирования филиала --}}
<div class="reveal" id="filial-edit" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>Редактирование филиала</h5>
    </div>
  </div>
  {{ Form::open(['id' => 'form-filial-edit', 'data-abide', 'novalidate']) }}
  {{ method_field('PATCH') }}
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 small-offset-1 cell">
         <label class="input-icon">Название города
          {{ Form::text('city_name', $value = null, ['id'=>'city-name-field-edit', 'autocomplete'=>'off', 'required']) }}
          <div class="sprite-input-right icon-success load">лол</div>
          <span class="form-error">Уж постарайтесь, введите хотя бы 3 символа!</span>
        </label>
        </label>
        <label>Название филиала
           {{ Form::text('filial_name', $value = null, ['class'=>'filial-name-field', 'autocomplete'=>'off', 'required']) }}
        </label>
        <label>Адресс филиала
           {{ Form::text('filial_address', $value = null, ['class'=>'filial-address-field', 'autocomplete'=>'off', 'required']) }}
        </label>
        <label>Телефон филиала
           {{ Form::text('filial_phone', $value = null, ['class'=>'filial-phone-field phone-field', 'autocomplete'=>'off', 'required']) }}
        </label>
        <input type="hidden" name="city_id" id="city-id-field-edit">
        <input type="hidden" name="filial_database" id="filial-database-edit" value="0">
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        {{ Form::submit('Сохранить', ['class'=>'button modal-button', 'id'=>'submit-filial-edit']) }}
      </div>
    </div>
  {{ Form::close() }}
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>
{{-- Конец модалки редактирования филиала --}}

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
              <label>Добавляем отдел в:
                {{ Form::select('departments_list', $departments_list, null, ['id'=>'dep-tree-select']) }}
              </label>
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
              <input type="hidden" name="filial_id" id="dep-filial-id-field">
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
              <label>Добавляем должность в:
                {{ Form::select('departments_list', $departments_list, null, ['id'=>'pos-tree-select']) }}
              </label>
              <label>Должность
                {{ Form::select('position_id', $positions_list) }}
              </label>
              <input type="hidden" name="filial_id" id="pos-filial-id-field">
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
        <label>Отдел находится в:
          {{ Form::select('department_parent_id', $departments_list, null, ['id'=>'dep-select-edit']) }}
        </label>
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
        <input type="hidden" name="filial_id" id="dep-filial-id-field-edit">
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

@include('includes.scripts.city-list')

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

  // При добавлении филиала ищем город в нашей базе
  $('#city-name-field-add').keyup(function() {
    // Блокируем кнопку
    $('#submit-filial-add').prop('disabled', true);
    // $('#filial-database-add').val(0);
    checkCity();
  });
  // При клике на город в модальном окне добавления филиала заполняем инпуты
  $(document).on('click', '#form-filial-add .city-add', function() {
    var cityId = $(this).closest('tr').find('a.city-add').data('city-id');
    var cityName = $(this).closest('tr').find('[data-city-id=' + cityId +']').html();
    $('#city-id-field-add').val(cityId);
    $('#city-name-field-add').val(cityName);
    $('.table-over').remove();

    $('#submit-filial-add').prop('disabled', false);
    $('#filial-database-add').val(1);
    $('.icon-success').removeClass('load');

    if($('#city-id-field-add').val() != '') {

    };
  });
  // Редактируем филиал
  $(document).on('click', '[data-open="filial-edit"]', function() {
    // Блокируем кнопку
    $('#submit-filial-edit').prop('disabled', false);
      // Получаем данные о филиале
      var id = $(this).closest('.parent').attr('id').split('-')[1];
      $('#form-filial-edit').attr('action', '/departments/' + id);
      // Сам ajax запрос
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "/departments/" + id + "/edit",
        type: "GET",
        success: function(date){
          var result = $.parseJSON(date);
          $('#city-name-field-edit').val(result.city_name);
          $('.filial-name-field').val(result.filial_name);
          $('.filial-address-field').val(result.filial_address);
          $('.filial-phone-field').val(result.filial_phone);
          $('#city-id-field-edit').val(result.city_id);
          $('#filial-database-edit').val(1);
        }
      });
  });
  // При редактировании города филиала  
  $('#city-name-field-edit').keyup(function() {
    // Блокируем кнопку
    $('#submit-filial-edit').prop('disabled', true);
    $('#filial-database-edit').val(0);
    // Получаем фрагмент текста
    var city = $('#city-name-field-edit').val();
    var filialDb = $('#filial-database-edit').val();
    checkCity(city, filialDb);
  });
  // При клике на город в модальном окне редактирования филиала заполняем инпуты
  $(document).on('click', '#form-filial-edit .city-add', function() {
    var cityId = $(this).closest('tr').find('a.city-add').data('city-id');
    var cityName = $(this).closest('tr').find('[data-city-id=' + cityId +']').html();
    $('#city-id-field-edit').val(cityId);
    $('#city-name-field-edit').val(cityName);
    $('.table-over').remove();

    $('#submit-filial-edit').prop('disabled', false);
    $('#filial-database-edit').val(1);
    $('.icon-success').removeClass('load');

    if($('#city-id-field-edit').val() != '') {

    };
  });

  // Добавление отдела или должности
  // Переносим id родителя и филиала в модалку
  $(document).on('click', '[data-open="department-add"]', function() {
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
  $(document).on('click', '[data-open="department-edit"]', function() {
    var id = $(this).closest('.parent').attr('id').split('-')[1];
    // Отмечам в какой пункт будем добавлять
    // $('#dep-select-edit>[value="' + id + '"]').prop('selected', true);
    // Блокируем кнопку
    $('#submit-department-edit').prop('disabled', false);
      // Получаем данные о филиале
      $('#form-department-edit').attr('action', '/departments/' + id);
      // Сам ajax запрос
      // alert(id);
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "/departments/" + id + "/edit",
        type: "GET",
        success: function(date){
          var result = $.parseJSON(date);
          // alert(result);
          $('#dep-city-name-field-edit').val(result.city_name);
          $('.department-name-field').val(result.department_name);
          $('.department-address-field').val(result.filial_address);
          $('.department-phone-field').val(result.filial_phone);
          $('#dep-city-id-field-edit').val(result.city_id);
          $('#department-db-edit').val(1);
          $('#dep-filial-id-field-edit').val(result.filial_id);
          $('#dep-select-edit>[value="' + result.department_parent_id + '"]').prop('selected', true);
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
  $('#department-name-field').keyup(function() {
    // Блокируем кнопку
    $('#submit-department-add').prop('disabled', true);
    $('#department-database').val(0);
    // Получаем фрагмент текста
    var department = $('#department-name-field').val();
    // Первая буква отдела заглавная
    department = department.charAt(0).toUpperCase() + department.substr(1);
    // alert(department);
    // Смотрим сколько символов
    var lenDepartment = department.length;
    // Если символов больше 3 - делаем запрос
    if (lenDepartment > 2) {
      // Сам ajax запрос
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "/departments",
        type: "POST",
        data: {department_name: department, filial_id: $('#filial-id-field').val(), department_database: $('#department-database').val()},
        beforeSend: function () {
          $('.icon-load').removeClass('load');
        },
        success: function(date){
          $('.icon-load').addClass('load');
          // Удаляем все значения чтобы вписать новые
          $('#tbody-department-add>tr').remove();
          var result = $.parseJSON(date);
          var data = '';
          // alert(result.error_status);
          if (result.error_status == 0) {
            data = "<tr><td>Данный отдел уже сущестует в этой компании!</td></tr>";
            // Выводим пришедшие данные на страницу
            $('#tbody-department-add').append(data);
          };
          if (result.error_status == 1) {
            $('#department-database').val(1);
            $('#submit-department-add').prop('disabled', false);
          };
        }
      });
    };
    if (lenDepartment <= 2) {
      // Удаляем все значения, если символов меньше 3х
      $('#tbody-department-add>tr').remove();
      $('.item-error').remove();
      // $('#city-name-field').val('');
    };
  });

  // При закрытии модалки очищаем поля
  $(document).on('click', '.close-modal', function() {
    $('#city-name-field-add').val('');
    $('#city-name-field-edit').val('');
    $('.filial-name-field').val('');
    $('.filial-address-field').val('');
    $('.filial-phone-field').val('');
    $('.city-id-field').val('');
    $('.table-over').val('');
    
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
  // @if(!empty($data))
  //   backlightItems ();
  // @endif
  // Открываем меню и подменю, если только что добавили населенный пункт
  @if(!empty($data))
    // Общие правила
    // Подсвечиваем Филиал
    $('#departments-{{ $data['section_id'] }}').addClass('first-active').find('.icon-list:first').attr('aria-hidden', 'false').css('display', 'block');
     // Открываем только Филиал
     

   // Отображаем отдел и филиал, без должностей
    if ({{ $data['item_id'] }} == 0) {
      var firstItem = $('#departments-{{ $data['section_id'] }}').find('.medium-list:first');
      // Открываем аккордион
      $('#content-list').foundation('down', firstItem); 
    } else {
      // Перебираем родителей и подсвечиваем их
      $.each($('#departments-{{ $data['item_id'] }}').parents('.parent-item').get().reverse(), function (index) {
        $(this).children('.medium-link:first').addClass('medium-active');
        $(this).children('.icon-list:first').attr('aria-hidden', 'false').css('display', 'block');
        $('#content-list').foundation('down', $(this).closest('.medium-list'));
      });
      // Если родитель содержит не пустой элемент
      if ($('#departments-{{ $data['item_id'] }}').parent('.parent').has('.parent-item')) {
        $('#content-list').foundation('down', $('#departments-{{ $data['item_id'] }}').closest('.medium-list'));
      };
      // Если элемент содержит вложенность, открываем его
      if ($('#departments-{{ $data['item_id'] }}').hasClass('parent')) {
        $('#departments-{{ $data['item_id'] }}').children('.medium-link:first').addClass('medium-active');
        $('#departments-{{ $data['item_id'] }}').children('.icon-list:first').attr('aria-hidden', 'false').css('display', 'block');
        $('#content-list').foundation('down', $('#departments-{{ $data['item_id'] }}').children('.medium-list:first'));
        // alert('open');
      }
    };
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