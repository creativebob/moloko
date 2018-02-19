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
        @can('create', App\City::class)
          <a class="icon-add sprite" data-open="city-add"></a>
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
    @if(!empty($regions))
    <ul class="vertical menu accordion-menu content-list" id="content-list" data-accordion-menu data-allow-all-closed data-multi-open="false" data-slide-speed="250">
      @foreach ($regions as $region)      
      <li class="first-item parent" id="regions-{{ $region->id }}" data-name="{{ $region->region_name }}">
        {{-- <ul class="icon-list">
          @can('create', App\City::class)
          <li><div class="icon-list-add sprite" data-open="city-add"></div></li>
          @endcan
          @if (($region->system_item !== 1) && ((count($region->areas) + count($region->cities)) == 0))
            @can('delete', $region)
            <li><div class="icon-list-delete sprite" data-open="item-delete-ajax"></div></li>
            @endcan
          @endif
        </ul> --}}
        <a data-list="{{ $region->id }}" class="first-link">
          <div class="list-title">
            <div class="icon-open sprite"></div>
            <span class="first-item-name">{{ $region->region_name }}</span>
            <span class="number">{{ count($region->areas) + count($region->cities) }}</span>
          </div>
        </a>
        @if((count($region->areas) > 0) || (count($region->cities) > 0))
        <ul class="menu vertical medium-list accordion-menu" data-accordion-menu data-allow-all-closed data-multi-open="false">
          @foreach ($region->areas as $area)
            <li class="medium-item parent" id="areas-{{ $area->id }}" data-name="{{ $area->area_name }}">
              <a class="medium-link">
                <div class="list-title">
                  <div class="icon-open sprite"></div>
                  <span>{{ $area->area_name }}</span>
                  <span class="number">{{ count($area->cities) }}</span>
                </div>
              </a>
              {{-- <ul class="icon-list">
                @if((count($area->cities) == 0) && (($area->system_item !== 1)))
                  @can('delete', $area)
                  <li><div class="icon-list-delete sprite" data-open="item-delete"></div></li>
                  @endcan
                @endif
              </ul> --}}
              @if(count($area->cities) > 0)
              <ul class="menu vertical nested last-list">
                @foreach ($area->cities as $city)
                  <li class="last-item parent" id="cities-{{ $city->id }}" data-name="{{ $city->city_name }}">
                    <div class="last-link">{{ $city->city_name }}
                      {{-- <ul class="icon-list">
                        @if($city->system_item !== 1)
                          @can('delete', $city)
                          <li><div class="icon-list-delete sprite" data-open="item-delete"></div></li>
                          @endcan
                        @endif
                      </ul> --}}
                    </div>
                @endforeach
              </ul>
              @endif
            </li>
          @endforeach
          @if(count($region->cities) > 0)
            @foreach ($region->cities as $city)
              <li class="medium-item parent" id="cities-{{ $city->id }}" data-name="{{ $city->city_name }}">
                <div class="medium-as-last">{{ $city->city_name }}
                  {{--<ul class="icon-list">
                    @can('delete', $city)
                    <li><div class="icon-list-delete sprite" data-open="item-delete"></div></li>
                    @endcan
                  </ul> --}}
                </div>
              </li>
            @endforeach
          @endif
        </ul>
        @endif
      </li>
      @endforeach
    </ul>
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

