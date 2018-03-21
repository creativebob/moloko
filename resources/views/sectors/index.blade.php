@extends('layouts.app')
 
@section('inhead')
  <meta name="description" content="{{ $page_info->page_description }}" />
  {{-- Скрипты меню в шапке --}}
  @include('includes.scripts.menu-inhead')
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
                {{ Form::select('user_type', ['all' => 'Все пользователи', '1' => 'Сотрудник', '2' => 'Клиент'], 'all') }}
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
      {{-- Шаблон вывода и динамического обновления --}}
      @include('sectors.industry-list', $sectors_tree)
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
  
  {{ Form::open(['id'=>'form-first-add', 'class'=>'form-add', 'data-abide', 'novalidate']) }}
  {{-- <form id="form-first-add" class="form-add" data-abide novalidate> --}}
    <div class="grid-x grid-padding-x align-center modal-content inputs">
      <div class="small-10 cell">
        <label>Название индустрии
          @include('includes.inputs.name', ['value'=>null, 'name'=>'name'])
          <div class="sprite-input-right find-status"></div>
          <div class="item-error">Такая индустрия уже существует!</div>
        </label>
        <input type="hidden" name="first_item" class="first-item" value="0" pattern="[0-9]{1}">
        @can('god', App\Sector::class)
        <div class="checkbox">
          {{ Form::checkbox('system_item', 1, null, ['id'=>'system-first-add']) }}
          <label for="system-first-add"><span>Системная запись.</span></label>
        </div>
        @endcan
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        {{ Form::submit('Сохранить', ['class'=>'button modal-button submit-add', 'data-close']) }}
      </div>
    </div>
  {{-- </form> --}}
  {{ Form::close() }}
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
  {{ Form::open(['id'=>'form-first-edit', 'class'=>'form-edit', 'data-abide', 'novalidate']) }}
    <div class="grid-x grid-padding-x align-center modal-content inputs">
      <div class="small-10 cell">
        <label>Название индустрии
          @include('includes.inputs.name', ['value'=>null, 'name'=>'name'])
          <div class="sprite-input-right find-status"></div>
          <div class="item-error">Такая индустрия уже существует!</div>
        </label>
        <input type="hidden" name="id" class="item-id">
        <input type="hidden" name="first_item" class="first-item" value="0">

        @can('god', App\Sector::class)
        <div class="checkbox">
          {{ Form::checkbox('system_item', 1, null, ['id'=>'system-first-edit']) }}
          <label for="system-first-edit"><span>Системная запись.</span></label>
        </div>
        @endcan
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        {{ Form::submit('Сохранить', ['class'=>'button modal-button submit-edit', 'data-close']) }}
      </div>
    </div>
  {{ Form::close() }}
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
  {{ Form::open(['id'=>'form-medium-add', 'class'=>'form-add', 'data-abide', 'novalidate']) }}
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 small-offset-1 cell">
        <label>Название сектора
          @include('includes.inputs.name', ['value'=>null, 'name'=>'name'])
          <div class="sprite-input-right find-status"></div>
          <div class="item-error">Такой сектор уже существует!</div>
        </label>
        <input type="hidden" name="medium_parent_id" class="medium-parent-id-field">
        <input type="hidden" name="first_id" class="first-id-field">
        <input type="hidden" name="medium_item" class="medium-item" value="0">
        @can('god', App\Sector::class)
        <div class="checkbox">
          {{ Form::checkbox('system_item', 1, null, ['id'=>'system-medium-add']) }}
          <label for="system-medium-add"><span>Системная запись.</span></label>
        </div>
        @endcan
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        {{ Form::submit('Сохранить', ['class'=>'button modal-button submit-add', 'data-close']) }}
      </div>
    </div>
  {{ Form::close() }}
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>
{{-- Конец модалки добавления сектора --}}

