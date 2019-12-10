@extends('layouts.app')

@section('title', 'Редактировать сайт')

@section('breadcrumbs', Breadcrumbs::render('alias-edit', $page_info, $site))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">РЕДАКТИРОВАТЬ сайт</h2>
   </div>
   <div class="top-bar-right">
   </div>
</div>
@endsection

@section('content')

{{ Form::model($site, ['route' => ['sites.update', $site->id], 'data-abide', 'novalidate']) }}
{{ method_field('PATCH') }}

@include('sites.form', ['submit_text' => 'Редактировать'])

{{ Form::close() }}



@endsection

@section('modals')
    @include('includes.modals.modal-delete-ajax')
    <div id="modal"></div>
@endsection

@push('scripts')
    <script>

        var site_id = '{{ $site->id }}';

        $(document).on('click', '#button-create-plugin', function (event) {

            $.get('/admin/plugins/create', {
              site_id: site_id
            }, function (html) {
                $('#modal').html(html).foundation();
                $('#modal-plugin').foundation('open');
            });
        });

        $(document).on('click', '#submit-store-plugin', function (event) {
            event.preventDefault();

            var buttons = $('.button');

            buttons.prop('disabled', true);

            $.post('/admin/plugins', $('#form-plugin').serialize(), function (html) {
                $('#table-plugins tbody').append(html);
                $('#modal-plugin').closest('.reveal-overlay').remove();

                buttons.prop('disabled', false);
            });
        });

        $(document).on('click', '.sprite-edit', function (event) {
            var id = $(this).closest('.item').attr('id').split('-')[1];

            $.get('/admin/plugins/' + id + '/edit', function (html) {
                $('#modal').html(html).foundation();
                $('#modal-plugin').foundation('open');
            });
        });

        $(document).on('click', '#submit-update-plugin', function (event) {
            event.preventDefault();

            var buttons = $('.button');

            buttons.prop('disabled', true);

            var id = $('#item-id').val();

            $.ajax({
                url: '/admin/plugins/' + id,
                type: "PATCH",
                data: $('#form-plugin').serialize(),
                success:function(html) {

                    $('#modal-plugin').closest('.reveal-overlay').remove();
                    buttons.prop('disabled', false);
                }
            });
        });

        // Мягкое удаление с ajax
        $(document).on('click', '[data-open="item-delete-ajax"]', function() {

            // Находим описание сущности, id и название удаляемого элемента в родителе
            var parent = $(this).closest('.item');
            var id = parent.attr('id').split('-')[1];
            var name = parent.data('name');

            $('.title-delete').text(name);
            $('.delete-button-ajax').attr('id', 'plugins-' + id);
        });

        // Подтверждение удаления и само удаление
        $(document).on('click', '.delete-button-ajax', function(event) {

            // Блочим отправку формы
            event.preventDefault();
            var id = $(this).attr('id').split('-')[1];
            var buttons = $('.button');
            buttons.prop('disabled', true);

            // Ajax
            $.ajax({
                url: '/admin/plugins/' + id + '/ajax_delete',
                type: "DELETE",
                success: function (data) {
                    if (data == true) {
                        $('#plugins-' + id).remove();
                        $('#item-delete-ajax').foundation('close');
                        buttons.prop('disabled', false);
                    }
                }
            });
        });

        // ---------------------------------- Закрытие модалки -----------------------------------
        $(document).on('click', '.close-modal', function() {
            $(this).closest('.reveal-overlay').remove();
        });
    </script>
@endpush