{{-- Модалка добавления города и района --}}
<div class="reveal rev-large" id="city-add" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ НАСЕЛЕННОГО ПУНКТА</h5>
    </div>
  </div>
  {{ Form::open(['url' => '/cities', 'id' => 'form-city-add']) }}
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 medium-4 cell">
        <label class="input-icon">Название населенного пункта
          {{-- Form::text('city_name', null, ['class'=>'text-field', 'id'=>'city-name-field' 'maxlength'=>'30', 'autocomplete'=>'off', 'pattern'=>'[А-Яа-яЁё0-9-_\s]{3,30}', 'required']) --}}
          <input type="text" name="city_name" id="city-name-field" autocomplete="off" pattern="[А-Яа-я0-9-_\s]{3,30}" required>
          <div class="sprite-input-right find-status"></div>
          <span class="form-error">Уж постарайтесь, введите хотя бы 3 символа!</span>
        </label>
        <label>Район
          {{-- Form::text('area_name', null, ['class'=>'text-field', 'id'=>'area-name' 'maxlength'=>'30', 'autocomplete'=>'off', 'pattern'=>'[А-Яа-я0-9-_\s]{3,30}', 'readonly']) --}}
          <input type="text" name="area_name" id="area-name" pattern="[А-Яа-яЁё0-9-_\s]{3,30}" readonly>
        </label>
        <label>Область
          {{-- Form::text('region_name', null, ['class'=>'text-field', 'id'=>'region_name' 'maxlength'=>'30', 'autocomplete'=>'off', 'pattern'=>'[А-Яа-я0-9-_\s]{3,30}', 'readonly']) --}}
          <input type="text" name="region_name" id="region-name" pattern="[А-Яа-яЁё0-9-_\s]{3,30}" readonly>
        </label>
        <div class="small-12 cell checkbox">
          <input type="checkbox" name="search_all" id="search-all-checkbox">
          <label for="search-all-checkbox"><span class="search-checkbox">Искать везде</span></label>
        </div>
        <input type="hidden" name="city_vk_external_id" id="city-id-field" pattern="[0-9]{1,20}">
        <input type="hidden" name="city_database" id="city-database" value="0" pattern="[0-9]{1}">
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
        <button data-close class="button modal-button" id="submit-city-add" type="submit" disabled>Сохранить</button>
      </div>
    </div>
  {{ Form::close() }}
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>
{{-- Конец модалки добавления города и района --}}

