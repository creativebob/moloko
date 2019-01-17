<script type="text/javascript">

    // Определяем сущность для работы
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
                // Ajax запрос
                $.ajax({
                    url: '/admin/' + entity + '/' + form.find('input[name=id]').val(),
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