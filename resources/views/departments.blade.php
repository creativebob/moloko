@extends('layouts.app')
 
@section('inhead')

@endsection

@section('title', 'Филиалы')

@section('title-content')
<div data-sticky-container id="head-content">
  <div class="sticky sticky-topbar" id="head-sticky" data-sticky data-margin-top="2.4" data-options="stickyOn: small;" data-top-anchor="head-content:top">
    <div class="top-bar head-content">
      <div class="top-bar-left">
        <h2 class="header-content">Филиалы</h2>
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




    @if($departments)
    <ul class="vertical menu accordion-menu content-list" id="content-list" data-accordion-menu data-allow-all-closed data-multi-open="false" data-slide-speed="250">
      @each('departments-list', $departments, 'department')
    </ul>
    @endif


    

    <!-- <ul class="vertical menu accordion-menu content-list" id="content-list" data-accordion-menu data-allow-all-closed data-multi-open="false" data-slide-speed="250">
      
    </ul> -->


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
  {{ Form::open(['id' => 'form-filial-add', 'data-abide', 'novalidate']) }}
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 medium-4 cell">
        <label class="input-icon">Введите город
          {{ Form::text('city_name', $value = null, ['id'=>'city-name-field', 'autocomplete'=>'off', 'required']) }}
          <div class="sprite-input-right icon-success load">лол</div>
          <span class="form-error">Уж постарайтесь, введите хотя бы 3 символа!</span>
        </label>
        </label>
        <label>Название филиала
           {{ Form::text('filial_name', $value = null, ['id'=>'filial-name-field', 'autocomplete'=>'off', 'required']) }}
        </label>
        <label>Адресс филиала
           {{ Form::text('filial_address', $value = null, ['id'=>'filial-address-field', 'autocomplete'=>'off', 'required']) }}
        </label>
        <label>Телефон филиала
           {{ Form::text('filial_phone', $value = null, ['id'=>'filial-phone-field', 'autocomplete'=>'off', 'required', 'class'=>'phone-field']) }}
        </label>
        <input type="hidden" name="city_id" id="city-id-field">
        <input type="hidden" name="filial_database" id="filial-database" value="0">
      </div>
      <div class="small-12 medium-8 cell">
        <table class="table-content-search">
          <caption>Результаты поиска в нашей базе данных:</caption>
          <tbody id="tbody-filial-add">
          </tbody>
        </table>
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        {{ Form::submit('Сохранить', ['data-close', 'class'=>'button modal-button', 'id'=>'submit-filial-add']) }}
      </div>
    </div>
  {{ Form::close() }}
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>
{{-- Конец модалки добавления филиала --}}

{{-- Модалка редактирования области --}}
<div class="reveal" id="region-edit" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ Области</h5>
    </div>
  </div>
  <form action="/cities" method="post">
    {{ csrf_field() }}
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 medium-4 cell">
        <label>Название области
          <input type="text" name="region_name" id="region-title-field" value="lol" autocomplete="off" required>
          <span class="form-error">Уж постарайтесь, введните хотя бы 3 символа!</span>
        </label>
        <input type="hidden" name="region_vk_external_id" id="region-id-field" value="lol">
      </div>
      <div class="small-12 medium-8 cell">
        <table class="table-content-search">
          <caption>Результаты поиска в сторонней базе данных:</caption>
          <tbody id="tbody-region-add">
          </tbody>
        </table>
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        <button class="button modal-button" id="submit-region-add" type="submit" disabled>Сохранить</button>
      </div>
    </div>
  </form>
  <div data-close class="icon-close-modal sprite close-modal"></div> 
</div>
{{-- Конец модалки редактирования области --}}

