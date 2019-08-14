<script type="application/javascript">

    // Меняем режим отображения
    $(document).on('click', '[data-open="item-system"]', function(event) {
        event.preventDefault();

        let item = $(this);
        let id = item.closest('.item').attr('id').split('-')[1];
        // let entity_alias = $('#content').data('entity-alias');
        let entity_alias = item.closest('.item').attr('id').split('-')[0];

        // Oпределяем тип
        if ($('#content').hasClass('content-list')) {
            var type = 'menu';
            var parent = item.closest('.controls-list');
        } else {
            var type = 'table';
            var parent = item.closest('.item');
        }

        var nested = item.data('nested');

        action = item.hasClass("icon-system-unlock") ? 'lock' : 'unlock';

        // Ajax
        $.post('/admin/system', {
            id: id,
            action: action,
            entity_alias: entity_alias
        }, function (system) {
            // Если нет ошибки
            if (system == true) {
                if (action == 'lock') {
                    item.removeClass('icon-system-unlock');
                    item.addClass('icon-system-lock');

                    if (type == 'menu') {
                        parent.siblings('.actions-list').find('.del').html('');
                    } else {
                        parent.find('.td-delete').html('');
                    }

                } else {
                    item.removeClass('icon-system-lock');
                    item.addClass('icon-system-unlock');
                    if (nested == 0) {

                        if (type == 'menu') {
                           parent.siblings('.actions-list').find('.del').html('<div class="icon-list-delete sprite" data-open="item-delete-ajax"></div>');
                        } else {
                            parent.find('.td-delete').html('<a class="icon-delete sprite" data-open="item-delete"></a>');
                        }

                    };
                }
            } else {
                // Выводим ошибку на страницу
                alert(system);
            };
        });
    });
</script>
