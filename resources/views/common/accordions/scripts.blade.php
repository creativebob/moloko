<script type="application/javascript">

    // Определяем сущность для работы
    var entity = $('#content').data('entity-alias');

    $(function() {

        // ----------- Добавление -------------
        $(document).on('click', '[data-open="modal-create"]', function() {
            $.get('/admin/' + entity + '/create', {
                parent_id: $(this).closest('.item').hasClass('item') ? $(this).closest('.item').attr('id').split('-')[1] : null,
                category_id: $(this).closest('.first-item').hasClass('item') ? $(this).closest('.first-item').attr('id').split('-')[1] : null
            }, function(html) {
                $('#modal').html(html);
                $('#modal-create').foundation().foundation('open');
            });
        });

        // ----------- Изменение -------------
        $(document).on('click', '.sprite-edit', function() {
            let id = $(this).closest('.item').attr('id').split('-')[1];

            $.get("/admin/" + entity + "/" + id + "/edit", function(html) {
                $('#modal').html(html);
                $('#modal-edit').foundation().foundation('open');
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

    // Присваиваем при клике на первый элемент списка активный класс

    // При клике на первый элемент списка
    $(document).on('click', '.first-link', function() {

        if ($(this).closest('.first-item').hasClass('first-active')) {
            // Если имеет активный класс - сносим его
            $(this).closest('.first-item').removeClass('first-active');
        } else {
            // Иначе ставим элементу активный класс
            $('.content-list .first-item').removeClass('first-active');
            $(this).closest('.first-item').addClass('first-active');
        };

        // Сносим все активные медиумы
        $('.medium-active').removeClass('medium-active');
        // $('.medium-item').attr('aria-expanded', 'false');
    });

    // Видим клик по среднему пункту
    $(document).on('click', '.medium-link', function() {
        if ($(this).closest('.medium-item').hasClass('medium-active')) {
            // Если имеет активный класс - сносим его
            $('.medium-item').removeClass('medium-active');
            // $(this).closest('.medium-item').attr('aria-expanded', 'false');
            // $('#content-list').foundation('toggle', $(this).closest('.medium-item').find('.last-list'));
        } else {
            // Иначе ставим элементу активный класс
            $('.medium-item').removeClass('medium-active');
            $(this).closest('.medium-item').addClass('medium-active');
        };

        // Перебираем родителей и посвечиваем их
        var parents = $(this).parents('.medium-list');
        for (var i = 0; i < parents.length; i++) {
            $(parents[i]).parent('.medium-item').addClass('medium-active');
        };
    });

</script>