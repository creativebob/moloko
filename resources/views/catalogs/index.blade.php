@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $page_info->page_description }}" />
{{-- Скрипты меню в шапке --}}
@include('includes.scripts.sortable-inhead')
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('section', $parent_page_info, $site, $page_info))

@section('content-count')
{{-- Количество элементов --}}
{{ $catalogs->isNotEmpty() ? num_format($catalogs->count(), 0) : 0 }}
@endsection

@section('title-content')
{{-- Меню --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\Catalog::class, 'type' => 'menu'])
@endsection

@section('content')
{{-- Список --}}
<div class="grid-x">
    <div class="small-12 cell">
        <ul class="vertical menu accordion-menu content-list" id="content" data-accordion-menu data-multi-open="false" data-slide-speed="250" data-entity-alias="{{ $entity }}">

            @if ($catalogs->isNotEmpty())
            {{-- Шаблон вывода и динамического обновления --}}
            @include('includes.menu_views.category_list', ['items' => $catalogs, 'alias' => $site->alias])
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
    $(function() {

        // Берем алиас сайта
        var site_alias = '{{ $site->alias }}';

        // ----------- Добавление -------------
        $(document).on('click', '[data-open="modal-create"]', function() {
            $.get('/admin/sites/' + site_alias + '/catalogs/create', {
                parent_id: $(this).closest('.item').hasClass('item') ? $(this).closest('.item').attr('id').split('-')[1] : null,
                category_id: $(this).closest('.first-item').hasClass('item') ? $(this).closest('.first-item').attr('id').split('-')[1] : null
            }, function(html) {
                $('#modal').html(html).foundation();
                $('#modal-create').foundation('open');
            });
        });
        // ------------------------ Кнопка добавления ---------------------------------------
        $(document).on('click', '.submit-create', function(event) {
            var form = $(this).closest('form');
            if (submitAjax(form.attr('id'))) {
                $(this).prop('disabled', true);
                $.post('/admin/sites/' + site_alias + '/catalogs', form.serialize(), function(html) {
                    $('#content').html(html);
                    form.closest('.reveal-overlay').remove();
                    Foundation.reInit($('#content'));
                });
            }
        });

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

            var buttons = $('button');
            buttons.prop('disabled', true);

            var entity_alias = $(this).attr('id').split('-')[1];
            var id = $(this).attr('id').split('-')[2];

            // Ajax
            $.ajax({
                url: '/admin/sites/' + site_alias + '/catalogs/' + id,
                type: "DELETE",
                success: function (html) {
                    $('#content').html(html);
                    Foundation.reInit($('#content'));
                    $('#delete-button-ajax').removeAttr('id');
                    $('.title-delete').text('');
                    $('#item-delete-ajax').foundation('close');
                    buttons.prop('disabled', false);
                }
            });
        });

        // ---------------------------------- Закрытие модалки -----------------------------------
        $(document).on('click', '.icon-close-modal', function() {
            $(this).closest('.reveal-overlay').remove();
        });
    });
</script>

{{-- Маска ввода --}}
@include('includes.scripts.inputs-mask')

{{-- Скрипт подсветки многоуровневого меню --}}
@include('includes.scripts.multilevel-menu-active-scripts')

{{-- Скрипт отображения на сайте --}}
@include('includes.scripts.ajax-display')

{{-- Скрипт системной записи --}}
@include('includes.scripts.ajax-system')

@include('catalogs.scripts')
@endsection