{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete')
@endsection

@section('scripts')
<script type="text/javascript">
$(function() {
  // Функция получения городов из вк или с фильтром по нашей базе
  function getCityVk () {  
    $('#submit-city-add').prop('disabled', true);
    $('#city-database').val(0);
    // Получаем фрагмент текста
    var city = {city:$('#city-name-field').val(), checkbox:$('#search-all-checkbox').prop('checked')};
    // Смотрим сколько символов
    var lenCity = $('#city-name-field').val().length;
    // Если символов больше 2 - делаем запрос
    if(lenCity > 2){
      $('.find-status').removeClass('icon-find-ok');
      // Сам ajax запрос
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "/city",
        type: "POST",
        data: city,
        beforeSend: function () {
          $('.find-status').addClass('icon-load');
        },
        success: function(date){
          $('.find-status').removeClass('icon-load');
          // Удаляем все значения чтобы вписать новые
          $('#tbody-city-add>tr').remove();
          var result = $.parseJSON(date);
          var data = '';
          if ($('#search-all-checkbox').prop('checked') == true) {
            var countRes = result.response.count;
            if (countRes == 0) {
              $('.find-status').addClass('icon-find-no');
              data = "<tr><td>Ничего не найдено...</td></tr>";
            };
            if (countRes > 0) {
              // Перебираем циклом
              for (var i = 0; i < countRes; i++) {
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
              // Формируем содержимое
              data = data + "<tr data-tr=\"" + i + "\"><td><a class=\"city-add\" data-city-id=\"" + i + "\" data-city-vk-external-id=\"" + result.response.items[i].id + "\">" + result.response.items[i].title + "</a></td><td><a class=\"city-add\" data-area-id=\"" + i + "\" data-area-name=\"" + result.response.items[i].area + "\">" + areaName + "</a></td><td><a class=\"city-add\" data-region-id=\"" + i + "\" data-region-name=\"" + result.response.items[i].region + "\">" + regionName + "</a></td></tr>";
              };
            };
          }; 
          if ($('#search-all-checkbox').prop('checked') == false) {
            if (result.count == 0) {
              $('.find-status').addClass('icon-find-no');
              data = "<tr><td>Ничего не найдено...</td></tr>";
            } else {
              var countRes = result.region.length;
              // alert(result.region);
              if (countRes == 0) {
                $('.find-status').addClass('icon-find-no');
                data = "<tr><td>Ничего не найдено...</td></tr>";
              };
              if (countRes > 0) {
                // Перебираем циклом
                for (var i = 0; i < countRes; i++) {
                  // Если области нет
                  if (result.region[i] == null) {
                    var regionName = '';
                  } else {
                    var regionName = result.region[i];
                  };
                  // Если района нет
                  if (result.area[i] == null) {
                    var areaName = '';
                  } else {
                    var areaName = result.area[i];
                  };
                  // Формируем содержимое
                  data = data + "<tr data-tr=\"" + i + "\"><td><a class=\"city-add\" data-city-id=\"" + i + "\" data-city-vk-external-id=\"" + result.id[i] + "\">" + result.title[i] + "</a></td><td><a class=\"city-add\" data-area-id=\"" + i + "\" data-area-name=\"" + result.area[i] +"\">"+ areaName +"</a></td><td><a class=\"city-add\" data-region-id=\"" + i + "\" data-region-name=\"" + result.region[i] + "\">" + regionName + "</a></td></tr>";
                };
              };
            }
          };
          // Вставляем
          $('#tbody-city-add').append(data);
        }
      });
    } else {
      // Удаляем все значения, если символов меньше 2х
      $('#tbody-city-add>tr').remove();
      $('#city-id-field').val('');
      $('#area-name').val('');
      $('#region-name').val('');
      $('.city-error').remove();
      $('.find-status').removeClass('icon-find-ok');
      $('.find-status').removeClass('icon-find-no');
      $('#area-name').val('');
      $('#region-name').val('');
    };
  };
  // Функция появления окна с ошибкой
  function showError (msg) {
    var error = "<div class=\"callout item-error\" data-closable><p>" + msg + "</p><button class=\"close-button error-close\" aria-label=\"Dismiss alert\" type=\"button\" data-close><span aria-hidden=\"true\">&times;</span></button></div>";
    return error;
  };
  // Отображение области по ajax через api vk
  $('#region-name-field').keyup(function() {
    // Блокируем кнопку
    $('#submit-region-add').prop('disabled', true);
    $('#region-database').val(0);
    // Получаем фрагмент текста
    var region = $('#region-name-field').val();
    // Смотрим сколько символов
    var lenRegion = region.length;
    // Если символов больше 3 - делаем запрос

    if (lenRegion > 3) {
      // alert($('#region-name-field').val());
      // setTimeout(function () {
        // Сам ajax запрос
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: "/region",
          type: "POST",
          data: {region: $('#region-name-field').val()},
          beforeSend: function () {
            $('.find-status').addClass('icon-load');
          },
          success: function(date){
            $('.find-status').removeClass('icon-load');
            // Удаляем все значения чтобы вписать новые
            $('#tbody-region-add>tr').remove();
            var result = $.parseJSON(date);
            // alert(result);
            var count = result.response.count;
            var data = '';
            if (count == 0) {
              $('.find-status').addClass('icon-find-no');
              data = "<tr><td>Ничего не найдено...</td></tr>";
            };
            if (count > 0) {
              $('.find-status').addClass('icon-find-ok');
              // Перебираем циклом
              for (var i = 0; i < count; i++) {
                data = data + "<tr data-tr=\"" + i + "\"><td><a class=\"region-add\" data-region-vk-external-id=\"" + i + "\">" + result.response.items[i].id + "</a></td><td><a class=\"region-add\" data-region-name=\"" + i + "\">" + result.response.items[i].title + "</a></td></tr>";
              };
            };
            // Выводим пришедшие данные на страницу
            $('#tbody-region-add').append(data);
          }
        });
      // }, 1000);
    } else {
      // Удаляем все значения, если символов меньше 3х
      $('#tbody-region-add>tr').remove();
      $('.item-error').remove();
      $('#region-id-field').val('');
      $('.find-status').removeClass('icon-find-ok');
      $('.find-status').removeClass('icon-find-no');
    };
  });
  // При клике на регион в модальном окне заполняем инпуты
  $(document).on('click', '.region-add', function() {
    var itemId = $(this).closest('tr').data('tr');
    var regionId = $('[data-region-vk-external-id="' + itemId + '"]').html();
    var regionName = $('[data-region-name="' + itemId + '"]').html();
    $('#region-id-field').val(regionId);
    $('#region-name-field').val(regionName);
    if($('#region-id-field').val() != '') {
      var region = {region_name:$('#region-name-field').val(), region_database:$('#region-database').val()};
      // Ajax
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "/regions",
        type: "POST",
        data: region,
        success: function (data) {
          var result = $.parseJSON(data);
          // alert(result.session);
          if (result.error_status == 1) {
            var error = showError (result.error_message);
            $('#region-name-field').after(error);
          };
          if (result.error_status == 0) {
            $('#region-database').val(1);
            $('#submit-region-add').prop('disabled', false);
          };
        }
      });
    };
  });
  // Сохраняем область в базу и отображаем на странице по ajax   
  $('#submit-region-add').click(function (event) {
    //чтобы не перезагружалась форма
    event.preventDefault(); 
    // Дергаем все данные формы
    var formRegion = $('#form-region-add').serialize();
    // var region = {region_vk_external_id: $('#region-id-field').val(), region_name:$('#region-name-field').val()};
    // Ajax
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/regions",
      type: "POST",
      // data: region,
      data: formRegion,
      success: function (data) {
        var result = $.parseJSON(data);
        result = "<li class=\"first-item parent\" id=\"regions-" + result.region_id + "\" data-name=\"" + result.region_name + "\"><ul class=\"icon-list\"><li><div class=\"icon-list-add sprite\" data-open=\"city-add\"></div></li></ul><a data-list=\"" + result.region_id +"\" class=\"first-link\"><div class=\"list-title\"><div class=\"icon-open sprite\"></div><span class=\"first-item-name\">" + result.region_name + "</span><span class=\"number\">0</span></div></a>";
        // Выводим пришедшие данные на страницу
        $('#content-list').append(result);
        // Обнуляем модалку
        $('#region-name-field').val('');
        $('#region-id-field').val('');
        $('#region-database').val(0);
        $('#submit-region-add').prop('disabled', true);
        $('#tbody-region-add>tr').remove();
      }
    });
  });
  // Отображение города по ajax через api vk
  $('#city-name-field').keyup(function() {
    getCityVk ();
  });
  // Оптравляем запрос при клике на чекбокс
  $(document).on('change', '#search-all-checkbox', function() {
    getCityVk ();
  });
  // При клике на город в модальном окне заполняем инпуты
  $(document).on('click', '.city-add', function() {
    var itemId = $(this).closest('tr').data('tr');
    var cityId = $('[data-city-id="' + itemId + '"]').data('city-vk-external-id');
    var cityName = $('[data-city-id="' + itemId + '"]').html();
    var areaName = $('[data-area-id="' + itemId + '"]').html();
    var regionName = $('[data-region-id="' + itemId + '"]').html();
    $('.find-status').addClass('icon-find-ok');
    $('#city-id-field').val(cityId);
    $('#city-name-field').val(cityName);
    $('#area-name').val(areaName);
    $('#region-name').val(regionName);
    if($('#city-id-field').val() != '') {
      var city = {city_name:$('#city-name-field').val(), city_database:$('#city-database').val(), area_name:$('#area-name').val()};
      // Ajax
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "/cities",
        type: "POST",
        data: city,
        success: function (data) {
          var result = $.parseJSON(data);
          if (result.error_status == 1) {
            var error = showError (result.error_message);
            $('#city-name-field').after(error);
            $('#city-database').val(0);
          };
          if (result.error_status == 0) {
            $('#city-database').val(1);
            $('.item-error').remove();
            $('#submit-city-add').prop('disabled', false);
          };
        }
      });
    };
  });
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
    $('.find-status').removeClass('icon-find-ok');
    $('.find-status').removeClass('icon-find-no');
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
  @if(!empty($data))
  if ({{ $data != null }})  {

    // Общие правила
    // Подсвечиваем область
    $('#regions-' + {{ $data['region_id'] }}).addClass('first-active').find('.icon-list:first-child').attr('aria-hidden', 'false').css('display', 'block');
    // Открываем область
    var firstItem = $('#regions-' + {{ $data['region_id'] }}).find('.medium-list');
    // Открываем аккордионы
    $('#content-list').foundation('down', firstItem);

    // Если удален город, имеющий район
    if ({{ $data['area_id'] }} !== 0) {
      // Подсвечиваем ссылку
      $('#areas-{{ $data['area_id'] }}').find('.medium-link').addClass('medium-active');
      // Открываем меню удаления в середине
       $('#areas-{{ $data['area_id'] }}').find('.icon-list').attr('aria-hidden', 'false').css('display', 'block');

      // Находим средние элементы
      var mediumItem = $('#areas-{{ $data['area_id'] }}').find('.last-list');
      if (mediumItem.is('.last-list')) {
        $('#content-list').foundation('down', mediumItem);
      };
    };    
  }
  @endif
});
</script>
{{-- Скрипт подсветки многоуровневого меню --}}
@include('includes.scripts.multilevel-menu-active-scripts')

{{-- Скрипт модалки удаления ajax --}}
@include('includes.scripts.modal-delete-ajax-script')
@endsection