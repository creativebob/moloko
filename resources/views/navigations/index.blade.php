@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $page_info->name. ' ' . $site->name }}" />
{{-- Скрипты меню в шапке --}}
@include('includes.scripts.sortable-inhead')
@endsection

@section('title', $page_info->title . ' ' . $site->name)

@section('breadcrumbs', Breadcrumbs::render('section', $parent_page_info, $site, $page_info))

@section('title-content')
{{-- Меню --}}
@include('includes.title-content', ['page_info' => $page_info, 'page_alias' => 'sites/'.$site->alias.'/'.$page_info->alias, 'class' => App\Navigation::class, 'type' => 'sections-menu', 'name' => $site->name])
@endsection

@section('content')
{{-- Список --}}
<div class="grid-x">
  <div class="small-12 cell">
    <ul class="vertical menu accordion-menu content-list" id="content" data-accordion-menu data-multi-open="false" data-slide-speed="250" data-entity-alias="navigations">
      @if($navigations)

      {{-- Шаблон вывода и динамического обновления --}}
      @include('navigations.navigations-list', ['navigations' => $navigations, 'class' => 'App\Navigation', 'entity' => $entity, 'type' => 'modal'])

      @endif
    </ul>
  </div>
</div>
@endsection

@section('modals')
{{-- Модалки --}}
<section id="modal"></section>
{{-- Модалка удаления ajax --}}
@include('includes.modals.modal-delete-ajax')
@endsection

@section('scripts')
{{-- Маска ввода --}}
@include('includes.scripts.inputs-mask')

{{-- Скрипт подсветки многоуровневого меню --}}
@include('includes.scripts.multilevel-menu-active-scripts')

{{-- Скрипт отображения на сайте --}}
@include('includes.scripts.ajax-display')

{{-- Скрипт системной записи --}}
@include('includes.scripts.ajax-system')