{{-- Модалка добавления отдела --}}
<div class="reveal" id="department-add" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ отдела</h5>
    </div>
  </div>
  {{ Form::open(['url' => '/departments', 'id' => 'form-department-add']) }}
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 medium-4 cell">
        <label>Название отдела
          {{ Form::text('department_name', $value = null, ['id'=>'department-name-field', 'autocomplete'=>'off', 'required']) }}
          <span class="form-error">Уж постарайтесь, введите хотя бы 2 символа!</span>
        </label>
         <label>Адресс отдела
           {{ Form::text('department_address', $value = null, ['id'=>'department-address-field', 'autocomplete'=>'off']) }}
        </label>
        <label>Телефон отдела
           {{ Form::text('department_phone', $value = null, ['id'=>'department-phone-field', 'autocomplete'=>'off', 'class'=>'phone-field']) }}
        </label>
        <input type="hidden" name="department_database" id="department-database" value="2">
        <input type="hidden" name="filial_id" id="filial-id-field">
      </div>
      <div class="small-12 medium-8 cell">
        <table class="table-content-search">
          <caption>Результаты поиска в сторонней базе данных:</caption>
          <tbody id="tbody-department-add">
          </tbody>
        </table>
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        <button data-close class="button modal-button" id="submit-department-add" type="submit" disabled>Сохранить</button>
      </div>
    </div>
  {{ Form::close() }}
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>
{{-- Конец модалки добавления города и района --}}

{{-- Модалка редактирования --}}
<div class="reveal" id="edit" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>РЕДАКТИРОВАНИЕ НАСЕЛЕННОГО ПУНКТА</h5>
    </div>
  </div>
  <div class="grid-x grid-padding-x modal-content inputs">
    <div class="small-10 medium-4 cell">
      <label>Область
        <input type="text" name="" required>
        <span class="form-error">Уж постарайтесь, придумайте что-нибудь!</span>
      </label>
      <label>Район
        <input type="text" name="" required>
        <span class="form-error">Уж постарайтесь, придумайте что-нибудь!</span>
      </label>
    </div>
    <div class="small-12 medium-8 cell">
      <div class="grid-x grid-padding-x">
        <div class="small-10 medium-8 cell">
          <label>Название населенного пункта
            <input type="text" name="" required>
            <span class="form-error">Уж постарайтесь, придумайте что-нибудь!</span>
          </label>
        </div>
      </div>
      <table class="table-content-search">
        <caption>Результаты поиска в сторонней базе данных:</caption>
        <tbody id="tbody-content-search">
          <tr>
            <td><a href="#">Кимильтей</a></td>
            <td><a href="#">Куйтунский район</a></td>
            <td><a href="#">Иркутская область</a></td>
          </tr>
          <tr>
            <td><a href="#">Кимильтей</a></td>
            <td><a href="#">Куйтунский район</a></td>
            <td><a href="#">Иркутская область</a></td>
          </tr>
          <tr>
            <td><a href="#">Кимильтей</a></td>
            <td><a href="#">Куйтунский район</a></td>
            <td><a href="#">Иркутская область</a></td>
          </tr>
        </tbody>
      </table>
      <div class="grid-x ">
        <div class="small-6 small-centered cell">
          <a href="#" class="button modal-button">Сохранить</a>
        </div>
      </div>
    </div>
  </div>
  <div data-close class="icon-close-modal sprite close-modal"></div> 
</div>
{{-- Конец модалки редактирования --}}

{{-- Модалка удаления с refresh --}}
<div class="reveal" id="item-delete" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>удаление области</h5>
    </div>
  </div>
  <div class="grid-x align-center modal-content ">
    <div class="small-10 medium-4 cell">
      <p>Удаляем "<span class="title-delete"></span>", вы уверены?</p>
    </div>
  </div>

  <div class="grid-x align-center grid-padding-x">
    <div class="small-6 medium-4 cell">
      {!! Form::open(['id' => 'form-item-del']) !!}
      {{ method_field('DELETE') }}
        <button data-close class="button modal-button delete-button" type="submit">Удалить</button>
      {!! Form::close() !!}
    </div>
    <div class="small-6 medium-4 cell">
      <button data-close class="button modal-button" id="save-button" type="submit">Отменить</button>
    </div>
  </div>
  <div data-close class="icon-close-modal sprite close-modal"></div> 
</div>
{{-- Конец модалки удаления с refresh --}}

{{-- Модалка удаления ajax --}}
@include('includes.modals.modal-delete-ajax')

@endsection

