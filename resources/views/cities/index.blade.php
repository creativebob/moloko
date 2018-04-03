@extends('layouts.app')

@section('inhead')
  <meta name="description" content="{{ $page_info->page_description }}" />
  {{-- Скрипты меню в шапке --}}
  @include('includes.scripts.menu-inhead')
@endsection

@section('title', $page_info->page_name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('title-content')
{{-- Меню --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\City::class, 'type' => 'menu'])
@endsection
 
@section('content')
{{-- Список --}}
<div class="grid-x">
  <div class="small-12 cell">
    @if($regions)
      {{-- Шаблон вывода и динамического обновления --}}
      @include('cities.cities-list', $regions)
    @endif
  </div>
</div>
@endsection

@section('modals')
{{-- Модалка добавления области
<div class="reveal rev-large" id="region-add" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ Области</h5>
    </div>
  </div>
  {{ Form::open(['id' => 'form-region-add']) }}
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 medium-4 cell">
        <label class="input-icon">Название области
          <input type="text" name="region_name" id="region-name-field" autocomplete="off" required>
          <div class="sprite-input-right find-status"></div>
          <span class="form-error">Уж постарайтесь, введите хотя бы 3 символа!</span>
        </label>
        <input type="hidden" name="region_vk_external_id" id="region-id-field">
        <input type="hidden" name="region_database" id="region-database" value="0">
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
        <button data-close class="button modal-button" id="submit-region-add" type="submit" disabled>Сохранить</button>
      </div>
    </div>
  {{ Form::close() }}
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>
Конец модалки добавления области --}}

{{-- Модалка добавления города --}}
<div class="reveal rev-large" id="first-add" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ НАСЕЛЕННОГО ПУНКТА</h5>
    </div>
  </div>
  {{ Form::open(['id' => 'form-add']) }}
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 medium-4 cell">
        <label class="input-icon">Название населенного пункта
          {{-- Form::text('city_name', null, ['class'=>'varchar-field', 'id'=>'city-name-field' 'maxlength'=>'30', 'autocomplete'=>'off', 'pattern'=>'[А-Яа-яЁё0-9-_\s]{3,30}', 'required']) --}}
          <input type="text" name="city_name" id="city-name-field" autocomplete="off" pattern="[А-Яа-я0-9-_\s]{3,30}" required>
          <div class="sprite-input-right find-status"></div>
          <div class="item-error">Такой населенный пункт уже существует!</div>
          <span class="form-error">Уж постарайтесь, введите хотя бы 3 символа!</span>
        </label>
        <label>Район
          {{-- Form::text('area_name', null, ['class'=>'varchar-field', 'id'=>'area-name' 'maxlength'=>'30', 'autocomplete'=>'off', 'pattern'=>'[А-Яа-яЁё0-9-_\s]{3,30}', 'readonly']) --}}
          <input type="text" name="area_name" id="area-name" pattern="[А-Яа-яЁё0-9-_\s]{3,30}" readonly>
        </label>
        <label>Область
          {{-- Form::text('region_name', null, ['class'=>'varchar-field', 'id'=>'region_name' 'maxlength'=>'30', 'autocomplete'=>'off', 'pattern'=>'[А-Яа-яЁё0-9-_\s]{3,30}', 'readonly']) --}}
          <input type="text" name="region_name" id="region-name" pattern="[А-Яа-яЁё0-9-_\s]{3,30}" readonly>
        </label>
        <div class="small-12 cell checkbox">
          <input type="checkbox" name="search_all" id="search-all-checkbox">
          <label for="search-all-checkbox"><span class="search-checkbox">Искать везде</span></label>
        </div>
        <input type="hidden" name="city_vk_external_id" id="city-id-field" pattern="[0-9]{1,20}">
        <input type="hidden" name="city_db" id="city-db" value="0" pattern="[0-9]{1}">
      </div>
      <div class="small-12 medium-8 cell">
        <table class="table-content-search">
          <caption>Результаты поиска в сторонней базе данных:</caption>
          <tbody id="tbody-city-add">
          </tbody>
        </table>
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        {{ Form::submit('Сохранить', ['class'=>'button modal-button', 'id'=>'submit-add', 'data-close', 'disabled']) }}
      </div>
    </div>
  {{ Form::close() }}
  <div data-close class="icon-close-modal sprite close-modal"></div> 
</div>
{{-- Конец модалки добавления города и района --}}

{{-- Модалка удаления ajax --}}
@include('includes.modals.modal-delete-ajax')
@endsection

@section('scripts')
{{-- Скрипт модалки удаления ajax --}}
@include('includes.scripts.delete-ajax-script')
{{-- Маска ввода --}}
@include('includes.scripts.inputs-mask')
{{-- Скрипт подсветки многоуровневого меню --}}
@include('includes.scripts.multilevel-menu-active-scripts')
<script type="text/javascript">
$(function() {

  // Обозначаем таймер для проверки
  var timerId;
  var time = 400;

  // Функция получения городов из вк или с фильтром по нашей базе
  function getCityVk () {  
    $('.find-status').removeClass('icon-find-ok');
    // Сам ajax запрос
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/city_vk",
      type: "POST",
      data: {city: $('#city-name-field').val(), checkbox:$('#search-all-checkbox').prop('checked')},
      beforeSend: function () {
        $('.find-status').addClass('icon-load');
      },
      success: function(date){
        $('.find-status').removeClass('icon-load');
        // Удаляем все значения чтобы вписать новые
        $('#tbody-city-add>tr').remove();
        // Парсим
        var result = $.parseJSON(date);
        var data = '';
        if (result.response.count > 0) {
          // Перебираем циклом
          for (var i = 0; i < result.response.count; i++) {
          // Если области нет
          if (result.response.items[i].region == undefined) {
            var regionName = '';
          } else {
            var regionName = result.response.items[i].region;
          };
          // Если района нет
          if (result.response.items[i].area == undefined) {
            var areaName = '';
          } else {
            var areaName = result.response.items[i].area;
          };
          // Если города нет
          if (result.response.items[i].title == undefined) {
            var titleName = '';
          } else {
            var titleName = result.response.items[i].title;
          };
          // Формируем содержимое
          data = data + "<tr data-tr=\"" + i + "\"><td><a class=\"city-add\" data-city-id=\"" + i + "\" data-city_vk_external_id=\"" + result.response.items[i].id + "\">" + titleName + "</a></td><td><a class=\"city-add\" data-area-id=\"" + i + "\" data-area-name=\"" + result.response.items[i].area + "\">" + areaName + "</a></td><td><a class=\"city-add\" data-region-id=\"" + i + "\" data-region-name=\"" + result.response.items[i].region + "\">" + regionName + "</a></td></tr>";
          };
        } else {
          $('.find-status').addClass('icon-find-no');
          data = "<tr><td>Ничего не найдено...</td></tr>";
        };
        // Вставляем
        $('#tbody-city-add').append(data);
      }
    });
  };
  // Функция появления окна с ошибкой
  function showError (msg) {
    var error = "<div class=\"callout item-error\" data-closable><p>" + msg + "</p><button class=\"close-button error-close\" aria-label=\"Dismiss alert\" type=\"button\" data-close><span aria-hidden=\"true\">&times;</span></button></div>";
    return error;
  };

  // Отображение городов из api vk
  $('#city-name-field').keyup(function() {
    $('.item-error').css('display', 'none');
    $('#city-db').val(0);
    $('#submit-add').prop('disabled', true);
    $('#area-name').val('');
    $('#region-name').val('');
    // Смотрим сколько символов
    var lenCity = $('#city-name-field').val().length;
    // Если символов больше 2 - делаем запрос
    if(lenCity > 2) {
      // Выполняем запрос
      clearTimeout(timerId);   
      timerId = setTimeout(function() {
        getCityVk ();
      }, time); 
    } else {
      // Удаляем все значения, если символов меньше 2х
      $('#tbody-city-add>tr').remove();
      $('#city-db').val(0);
      // $('#form-add')[0].reset();
      $('#city-id-field').val('');
      $('#area-name').val('');
      $('#region-name').val('');
      $('#region-name').val('');
      $('.item-error').css('display', 'none');
      $('.city-error').remove();
      $('.find-status').removeClass('icon-find-ok');
      $('.find-status').removeClass('icon-find-no');
    };
  });

  // Отправляем запрос при клике на чекбокс
  $(document).on('change', '#search-all-checkbox', function() {
    // Смотрим сколько символов
    var lenCity = $('#city-name-field').val().length;
    // Если символов больше 2 - делаем запрос
    if(lenCity > 2) {
      // Выполняем запрос
      clearTimeout(timerId);   
      timerId = setTimeout(function() {
        getCityVk ();
      }, time); 
    };
  });

  // При клике на город в модальном окне заполняем инпуты
  $(document).on('click', '.city-add', function() {

    var itemId = $(this).closest('tr').data('tr');
    $('#city-id-field').val($('[data-city-id="' + itemId + '"]').data('city_vk_external_id'));
    var cityName = $('[data-city-id="' + itemId + '"]').html();
    var areaName = $('[data-area-id="' + itemId + '"]').html();
    var regionName = $('[data-region-id="' + itemId + '"]').html();
    $('#city-name-field').val(cityName);
    $('#area-name').val(areaName);
    $('#region-name').val(regionName);
    
    // alert($('#form-add').serialize());

    // Выполняем запрос
    clearTimeout(timerId);   
    timerId = setTimeout(function() {
      if($('#city-id-field').val() != '') {
        // Ajax
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: "/city_check",
          type: "POST",
          data: $('#form-add').serialize(),
          success: function (data) {
            // alert(data);
            var result = $.parseJSON(data);
            // Город не существует
            if (result.error_status == 0) {
              $('#city-db').val(1);
              $('#submit-add').prop('disabled', false);
              $('.item-error').css('display', 'none');
              $('.find-status').addClass('icon-find-ok');
              
            } else {
              // Город существует
              $('#submit-add').prop('disabled', true);
              $('#city-database').val(0);
              $('.item-error').css('display', 'block');
            };
          }
        });
      };
    }, 200); 
  });

  // Добавляем город
  $(document).on('click', '#submit-add', function(event) {
    event.preventDefault();

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: '/cities',
      type: "POST",
      data: $(this).closest('#form-add').serialize(),
      success:function(html){
        $('#content').html(html);
        Foundation.reInit($('#content')); 
      }
    });
  });

  // При закрытии модалки очищаем поля
  $(document).on('click', '.close-modal, #submit-add', function() {
    $('#tbody-city-add>tr').remove();
    $('#form-add')[0].reset();
    $('#city-db').val(0);
    // $('#city-id-field').val('');
    // $('#city-name-field').val('');
    // $('#area-name').val('');
    // $('#region-name').val('');
    // $('.item-error').remove();
    // $('#tbody-region-add>tr').remove();
    // $('#region-id-field').val('');
    // $('#region-name-field').val('');
  });
  
  // При закрытии окна с ошибкой очищаем модалку
  $(document).on('click', '.error-close', function() {
    $('.item-error').remove();
    $('#tbody-city-add>tr').remove();
    $('#tbody-region-add>tr').remove();
    $('#form-add')[0].reset();
    $('#city-db').val(0);
    // $('#city-name-field').val('');
    // $('#region-name-field').val('');
    // $('#area-name').val('');
    // $('#region-name').val('');
    $('.find-status').removeClass('icon-find-ok');
    $('.find-status').removeClass('icon-find-no');
  });

  // // Мягкое удаление с refresh
  // $(document).on('click', '[data-open="item-delete"]', function() {
  //   // находим описание сущности, id и название удаляемого элемента в родителе
  //   var parent = $(this).closest('.parent');
  //   var type = parent.attr('id').split('-')[0];
  //   var id = parent.attr('id').split('-')[1];
  //   var name = parent.data('name');
  //   $('.title-delete').text(name);
  //   $('.delete-button').attr('id', 'del-' + type + '-' + id);
  //   $('#form-item-del').attr('action', '/' + type + '/' + id);
  // });

  // // Отображение области по ajax через api vk
  // $('#region-name-field').keyup(function() {
  //   // Блокируем кнопку
  //   $('#submit-region-add').prop('disabled', true);
  //   $('#region-database').val(0);
  //   // Получаем фрагмент текста
  //   var region = $('#region-name-field').val();
  //   // Смотрим сколько символов
  //   var lenRegion = region.length;
  //   // Если символов больше 3 - делаем запрос

  //   if (lenRegion > 3) {
  //     // alert($('#region-name-field').val());
  //     // setTimeout(function () {
  //       // Сам ajax запрос
  //       $.ajax({
  //         headers: {
  //           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  //         },
  //         url: "/region",
  //         type: "POST",
  //         data: {region: $('#region-name-field').val()},
  //         beforeSend: function () {
  //           $('.find-status').addClass('icon-load');
  //         },
  //         success: function(date){
  //           $('.find-status').removeClass('icon-load');
  //           // Удаляем все значения чтобы вписать новые
  //           $('#tbody-region-add>tr').remove();
  //           var result = $.parseJSON(date);
  //           // alert(result);
  //           var count = result.response.count;
  //           var data = '';
  //           if (count == 0) {
  //             $('.find-status').addClass('icon-find-no');
  //             data = "<tr><td>Ничего не найдено...</td></tr>";
  //           };
  //           if (count > 0) {
  //             $('.find-status').addClass('icon-find-ok');
  //             // Перебираем циклом
  //             for (var i = 0; i < count; i++) {
  //               data = data + "<tr data-tr=\"" + i + "\"><td><a class=\"region-add\" data-region-vk-external-id=\"" + i + "\">" + result.response.items[i].id + "</a></td><td><a class=\"region-add\" data-region-name=\"" + i + "\">" + result.response.items[i].title + "</a></td></tr>";
  //             };
  //           };
  //           // Выводим пришедшие данные на страницу
  //           $('#tbody-region-add').append(data);
  //         }
  //       });
  //     // }, 1000);
  //   } else {
  //     // Удаляем все значения, если символов меньше 3х
  //     $('#tbody-region-add>tr').remove();
  //     $('.item-error').remove();
  //     $('#region-id-field').val('');
  //     $('.find-status').removeClass('icon-find-ok');
  //     $('.find-status').removeClass('icon-find-no');
  //   };
  // });
  // // При клике на регион в модальном окне заполняем инпуты
  // $(document).on('click', '.region-add', function() {
  //   var itemId = $(this).closest('tr').data('tr');
  //   var regionId = $('[data-region-vk-external-id="' + itemId + '"]').html();
  //   var regionName = $('[data-region-name="' + itemId + '"]').html();
  //   $('#region-id-field').val(regionId);
  //   $('#region-name-field').val(regionName);
  //   if($('#region-id-field').val() != '') {
  //     var region = {region_name:$('#region-name-field').val(), region_database:$('#region-database').val()};
  //     // Ajax
  //     $.ajax({
  //       headers: {
  //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  //       },
  //       url: "/regions",
  //       type: "POST",
  //       data: region,
  //       success: function (data) {
  //         var result = $.parseJSON(data);
  //         // alert(result.session);
  //         if (result.error_status == 1) {
  //           var error = showError (result.error_message);
  //           $('#region-name-field').after(error);
  //         };
  //         if (result.error_status == 0) {
  //           $('#region-database').val(1);
  //           $('#submit-region-add').prop('disabled', false);
  //         };
  //       }
  //     });
  //   };
  // });
  // // Сохраняем область в базу и отображаем на странице по ajax   
  // $('#submit-region-add').click(function (event) {
  //   //чтобы не перезагружалась форма
  //   event.preventDefault(); 
  //   // Дергаем все данные формы
  //   var formRegion = $('#form-region-add').serialize();
  //   // var region = {region_vk_external_id: $('#region-id-field').val(), region_name:$('#region-name-field').val()};
  //   // Ajax
  //   $.ajax({
  //     headers: {
  //       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  //     },
  //     url: "/regions",
  //     type: "POST",
  //     // data: region,
  //     data: formRegion,
  //     success: function (data) {
  //       var result = $.parseJSON(data);
  //       result = "<li class=\"first-item parent\" id=\"regions-" + result.region_id + "\" data-name=\"" + result.region_name + "\"><ul class=\"icon-list\"><li><div class=\"icon-list-add sprite\" data-open=\"city-add\"></div></li></ul><a data-list=\"" + result.region_id +"\" class=\"first-link\"><div class=\"list-title\"><div class=\"icon-open sprite\"></div><span class=\"first-item-name\">" + result.region_name + "</span><span class=\"number\">0</span></div></a>";
  //       // Выводим пришедшие данные на страницу
  //       $('#regions').append(result);
  //       // Обнуляем модалку
  //       $('#region-name-field').val('');
  //       $('#region-id-field').val('');
  //       $('#region-database').val(0);
  //       $('#submit-region-add').prop('disabled', true);
  //       $('#tbody-region-add>tr').remove();
  //     }
  //   });
  // });
});
</script>
@endsection