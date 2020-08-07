@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $page_info->page_description }}" />
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('content-count')
{{-- Количество элементов --}}
{{ $departments->isNotEmpty() ? num_format($departments->count(), 0) : 0 }}
@endsection

@section('title-content')
{{-- Меню --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => $class, 'type' => $type])
@endsection

@section('content')
{{-- Список --}}
<div class="grid-x">
    <div class="small-12 cell">
        <ul class="vertical menu accordion-menu content-list" id="content" data-accordion-menu data-multi-open="false" data-slide-speed="250" data-entity-alias="departments">

            @if($departments->isNotEmpty())
                {{-- Шаблон вывода и динамического обновления --}}
                @include('system.pages.hr.departments.filials_list')
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

@push('scripts')
    @include('includes.scripts.sortable-inhead')
    @include('includes.scripts.units-scripts')

    {{-- Скрипт модалки удаления ajax --}}
    @include('includes.scripts.delete-ajax-script')

    {{-- Маска ввода --}}
    @include('includes.scripts.inputs-mask')

    {{-- Скрипт подсветки многоуровневого меню --}}
    @include('includes.scripts.multilevel-menu-active-scripts')

    {{-- Список городов --}}
    @include('includes.scripts.class.city_search')

    {{-- Скрипт отображения на сайте --}}
    @include('includes.scripts.ajax-display')

    {{-- Скрипт системной записи --}}
    @include('includes.scripts.ajax-system')

    <script type="application/javascript">
        $(function() {

            // ------------------------ Проверка на совпадение названия --------------------
            function departmentCheck (check) {

                var item = check;
                var name = item.val();
                var id = item.closest('form').find('#item-id').val();
                var filial_id = item.closest('form').find('#filial-id').val();
                var submit = item.closest('form').find('.button');

                // Если символов больше 3 - делаем запрос
                if (name.length > 3) {

                    // $(submit).prop('disabled', true);

                    // Сам ajax запрос
                    $.ajax({
                        url: "/admin/department_check",
                        type: "POST",
                        data: {name: name, filial_id: filial_id, id: id},
                        beforeSend: function () {
                            item.siblings('.find-status').addClass('icon-load');
                        },
                        success: function(count){
                            item.siblings('.find-status').removeClass('icon-load');

                            // Состояние ошибки
                            if (count > 0) {
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
            $(document).on('keyup', '.name-field', function() {

                var check = $(this);

                // Обозначаем таймер для проверки
                let timerId;
                // Выполняем запрос
                clearTimeout(timerId);
                timerId = setTimeout(function() {
                    departmentCheck (check);
                }, 300);
            });

            // ----------- Добавление -------------
            $(document).on('click', '[data-open="modal-create"]', function() {
                $.get('/admin/departments/create', {
                    parent_id: $(this).closest('.item').hasClass('item') ? $(this).closest('.item').attr('id').split('-')[1] : null,
                    filial_id: $(this).closest('.first-item').hasClass('item') ? $(this).closest('.first-item').attr('id').split('-')[1] : null
                }, function(html){
                    $('#modal').html(html);
                    $('#modal-create').foundation().foundation('open');
                });
            });

            // ----------- Изменение -------------
            $(document).on('click', '[data-open="modal-edit"]', function() {
                let id = $(this).closest('.item').attr('id').split('-')[1];

                $.get("/admin/departments/" + id + "/edit", function(html) {
                    $('#modal').html(html);
                    $('#modal-edit').foundation().foundation('open');
                });
            });

            // ------------------------ Кнопка добавления ---------------------------------------
            $(document).on('click', '.submit-create', function(event) {
                var form = $(this).closest('form');
                if (window.submitAjax(form.attr('id'))) {
                    $(this).prop('disabled', true);
                    $.post('/admin/departments', form.serialize(), function(html) {
                        form.closest('.reveal-overlay').remove();
                        $('#content').html(html);
                        Foundation.reInit($('#content'));
                    });
                }
            });

            // ------------------------ Кнопка добавления ---------------------------------------
            $(document).on('click', '#submit-staffer-create', function(event) {
                var form = $(this).closest('form');
                $(this).prop('disabled', true);
                $.post('/admin/staff', form.serialize(), function(html) {
                    form.closest('.reveal-overlay').remove();
                    $('#content').html(html);
                    Foundation.reInit($('#content'));
                });
            });

            // ------------------------ Кнопка обновления ---------------------------------------
            $(document).on('click', '.submit-edit', function(event) {
                // Блокируем отправку формы по кнопке
                event.preventDefault();

                var form = $(this).closest('form');
                if (window.submitAjax(form.attr('id'))) {
                    $(this).prop('disabled', true);
                    $.ajax({
                        url: '/admin/departments/' + form.find('#item-id').val(),
                        type: "PATCH",
                        data: form.serialize(),
                        success:function(html) {
                            form.closest('.reveal-overlay').remove();
                            // alert(html);
                            $('#content').html(html);
                            Foundation.reInit($('#content'));
                        }
                    });
                };
            });

            // ---------------------------------- Закрытие модалки -----------------------------------
            $(document).on('click', '.close-modal', function() {
                $(this).closest('.reveal-overlay').remove();
            });
        });
    </script>

    {{-- Скрипт чекбоксов --}}
    @include('includes.scripts.checkbox-control')

@endpush
