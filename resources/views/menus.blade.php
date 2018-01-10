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
        <a class="icon-add sprite" data-open="filial-add"></a>
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
              <li><div class="icon-list-edit sprite" data-open="filial-edit"></div></li>
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
                <span class="number">{{ count($menu['children']) }}</span>
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
  function checkCity(city, filialDb) {
    // Смотрим сколько символов
    var lenCity = city.length;
    // Если символов больше 3 - делаем запрос
    if (lenCity > 3) {
      // Сам ajax запрос
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "/menu",
        type: "POST",
        data: {city_name: city, filial_database: filialDb},
        beforeSend: function () {
          $('.icon-load').removeClass('load');
        },
        success: function(date){
          $('.icon-load').addClass('load');
          // Удаляем все значения чтобы вписать новые
          $('.table-over').remove();
          var result = $.parseJSON(date);
          var data = '';
          if (result.error_status == 0) {
            // Перебираем циклом
            data = "<table class=\"table-content-search table-over\"><tbody>";
            for (var i = 0; i < result.count; i++) {
              data = data + "<tr data-tr=\"" + i + "\"><td><a class=\"city-add\" data-city-id=\"" + result.cities.city_id[i] + "\">" + result.cities.city_name[i] + "</a></td><td><a class=\"city-add\">" + result.cities.area_name[i] + "</a></td><td><a class=\"city-add\">" + result.cities.region_name[i] + "</a></td></tr>";
            };
            data = data + "</tbody><table>";
          };
          if (result.error_status == 1) {
            data = "<table class=\"table-content-search table-over\"><tbody><tr><td>Населенный пункт не существует в нашей базе данных, добавьте его!</td></tr></tbody><table>";
          };
          // Выводим пришедшие данные на страницу
          $('.input-icon').after(data);
        }
      });
    };
    if (lenCity <= 3) {
      // Удаляем все значения, если символов меньше 3х
      $('.table-over').remove();
      $('.item-error').remove();
      // $('#city-name-field').val('');
    };
  };
  // При добавлении филиала ищем город в нашей базе
  $('#city-name-field-add').keyup(function() {
    // Блокируем кнопку
    $('#submit-filial-add').prop('disabled', true);
    $('#filial-database-add').val(0);
    // Получаем фрагмент текста
    var city = $('#city-name-field-add').val();
    var filialDb = $('#filial-database-add').val();
    checkCity(city, filialDb);
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
      $('#form-filial-edit').attr('action', '/menu/' + id);
      // Сам ajax запрос
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "/menu/" + id + "/edit",
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
          $('#dep-filial-id-field-edit').val(result.filial_id);
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
        data: {menu_name: menu, filial_id: $('#filial-id-field').val(), menu_database: $('#menu-database').val()},
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

  // Открываем меню и подменю, если только что добавили населенный пункт
  @if(!empty($data))
  if ({{ $data != null }})  {

    // Общие правила
    // Подсвечиваем область
    $('#menu-' + {{ $data['filial_id'] }}).addClass('first-active').find('.icon-list:first-child').attr('aria-hidden', 'false').css('display', 'block');
    // Открываем область
    var firstItem = $('#menu-' + {{ $data['filial_id'] }}).find('.medium-list');
    // Открываем аккордионы
    $('#content-list').foundation('down', firstItem);

    // Отображаем отдел и филиал, без должностей
    if (({{ $data['position_id'] }} == 0) && ({{ $data['menu_id'] }} !== 0)) {
      // Подсвечиваем ссылку
      $('#menu-{{ $data['menu_id'] }}').find('.medium-link').addClass('medium-active');
      // Открываем меню удаления в середине
       $('#menu-{{ $data['menu_id'] }}').find('.icon-list').attr('aria-hidden', 'false').css('display', 'block');
    };

    // 

        // Перебираем родителей и посвечиваем их
    // var parents = $('#menu-{{ $data['menu_id'] }}').parents('.parent');
    // for (var i = 0; i < parents.length; i++) {
    //   $(parents[i]).find('.medium-link').addClass('medium-active');
    //   $(parents[i]).find('.icon-list').css('display', 'block').attr('aria-hiden', 'false');
    // };
  // });

  // Перебираем родителей и посвечиваем их
    // var parents = $(this).parents('.medium-list');
    // for (var i = 0; i < parents.length; i++) {
    //   $(parents[i]).parent('li').children('a').addClass('medium-active');
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