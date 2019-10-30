<script type="application/javascript">

    var catalog_id = '{{ $rubricator_id }}';

    $(function() {

        // ----------- Добавление -------------
        $(document).on('click', '[data-open="modal-create"]', function() {

            $.get('/admin/rubricators/' + catalog_id + '/rubricators_items/create', {
                parent_id: $(this).closest('.item').hasClass('item') ? $(this).closest('.item').attr('id').split('-')[1] : null,
                category_id: $(this).closest('.first-item').hasClass('item') ? $(this).closest('.first-item').attr('id').split('-')[1] : null
            }, function(html) {
                $('#modal').html(html);
                $('#modal-create').foundation().foundation('open');
            });
        });

        // ----------- Кнопка добавления ---------------------------------------
        $(document).on('click', '.submit-create', function(event) {
            var form = $(this).closest('form');

            if (window.submitAjax(form.attr('id'))) {
                $(this).prop('disabled', true);
                $.post('/admin/rubricators/' + catalog_id + '/rubricators_items', form.serialize(), function(html) {
                    form.closest('.reveal-overlay').remove();
                    $('#content').html(html);
                    Foundation.reInit($('#content'));
                });
            }
        });

        // ----------- Изменение -------------
        $(document).on('click', '.sprite-edit', function() {
            let id = $(this).closest('.item').attr('id').split('-')[1];

            $.get('/admin/rubricators/' + catalog_id + '/rubricators_items/' + id + '/edit', function(html) {
                $('#modal').html(html);
                $('#modal-edit').foundation().foundation('open');
            });
        });

        // ----------- Кнопка обновления ---------------------------------------
        $(document).on('click', '.submit-edit', function(event) {
            var form = $(this).closest('form');
            if (window.submitAjax(form.attr('id'))) {
                $(this).prop('disabled', true);

                // Ajax запрос
                $.ajax({
                    url: '/admin/rubricators/' + catalog_id + '/rubricators_items/' + form.find('#item-id').val(),
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

        // ----------- Удаление -------------
        $(document).on('click', '[data-open="item-delete-ajax"]', function() {

            // Находим описание сущности, id и название удаляемого элемента в родителе
            var parent = $(this).closest('.item');
            var entity_alias = parent.attr('id').split('-')[0];
            var id = parent.attr('id').split('-')[1];
            var name = parent.data('name');
            $('.title-delete').text(name);
            $('.delete-button-ajax').attr('id', 'del-' + entity_alias + '-' + id);
        });

        // ----------- Кнопка удаления ---------------------------------------
        $(document).on('click', '.delete-button-ajax', function(event) {

            // Блочим отправку формы
            event.preventDefault();
            var entity_alias = $(this).attr('id').split('-')[1];
            var id = $(this).attr('id').split('-')[2];
            var buttons = $('.button');
            buttons.prop('disabled', true);

            // Ajax
            $.ajax({
                url: '/admin/rubricators/' + catalog_id + '/rubricators_items/' + id,
                type: "DELETE",
                success: function (html) {
                    // alert(html);
                    $('#item-delete-ajax').foundation('close');
                    $('#content').html(html);
                    Foundation.reInit($('#content'));
                    $('.delete-button-ajax').removeAttr('id');
                    buttons.prop('disabled', false);
                }
            });
        });

        // ----------- Закрытие модалки -----------------------------------
        $(document).on('click', '.remove-modal', function() {
            $(this).closest('.reveal-overlay').remove();
        });

    });

</script>
