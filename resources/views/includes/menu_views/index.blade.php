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

<script type="text/javascript">
    $(function() {

        var entity = $('#content').data('entity-alias');

        // Проверка существования
        $(document).on('keyup', '.check-field', function() {
            var entity = $('#content').data('entity-alias');
            var check = $(this);

            let timerId;
            clearTimeout(timerId);
            timerId = setTimeout(function() {
                checkField(check);
            }, 300);
        });

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
            event.preventDefault();
            $.post('/admin/' + entity, $(this).closest('form').serialize(), function(html) {
                $('#content').html(html);
                Foundation.reInit($('#content'));
            });
        });

        // ------------------------ Кнопка обновления ---------------------------------------
        $(document).on('click', '.submit-edit', function(event) {
            event.preventDefault();
            var id = $(this).closest('form').find('input[name=id]').val();

            // Ajax запрос
            $.ajax({
                url: '/admin/sectors/' + id,
                type: "PATCH",
                data: $(this).closest('form').serialize(),
                success:function(html) {
                    // $(this).closest('.reveal').foundation('close');
                    $('#content').html(html);
                    Foundation.reInit($('#content'));
                }
            });
        });

        // ---------------------------------- Закрытие модалки -----------------------------------
        $(document).on('click', '.icon-close-modal, .submit-create, .submit-edit', function() {
            $(this).closest('.reveal-overlay').remove();
        });
    });
</script>
@endsection