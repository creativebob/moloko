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
            @include('includes.menu_views.category_list', ['items' => $items, 'class' => $class, 'entity' => $entity, 'type' => $type])

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
<script type="text/javascript">
    // Определяем сущьность для работы
    var entity = $('#content').data('entity-alias');

    $(function() {
        // ----------- Добавление -------------
        $(document).on('click', '[data-open="modal-create"]', function() {
            $.get('/admin/' + entity + '/create', {
                parent_id: $(this).closest('.item').hasClass('item') ? $(this).closest('.item').attr('id').split('-')[1] : null,
                category_id: $(this).closest('.first-item').hasClass('item') ? $(this).closest('.first-item').attr('id').split('-')[1] : null
            }, function(html) {
                $('#modal').html(html).foundation();
                $('#modal-create').foundation('open');
            });
        });

        // ----------- Изменение -------------
        $(document).on('click', '.sprite-edit', function() {
            let id = $(this).closest('.item').attr('id').split('-')[1];

            $.get("/admin/" + entity + "/" + id + "/edit", function(html) {
                $('#modal').html(html).foundation();
                $('#modal-edit').foundation('open');
            });
        });

        // ------------------------ Кнопка добавления ---------------------------------------
        $(document).on('click', '.submit-create', function(event) {
            var form = $(this).closest('form');
            if (submitAjax(form.attr('id'))) {
                $(this).prop('disabled', true);
                $.post('/admin/' + entity, form.serialize(), function(html) {
                    form.closest('.reveal-overlay').remove();
                    $('#content').html(html);
                    Foundation.reInit($('#content'));
                });
            }
        });

        // ------------------------ Кнопка обновления ---------------------------------------
        $(document).on('click', '.submit-edit', function(event) {
            var form = $(this).closest('form');
            if (submitAjax(form.attr('id'))) {
                $(this).prop('disabled', true);
                var id = form.find('input[name=id]').val();

                // Ajax запрос
                $.ajax({
                    url: '/admin/' + entity + '/' + id,
                    type: "PATCH",
                    data: form.serialize(),
                    success:function(html) {
                        form.closest('.reveal-overlay').remove();
                        $('#content').html(html);
                        Foundation.reInit($('#content'));
                    }
                });
            }
        });

        // ---------------------------------- Закрытие модалки -----------------------------------
        $(document).on('click', '.icon-close-modal', function() {
            $(this).closest('.reveal-overlay').remove();
        });
    });
</script>

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

{{-- Проверка поля на существование --}}
@include('includes.scripts.check')
@endsection