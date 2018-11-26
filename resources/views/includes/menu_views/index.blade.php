@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $page_info->page_description }}" />
{{-- Скрипты меню в шапке --}}
@include('includes.scripts.sortable-inhead')
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('content-count')
{{-- Количество элементов --}}
{{ (isset($items) && $items->isNotEmpty()) ? num_format($items->count(), 0) : 0 }}
@endsection

@section('title-content')
{{-- Меню --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => $class, 'type' => 'menu'])
@endsection

@section('content')
{{-- Список --}}
<div class="grid-x">
    <div class="small-12 cell">
        <ul class="vertical menu accordion-menu content-list" id="content" data-accordion-menu data-multi-open="false" data-slide-speed="250" data-entity-alias="{{ $entity }}">

            @if (isset($items) && $items->isNotEmpty())

            {{-- Шаблон вывода и динамического обновления --}}
            @include('includes.menu_views.category_list', ['items' => $items, 'class' => $class, 'entity' => $entity, 'type' => 'modal'])

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

        var entity = '{{ $entity }}';

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
        function checkField (check, entity_alias, field = null) {
            var item = check;
            var value = item.val();
            var submit = item.closest('form').find('input[type=submit]');
            field = field != null ? field : item.attr('name');
            entity_alias = entity_alias != null ? entity_alias : $('#content').data('entity-alias');

            // Если символов больше 3 - делаем запрос
            if (value.length > 3) {

                // Сам ajax запрос
                $.ajax({
                    url: "/admin/check",
                    type: "POST",
                    data: {value: value, field: field, entity_alias: entity_alias},
                    beforeSend: function () {
                        item.siblings('.find-status').addClass('icon-load');
                    },
                    success: function(data){
                        item.siblings('.find-status').removeClass('icon-load');

                        // Состояние ошибки
                        if (data > 0) {
                            item.siblings('.item-error').show();
                        } else {
                            item.siblings('.item-error').hide();
                        };

                        // Состояние кнопки
                        $(submit).prop('disabled', item.closest('form').find($(".item-error:visible")).length > 0);

                    }
                });
            } else {
                item.siblings('.item-error').hide();
                $(submit).prop('disabled', item.closest('form').find($(".item-error:visible")).length > 0);
            };
        };


        // Проверка существования
        $(document).on('keyup', 'input[name=name], input[name=tag]', function() {
            var check = $(this);

            // Выполняем запрос
            clearTimeout(timerId);
            timerId = setTimeout(function() {
                checkField(check);
            }, time);
        });

  // ----------- Добавление -------------
    // Открываем модалку
    $(document).on('click', '[data-open="first-add"], [data-open="medium-add"]', function() {
        let parent = $(this).closest('.first-item').hasClass('item') ? $(this).closest('.item').attr('id').split('-')[1] : null;

        $.get('/admin/' + entity + '/create', {sector_id: parent}, function(html){
            $('#modal').html(html).foundation();
            $('#first-add').foundation('open');
        });
    });




  // ----------- Изменение -------------

  // Открываем модалку
  $(document).on('click', '[data-open="first-edit"], [data-open="medium-edit"]', function() {
    // Получаем данные о разделе
    var id = $(this).closest('.item').attr('id').split('-')[1];

    $.get("/admin/" + entity + "/" + id + "/edit", function(html) {
        $('#modal').html(html);
        $('#first-edit').foundation();
        $('#first-edit').foundation('open');
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