<script type="text/javascript">
  $(function() {

  // Берем алиас сайта
  var siteAlias = '{{ $alias }}';

  // ------------------------------ Удаление ajax -------------------------------------------
  $(document).on('click', '[data-open="item-delete-ajax"]', function() {
    // Находим описание сущности, id и название удаляемого элемента в родителе
    var parent = $(this).closest('.item');
    var entity_alias = parent.attr('id').split('-')[0];
    var id = parent.attr('id').split('-')[1];
    var name = parent.data('name');
    $('.title-delete').text(name);
    $('.delete-button-ajax').attr('id', 'del-' + entity_alias + '-' + id);
  });

  // Подтверждение удаления и само удаление
  $(document).on('click', '.delete-button-ajax', function(event) {

    // Блочим отправку формы
    event.preventDefault();
    var entity_alias = $(this).attr('id').split('-')[1];
    var id = $(this).attr('id').split('-')[2];

    // Ajax
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: '/admin/sites/' + siteAlias + '/' + entity_alias + '/' + id,
      type: "DELETE",
      success: function (html) {
        $('#content').html(html);
        Foundation.reInit($('#content'));
        $('#delete-button-ajax').removeAttr('id');
        $('.title-delete').text('');
      }
    });
  });

  // Функция появления окна с ошибкой
  function showError (msg) {
    var error = "<div class=\"callout item-error\" data-closable><p>" + msg + "</p><button class=\"close-button error-close\" aria-label=\"Dismiss alert\" type=\"button\" data-close><span aria-hidden=\"true\">&times;</span></button></div>";
    return error;
  };

  // ------------------- Проверка на совпадение имени --------------------------------------
  // Обозначаем таймер для проверки
  var timerId;
  var time = 400;

  // Первая буква заглавная
  function newParagraph (name) {
    name = name.charAt(0).toUpperCase() + name.substr(1).toLowerCase();
    return name;
  };
  
  function navigationCheck (name, submit, db) {

    // Блокируем аттрибут базы данных
    $(db).val(0);

    // Смотрим сколько символов
    var lenName = name.length;

    // Если символов больше 3 - делаем запрос
    if (lenName > 3) {

      // Первая буква сектора заглавная
      name = newParagraph (name);

      // Сам ajax запрос
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/admin/sites/'+ siteAlias + '/navigation_check',
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
    } else {
      // Удаляем все значения, если символов меньше 3х
      $(submit).prop('disabled', false);
      $('.item-error').css('display', 'none');
      $(db).val(0);
    };
  };

  // -------------------------------- Добавляем навигацию -------------------------------------
  // Открываем модалку
  $(document).on('click', '[data-open="first-add"]', function() {
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: '/admin/sites/' + siteAlias + '/navigations/create',
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
    var submit = '#submit-first-add';
    // Значение поля с разрешением
    var db = '#form-first-add .first-item';
    // Выполняем запрос
    clearTimeout(timerId);   
    timerId = setTimeout(function() {
      navigationCheck (name, submit, db);
    }, time); 
  });

  // Добавляем
  $(document).on('click', '#submit-first-add', function(event) {
    event.preventDefault();

    // Ajax запрос
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: '/admin/sites/' + siteAlias + '/navigations',
      type: "POST",
      data: $('#form-first-add').serialize(),
      success:function(html) {
        $('#content').html(html);
        Foundation.reInit($('#content'));
      }
    });
  });

  // ------------------------------- Редактируем навигацию -------------------------------------
  // Открываем модалку
  $(document).on('click', '[data-open="first-edit"]', function() {
    // Получаем данные о разделе
    var id = $(this).closest('.item').attr('id').split('-')[1];

    // Ajax запрос
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/admin/sites/" + siteAlias + "/navigations/" + id + "/edit",
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
    var submit = '#submit-first-edit';
    // Значение поля с разрешением
    var db = '#form-first-edit .first-item';
    // Выполняем запрос
    clearTimeout(timerId);   
    timerId = setTimeout(function() {
      navigationCheck (name, submit, db);
    }, time); 
  });

  // Меняем данные
  $(document).on('click', '#submit-first-edit', function(event) {
    event.preventDefault();

    // Получаем id навигации
    var id = $('#navigation-id').val();

    // Ajax запрос
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: '/admin/sites/' + siteAlias + '/navigations/' + id,
      type: "PATCH",
      data: $('#form-first-edit').serialize(),
      success:function(html) {
        $('#content').html(html);
        Foundation.reInit($('#content'));
      }
    });
  });

  // -------------------------------- Добавление пункта меню -----------------------------------
  // Открываем модалку
  $(document).on('click', '[data-open="medium-add"]', function() {
    var parent = $(this).closest('.item').attr('id').split('-')[1];
    var navigation = $(this).closest('.first-item').attr('id').split('-')[1];
    // alert(navigation + parent);
    if (parent == navigation) {
      // Если id родителя совпадает с id навигации, значит родитель навигация
      parent = null;
    };

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: '/admin/sites/' + siteAlias + '/menus/create',
      type: "GET",
      data: {navigation_id: navigation, menu_parent_id: parent},
      success: function(html){
        $('#modal').html(html);
        $('#medium-add').foundation();
        $('#medium-add').foundation('open');
      }
    }); 
  });

  // Отправляем
  $(document).on('click', '#submit-medium-add', function(event) {
    event.preventDefault();

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: '/admin/sites/' + siteAlias + '/menus',
      type: "POST",
      data: $('#form-medium-add').serialize(),
      success: function(html){
        $('#content').html(html);
        Foundation.reInit($('#content'));
      }
    }); 
  });

  // ----------------------------------- Редактируем меню -------------------------------------
  // Открываем модалку
  $(document).on('click', '[data-open="medium-edit"]', function() {
    var id = $(this).closest('.item').attr('id').split('-')[1];
    // Аjax запрос
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/admin/sites/" + siteAlias + "/menus/" + id + "/edit",
      type: "GET",
      success: function(html){
        // alert(html);
        $('#modal').html(html);
        $('#medium-edit').foundation();
        $('#medium-edit').foundation('open');
        // $('#menu_id').val(id);
      }
    });
  });

  // Отправляем
  $(document).on('click', '#submit-medium-edit', function(event) {
    event.preventDefault();
    var id =  $('#menu_id').val();
    // Аjax запрос
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: '/admin/sites/' + siteAlias + '/menus/' + id,
      type: "PATCH",
      data: $('#form-medium-edit').serialize(),
      success: function(html){
        $('#content').html(html);
        Foundation.reInit($('#content'));
      }
    }); 
  });

  // ---------------------------------- Закрытие модалки -----------------------------------
  $(document).on('click', '.icon-close-modal, #submit-first-add, #submit-first-edit, #submit-medium-add, #submit-medium-edit', function() {
    $(this).closest('.reveal-overlay').remove();
  });
});
</script>
@endsection