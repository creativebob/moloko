@extends('layouts.app')
 
@section('inhead')
  <meta name="description" content="{{ $page_info->page_description }}" />
@endsection

@section('title', $page_info->page_name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('title-content')
<div data-sticky-container id="head-content">
  <div class="sticky sticky-topbar" id="head-sticky" data-sticky data-margin-top="2.4" data-options="stickyOn: small;" data-top-anchor="head-content:top">
    <div class="top-bar head-content">
      <div class="top-bar-left">
        <h2 class="header-content">{{ $page_info->page_name }}</h2>
        @can('create', App\Sector::class)
        <a class="icon-add sprite" data-open="first-add"></a>
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
          {{ Form::open(['data-abide', 'novalidate', 'name'=>'filter', 'method'=>'GET']) }}
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

    @if($sectors_tree)
      <ul class="vertical menu accordion-menu content-list" id="content-list" data-accordion-menu data-allow-all-closed data-multi-open="false" data-slide-speed="250">
        @foreach ($sectors_tree as $sector)
         
          @if($sector['industry_status'] == 1)
            {{-- Если индустрия --}}
            <li class="first-item item 
            @if (isset($sector['children']))
            parent
            @endif" id="sectors-{{ $sector['id'] }}" data-name="{{ $sector['sector_name'] }}">
              <ul class="icon-list">
                <li>
                  @can('create', App\Sector::class)
                  <div class="icon-list-add sprite" data-open="medium-add"></div>
                  @endcan
                </li>
                <li>
                  @if($sector['edit'] == 1)
                  <div class="icon-list-edit sprite" data-open="first-edit"></div>
                  @endif
                </li>
                <li>
                  @if (!isset($sector['children']) && ($sector['system_item'] != 1) && $sector['delete'] == 1)
                    <div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>
                  @endif
                </li>
              </ul>
              <a data-list="" class="first-link">
                <div class="list-title">
                  <div class="icon-open sprite"></div>
                  <span class="first-item-name">{{ $sector['sector_name'] }}</span>
                  <span class="number">{{ $sector['count'] }}</span>
                </div>
              </a>
            @if (isset($sector['children']))
              <ul class="menu vertical medium-list accordion-menu" data-accordion-menu data-allow-all-closed data-multi-open="false">
                  @foreach($sector['children'] as $sector)
                    @include('sectors.sectors-list', $sector)
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
{{-- Модалка добавления индустрии --}}
<div class="reveal" id="first-add" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ индустрии</h5>
    </div>
  </div>
  
  <form id="form-first-add" data-abide novalidate method="POST">
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 small-offset-1 cell">
        <label>Название индустрии
          @include('includes.inputs.name', ['value'=>null, 'name'=>'industry_name'])
          <div class="sprite-input-right find-status"></div>
          <div class="item-error">Такая индустрия уже существует!</div>
        </label>
        <input type="hidden" name="first_db" class="first-db" value="0" pattern="[0-9]{1}">
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        {{ Form::submit('Сохранить', ['class'=>'button modal-button', 'id'=>'submit-first-add', 'data-close']) }}
      </div>
    </div>
  </form>
  {{-- Form::open(['id'=>'form-first-add', 'data-abide', 'novalidate']) --}}
  {{-- Form::close() --}}
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>
{{-- Конец модалки добавления индустрии --}}

{{-- Модалка редактирования индустрии --}}
<div class="reveal" id="first-edit" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>Редактирование индустрии</h5>
    </div>
  </div>
  <form id="form-first-edit" data-abide novalidate>
  {{ method_field('PATCH') }}
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 small-offset-1 cell">
        <label>Название индустрии
          @include('includes.inputs.name', ['value'=>null, 'name'=>'industry_name'])
          <div class="sprite-input-right find-status"></div>
          <div class="item-error">Такая индустрия уже существует!</div>
        </label>
        <input type="hidden" name="first_id" class="first-id">
        <input type="hidden" name="first_db" class="first-db" value="0">
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        {{ Form::submit('Сохранить', ['class'=>'button modal-button', 'id'=>'submit-first-edit', 'data-close']) }}
      </div>
    </div>
  </form>
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>
{{-- Конец модалки редактирования индустрии --}}

{{-- Модалка добавления сектора --}}
<div class="reveal" id="medium-add" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ сектора</h5>
    </div>
  </div>
  <!-- Добавляем сектор -->
  <form id="form-medium-add" data-abide novalidate method="POST">
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 small-offset-1 cell">
        <label>Название сектора
          @include('includes.inputs.name', ['value'=>null, 'name'=>'sector_name'])
          <div class="sprite-input-right find-status"></div>
          <div class="item-error">Такой сектор уже существует!</div>
        </label>
        <input type="hidden" name="medium_parent_id" class="medium-parent-id-field">
        <input type="hidden" name="first_id" class="first-id-field">
        <input type="hidden" name="medium_db" class="medium-db" value="0">
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        {{ Form::submit('Сохранить', ['data-close', 'class'=>'button modal-button', 'id'=>'submit-medium-add']) }}
      </div>
    </div>
  </form>
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>
{{-- Конец модалки добавления сектора --}}

{{-- Модалка редактирования сектора --}}
<div class="reveal" id="medium-edit" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>Редактирование сектора</h5>
    </div>
  </div>
  <!-- Редактируем отдел -->
  <form id="form-medium-edit" data-abide novalidate>
  {{ method_field('PATCH') }}
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 small-offset-1 cell">
        <label>Расположение
          @include('includes.inputs.sector', ['sector_id'=>null, 'name'=>'first_id'])
        </label>
        <label>Название сектора
          @include('includes.inputs.name', ['value'=>null, 'name'=>'sector_name'])
          <div class="sprite-input-right find-status"></div>
          <div class="item-error">Такой сектор уже существует!</div>
        </label>
        <input type="hidden" name="medium_parent_id" class="medium-parent-id-field">
        <input type="hidden" name="medium_db" class="medium-db" value="0">
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        {{ Form::submit('Сохранить', ['data-close', 'class'=>'button modal-button', 'id'=>'submit-medium-edit']) }}
      </div>
    </div>
  </form>
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>
{{-- Конец модалки сектора --}}

{{-- Модалка удаления ajax --}}
@include('includes.modals.modal-delete-ajax')
@endsection

@section('scripts')
  @include('includes.scripts.inputs-mask')
<script type="text/javascript">
$(function() {
  // Функция появления окна с ошибкой
  function showError (msg) {
    var error = "<div class=\"callout item-error\" data-closable><p>" + msg + "</p><button class=\"close-button error-close\" aria-label=\"Dismiss alert\" type=\"button\" data-close><span aria-hidden=\"true\">&times;</span></button></div>";
    return error;
  };
  
  // Обозначаем таймер для проверки
  var timerId;
  var time = 400;

  // Первая буква заглавная
  function newParagraph (name) {
    name = name.charAt(0).toUpperCase() + name.substr(1).toLowerCase();
    return name;
  };
 
  // ------------------- Проверка на совпадение имени --------------------------------------

  // Проверка на совпадение в базе
  function sectorCheck (name, submit, db) {

    // Блокируем аттрибут базы данных
    $(db).val(0);

    // Смотрим сколько символов
    var lenname = name.length;

    // Если символов больше 3 - делаем запрос
    if (lenname > 3) {

      // Первая буква сектора заглавная
      name = newParagraph (name);

      // Сам ajax запрос
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "/sector_check",
        type: "POST",
        data: {name: name},
        beforeSend: function () {
          $('.find-status').addClass('icon-load');
        },
        success: function(date){
          $('.find-status').removeClass('icon-load');
          var result = $.parseJSON(date);

          if (result.error_status == 1) {
            // Если ошибка
            $(submit).prop('disabled', true);
            $('.item-error').css('display', 'block');
            $(db).val(0);
          } else {
            // Выводим пришедшие данные на страницу
            $(submit).prop('disabled', false);
            $('.item-error').css('display', 'none');
            $(db).val(1);
          };
        }
      });
    };
    if (lenname <= 3) {
      // Удаляем все значения, если символов меньше 3х
      $(submit).prop('disabled', false);
      $('.item-error').css('display', 'none');
      $(db).val(0);
    };
  };

  // Добаление индустрии
  $('#form-first-add .name-field').keyup(function() {
    // Получаем фрагмент текста
    var name = $('#form-first-add .name-field').val();
    // Указываем название кнопки
    var submit = '#submit-first-add';
    // Значение поля с разрешением
    var db = '#form-first-add .first-db';
    // Выполняем запрос
    clearTimeout(timerId);   
    timerId = setTimeout(function() {
      sectorCheck (name, submit, db)
   }, time); 
  });

  // Изменение индустрии
  $('#form-first-edit .name-field').keyup(function() {
    // Получаем фрагмент текста
    var name = $('#form-first-edit .name-field').val();
    // Указываем название кнопки
    var submit = '#submit-first-edit';
    // Значение поля с разрешением
    var db = '#form-first-edit .first-db';
    // Выполняем запрос
    clearTimeout(timerId);   
    timerId = setTimeout(function() {
      sectorCheck (name, submit, db)
   }, time); 
  });

  // Добаление сектора
  $('#form-medium-add .name-field').keyup(function() {
    // Получаем фрагмент текста
    var name = $('#form-medium-add .name-field').val();
    // Указываем название кнопки
    var submit = '#submit-medium-add';
    // Значение поля с разрешением
    var db = '#form-medium-add .medium-db';
    // Выполняем запрос
    clearTimeout(timerId);   
    timerId = setTimeout(function() {
      sectorCheck (name, submit, db)
   }, time); 
  });

  // Изменение сектора
  $('#form-medium-edit .name-field').keyup(function() {
    // Получаем фрагмент текста
    var name = $('#form-medium-edit .name-field').val();
    // Указываем название кнопки
    var submit = '#submit-medium-edit';
    // Значение поля с разрешением
    var db = '#form-medium-edit .medium-db';
    // Выполняем запрос
    clearTimeout(timerId);   
    timerId = setTimeout(function() {
      sectorCheck (name, submit, db)
   }, time); 
  });


  // ---------------------------- Добавление / изменение ----------------------------------

  // Добавляем индустрию
  $(document).on('click', '#submit-first-add', function(event) {

    // Блочим отправку формы
    event.preventDefault();

    // Получаем данные
    var name = $('#form-first-add .name-field').val();
    var first_db = $('#form-first-add .first-db').val();

    // Первая буква сектора заглавная
    name = newParagraph (name);
    
    // Сам ajax запрос
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/sectors",
      type: "POST",
      data: {name: name, first_item: first_db},
      success: function(date){
        var result = $.parseJSON(date);
        if (result.error_status == 0) {

          // Формируем вставляемый пункт
          var data = '<li class="first-item item" id=\"sectors-'+ result.id +'\" data-name=\"'+ result.name +'\"><ul class="icon-list"><li>';

          if (result.create == 1) {
            data = data + '<div class=\"icon-list-add sprite\" data-open=\"medium-add\"></div>';
          };
          data = data + '</li><li>';
          if (result.edit == 1) {
            data = data + '<div class=\"icon-list-edit sprite\" data-open=\"first-edit\"></div>';
          };
          data = data + '</li><li>';
          if (result.delete == 1) {
            data = data + '<div class=\"icon-list-delete sprite\" data-open=\"item-delete-ajax\"></div>';
          };
          data = data + '</li></ul><a data-list="" class=\"first-link\"><div class=\"list-title\"><div class=\"icon-open sprite\"></div><span class=\"first-item-name\">' + result.name + '</span><span class=\"number\">0</span></div></a></li>';

          // Вставляем
          $('.content-list').append(data);
        } else {
          var error = showError (result.error_message);
          $('#form-first-add .name-field').after(error);
        }
      }
    });
  });

  // Редактируем индустрию
  // Открываем модалку
  $(document).on('click', '[data-open="first-edit"]', function() {

    // Блокируем кнопку
    $('.submit-first-edit').prop('disabled', false);

    // Получаем данные о филиале
    var id = $(this).closest('.parent').attr('id').split('-')[1];

    // Сам ajax запрос
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/sectors/" + id + "/edit",
      type: "GET",
      success: function(date){
        var result = $.parseJSON(date);
        $('#form-first-edit .name-field').val(result.name);
        $('#form-first-edit .first-id').val(result.id);
        $('#form-first-edit .first-db').val(1);
      }
    });
  });

  // Меняем данные индустрии
  $(document).on('click', '#submit-first-edit', function(event) {

    // Блочим отправку формы
    event.preventDefault();

    // Получаем данные
    var id = $('#form-first-edit .first-id').val();
    var name = $('#form-first-edit .name-field').val();
    var first_db = $('#form-first-edit .first-db').val();

    // Первая буква сектора заглавная
    name = newParagraph (name);
    
    // Сам ajax запрос
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/sectors/" + id,
      type: "PATCH",
      data: {name: name, first_item: first_db},
      success: function(date){
        var result = $.parseJSON(date);

        if (result.error_status == 0) {
          $('#sectors-' + result.id + ' .first-item-name').text(result.name);
          $('#sectors-' + result.id).data('name', result.name);
        } else {
          var error = showError (result.error_message);
          $('#form-first-add .name-field').after(error);
        }
      }
    });
  });

  // Добавление сектора
  // Открываем модалку
  $(document).on('click', '[data-open="medium-add"]', function() {
    var first = $(this).closest('.first-item').attr('id').split('-')[1];
    var parent = $(this).closest('.item').attr('id').split('-')[1];
    $('#form-medium-add .first-id-field').val(first);
    $('#form-medium-add .medium-parent-id-field').val(parent);
  });

  // Добавляем сектор
  $(document).on('click', '#submit-medium-add', function(event) {

    // Блочим отправку формы
    event.preventDefault();

    // Получаем данные
    var name = $('#form-medium-add .name-field').val();
    var medium_db = $('#form-medium-add .medium-db').val();
    var parent = $('#form-medium-add .medium-parent-id-field').val();

    // Первая буква сектора заглавная
    name = newParagraph (name);
    
    // Сам ajax запрос
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/sectors",
      type: "POST",
      data: {name: name, medium_item: medium_db, parent: parent},
      success: function(date){
        var result = $.parseJSON(date);
        if (result.error_status == 0) {

          

          // Если у родителя нет родительского класса
          if ($('#sectors-' + result.parent).hasClass('parent') == false) {

            // Добавляем родителю класс и список
            $('#sectors-' + result.parent).addClass('parent');

            // Формируем список
            var list = '<ul class="menu vertical medium-list accordion-menu" data-accordion-menu data-allow-all-closed data-multi-open="false"></ul>';

            // Вставляем
            $('#sectors-' + result.parent).append(list);

            // Убираем иконку удаления
            $('#sectors-' + result.parent).children('.icon-list:first').find('.icon-list-delete').remove();
          };

          // Формируем вставляемый пункт
          var data = '<li class="medium-item item" id=\"sectors-'+ result.id +'\" data-name=\"'+ result.name +'\"><a data-list="" class=\"medium-link\"><div class=\"list-title\"><div class=\"icon-open sprite\"></div><span class=\"medium-item-name\">' + result.name + '</span><span class=\"number\">0</span></div></a><ul class="icon-list"><li>';
          if (result.create == 1) {
            data = data + '<div class=\"icon-list-add sprite\" data-open=\"medium-add\"></div>';
          };
          data = data + '</li><li>';
          if (result.edit == 1) {
            data = data + '<div class=\"icon-list-edit sprite\" data-open=\"medium-edit\"></div>';
          };
          data = data + '</li><li>';
          if (result.delete == 1) {
            data = data + '<div class=\"icon-list-delete sprite\" data-open=\"item-delete-ajax\"></div>';
          };
          data = data + '</li></ul></li>';

          // Вставляем пункт
          $('#sectors-' + result.parent + ' .medium-list').append(data);

          // Меняем количество детей
          var count = $('#sectors-' + result.parent + ' .medium-list>li');
          $('#sectors-' + result.parent + ' .number:first').text(count.length);



        } else {
          var error = showError (result.error_message);
          $('#form-medium-add .name-field').after(error);
        }
      }
    });
  });

  // Редактируем сектор
  $(document).on('click', '[data-open="medium-edit"]', function() {
    var parent = $(this).closest('.parent').attr('id').split('-')[1];
    var first = $(this).closest('.first-item').attr('id').split('-')[1];
    $('.first-id-field').val(first);
    var id = $(this).closest('.parent').attr('id').split('-')[1];
    // Блокируем кнопку
    $('#submit-medium-edit').prop('disabled', false);
    // Получаем данные о филиале
    $('#form-medium-edit').attr('action', '/sectors/' + id);
    // Сам ajax запрос
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/sectors/" + id + "/edit",
      type: "GET",
      success: function(date){
        var result = $.parseJSON(date);
        $('#form-medium-edit .name-field').val(result.medium_name);
        $('#form-medium-edit .medium-db').val(1);
        $('#form-medium-edit option[value="' + result.first_id + '"]').attr("selected", "selected");
        $('#form-medium-edit .medium-parent-id-field').val(result.medium_parent_id);
      }
    });
  });

  // При закрытии модалки очищаем поля
  $(document).on('click', '.close-modal, .modal-button', function() {
    $('.name-field').val('');
    $('.first-db').val(0);
    $('.medium-db').val(0);
    $('.first-id-field').val('');
    $('.medium-parent-id-field').val('');
    $('.item-error').css('display', 'none');
    $('#sectors-select>option').each(function(i,elem) {
      $(elem).removeAttr('selected');
    });
  });

  // Открываем меню и подменю, если только что добавили населенный пункт
  @if(!empty($data))
    // Общие правила
    // Подсвечиваем Филиал
    $('#sectors-{{ $data['section_id'] }}').addClass('first-active').find('.icon-list:first').attr('aria-hidden', 'false').css('display', 'block');
    // Отображаем отдел и филиал, без должностей
    if ({{ $data['item_id'] }} == 0) {
      var firstItem = $('#sectors-{{ $data['section_id'] }}').find('.medium-list:first');
      // Открываем аккордион
      $('#content-list').foundation('down', firstItem); 
    } else {
      // Перебираем родителей и подсвечиваем их
      $.each($('#sectors-{{ $data['item_id'] }}').parents('.parent-item').get().reverse(), function (index) {
        $(this).children('.medium-link:first').addClass('medium-active');
        $(this).children('.icon-list:first').attr('aria-hidden', 'false').css('display', 'block');
        $('#content-list').foundation('down', $(this).closest('.medium-list'));
      });
      // Если родитель содержит не пустой элемент
      if ($('#sectors-{{ $data['item_id'] }}').parent('.parent').has('.parent-item')) {
        $('#content-list').foundation('down', $('#sectors-{{ $data['item_id'] }}').closest('.medium-list'));
      };
      // Если элемент содержит вложенность, открываем его
      if ($('#sectors-{{ $data['item_id'] }}').hasClass('parent')) {
        $('#sectors-{{ $data['item_id'] }}').children('.medium-link:first').addClass('medium-active');
        $('#sectors-{{ $data['item_id'] }}').children('.icon-list:first').attr('aria-hidden', 'false').css('display', 'block');
        $('#content-list').foundation('down', $('#sectors-{{ $data['item_id'] }}').children('.medium-list:first'));
      }
    };
  @endif
});
</script>
{{-- Скрипт подсветки многоуровневого меню --}}
@include('includes.scripts.multilevel-menu-active-scripts')
{{-- Скрипт модалки удаления ajax --}}
@include('includes.scripts.modal-delete-ajax-script')
@endsection