@section('scripts')
<script type="text/javascript" src="/js/jquery.inputmask.min.js"></script>
<script type="text/javascript">
$(function() {
  $('.phone-field').mask('8 (000) 000-00-00');
  // Присваиваем при клике на первый элемент списка активный класс
  $(document).on('click', '.first-link', function() {
    if ($(this).parent('.first-item').hasClass('first-active')) {
      $(this).parent('.first-item').removeClass('first-active');
      $('.medium-active').removeClass('medium-active');
    } else {
      $('.content-list .first-active').removeClass('first-active');
      $(this).parent('.first-item').addClass('first-active');
      $('.medium-active').removeClass('medium-active');
    };
  });
  // Отслеживаем плюсики во вложенных элементах
  $(document).on('click', '.medium-link', function() {
    console.log('Видим клик по среднему пункту');
    if ($(this).hasClass('medium-active')) {
      $(".medium-active").removeClass('medium-active');
      console.log('Видим что имеет medium-active');
      $(this).removeClass('medium-active');
      $(this).closest('.parent').attr('aria-expanded', 'false');
      var target = $(this).closest('.parent').find('.last-list');
      $('#content-list').foundation('toggle', target);
    } else {
      $(".medium-active").removeClass('medium-active');
      console.log('Видим что не имеет medium-active');
      $(this).addClass('medium-active');
    };
    // Перебираем родителей и посвечиваем их
    var parents = $(this).parents('.medium-list');
    for (var i = 0; i < parents.length; i++) {
      $(parents[i]).parent('li').children('a').addClass('medium-active');
    };
  });

  // Функция появления окна с ошибкой
  function showError (msg) {
    var error = "<div class=\"callout item-error\" data-closable><p>" + msg + "</p><button class=\"close-button error-close\" aria-label=\"Dismiss alert\" type=\"button\" data-close><span aria-hidden=\"true\">&times;</span></button></div>";
    return error;
  };

  // При добавлении филиала ищем город в нашей базе
  $('#city-name-field').keyup(function() {
    // Блокируем кнопку
    $('#submit-filial-add').prop('disabled', true);
    $('#filial-database').val(0);
    // Получаем фрагмент текста
    var city = $('#city-name-field').val();
    // Смотрим сколько символов
    var lenCity = city.length;
    // Если символов больше 3 - делаем запрос
    if (lenCity > 3) {
      // Сам ajax запрос
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "/departments",
        type: "POST",
        data: {city_name: $('#city-name-field').val(), filial_database: $('#filial-database').val()},
        beforeSend: function () {
          $('.icon-load').removeClass('load');
        },
        success: function(date){
          $('.icon-load').addClass('load');
          // Удаляем все значения чтобы вписать новые
          $('#tbody-filial-add>tr').remove();
          var result = $.parseJSON(date);
          var data = '';
          // alert(result.error_status);
          if (result.error_status == 0) {
            // Перебираем циклом
            for (var i = 0; i < result.count; i++) {
              data = data + "<tr data-tr=\"" + i + "\"><td><a class=\"city-add\" data-city-id=\"" + result.cities.city_id[i] + "\">" + result.cities.city_name[i] + "</a></td><td><a class=\"city-add\">" + result.cities.area_name[i] + "</a></td><td><a class=\"city-add\">" + result.cities.region_name[i] + "</a></td></tr>";
            };
          };
          if (result.error_status == 1) {
            data = "<tr><td>Населенный пункт не существует в нашей базе данных, добавьте его!</td></tr>";
          };
          // Выводим пришедшие данные на страницу
          $('#tbody-filial-add').append(data);
        }
      });
    };
    if (lenCity <= 3) {
      // Удаляем все значения, если символов меньше 3х
      $('#tbody-filial-add>tr').remove();
      $('.item-error').remove();
      // $('#city-name-field').val('');
    };
  });
  // При клике на город в модальном окне заполняем инпуты
  $(document).on('click', '.city-add', function() {
    var cityId = $(this).closest('tr').find('a.city-add').data('city-id');
    var cityName = $(this).closest('tr').find('[data-city-id=' + cityId +']').html();
    $('#city-id-field').val(cityId);
    $('#city-name-field').val(cityName);
    $('#tbody-filial-add>tr').remove();

    $('#submit-filial-add').prop('disabled', false);
    $('#filial-database').val(1);
    $('.icon-success').removeClass('load');

    if($('#city-id-field').val() != '') {

    };
  });
  // Сохраняем филиал в базу и отображаем на странице по ajax   
  $('#submit-filial-add').click(function (event) {
    //чтобы не перезагружалась форма
    event.preventDefault(); 
    // Дергаем все данные формы
    var formFilial = $('#form-filial-add').serialize();
    // var region = {region_vk_external_id: $('#region-id-field').val(), region_name:$('#region-name-field').val()};
    // alert(formFilial);
    // Ajax
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/departments",
      type: "POST",
      data: formFilial,
      success: function (data) {
        var result = $.parseJSON(data);

        result = "<li class=\"first-item parent\" id=\"departments-" + result.filial_id + "\" data-name=\"" + result.filial_name + "\"><ul class=\"icon-list\"><li><div class=\"icon-list-add sprite\" data-open=\"department-add\"></div></li><li><div class=\"icon-list-delete sprite\" data-open=\"item-delete-ajax\"></div></li></ul><a data-list=\"" + result.filial_id +"\" class=\"first-link\"><div class=\"list-title\"><div class=\"icon-open sprite\"></div><span class=\"first-item-name\">" + result.filial_name + "</span><span class=\"number\">0</span></div></a>";

        // Выводим пришедшие данные на страницу
        $('#content-list').append(result);
        // Обнуляем модалку
        $('#city-name-field').val('');
        $('#filial-name-field').val('');

        $('#filial-name-field').val('');
        $('#filial-address-field').val('');
        $('#filial-phone-field').val('');

        $('.icon-success').addClass('load');
        $('#filial-database').val(0);
        $('#submit-filial-add').prop('disabled', true);
        $('#tbody-filial-add>tr').remove();
      }
    });
  });


  // При добавлении филиала ищем город в нашей базе
  // $('#city-name-department-field').keyup(function() {
  //   // Блокируем кнопку
  //   $('#submit-department-add').prop('disabled', true);
  //   $('#department-database').val(2);
  //   // Получаем фрагмент текста
  //   var city = $('#city-name-department-field').val();
  //   // Смотрим сколько символов
  //   var lenCity = city.length;
  //   // Если символов больше 3 - делаем запрос
  //   if (lenCity > 3) {
  //     // Сам ajax запрос
  //     $.ajax({
  //       headers: {
  //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  //       },
  //       url: "/departments",
  //       type: "POST",
  //       data: {city_name: $('#city-name-field').val(), filial_database: $('#filial-database').val()},
  //       beforeSend: function () {
  //         $('.icon-load').removeClass('load');
  //       },
  //       success: function(date){
  //         $('.icon-load').addClass('load');
  //         // Удаляем все значения чтобы вписать новые
  //         $('#tbody-filial-add>tr').remove();
  //         var result = $.parseJSON(date);
  //         var data = '';
  //         // alert(result.error_status);
  //         if (result.error_status == 0) {
  //           // Перебираем циклом
  //           for (var i = 0; i < result.count; i++) {
  //             data = data + "<tr data-tr=\"" + i + "\"><td><a class=\"city-add\" data-city-id=\"" + result.cities.city_id[i] + "\">" + result.cities.city_name[i] + "</a></td><td><a class=\"city-add\">" + result.cities.area_name[i] + "</a></td><td><a class=\"city-add\">" + result.cities.region_name[i] + "</a></td></tr>";
  //           };
  //         };
  //         if (result.error_status == 1) {
  //           data = "<tr><td>Населенный пункт не существует в нашей базе данных, добавьте его!</td></tr>";
  //         };
  //         // Выводим пришедшие данные на страницу
  //         $('#tbody-filial-add').append(data);
  //       }
  //     });
  //   };
  //   if (lenCity <= 3) {
  //     // Удаляем все значения, если символов меньше 3х
  //     $('#tbody-filial-add>tr').remove();
  //     $('.item-error').remove();
  //     // $('#city-name-field').val('');
  //   };
  // });
  // При клике на город в модальном окне заполняем инпуты
  // $(document).on('click', '.city-add', function() {
  //   var cityId = $(this).closest('tr').find('a.city-add').data('city-id');
  //   var cityName = $(this).closest('tr').find('[data-city-id=' + cityId +']').html();
  //   $('#city-id-field').val(cityId);
  //   $('#city-name-field').val(cityName);
  //   $('#tbody-filial-add>tr').remove();

  //   $('#submit-filial-add').prop('disabled', false);
  //   $('#filial-database').val(1);
  //   $('.icon-success').removeClass('load');

  //   if($('#city-id-field').val() != '') {


  //   };
  // });

  // Переносим id родителя в модалку
  $(document).on('click', '[data-open="department-add"]', function() {
    var parent = $(this).closest('.parent');
    var id = parent.attr('id').split('-')[1];
    $('#filial-id-field').val(id);

  });
  // Чекаем отдел в нашей бд
  $('#department-name-field').keyup(function() {
    // Блокируем кнопку
    $('#submit-department-add').prop('disabled', true);
    $('#department-database').val(2);
    // Получаем фрагмент текста
    var department = $('#department-name-field').val();
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
        data: {department_name: $('#department-name-field').val(), filial_id: $('#filial-id-field').val(), department_database: $('#department-database').val()},
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
            $('#department-database').val(3);
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

  // Сохраняем филиал в базу и отображаем на странице по ajax   
  // $('#submit-department-add').click(function (event) {
  //   //чтобы не перезагружалась форма
  //   event.preventDefault(); 
  //   // Дергаем все данные формы
  //   var formFilial = $('#form-filial-add').serialize();
  //   // var region = {region_vk_external_id: $('#region-id-field').val(), region_name:$('#region-name-field').val()};
  //   // Ajax
  //   $.ajax({
  //     headers: {
  //       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  //     },
  //     url: "/departments",
  //     type: "POST",
  //     // data: region,
  //     data: formFilial,
  //     success: function (data) {
  //       var result = $.parseJSON(data);

  //       result = "<li class=\"first-item parent\" id=\"departments-" + result.filial_id + "\" data-name=\"" + result.filial_name + "\"><ul class=\"icon-list\"><li><div class=\"icon-list-add sprite\" data-open=\"department-add\"></div></li><li><div class=\"icon-list-delete sprite\" data-open=\"item-delete-ajax\"></div></li></ul><a data-list=\"" + result.filial_id +"\" class=\"first-link\"><div class=\"list-title\"><div class=\"icon-open sprite\"></div><span class=\"first-item-name\">" + result.filial_name + "</span><span class=\"number\">0</span></div></a>";

  //       // Выводим пришедшие данные на страницу
  //       $('#content-list').append(result);
  //       // Обнуляем модалку
  //       $('#city-name-field').val('');
  //       $('#filial-name-field').val('');

  //       $('#filial-name-field').val('');
  //       $('#filial-address-field').val('');
  //       $('#filial-phone-field').val('');

  //       $('.icon-success').addClass('load');
  //       $('#filial-database').val(0);
  //       $('#submit-filial-add').prop('disabled', true);
  //       $('#tbody-filial-add>tr').remove();
  //     }
  //   });
  // });



  

  
  // При закрытии модалки очищаем поля
  $(document).on('click', '.add-item', function() {
    $('#tbody-city-add>tr').remove();
    $('#city-id-field').val('');
    $('#city-name-field').val('');
    $('#area-name').val('');
    $('#region-name').val('');
    $('.item-error').remove();
    $('#tbody-region-add>tr').remove();
    $('#region-id-field').val('');
    $('#region-name-field').val('');
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

  // Мягкое удаление с refresh
  $(document).on('click', '[data-open="item-delete"]', function() {
    // находим описание сущности, id и название удаляемого элемента в родителе
    var parent = $(this).closest('.parent');
    var type = parent.attr('id').split('-')[0];
    var id = parent.attr('id').split('-')[1];
    var name = parent.data('name');
    $('.title-delete').text(name);
    $('.delete-button').attr('id', 'del-' + type + '-' + id);
    $('#form-item-del').attr('action', '/' + type + '/' + id);
  });

  // Открываем меню и подменю, если только что добавили населенный пункт
  // Открываем меню и подменю, если только что добавили населенный пункт
  @if(!empty($data))
  if ({{ $data != null }})  {

    // Общие правила
    // Подсвечиваем область
    $('#departments-' + {{ $data['parent_id'] }}).addClass('first-active').find('.icon-list:first-child').attr('aria-hidden', 'false').css('display', 'block');
    // Открываем область
    var firstItem = $('#departments-' + {{ $data['parent_id'] }}).find('.medium-list');
    // Открываем аккордионы
    $('#content-list').foundation('down', firstItem);

    // 
  
      // Перебираем родителей и посвечиваем их
  //   var parents = $(this).parents('.medium-list');
  //   for (var i = 0; i < parents.length; i++) {
  //     $(parents[i]).parent('li').children('a').addClass('medium-active');
  //   };
  // });
        
  }
  @endif
});
</script>

{{-- Скрипт модалки удаления ajax --}}
@include('includes.modals.modal-delete-ajax-script')

@endsection