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
@include('includes.title-content.menu', ['page_info' => $page_info, 'class' => App\Department::class])
@endsection

@section('content')
{{-- Список --}}
<div class="grid-x">
  <div class="small-12 cell">
    @if($departments_tree)
    {{-- Шаблон вывода и динамического обновления --}}
    @include('departments.filials-list', $departments_tree)
    @endif
  </div>
</div>
@endsection

@section('modals')
<section id="modal"></section>
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

{{-- Список городов --}}
@include('includes.scripts.cities-list')
<script type="text/javascript">
  $(function() {

  // Обозначаем таймер для проверки
  var timerId;
  var time = 400;

  // ------------------------ Проверка на совпадение имени филиала --------------------
  function departmentCheck (name, submit, db, filial) {
    // Блокируем аттрибут базы данных
    $(db).val(0);

    // Смотрим сколько символов
    var lenName = name.length;

    // Если символов больше 3 - делаем запрос
    if (lenName > 3) {

      // Сам ajax запрос
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/department_check',
        type: "POST",
        data: {name: name, filial_id: filial},
        beforeSend: function () {
          $('#name-check').addClass('icon-load');
        },
        success: function(date){
          $('#name-check').removeClass('icon-load');
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
    } else {
      $(submit).prop('disabled', false);
      $('.item-error').css('display', 'none');
      $(db).val(0);
    };
  };

  // ---------------------------- Филиал -----------------------------------------------

  // ----------- Добавление -------------
  // Открываем модалку
  $(document).on('click', '[data-open="first-add"]', function() {
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: '/departments/create',
      type: "GET",
      success: function(html){
        $('#modal').html(html);
        $('#first-add').foundation();
        $('#first-add').foundation('open');
      }
    }); 
  });

  // Проверка существования
  $(document).on('keyup', '#form-first-add .name-field', function() {
    // Получаем фрагмент текста
    var name = $('#form-first-add .name-field').val();
    // Указываем название кнопки
    var submit = '.submit-add';
    // Значение поля с разрешением
    var db = '#form-first-add .first-item';
    // Выполняем запрос
    clearTimeout(timerId);   
    timerId = setTimeout(function() {
      departmentCheck (name, submit, db, null);
    }, time); 
  });

  // ----------- Изменение -------------

  // Открываем модалку
  $(document).on('click', '[data-open="first-edit"]', function() {
    // Получаем данные о разделе
    var id = $(this).closest('.item').attr('id').split('-')[1];

    // Ajax запрос
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/departments/" + id + "/edit",
      type: "GET",
      success: function(html) {
        $('#modal').html(html);
        $('#first-edit').foundation();
        $('#first-edit').foundation('open');
      }
    });
  });

  // Проверка существования
  $(document).on('keyup', '#form-first-edit .name-field', function() {
    // Получаем фрагмент текста
    var name = $('#form-first-edit .name-field').val();
    // Указываем название кнопки
    var submit = '.submit-edit';
    // Значение поля с разрешением
    var db = '#form-first-edit .first-item';
    // Выполняем запрос
    clearTimeout(timerId);   
    timerId = setTimeout(function() {
      departmentCheck (name, submit, db, null);
    }, time); 
  });

  // ------------------------------- Отдел --------------------------------------------

  // ----------- Добавление -------------
  // Модалка
  $(document).on('click', '[data-open="medium-add"]', function() {

    var parent = $(this).closest('.item').attr('id').split('-')[1];
    var filial = $(this).closest('.first-item').attr('id').split('-')[1];
    
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: '/departments/create',
      type: "GET",
      data: {department_parent_id: parent},
      success: function(html){
        $('#modal').html(html);
        $('#medium-add').foundation();
        $('#medium-add').foundation('open');
        $('.filial-id').val(filial);
      }
    }); 
  });

  // Проверка существования
  $(document).on('keyup', '#form-medium-add .name-field', function() {
    // Получаем фрагмент текста
    var name = $('#form-medium-add .name-field').val();
    // Указываем название кнопки
    var submit = '.submit-add';
    // Значение поля с разрешением
    var db = '#form-medium-add .medium-item';
    // Филиал
    var filial = $('#filial-id').val();
    // Выполняем запрос
    clearTimeout(timerId);   
    timerId = setTimeout(function() {
      departmentCheck (name, submit, db, filial)
    }, time); 
  });

  // ----------- Изменение -------------
  // Открываем модалку
  $(document).on('click', '[data-open="medium-edit"]', function() {
    // Получаем данные о разделе
    var id = $(this).closest('.item').attr('id').split('-')[1];

    // Ajax запрос
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/departments/" + id + "/edit",
      type: "GET",
      success: function(html) {
        // alert(html);
        $('#modal').html(html);
        $('#medium-edit').foundation();
        $('#medium-edit').foundation('open');
      }
    });
  });

  // Проверка существования
  $(document).on('keyup', '#form-medium-edit .name-field', function() {
    // Получаем фрагмент текста
    var name = $('#form-medium-edit .name-field').val();
    // Указываем название кнопки
    var submit = '.submit-edit';
    // Значение поля с разрешением
    var db = '#form-medium-edit .medium-item';
    // Филиал
    var filial = $('#filial-id').val();
    // Выполняем запрос
    clearTimeout(timerId);   
    timerId = setTimeout(function() {
      departmentCheck (name, submit, db, filial)
    }, time); 
  });

  // ------------------------ Кнопка добавления ---------------------------------------
  $(document).on('click', '.submit-add', function(event) {
    event.preventDefault();

    // alert($(this).closest('form').serialize());
    // Ajax запрос
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: '/departments',
      type: "POST",
      data: $(this).closest('form').serialize(),
      success:function(html) {
        $('#content').html(html);
        Foundation.reInit($('#content'));
      }
    });
  });

  // Добавляем должность в филиал или отдел
  $(document).on('click', '#submit-position-add', function(event) {
    event.preventDefault();

    // alert($(this).closest('form').serialize());
    // Ajax запрос
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: '/staff',
      type: "POST",
      data: $(this).closest('form').serialize(),
      success:function(html) {
        $('#content').html(html);
        Foundation.reInit($('#content'));
      }
    });
  });

  // ------------------------ Кнопка обновления ---------------------------------------
  $(document).on('click', '.submit-edit', function(event) {
    event.preventDefault();

    var id = $('#department-id').val();
    // alert($(this).closest('form').serialize());

    // Ajax запрос
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: '/departments/' + id,
      type: "PATCH",
      data: $(this).closest('form').serialize(),
      success:function(html) {
        // alert(html);
        $('#content').html(html);
        Foundation.reInit($('#content'));
      }
    });
  });

  // ---------------------------------- Закрытие модалки -----------------------------------
  $(document).on('click', '.icon-close-modal, .submit-add, .submit-edit, #submit-position-add', function() {
    $(this).closest('.reveal-overlay').remove();
  });

  // Открываем меню и подменю, если только что добавили населенный пункт
  @if(!empty($data))
    // Общие правила
    // Подсвечиваем Филиал
    $('#departments-{{ $data['section_id'] }}').addClass('first-active').find('.icon-list:first').attr('aria-hidden', 'false').css('display', 'block');
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
      }
    };
    @endif
  });
</script>
@endsection