{{-- Модалка редактирования сектора --}}
<div class="reveal" id="medium-edit" data-reveal data-close-on-click="false">
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>Редактирование сектора</h5>
    </div>
  </div>
  <!-- Редактируем отдел -->
  {{ Form::open(['id'=>'form-medium-edit', 'class'=>'form-edit', 'data-abide', 'novalidate']) }}
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 small-offset-1 cell">
        <label>Расположение
          <select name="sector_parent_id" class="sectors-list"></select>
        </label>
        <label>Название сектора
          @include('includes.inputs.name', ['value'=>null, 'name'=>'name'])
          <div class="sprite-input-right find-status"></div>
          <div class="item-error">Такой сектор уже существует!</div>
        </label>
        <input type="hidden" name="medium_parent_id" class="medium-parent-id-field">
        <input type="hidden" name="id" class="item-id">
        <input type="hidden" name="medium_item" class="medium-item" value="0">
        @can('god', App\Sector::class)
        <div class="checkbox">
          {{ Form::checkbox('system_item', 1, null, ['id'=>'system-medium-edit']) }}
          <label for="system-medium-edit"><span>Системная запись.</span></label>
        </div>
        @endcan
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        {{ Form::submit('Сохранить', ['data-close', 'class'=>'button modal-button submit-edit']) }}
      </div>
    </div>
  {{ Form::close() }}
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>
{{-- Конец модалки сектора --}}

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
          // Если ошибка
          if (result.error_status == 1) {
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
    // Удаляем все значения, если символов меньше 3х
    if (lenname <= 3) {
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
    var db = '#form-first-add .first-item';
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
    var db = '#form-first-edit .first-item';
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
    var db = '#form-medium-add .medium-item';
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
    var db = '#form-medium-edit .medium-item';
    // Выполняем запрос
    clearTimeout(timerId);   
    timerId = setTimeout(function() {
      sectorCheck (name, submit, db)
   }, time); 
  });

  // ---------------------------- Добавление ---------------------------------------
  // Открываем модалку сектора
  $(document).on('click', '[data-open="medium-add"]', function() {
    var parent = parent = $(this).closest('.item').attr('id').split('-')[1];;
    $('#form-medium-add .medium-parent-id-field').val(parent);
  });

  // Добавляем
  $(document).on('click', '.submit-add', function(event) {

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: '/sectors',
      type: "POST",
      data: $(this).closest('.form-add').serialize(),
      success:function(html){
        $('#content').html(html);
        Foundation.reInit($('#content'));
        $('.form-add').foundation('resetForm');  
      }
    });
  });
  // ----------------------------- Изменение ----------------------------------------
  
  // Редактируем индустрию
  // Открываем модалку
  $(document).on('click', '[data-open="first-edit"]', function() {

    // Получаем данные о индустрии
    var id = $(this).closest('.item').attr('id').split('-')[1];

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
        $('#form-first-edit .first-item').val(1);
        $('#form-first-edit .item-id').val(id);
        if (result.moderation == 1) {
          var data = '<div class="checkbox"><input type="checkbox" name="moderation" id="indystry-moderation" checked value="1"><label for="indystry-moderation"><span>Временная запись.</span></label></div>';
          $('#form-first-edit .first-item').after(data);
        };
        if (result.system_item == 1) {
          $('#system-first-edit').prop('checked', true);
        };
      }
    });
  });

  // Редактируем сектор
  // Открываем модалку
  $(document).on('click', '[data-open="medium-edit"]', function() {
    var id = $(this).closest('.item').attr('id').split('-')[1];
    var first = $(this).closest('.first-item').attr('id').split('-')[1];
    $('.first-id-field').val(first);

    var parent;
    if ($(this).closest('.item').hasClass('parent')) {
      var parent = $(this).closest('.parent').parent().parent('.parent').attr('id').split('-')[1];
    } else {
      parent = $(this).closest('.parent').attr('id').split('-')[1];
    };
    
    // Получаем список секторов
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/sectors_list",
      type: "POST",
      data: {id: id, parent: parent},
      success: function(date){
        var result = $.parseJSON(date);
        $('.sectors-list').append(result);
      }
    });

    // Получаем данные о medium
    // Сам ajax запрос
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/sectors/" + id + "/edit",
      type: "GET",
      success: function(date) {
        var result = $.parseJSON(date);
        $('#form-medium-edit .name-field').val(result.name);
        $('#form-medium-edit .medium-item').val(1);
        $('#form-medium-edit .medium-parent-id-field').val(result.parent_id);
        $('#form-medium-edit .item-id').val(id);
        if (result.moderation == 1) {
          var data = '<div class="checkbox"><input type="checkbox" name="moderation" id="indystry-moderation" checked value="1"><label for="indystry-moderation"><span>Временная запись.</span></label></div>';
          $('#form-medium-edit .medium-item').after(data);
        };
        if (result.system_item == 1) {
          $('#system-medium-edit').prop('checked', true);
        };
      }
    });
  });

  // Отправляем Ajax
  $(document).on('click', '.submit-edit', function(event) {
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: '/sectors/' + $(this).closest('.form-edit').find('.item-id:first').val(),
      type: "PATCH",
      data: $(this).closest('.form-edit').serialize(),
      success:function(html){
        $('#content').html(html);
        Foundation.reInit($('#content'));
        $('.form-edit').foundation('resetForm'); 
      }
    });
  });

  // При закрытии модалки очищаем поля
  $(document).on('click', '.close-modal, .modal-button', function() {
    $('.name-field').val('');
    $('.first-item').val(0);
    $('.medium-item').val(0);
    $('.first-id-field').val('');
    $('.medium-parent-id-field').val('');
    $('.item-error').css('display', 'none');
    $('.sectors-list').empty();
    $('input[name=moderation]').closest('.checkbox').remove();
    $('input[name=system_item]').prop('checked', false);
  });
});
</script>
@endsection