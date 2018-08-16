@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $page_info->page_description }}" />
{{-- Скрипты меню в шапке --}}
@include('includes.scripts.sortable-inhead')
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('title-content')
{{-- Меню --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\Sector::class, 'type' => 'menu'])
@endsection

@section('content')
{{-- Список --}}
<div class="grid-x">
  <div class="small-12 cell">
    <ul class="vertical menu accordion-menu content-list" id="content" data-accordion-menu data-multi-open="false" data-slide-speed="250" data-entity-alias="sectors">
    @if($sectors)

    {{-- Шаблон вывода и динамического обновления --}}
    @include('includes.menu-views.enter', ['grouped_items' => $sectors, 'class' => 'App\Sector', 'entity' => $entity, 'type' => 'modal'])

    @endif
    </ul>
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

{{-- Скрипт чекбоксов --}}
@include('includes.scripts.checkbox-control')

{{-- Скрипт отображения на сайте --}}
@include('includes.scripts.ajax-display')

{{-- Скрипт системной записи --}}
@include('includes.scripts.ajax-system')

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
        url: "/admin/sector_check",
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

  // ---------------------------- Индустрия -----------------------------------------------

  // ----------- Добавление -------------
  // Открываем модалку
  $(document).on('click', '[data-open="first-add"]', function() {
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: '/admin/sectors/create',
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
      sectorCheck (name, submit, db)
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
      url: "/admin/sectors/" + id + "/edit",
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
      sectorCheck (name, submit, db)
    }, time); 
  });

  // ------------------------------- Сектор --------------------------------------------

  // ----------- Добавление -------------
  // Модалка
  $(document).on('click', '[data-open="medium-add"]', function() {

    var parent;
    if ($(this).closest('.first-item').hasClass('parent')) {
      parent = $(this).closest('.item').attr('id').split('-')[1];
    } else {
      parent = $(this).closest('.item').attr('id').split('-')[1];
    };
    
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: '/admin/sectors/create',
      type: "GET",
      data: {parent_id: parent},
      success: function(html){
        // alert(html);
        $('#modal').html(html);
        $('#medium-add').foundation();
        $('#medium-add').foundation('open');
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
    // Выполняем запрос
    clearTimeout(timerId);   
    timerId = setTimeout(function() {
      sectorCheck (name, submit, db)
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
      url: "/admin/sectors/" + id + "/edit",
      type: "GET",
      success: function(html) {
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
    // Выполняем запрос
    clearTimeout(timerId);   
    timerId = setTimeout(function() {
      sectorCheck (name, submit, db)
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
      url: '/admin/sectors',
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

    var id = $('#sector-id').val();

    // Ajax запрос
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: '/admin/sectors/' + id,
      type: "PATCH",
      data: $(this).closest('form').serialize(),
      success:function(html) {
        $('#content').html(html);
        Foundation.reInit($('#content'));
      }
    });
  });

  // ---------------------------------- Закрытие модалки -----------------------------------
  $(document).on('click', '.icon-close-modal, .submit-add, .submit-edit', function() {
    $(this).closest('.reveal-overlay').remove();
  });
});
</script>
@endsection